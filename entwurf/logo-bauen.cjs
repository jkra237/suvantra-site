// Baut das Suvantra-Logo aus Dancing Script Bold (SIL OFL) als reine Pfade – die Website lädt keine Schrift.
//   node entwurf/logo-bauen.cjs   (aus suvantra-site)
// Ergebnis in assets/: suvantra-logo.svg (Glow-Verlauf, für dunklen Grund), suvantra-logo-hell.svg (einfarbig hell),
// suvantra-logo-dunkel.svg (einfarbig schwarz, Druck auf Weiß).
const fs = require('fs');
const path = require('path');
const fontkit = require(path.join(__dirname, '..', '..', 'pdf-editor', 'node_modules', '@pdf-lib', 'fontkit'));

const font = fontkit.create(fs.readFileSync(path.join(__dirname, 'schrift', 'DancingScript-Bold.ttf')));
const run = font.layout('Suvantra');
const upm = font.unitsPerEm;

// Glyphen nebeneinandersetzen (mit Kerning/Ligaturen aus dem Layout), y nach unten spiegeln
let x = 0;
const teile = [];
let minX = Infinity, maxX = -Infinity, minY = Infinity, maxY = -Infinity;
run.glyphs.forEach((g, i) => {
  const pos = run.positions[i];
  const p = g.path.translate(x + pos.xOffset, pos.yOffset).scale(1, -1);
  const bb = p.bbox;
  if (isFinite(bb.minX)) { minX = Math.min(minX, bb.minX); maxX = Math.max(maxX, bb.maxX); minY = Math.min(minY, bb.minY); maxY = Math.max(maxY, bb.maxY); }
  teile.push(p.toSVG());
  x += pos.xAdvance;
});
const rand = upm * 0.04;
const vb = [minX - rand, minY - rand, maxX - minX + 2 * rand, maxY - minY + 2 * rand].map((n) => Math.round(n));
const d = teile.join(' ');
const breite = 600, hoehe = Math.round((breite * vb[3]) / vb[2]);

const svg = (fuellung, defs = '') => `<svg xmlns="http://www.w3.org/2000/svg" viewBox="${vb.join(' ')}" width="${breite}" height="${hoehe}" role="img" aria-label="Suvantra">
<title>Suvantra</title>${defs}
<path d="${d}" fill="${fuellung}"/>
</svg>
`;
const verlauf = `<defs><linearGradient id="sv" x1="0" y1="0" x2="1" y2="0.15"><stop offset="0" stop-color="#F2B3A3"/><stop offset=".45" stop-color="#FFFFFF"/><stop offset="1" stop-color="#A5F3FC"/></linearGradient></defs>`;

const assets = path.join(__dirname, '..', 'assets');
fs.writeFileSync(path.join(assets, 'suvantra-logo.svg'), svg('url(#sv)', verlauf));
fs.writeFileSync(path.join(assets, 'suvantra-logo-hell.svg'), svg('#EEF2F5'));
fs.writeFileSync(path.join(assets, 'suvantra-logo-dunkel.svg'), svg('#14171B'));
console.log('viewBox', vb.join(' '), 'Größe', breite, '×', hoehe, 'Pfadlänge', d.length);
