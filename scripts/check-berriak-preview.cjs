// Render isolated copies of the resulting production pages; no production writes.
const fs = require('fs');
const path = require('path');
const { chromium } = require(process.env.KBU_PLAYWRIGHT);
(async () => {
  const base = path.resolve(__dirname, '../output/berriak-20260916');
  const css = fs.readFileSync(path.join(base,'fixtures/main.css'),'utf8');
  const browser = await chromium.launch({headless:true,channel:'msedge'});
  try {
    for (const language of ['eu','es']) {
      for (const width of [1440,390]) {
        const page = await browser.newPage({viewport:{width,height:1000}});
        await page.route('**/*',route=>route.abort());
        let html = fs.readFileSync(path.join(base,`fixtures/result-${language}.html`),'utf8');
        html=html.replace(/<script\b[^>]*>[\s\S]*?<\/script>/gi,'').replace(/<link\b[^>]*>/gi,'').replace('</head>',`<style>${css}</style></head>`);
        await page.setContent(html);
        const count = await page.locator('.updates-entry').count();
        const overflow = await page.evaluate(()=>document.documentElement.scrollWidth>innerWidth);
        if(count!==35 || overflow) throw new Error(`${language} ${width}: count ${count}; overflow ${overflow}`);
        await page.screenshot({path:path.join(base,`preview-${language}-${width}.png`)});
        await page.locator('.updates-entry').first().scrollIntoViewIfNeeded();
        await page.screenshot({path:path.join(base,`news-${language}-${width}.png`)});
        console.log(`PASS ${language} ${width}px: 35 cards, no horizontal overflow`);
        await page.close();
      }
    }
  } finally {await browser.close();}
})().catch(e=>{console.error(e);process.exit(1)});
