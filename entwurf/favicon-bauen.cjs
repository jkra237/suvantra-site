// Favicon aus dem Logo: das „S“ aus Dancing Script Bold im Verlauf, auf dunklem Quadrat mit Leuchten.
//   node entwurf/favicon-bauen.cjs   → assets/favicon.svg (PNG-Größen rendert entwurf/favicon-png.mjs)
const fs = require('fs');
const path = require('path');
const fontkit = require(path.join(__dirname, '..', '..', 'pdf-editor', 'node_modules', '@pdf-lib', 'fontkit'));
const font = fontkit.create(fs.readFileSync(path.join(__dirname, 'schrift', 'DancingScript-Bold.ttf')));
const g = font.layout('S').glyphs[0];
const p = g.path.scale(1, -1);
const bb = p.bbox;
// Das S mittig in ein 512er-Quadrat setzen, Höhe etwa 70 %
const ziel = 360, s = ziel / (bb.maxY - bb.minY);
const tx = 256 - ((bb.minX + bb.maxX) / 2) * s, ty = 256 - ((bb.minY + bb.maxY) / 2) * s + 6;
const svg = `<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 512 512" width="512" height="512">
<defs>
<linearGradient id="v" x1="0" y1="0" x2="1" y2="1"><stop offset="0" stop-color="#F2B3A3"/><stop offset=".5" stop-color="#FFFFFF"/><stop offset="1" stop-color="#A5F3FC"/></linearGradient>
<radialGradient id="l1" cx="30%" cy="35%" r="55%"><stop offset="0" stop-color="#E07A62" stop-opacity=".35"/><stop offset="1" stop-color="#E07A62" stop-opacity="0"/></radialGradient>
<radialGradient id="l2" cx="72%" cy="68%" r="55%"><stop offset="0" stop-color="#22D3EE" stop-opacity=".35"/><stop offset="1" stop-color="#22D3EE" stop-opacity="0"/></radialGradient>
</defs>
<rect width="512" height="512" rx="112" fill="#07090D"/>
<rect width="512" height="512" rx="112" fill="url(#l1)"/>
<rect width="512" height="512" rx="112" fill="url(#l2)"/>
<path transform="translate(${tx.toFixed(1)} ${ty.toFixed(1)}) scale(${s.toFixed(4)})" d="${p.toSVG()}" fill="url(#v)"/>
</svg>
`;
fs.writeFileSync(path.join(__dirname, '..', 'assets', 'favicon.svg'), svg);
console.log('assets/favicon.svg');
