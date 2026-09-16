"""Read the supplied ODT and inspect linked source metadata, without copying articles."""
import concurrent.futures, json, re, urllib.request, zipfile
from pathlib import Path
from xml.etree import ElementTree as ET
from html import unescape

root = Path(__file__).resolve().parents[1]
out = root / 'output' / 'berriak-20260916'
out.mkdir(parents=True, exist_ok=True)
with zipfile.ZipFile('D:/Kerman/Berriak.odt') as z:
    tree = ET.fromstring(z.read('content.xml'))
ns = '{urn:oasis:names:tc:opendocument:xmlns:text:1.0}'
paras = [''.join(e.itertext()).strip() for e in tree.iter() if e.tag in (ns+'p', ns+'h')]
media = {'GasteizBerri','El Diario.es','Berria','Naiz','EITB','Noticias de Álava','SER','El Correo','Gasteiz hoy'}
items = []
medium = title = ''
for p in paras:
    if p in media: medium = p
    elif p.startswith('https://'):
        items.append(dict(medium=medium, title=title, url=p))
    elif p: title = p

def fetch(item):
    try:
        req = urllib.request.Request(item['url'], headers={'User-Agent':'Mozilla/5.0'})
        with urllib.request.urlopen(req, timeout=22) as r:
            html = r.read().decode('utf-8',errors='replace')
            item.update(status=r.status, final_url=r.url)
        item['metadata'] = [unescape(x) for x in re.findall(r'<meta\b[^>]+>',html,re.I) if re.search('description|published|date|og:title',x,re.I)]
        item['dates'] = sorted(set(re.findall(r'20\d{2}-\d{2}-\d{2}',html)))[:30]
    except Exception as e: item['error'] = str(e)
    return item
with concurrent.futures.ThreadPoolExecutor(max_workers=8) as pool:
    checked = list(pool.map(fetch, items))
(out/'source-check.json').write_text(json.dumps(checked,ensure_ascii=False,indent=2),encoding='utf-8')
for i,x in enumerate(checked):
    print(i, x['medium'], x['title'], x.get('status',x.get('error')), json.dumps(x.get('metadata',[]),ensure_ascii=False))
