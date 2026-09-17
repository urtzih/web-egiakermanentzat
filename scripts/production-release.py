"""SFTP backup and deployment for the approved 2026-09-17 release.

Never uploads assets/media, credentials, WordPress core, or arbitrary uploads.
Credentials are read from the ignored .env and never logged.
"""
from pathlib import Path
import argparse
import hashlib
import json
import stat
import time
from datetime import datetime, timezone
from urllib.parse import urljoin

import paramiko
import requests
from bs4 import BeautifulSoup

ROOT = Path(__file__).resolve().parents[1]
BASE = "https://egiakermanentzat.eus"
REMOTE = "www"
TOOL = "kermanentzat-production-release/kermanentzat-production-release.php"
PIN = "N4DQHF8Pzfs8s5/QgV8tEx5NgKZeghhkDfpnO+ZG7Fo"


def config():
    values = {}
    for line in (ROOT / ".env").read_text(encoding="utf-8-sig").splitlines():
        if line.strip() and not line.lstrip().startswith("#") and "=" in line:
            key, value = line.split("=", 1)
            values[key.strip()] = value.strip().strip("\"'")
    return values


def connect(cfg):
    import base64
    transport = paramiko.Transport(("ftp.cluster131.hosting.ovh.net", 22))
    transport.start_client(timeout=20)
    digest = base64.b64encode(hashlib.sha256(transport.get_remote_server_key().asbytes()).digest()).decode().rstrip("=")
    if digest != PIN:
        transport.close()
        raise RuntimeError("Unexpected SFTP host key")
    transport.auth_password(cfg["egiakez_cluster131_hosting_ovh_net_USER"], cfg["egiakez_cluster131_hosting_ovh_net_PASSWORD"])
    return transport, paramiko.SFTPClient.from_transport(transport)


def mkdirs(sftp, path):
    parts = path.split("/")
    for index in range(1, len(parts) + 1):
        item = "/".join(parts[:index])
        try:
            sftp.stat(item)
        except FileNotFoundError:
            sftp.mkdir(item)


def download_tree(sftp, remote, local, inventory):
    local.mkdir(parents=True, exist_ok=True)
    for item in sftp.listdir_attr(remote):
        path = remote + "/" + item.filename
        target = local / item.filename
        if stat.S_ISDIR(item.st_mode):
            download_tree(sftp, path, target, inventory)
        elif stat.S_ISREG(item.st_mode):
            sftp.get(path, str(target))
            if target.stat().st_size != item.st_size:
                raise RuntimeError("Backup size mismatch: " + path)
            with sftp.open(path, "rb") as stream:
                stream.prefetch(item.st_size)
                digest = hashlib.file_digest(stream, "sha256").hexdigest()
            if digest != hashlib.sha256(target.read_bytes()).hexdigest():
                raise RuntimeError("Backup hash mismatch: " + path)
            inventory.append({"path": path, "size": item.st_size, "sha256": digest})
        else:
            raise RuntimeError("Unsupported SFTP entry: " + path)


def upload_tree(sftp, local, remote):
    mkdirs(sftp, remote)
    for path in sorted(local.rglob("*")):
        relative = path.relative_to(local)
        if relative.parts[:2] == ("assets", "media"):
            continue
        target = remote + "/" + relative.as_posix()
        if path.is_dir():
            mkdirs(sftp, target)
        elif path.is_file():
            sftp.put(str(path), target)
            with sftp.open(target, "rb") as stream:
                stream.prefetch(path.stat().st_size)
                remote_hash = hashlib.file_digest(stream, "sha256").hexdigest()
            if remote_hash != hashlib.sha256(path.read_bytes()).hexdigest():
                raise RuntimeError("Upload hash mismatch: " + relative.as_posix())


