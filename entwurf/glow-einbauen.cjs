// Setzt assets/glow-ebene.css ans Ende von assets/site.css und wp/suvantra/style.css (ersetzt eine vorhandene Fassung).
//   node entwurf/glow-einbauen.cjs   (aus suvantra-site)
const fs = require('fs');
const path = require('path');
const site = path.join(__dirname, '..');
const ebene = fs.readFileSync(path.join(site, 'assets', 'glow-ebene.css'), 'utf8').trim();
const ANFANG = '/* ==== Glow-Ebene';
for (const datei of ['assets/site.css', 'wp/suvantra/style.css']) {
  const f = path.join(site, datei);
  let s = fs.readFileSync(f, 'utf8');
  const i = s.indexOf(ANFANG);
  if (i >= 0) s = s.slice(0, i).trimEnd();
  fs.writeFileSync(f, s + '\n\n' + ebene + '\n');
  console.log('eingesetzt:', datei);
}
