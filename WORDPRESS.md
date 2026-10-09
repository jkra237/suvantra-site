# suvantra.eu in WordPress einrichten

Auf der Domain liegt bereits WordPress. Statt die statischen Dateien danebenzulegen
— zwei Systeme auf einer Domain, und die Startseite gehört dann WordPress —
bekommt WordPress ein eigenes, minimales Theme mit demselben Aussehen.

**Warum kein fertiges Theme:** auf `/purequill/datenschutz/` steht, dass die Seite
keine Cookies setzt, nichts von fremden Servern lädt und keine Analysedienste
einbindet. Die meisten Themes laden Google Fonts nach, und WordPress selbst holt
sein Emoji-Skript von `s.w.org`. Mit einem fremden Theme wäre diese Zusage
nachweislich unwahr — auf genau der Seite, die im Microsoft Store als
Datenschutzerklärung verlinkt ist. Das Theme hier schaltet beides ab.

**Sprachaufbau:** Englisch liegt an der Wurzel, Deutsch unter `/de/`.
`x-default` zeigt auf die Wurzel — wer in keiner der beiden Sprachen sucht,
landet auf Englisch. Es wird **nicht** nach Browsersprache umgeleitet; das
verhindert, dass Suchmaschinen beide Fassungen sehen, und ein Seiten-Cache
würde die Weiche des ersten Besuchers an alle weiterreichen.

---

## 1. Bilder hochladen

Per FTP den Ordner `assets/` ins **Wurzelverzeichnis** der Domain (dort, wo
`wp-config.php` liegt), sodass `https://suvantra.eu/assets/site.css` erreichbar
ist. Bewusst nicht in die Mediathek: die Adressen bleiben so stabil, auch wenn
das Theme später einmal wechselt.

Prüfen: `https://suvantra.eu/assets/pq-schreiben.png` muss das Bild zeigen.

## 2. Theme hochladen

Den Ordner `wp/suvantra/` nach `wp-content/themes/` kopieren, sodass es
`wp-content/themes/suvantra/style.css` gibt. Dann unter **Design → Themes**
aktivieren.

Alternativ: `wp/suvantra/` zippen und über **Design → Themes → Installieren →
Theme hochladen** einspielen.

## 3. Seiten anlegen — der bequeme Weg

Im Paket liegt ein zweites Plugin: **Suvantra einrichten**. Es legt alle acht
Seiten an — mit Titel, Adresse, uebergeordneter Seite, Vorlage und Inhalt —,
macht die deutsche Startseite zur Startseite und baut die beiden Menues.

1. `wp/suvantra-einrichten/` nach `wp-content/plugins/` kopieren
   (oder `suvantra-einrichten.zip` unter **Plugins → Installieren → Plugin hochladen**).
2. Aktivieren.
3. **Werkzeuge → Suvantra einrichten** → **Seiten anlegen**.
4. **Einstellungen → Permalinks** oeffnen und einmal speichern.
5. Plugin **deaktivieren und loeschen**. Es ist Werkzeug, keine Ausstattung.

Der Aufruf laesst sich wiederholen: vorhandene Seiten werden an ihrer Adresse
erkannt und nicht doppelt angelegt. Ohne das Haekchen „Inhalt vorhandener
Seiten ueberschreiben" bleiben sie unberuehrt — von Hand eingetragene
Anschriften gehen also nicht verloren.

## 3b. Seiten anlegen — von Hand

Falls du es lieber selbst machst: nicht ueber den visuellen Editor, dort kommst
du an das HTML gar nicht heran. Der Weg fuehrt ueber den **Code-Editor**:

1. **Seiten → Erstellen**, Titel eintragen.
2. Oben rechts die **drei Punkte ⋮** (Optionen) → **Code-Editor**.
   Tastenkuerzel: `Strg` + `Umschalt` + `Alt` + `M`.
3. Den kompletten Inhalt der passenden Datei aus `wp-bloecke/` einfuegen.
4. Wieder ⋮ → **Visueller Editor**. Jetzt stehen dort fertige Bloecke.
5. Rechts unter **Seite** Vorlage und uebergeordnete Seite setzen, veroeffentlichen.

