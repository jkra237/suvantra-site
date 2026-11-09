// Baut den Entwurf „Glow“ für suvantra.eu als eine einzige HTML-Datei (Bilder eingebettet).
//   node entwurf/glow-entwurf-bauen.mjs  → entwurf/glow-entwurf.html
import { readFileSync, writeFileSync } from 'node:fs';
import { join, dirname } from 'node:path';
import { fileURLToPath } from 'node:url';

const site = join(dirname(fileURLToPath(import.meta.url)), '..');
const b64 = (f, typ) => `data:${typ};base64,${readFileSync(join(site, 'assets', f)).toString('base64')}`;
const PQ_ICON = b64('pq-icon-256.png', 'image/png');
const RP_ICON = b64('rp-icon-256.png', 'image/png');
const RP_SHOT = b64('rp-text.webp', 'image/webp');
const PQ_SHOT = b64('pq-schreiben.webp', 'image/webp');

const html = `<title>Suvantra Glow-Entwurf</title>
<meta name="claude:viewport-fit-sides" content="cover">
<style>
/* Layout: dunkle Seite, Firmenebene neutral; jedes Produkt bringt sein eigenes Leuchten mit
   (PureQuill Terrakotta, Rewrite PDF Cyan) – wie in den App-Symbolen. Eine Fassung, bewusst nur dunkel. */
:root{
  --grund:#07090D; --flaeche:#0D1117; --glas:rgba(255,255,255,.035); --glas-rand:rgba(255,255,255,.08);
  --ink:#EEF2F5; --ink-soft:#9AA6B2; --ink-faint:#66727E;
  --pq:#E07A62; --pq-glow:rgba(224,122,98,.30);
  --rp:#22D3EE; --rp-glow:rgba(34,211,238,.30);
  --firma:#C9D4DE;
  --ui:"Segoe UI Variable Text","Segoe UI",system-ui,sans-serif;
  --display:"Segoe UI Variable Display","Segoe UI",system-ui,sans-serif;
  color-scheme:dark;
}
*{box-sizing:border-box}
body{margin:0;background:var(--grund);color:var(--ink);font:17px/1.7 var(--ui);-webkit-font-smoothing:antialiased}
img{max-width:100%;display:block}
a{color:inherit}
.wrap{max-width:1180px;margin:0 auto;padding-left:max(16px,env(safe-area-inset-left,0px));padding-right:max(16px,env(safe-area-inset-right,0px))}
.hinweis-entwurf{background:#151a21;color:var(--ink-soft);font-size:13.5px;text-align:center;padding-block:8px;border-bottom:1px solid var(--glas-rand)}

/* Kopf: Glasleiste */
.top{position:sticky;top:env(safe-area-inset-top,0px);z-index:5;background:rgba(7,9,13,.72);backdrop-filter:blur(14px) saturate(1.3);border-bottom:1px solid var(--glas-rand)}
.top .wrap{display:flex;align-items:center;gap:20px;height:64px}
.marke{display:flex;align-items:center;gap:10px;font-weight:600;text-decoration:none}
.marke i{width:11px;height:11px;border-radius:3px;background:var(--firma);box-shadow:0 0 12px rgba(201,212,222,.5)}
nav{margin-left:auto;display:flex;gap:22px;font-size:15px;flex-wrap:wrap}
nav a{color:var(--ink-soft);text-decoration:none}
nav a:hover{color:var(--ink)}
.lang{font-size:13px;border:1px solid var(--glas-rand);border-radius:99px;padding:4px 12px;color:var(--ink-soft);text-decoration:none}

/* Held: ein großes, weiches Leuchten in beiden Produktfarben */
.held{position:relative;overflow:hidden;padding-block:clamp(72px,11vw,140px) clamp(48px,6vw,80px)}
.held::before{content:"";position:absolute;inset:-30% -10% auto;height:120%;pointer-events:none;
  background:radial-gradient(40% 50% at 30% 30%,rgba(224,122,98,.16),transparent 70%),radial-gradient(40% 50% at 72% 26%,rgba(34,211,238,.16),transparent 70%)}
.held .wrap{position:relative}
.augbraue{font:600 12.5px/1 var(--ui);letter-spacing:.12em;text-transform:uppercase;color:var(--ink-faint);margin:0 0 18px}
h1,h2,h3{font-family:var(--display);margin:0;letter-spacing:-.02em;line-height:1.08;text-wrap:balance}
h1{font-size:clamp(40px,6.4vw,72px);font-weight:700;max-width:16ch}
h1 em{font-style:normal;background:linear-gradient(90deg,var(--pq),var(--rp));-webkit-background-clip:text;background-clip:text;color:transparent}
.lead{font-size:clamp(18px,1.9vw,21px);line-height:1.55;color:var(--ink-soft);max-width:58ch;margin:22px 0 0}
h2{font-size:clamp(28px,3.6vw,40px);font-weight:700;margin-bottom:14px}
h3{font-size:22px;font-weight:650;margin-bottom:8px}
p{margin:0 0 14px;max-width:64ch}

/* Produktkarten: Glas mit dem Leuchten des jeweiligen Produkts */
section{padding-block:clamp(48px,7vw,96px)}
.karten{display:grid;grid-template-columns:repeat(auto-fit,minmax(280px,1fr));gap:22px;margin-top:34px}
.karte{position:relative;display:flex;flex-direction:column;gap:6px;padding:28px;border-radius:22px;text-decoration:none;
  background:var(--glas);border:1px solid var(--glas-rand);overflow:hidden;min-width:0;
  transition:transform .25s ease,border-color .25s ease,box-shadow .25s ease}
.karte::before{content:"";position:absolute;width:320px;height:320px;right:-110px;top:-140px;border-radius:50%;
  background:radial-gradient(closest-side,var(--glow),transparent);pointer-events:none;opacity:.9}
.karte.pq{--farbe:var(--pq);--glow:var(--pq-glow)}
.karte.rp{--farbe:var(--rp);--glow:var(--rp-glow)}
.karte:hover{transform:translateY(-3px);border-color:color-mix(in srgb,var(--farbe) 45%,transparent);box-shadow:0 0 0 1px color-mix(in srgb,var(--farbe) 25%,transparent),0 18px 50px -20px var(--glow)}
.karte img{width:64px;height:64px;border-radius:15px;margin-bottom:14px;position:relative;filter:drop-shadow(0 0 16px var(--glow))}
.karte p{color:var(--ink-soft);font-size:15.5px;position:relative}
.karte h3{position:relative}
.karte .mehr{margin-top:auto;font-weight:600;font-size:15px;color:var(--farbe);position:relative}
.marken{display:flex;gap:8px;flex-wrap:wrap;margin-bottom:6px;position:relative}
.marken span{font-size:12px;padding:3px 10px;border-radius:99px;border:1px solid color-mix(in srgb,var(--farbe) 40%,transparent);color:var(--farbe)}

/* Produktseite (Ausschnitt Rewrite PDF) */
.produkt{position:relative;overflow:hidden;border-top:1px solid var(--glas-rand)}
.produkt::before{content:"";position:absolute;left:50%;top:-10%;width:1100px;height:700px;transform:translateX(-50%);pointer-events:none;
  background:radial-gradient(closest-side,rgba(34,211,238,.14),transparent)}
.produkt .wrap{position:relative}
.p-kopf{display:grid;gap:18px;justify-items:start}
.p-icon{width:88px;height:88px;border-radius:22px;filter:drop-shadow(0 0 22px rgba(34,211,238,.45))}
.pille{font-size:13px;font-weight:600;padding:5px 12px;border-radius:99px;color:var(--rp);border:1px solid rgba(34,211,238,.4);background:rgba(34,211,238,.06)}
.knoepfe{display:flex;gap:12px;flex-wrap:wrap;margin-top:10px}
.btn{display:inline-flex;align-items:center;gap:8px;font:600 15px/1 var(--ui);padding:14px 22px;border-radius:99px;text-decoration:none;border:1px solid var(--glas-rand);color:var(--ink);background:var(--glas)}
.btn.pri{background:var(--rp);color:#032B36;border-color:var(--rp);box-shadow:0 0 24px rgba(34,211,238,.35)}
.btn:focus-visible,.karte:focus-visible{outline:2px solid var(--ink);outline-offset:3px}
.bild{margin-top:clamp(36px,5vw,60px);border-radius:20px;overflow:hidden;border:1px solid rgba(103,232,249,.45);
  box-shadow:0 0 0 1px rgba(34,211,238,.15),0 0 70px -10px rgba(34,211,238,.35),0 40px 80px -30px #000}
.saeulen{display:grid;grid-template-columns:repeat(auto-fit,minmax(220px,1fr));gap:18px;margin-top:40px}
.saeule{padding:22px;border-radius:18px;background:var(--glas);border:1px solid var(--glas-rand);min-width:0}
.saeule .nr{display:block;font-size:12px;letter-spacing:.1em;text-transform:uppercase;color:var(--rp);margin-bottom:10px;font-weight:600}
.saeule p{color:var(--ink-soft);font-size:15px;margin:0}
.leise{color:var(--ink-faint);font-size:14px;margin-top:28px}
@media (prefers-reduced-motion:reduce){.karte{transition:none}.karte:hover{transform:none}}
</style>

<div class="hinweis-entwurf">Entwurf – oben die Startseite, unten ein Ausschnitt der Produktseite Rewrite PDF</div>
<header class="top"><div class="wrap">
  <a class="marke" href="#"><i></i>Suvantra</a>
  <nav><a href="#">PureQuill Writer</a><a href="#">Rewrite PDF</a><a href="#">Impressum</a></nav>
  <a class="lang" href="#">English</a>
</div></header>

<section class="held"><div class="wrap">
  <p class="augbraue">Suvantra</p>
  <h1>Werkzeuge, die <em>offline</em> arbeiten.</h1>
  <p class="lead">Programme für Windows ohne Konto, ohne Server und ohne Abo. Was du schreibst und bearbeitest, bleibt auf deinem Rechner, weil die Programme deine Dateien nirgendwohin schicken.</p>
  <div class="karten">
    <a class="karte pq" href="#">
      <img src="${PQ_ICON}" alt="">
      <div class="marken"><span>Schreibprogramm</span><span>Kostenlos</span></div>
      <h3>PureQuill Writer</h3>
      <p>Schreiben ohne Ablenkung. Öffnet und speichert Word-Dateien mit hoher Kompatibilität, mit Zeitmaschine, Notizen an Textstellen und Rechtschreibprüfung in sieben Sprachen.</p>
      <span class="mehr">PureQuill Writer ansehen →</span>
    </a>
    <a class="karte rp" href="#">
      <img src="${RP_ICON}" alt="">
      <div class="marken"><span>PDF-Editor</span><span>Kostenlos · Pro</span></div>
      <h3>Rewrite PDF</h3>
      <p>Der PDF-Editor, der Text wirklich ändert, statt ihn zu übermalen. Dazu wirklich schwärzen, versteckte Daten finden und Scans bearbeiten.</p>
      <span class="mehr">Rewrite PDF ansehen →</span>
    </a>
  </div>
</div></section>

<section class="produkt"><div class="wrap">
  <div class="p-kopf">
    <img class="p-icon" src="${RP_ICON}" alt="">
    <span class="pille">Kostenlos · komplett offline · Windows</span>
    <h1 style="font-size:clamp(38px,5.6vw,64px)">Rewrite PDF</h1>
    <p class="lead" style="margin-top:0">Text in PDFs wirklich ändern, nicht übermalen. Ein Tippfehler im Vertrag, eine falsche Zahl in der Rechnung: Du klickst auf die Stelle und schreibst.</p>
    <div class="knoepfe"><a class="btn pri" href="#">Im Microsoft Store laden</a><a class="btn" href="#">Was Pro kann</a></div>
  </div>
  <img class="bild" src="${RP_SHOT}" alt="Rewrite PDF: Preis in einem Angebot wird direkt im Text geändert">
  <div class="saeulen">
    <div class="saeule"><span class="nr">Original­schrift</span><h3>Wie vorher gesetzt</h3><p>Schrift, Größe und Farbe der Stelle bleiben erhalten.</p></div>
    <div class="saeule"><span class="nr">Pro</span><h3>Wirklich schwärzen</h3><p>Geschwärzter Text wird aus der Datei gelöscht, nicht nur verdeckt.</p></div>
    <div class="saeule"><span class="nr">Pro</span><h3>Sicherheits-Check</h3><p>Findet Autor, alte Fassungen und unsichtbaren Text, und entfernt sie.</p></div>
  </div>
  <p class="leise">Auf der PureQuill-Seite leuchtet derselbe Aufbau in Terrakotta.</p>
  <img class="bild" style="border-color:rgba(224,122,98,.45);box-shadow:0 0 0 1px rgba(224,122,98,.15),0 0 70px -10px rgba(224,122,98,.32),0 40px 80px -30px #000" src="${PQ_SHOT}" alt="PureQuill Writer beim Schreiben">
</div></section>
`;
writeFileSync(join(site, 'entwurf', 'glow-entwurf.html'), html);
console.log('geschrieben', (html.length / 1024).toFixed(0), 'KB');
