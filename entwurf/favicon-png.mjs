import { readFileSync, writeFileSync } from 'node:fs';
const list = await (await fetch('http://127.0.0.1:9334/json')).json();
const ws = new WebSocket(list.find((t) => t.type === 'page').webSocketDebuggerUrl);
await new Promise((r) => ws.addEventListener('open', r));
let id = 0; const pend = new Map();
ws.addEventListener('message', (e) => { const m = JSON.parse(e.data); if (pend.has(m.id)) { pend.get(m.id)(m); pend.delete(m.id); } });
const ev = (expression) => new Promise((res) => { const i = ++id; pend.set(i, (m) => res(m.result.result?.value ?? m.result.exceptionDetails?.exception?.description)); ws.send(JSON.stringify({ id: i, method: 'Runtime.evaluate', params: { expression, awaitPromise: true, returnByValue: true } })); });
const dir = 'C:/Users/jkraj/cc test/suvantra-site/assets/';
for (const [quelle, ziel, w] of [['favicon.svg','favicon-32.png',32],['favicon.svg','favicon-180.png',180],['favicon.svg','favicon-512.png',512]]) {
  const svg = readFileSync(dir + quelle, 'utf8');
  const url = await ev(`(async () => { const img = new Image(); img.src = 'data:image/svg+xml;charset=utf-8,' + encodeURIComponent(${JSON.stringify(svg)}); await img.decode();
    const w = ${w}, h = ${w}; const c = document.createElement('canvas'); c.width = w; c.height = h;
    c.getContext('2d').drawImage(img, 0, 0, w, h); return c.toDataURL('image/png'); })()`);
  writeFileSync(dir + ziel, Buffer.from(url.split(',')[1], 'base64')); console.log(ziel);
}
ws.close();
