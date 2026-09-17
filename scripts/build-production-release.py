"""Build the temporary, self-contained WordPress production release tool."""
from pathlib import Path
import hashlib
import json
import zipfile

root = Path(__file__).resolve().parents[1]
package = root / "tools" / "kermanentzat-production-release"
output = root / "output" / "production-release-20260917"
output.mkdir(parents=True, exist_ok=True)

manifest = json.loads((package / "media-manifest.json").read_text(encoding="utf-8"))
source = root / "tmp" / "case-media-public"
for item in manifest:
    path = source / item["path"]
    if not path.is_file():
        raise SystemExit(f"Missing media: {item['path']}")
    if path.stat().st_size != item["size"] or hashlib.sha256(path.read_bytes()).hexdigest() != item["sha256"]:
        raise SystemExit(f"Media does not match manifest: {item['path']}")

target = output / "kermanentzat-production-release.zip"
with zipfile.ZipFile(target, "w", zipfile.ZIP_DEFLATED) as archive:
    for path in sorted(package.iterdir()):
        if path.is_file():
            archive.write(path, f"kermanentzat-production-release/{path.name}")
print(f"Built {target}")