def session(cfg):
    http = requests.Session()
    response = http.get(BASE + "/wp-login.php", timeout=30)
    response.raise_for_status()
    response = http.post(BASE + "/wp-login.php", data={
        "log": cfg["WP_PRO_ADMIN_USER"], "pwd": cfg["WP_PRO_ADMIN_PASSWORD"],
        "wp-submit": "Log In", "redirect_to": BASE + "/wp-admin/", "testcookie": "1",
    }, timeout=30)
    response.raise_for_status()
    if "wp-login.php" in response.url or not any("wordpress_logged_in" in c.name for c in http.cookies):
        raise RuntimeError("WordPress administrator login failed")
    return http


def page(http):
    response = http.get(BASE + "/wp-admin/tools.php?page=kermanentzat-production-release", timeout=90)
    response.raise_for_status()
    return BeautifulSoup(response.text, "html.parser")


def action(http, name):
    soup = page(http)
    field = soup.find("input", {"name": "action", "value": "kermanentzat_release_" + name})
    if field is None:
        message = " ".join(n.get_text(" ", strip=True) for n in soup.select(".notice-error"))
        raise RuntimeError("Release action unavailable: " + name + ": " + message)
    form = field.find_parent("form")
    data = {n.get("name"): n.get("value", "") for n in form.select("input[name]")}
    response = http.post(BASE + "/wp-admin/admin-post.php", data=data, timeout=180)
    response.raise_for_status()
    if name != "download" and "release_status=" not in response.url:
        raise RuntimeError("Release action did not complete: " + BeautifulSoup(response.text, "html.parser").get_text(" ", strip=True)[-600:])
    return response