| Titel | Adresse | Uebergeordnet | Vorlage | Inhalt aus |
|---|---|---|---|---|
| Suvantra | `start` *(Startseite der Website)* | — | Standard | `05-en-start.txt` |
| PureQuill Writer | `purequill` | — | Standard | `06-en-purequill.txt` |
| Privacy | `privacy` | PureQuill Writer | **Rechtstext** | `07-en-privacy.txt` |
| Imprint | `imprint` | — | **Rechtstext** | `08-en-imprint.txt` |
| Suvantra (DE) | `de` | — | Standard | `01-de-start.txt` |
| PureQuill Writer (DE) | `purequill` | Suvantra (DE) | Standard | `02-de-purequill.txt` |
| Datenschutz | `datenschutz` | PureQuill Writer (DE) | **Rechtstext** | `03-de-datenschutz.txt` |
| Impressum | `impressum` | Suvantra (DE) | **Rechtstext** | `04-de-impressum.txt` |

**Die vier Rechtstexte kommen als echte Absatz- und Ueberschriftenbloecke** — die
Anschrift traegst du also im normalen Editor ein und nicht in HTML. Die
Ueberschrift liefert die Vorlage aus dem Seitentitel.

Start- und Produktseiten sind bewusst ein einziger HTML-Block: ihr Markup ist
gestaltet und hat im Editor nichts zu suchen.

Danach unter **Einstellungen → Lesen** die deutsche Startseite als
*„Eine statische Seite"* festlegen.

## 4. Permalinks prüfen

**Einstellungen → Permalinks** auf *Beitragsname* stellen und speichern. Danach
müssen genau diese Adressen erreichbar sein:

```
https://suvantra.eu/                            Startseite, englisch
https://suvantra.eu/purequill/
https://suvantra.eu/purequill/privacy/          ← Pflichtfeld im Partner Center (englisch)
https://suvantra.eu/imprint/
https://suvantra.eu/de/                         deutsche Fassung
https://suvantra.eu/de/purequill/
https://suvantra.eu/de/purequill/datenschutz/   ← Pflichtfeld im Partner Center (deutsch)
https://suvantra.eu/de/impressum/
```

Diese Adresse trägst du ins Partner Center ein — **sie muss danach stabil
bleiben.** Ändert sich der Permalink später, zeigt das Pflichtfeld im Store ins
Leere.

## 4b. Aufbau des Themes (ab 1.2)

```
suvantra/
  style.css              Stylesheet und Theme-Kopf
  functions.php          lädt inc/, richtet ein, räumt auf
  header.php  footer.php
  index.php   404.php
  page.php               Inhaltsseiten
  page-legal.php         Vorlage „Legal text" (Datenschutz, Impressum)
  page-recht.php         Übergangsdatei — siehe unten
  screenshot.png
  inc/
    language.php         Sprachtabelle, Seitengruppen, Fußzeilen-Logik
    customizer.php       alle Einstellungen
    design.php           Farben, Maße, Kopfleisten-Klassen
    head-meta.php        canonical, hreflang, Open Graph
    upgrade.php          einmalige Übernahme der 1.1-Einstellungen
  template-parts/
    footer-row.php  footer-columns.php
  assets/css/editor.css  Stil des Block-Editors
  languages/             suvantra.pot, de_DE.po, de_DE.mo
```

**Funktions- und Dateinamen sind englisch**, wie bei jedem anderen Theme auch.
Kommentare bleiben deutsch. **Die CSS-Klassennamen bleiben ebenfalls deutsch**
(`.marke`, `.held`, `.merkmale`, `.dunkelband` …) — sie stehen im Seiteninhalt
in der Datenbank, und sie umzubenennen hieße, alle acht Seiten neu zu
importieren.

**Texte laufen über `__()` mit der Textdomäne `suvantra`.** Die Besonderheit:
ein Filter auf `locale` in `inc/language.php` setzt die Sprache **je Seite am
Pfad** — deshalb bekommt eine englische Seite wirklich englischen Text, obwohl
WordPress selbst auf de_DE steht. Admin, AJAX und REST bleiben unberührt.

