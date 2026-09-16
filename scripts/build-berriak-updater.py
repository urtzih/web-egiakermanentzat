"""Build a self-contained, temporary WordPress updater and offline preview."""
import csv, html, json, re, zipfile
from pathlib import Path

root = Path(__file__).resolve().parents[1]
package = root/'tools'/'kermanentzat-berriak-update'
out = root/'output'/'berriak-20260916'
sources = json.loads((out/'source-check.json').read_text(encoding='utf-8'))
rows = list(csv.DictReader((root/'scripts'/'berriak-20260916.tsv').open(encoding='utf-8'), delimiter='\t'))
pairs = {16:17, 18:19, 21:22}
items = []
for row in rows:
    i = int(row['index'])
    src = sources[i]
    url = src['url']
    if i == 20: url = url.removesuffix('a')
    medium = {'El Diario.es':'elDiario.es','EITB':'ORAIN / EITB','SER':'Cadena SER','Gasteiz hoy':'Gasteiz Hoy'}.get(src['medium'],src['medium'])
    items.append(dict(id=f'berriak-{i:02}',medium=medium,date=row['date'],
        kind='opinion' if i==11 else 'audio' if i in (14,28) else 'press',
        source_title=src['title'],
        eu=dict(title=row['title_eu'],summary=row['summary_eu'],url=url),
        es=dict(title=row['title_es'],summary=row['summary_es'],url=sources[pairs[i]]['url'] if i in pairs else url)))
assert len(items)==35
items.sort(key=lambda x:x['date'], reverse=True)
for lang in ('eu','es'):
    assert len({x[lang]['url'] for x in items})==35
package.mkdir(parents=True,exist_ok=True)
(package/'news.json').write_text(json.dumps(items,ensure_ascii=False,indent=2)+'\n',encoding='utf-8')
preview = ['<!doctype html><html lang="es"><meta charset="utf-8"><meta name="viewport" content="width=device-width,initial-scale=1"><title>Vista previa · Berriak / Actualidad</title><style>body{margin:0;background:#f3eee5;color:#20211e;font:18px/1.6 system-ui}main{max-width:1060px;margin:auto;padding:32px}nav{position:sticky;top:0;background:#f3eee5;padding:12px}a{color:#374b37}article{display:grid;grid-template-columns:180px 1fr;gap:28px;border-top:1px solid #aaa;padding:24px 0}h3{margin:0}p{margin:12px 0}small{display:block}section{margin:45px 0}@media(max-width:650px){article{grid-template-columns:1fr;gap:8px}main{padding:18px}}</style><main><h1>Berriak / Actualidad</h1><p>35 referencias por idioma. La noticia de ORAIN del 2 de agosto ya publicada conservará su texto actual. Esta vista muestra las propuestas del paquete.</p><nav><a href="#eu">Euskara</a> · <a href="#es">Castellano</a></nav>']
for lang,label in [('eu','Berriak'),('es','Actualidad')]:
    preview.append(f'<section id="{lang}" lang="{lang}"><h2>{label}</h2>')
    for item in items:
        n = {k:html.escape(v,quote=True) for k,v in item[lang].items()}
        preview.append(f'<article><div>{html.escape(item["medium"])}<small>{item["date"]}</small></div><div><h3>{n["title"]}</h3><p>{n["summary"]}</p><a href="{n["url"]}" target="_blank" rel="noopener noreferrer">{"Jatorrizko argitalpena" if lang=="eu" else "Publicación original"} ↗</a></div></article>')
    preview.append('</section>')
preview.append('</main></html>')
(out/'vista-previa.html').write_text('\n'.join(preview),encoding='utf-8')
with zipfile.ZipFile(out/'kermanentzat-berriak-update.zip','w',zipfile.ZIP_DEFLATED) as archive:
    for path in sorted(package.iterdir()):
        if path.is_file(): archive.write(path, 'kermanentzat-berriak-update/'+path.name)
print('Built',out/'kermanentzat-berriak-update.zip')