def run(args):
    cfg = config()
    if args.command in ("backup", "install-tool", "deploy", "restore-code", "remove-tool"):
        transport, sftp = connect(cfg)
        try:
            if args.command == "backup":
                destination = ROOT / "output" / "production-backups" / datetime.now(timezone.utc).strftime("%Y%m%dT%H%M%SZ")
                inventory = []
                for directory in ("themes", "plugins", "mu-plugins", "uploads"):
                    download_tree(sftp, REMOTE + "/wp-content/" + directory, destination / "wp-content" / directory, inventory)
                for filename in ("wp-config.php", ".htaccess"):
                    sftp.get(REMOTE + "/" + filename, str(destination / filename))
                (destination / "inventory.json").write_text(json.dumps(inventory, indent=2), encoding="utf-8")
                print("Verified local backup: " + str(destination) + "; files=" + str(len(inventory)))
            elif args.command == "install-tool":
                upload_tree(sftp, ROOT / "wp-content/plugins/kermanentzat-editorial", REMOTE + "/wp-content/plugins/kermanentzat-editorial")
                upload_tree(sftp, ROOT / "tools/kermanentzat-production-release", REMOTE + "/wp-content/plugins/kermanentzat-production-release")
                print("Inactive editorial plugin and release tool uploaded")
            elif args.command == "restore-code":
                journal = json.loads((Path(args.backup) / "remote-code-restore.json").read_text())
                for change in reversed(journal):
                    if not change['target'].startswith('www/wp-content/') or not change['previous'].startswith('kermanentzat-release-backups/'):
                        raise RuntimeError('Invalid restoration path')
                    sftp.rename(change['target'], change['previous'] + '.replaced')
                    sftp.rename(change['previous'], change['target'])
                print('Previous production code restored')
            elif args.command == "remove-tool":
                tool_root = REMOTE + '/wp-content/plugins/kermanentzat-production-release'
                for filename in sftp.listdir(tool_root):
                    if not stat.S_ISREG(sftp.lstat(tool_root + '/' + filename).st_mode):
                        raise RuntimeError('Unexpected release tool entry')
                    sftp.remove(tool_root + '/' + filename)
                sftp.rmdir(tool_root)
                print('Temporary release tool removed')
            else:
                if not args.backup or not (Path(args.backup) / "database-before.json").is_file():
                    raise RuntimeError("A verified file backup and downloaded database-before.json are required")
                stamp = datetime.now(timezone.utc).strftime("%Y%m%dT%H%M%SZ")
                preserved = "kermanentzat-release-backups/" + stamp
                mkdirs(sftp, preserved)
                changes = []
                for local, parent, name in (
                    (ROOT / "wp-content/themes/kermanentzat-prototype", REMOTE + "/wp-content/themes", "kermanentzat-prototype"),
                    (ROOT / "wp-content/plugins/kermanentzat-editorial", REMOTE + "/wp-content/plugins", "kermanentzat-editorial"),
                ):
                    provisional = parent + "/." + name + "-release-" + stamp
                    upload_tree(sftp, local, provisional)
                    target = parent + "/" + name
                    previous = preserved + "/" + name
                    sftp.rename(target, previous)
                    try:
                        sftp.rename(provisional, target)
                    except Exception:
                        sftp.rename(previous, target)
                        raise
                    changes.append({"target": target, "previous": previous})
                    (Path(args.backup) / "remote-code-restore.json").write_text(json.dumps(changes, indent=2), encoding="utf-8")
                for file in (ROOT / "wp-content/mu-plugins").glob("*.php"):
                    target = REMOTE + "/wp-content/mu-plugins/" + file.name
                    temporary = target + ".release-" + stamp
                    sftp.put(str(file), temporary)
                    try:
                        sftp.rename(target, preserved + "/" + file.name)
                    except FileNotFoundError:
                        pass
                    sftp.rename(temporary, target)
                    changes.append({"target": target, "previous": preserved + "/" + file.name})
                    (Path(args.backup) / "remote-code-restore.json").write_text(json.dumps(changes, indent=2), encoding="utf-8")
                media_target = REMOTE + "/wp-content/uploads/kermanentzat-release-20260917"
                mkdirs(sftp, media_target)
                with sftp.open(media_target + "/.htaccess", "w") as stream:
                    stream.write("Require all denied\n")
                upload_tree(sftp, ROOT / "tmp/case-media-public", media_target)
                (Path(args.backup) / "remote-code-restore.json").write_text(json.dumps(changes, indent=2), encoding="utf-8")
                print("Code and protected media uploaded; WordPress apply remains pending")
        finally:
            sftp.close()
            transport.close()
        return

    http = session(cfg)
    if args.command in ("activate-tool", "deactivate-tool"):
        response = http.get(BASE + "/wp-admin/plugins.php", timeout=30)
        soup = BeautifulSoup(response.text, "html.parser")
        row = soup.find("tr", {"data-plugin": TOOL})
        link = row.select_one(".activate a" if args.command == "activate-tool" else ".deactivate a") if row else None
        if link:
            result = http.get(urljoin(response.url, link["href"]), timeout=90)
            result.raise_for_status()
        print("Release tool " + args.command + " completed")
    elif args.command == "db-backup":
        action(http, "backup")
        response = action(http, "download")
        saved = response.json()
        if saved.get("release") != "2026-09-17-case-editorial-v1" or not saved.get("before_hash"):
            raise RuntimeError("Unexpected database backup")
        if not args.backup:
            raise RuntimeError("--backup must name the SFTP backup directory")
        (Path(args.backup) / "database-before.json").write_bytes(response.content)
        print("Database backup downloaded; affected posts=" + str(len(saved["before"]["posts"])))
    elif args.command == "apply":
        action(http, "apply")
        print("Release applied")
        if args.backup:
            (Path(args.backup) / "database-after.json").write_bytes(action(http, "download").content)
    elif args.command == "restore":
        action(http, "restore")
        print("Database release restored; restore previous SFTP directories separately")


if __name__ == "__main__":
    parser = argparse.ArgumentParser(description=__doc__)
    parser.add_argument("command", choices=["backup", "install-tool", "activate-tool", "deactivate-tool", "remove-tool", "db-backup", "deploy", "apply", "restore", "restore-code"])
    parser.add_argument("--backup")
    run(parser.parse_args())