Neue Texte übersetzt man so:

```bash
node "C:/Users/jkraj/cc test/werkzeuge/website/uebersetzung-bauen.js"
```

Das Skript sammelt alle Zeichenketten in `languages/suvantra.pot`, ergänzt
`de_DE.po` um neue Schlüssel (vorhandene Übersetzungen bleiben) und baut
daraus `de_DE.mo`. Die deutschen Fassungen trägt man in `de_DE.po` nach und
lässt es noch einmal laufen.

**`page-recht.php`** ist nur eine Übergangsdatei. Bis 1.1 hieß die Vorlage so,
und WordPress hat diesen Dateinamen auf den beiden Rechtsseiten gespeichert;
ohne sie fielen die Seiten auf `page.php` zurück und verlören die schmale
Spalte. Sobald du bei Datenschutz und Impressum in der Seitenleiste die Vorlage
**„Legal text"** ausgewählt hast, kann die Datei weg.

**Deine Einstellungen und Menüzuordnungen wandern beim ersten Aufruf von
allein mit** (`inc/upgrade.php`) — auch die Menüpositionen, die von `haupt_de`
auf `main_de` und von `fuss_de` auf `footer_de` umbenannt wurden.

## 5. Menüs

Das Einrichtungs-Plugin legt die beiden Hauptnavigationen an. Von Hand:
**Design → Menüs**, ein Menü je Position.

Es gibt **vier Positionen — je Sprache eine für Kopf und eine für Fuß**:

- **Hauptnavigation — English (Wurzel)** — *PureQuill Writer*, *Imprint*
- **Hauptnavigation — Deutsch (/de/)** — *PureQuill Writer*, *Impressum*
- **Fußzeile — English (Wurzel)** — frei, hier kommen weitere Verweise hin
- **Fußzeile — Deutsch (/de/)** — dito

**Zur Fußzeile:** *Datenschutz* und *Impressum* hängt das Theme von sich aus
an, wenn sie im Fußmenü fehlen — ein leeres oder versehentlich geleertes Menü
kann das Impressum also nicht unerreichbar machen. Stehen sie im Menü, bestimmt
das Menü ihre Reihenfolge und sie werden nicht doppelt ausgegeben.

Die Fußzeile kann **einzeilig oder mehrspaltig**. In der Spaltenfassung
(Customizer → Suvantra → Fußzeile → Aufbau) werden die **obersten**
Menüeinträge zu Spaltenköpfen und ihre Unterpunkte zu den Verweisen darunter;
Einträge ohne Unterpunkte sammeln sich in einer letzten Spalte. Spalten lohnen
sich ab etwa fünf Verweisen.

**Den Sprachumschalter musst du nicht pflegen.** Das Theme rechnet ihn aus:
auf `/purequill/` zeigt er nach `/de/purequill/`, auf `/de/impressum/` nach
`/imprint/`. Gibt es kein Gegenstück, landet er auf der Startseite der anderen
Sprache. Die Tabelle dafür steht in `inc-sprache.php`; eine neunte Seite
trägst du dort mit einer Zeile nach.

## 6. Customizer

**Design → Customizer → Suvantra.** Alles liegt in diesem einen Panel.

### Suvantra → Marke

Logo, Website-Symbol und Kopfgrafik stehen hier zusammen. Es sind **dieselben**
WordPress-Einstellungen wie sonst unter *Website-Informationen* und *Kopfgrafik*
— das Theme hängt die Bedienelemente nur um, damit man sie nicht an drei Orten
sucht. Zuschneider und Größenhinweise sind unverändert.

- **Logo** — tritt in der Kopfleiste an die Stelle von Punkt und Wortmarke.
  Die Höhe begrenzt das Stylesheet auf 28 px am Schirm und 24 px am Handy, die
  Breite richtet sich danach; hoch- und querformatige Marken passen also beide.
  Ohne Logo bleibt es beim Schriftzug „Suvantra“.
- **Website-Symbol** (das Favicon) — sobald eines gesetzt ist, hört das Theme
  auf, `favicon-32.png` selbst einzutragen.
- **Kopfgrafik** — vorgeschlagen 2400 × 600, jedes Format erlaubt. Sie sitzt
  als Band **unter** der Leiste, nicht dahinter: die Leiste ist durchscheinend
  und wäre auf einem hellen Bild unlesbar. Die Höhe ist auf 340 px gedeckelt
  und mittig zugeschnitten, damit der Vorspann nicht unter die Falz rutscht.
- **Kopfgrafik zeigen** — auf allen Seiten oder nur auf den Startseiten.

**Ohne hochgeladene Kopfgrafik gibt das Theme an dieser Stelle nichts aus** —
die Seiten sehen dann aus wie bisher.

### Suvantra → Produkt

- **Adresse im Microsoft Store** — Platzhalter im Seiteninhalt: `%STORE_URL%`
- **Adresse des direkten Downloads** — Platzhalter: `%DOWNLOAD_URL%`

Beide gelten für **alle** Produktseiten in allen Sprachen. Bleibt ein Feld
leer, zeigt der zugehörige Knopf auf `#`.

### Suvantra → Kopfzeile

- **Anordnung** — vier Möglichkeiten:
  1. Marke links, Navigation rechts (Voreinstellung)
  2. Marke links, Navigation direkt daneben
  3. Marke mittig, Navigation darunter
  4. Navigation links, Marke rechts
- **Kopfleiste mitlaufen lassen** — an: bleibt oben stehen, leicht
  durchscheinend mit Unschärfe. Aus: scrollt mit dem Inhalt weg — dann fällt
  auch die Unschärfe weg, die dort nur Rechenzeit kostet.
- **Höhe der Kopfleiste** (52–104 px, Voreinstellung 66)
- **Trennlinie unter der Kopfleiste**
- **Sprachumschalter anzeigen**

**Ab 561 px Breite** wirken die Anordnungen; darunter bricht die Leiste
ohnehin um und sieht in allen vier Fällen gleich aus. Die eingestellte Höhe
gilt weder für die mittige Anordnung (sie richtet sich nach ihrem Inhalt) noch
für schmale Geräte.

### Suvantra → Fußzeile

- **Aufbau** — eine Zeile oder Spalten (siehe Abschnitt 5)
- **Zeile links**, je Sprache — `%JAHR%` wird durch das laufende Jahr ersetzt
- **Nachsatz**, je Sprache — leisere zweite Zeile, leer = aus
- **Kontaktadresse** und **Kontaktadresse anzeigen** — wird verschleiert
  ausgegeben (`antispambot`)

Diese Felder ziehen in der Vorschau ohne Neuladen nach; WordPress tauscht dafür
nur den Fußbereich aus. Auf der Seite selbst läuft davon nichts.

### Suvantra → Gestaltung

- **Grundschriftgröße** (15–20 px, Voreinstellung 17) — auf schmalen Geräten
  bleibt es automatisch ein Pixel kleiner
- **Größte Seitenbreite** (960–1440 px, Voreinstellung 1180) — betrifft Kopf,
  Fuß und die Abschnitte, nicht die Textspalte: die bleibt bei 66 Zeichen
- **Dunkelmodus mitliefern** — aus schaltet die Seite fest auf hell

### Suvantra → Farben (zwei Abschnitte: heller und dunkler Modus)

Je acht Farben, in beiden Modi dieselben: **Grundfläche**, **abgesetzte
Fläche**, **Knopffläche**, **Text**, **Text leiser**, **Linien und Ränder**,
**Akzent der Firma**, **Akzent PureQuill**.

Zwei Werte werden **nicht** gewählt, sondern abgeleitet: die hellere
Knopffläche und die blasseste Schrift mischt das Theme über `color-mix` aus
den anderen. Die Schriftfarbe auf farbigen Flächen rechnet es nach
WCAG-Leuchtdichte aus — damit die Knopfbeschriftung lesbar bleibt, egal welche
Farbe darunter liegt.

Alles wirkt über CSS-Variablen. **Steht ein Wert auf der Voreinstellung, wird
dafür gar nichts ausgegeben** — wer alles zurücksetzt, bekommt exakt
`style.css` zurück. Farbverläufe sind im Brandkit ausgeschlossen, deshalb gibt
es dafür kein Feld; auch der Block-Editor bietet keine an.

### Suvantra → Kopfdaten und Teilen-Bild

- **Kopfdaten vom Theme ausgeben** (Voreinstellung: an)
- **Standardbeschreibung**, je Sprache
- **Teilen-Bild**, je Sprache

`canonical`, `hreflang`, `og:` und `twitter:` kommen aus dem Theme, weil nur
das die Sprache **am Pfad** erkennt. Solange der Haken steht, meldet das Theme
die entsprechenden Ausgaben von Yoast ab, damit nichts doppelt im Kopf steht.

Titel und Beschreibung **einzelner** Seiten stehen im Editor: der Titel als
Seitentitel, die Beschreibung im Feld **Textauszug**.

Das Teilen-Bild sucht sich das Theme in dieser Reihenfolge:

1. **Beitragsbild der Seite** — damit lässt sich jede Seite einzeln steuern
2. `/assets/og-<gruppe>-<sprache>.png` — also die vier bereits gebauten Dateien
3. das hier hinterlegte Bild der Sprache
4. `pq-icon-512.png`

## 7. Nachkontrolle

Auf jeder Seite die Entwicklerwerkzeuge öffnen, Reiter **Netzwerk**, Seite neu
laden. **Es darf kein einziger Zugriff auf eine fremde Domain erscheinen** —
kein `fonts.googleapis.com`, kein `s.w.org`, kein `gravatar.com`. Sonst stimmt
die Datenschutzerklärung nicht mehr, und die Ursache ist dann fast immer ein
Plugin, nicht das Theme.

Ebenso prüfen: keine Cookies vor einer Anmeldung.

## 8. Linux-Downloads

Die Pakete liegen **nicht** in WordPress und nicht in der Mediathek, sondern
als normale Dateien unter `/download/linux/` im Wurzelverzeichnis der Domain
(neben `wp-config.php`, wie `assets/` und `app/`). So bleiben die Adressen
stabil, und alles kommt vom eigenen Server — die Datenschutzzusage „nichts von
fremden Servern“ gilt auch für den Download.

Bei jeder neuen Version:

1. In `purequillwriter/desktop/` bauen (siehe dortige README, Abschnitt Linux).
   Die vier Pakete liegen danach in `desktop/release/linux/`.
2. Prüfsummen erzeugen, im selben Ordner: `sha256sum *.deb *.AppImage > SHA256SUMS`
3. Per FTP die vier Pakete **und** `SHA256SUMS` nach `/download/linux/`.
4. Auf beiden Produktseiten Versionsnummer, Dateinamen und Größen im Abschnitt
   `id="linux"` anpassen (die Dateinamen tragen die Version), dann die
   Kette aus `werkzeuge/README.md` laufen lassen und die beiden Seiten in
   WordPress ersetzen.
5. Alte Pakete erst löschen, wenn die Seiten auf die neuen zeigen.

---

## Was noch fehlt

- **Anschrift** in Impressum und Datenschutz, beide Sprachen. Im Impressum
  steht als HTML-Kommentar, was je nach Rechtsform hineingehört.
- **Zweiter Kontaktweg** neben der E-Mail (§ 5 DDG).
- **USt-IdNr.** oder den Abschnitt streichen.

## Nicht vergessen

`DICT_REMOTE` in `purequillwriter/index.html` zeigt weiterhin auf
`https://jkra237.github.io/purequillwriter/dict/`. Sobald `suvantra.eu/dict/`
die Wörterbücher ausliefert, gehört die Konstante dorthin — **vorher nicht**,
sonst bricht die deutsche Rechtschreibprüfung in der Store-Fassung.
