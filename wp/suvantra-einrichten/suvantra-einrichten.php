<?php
/**
 * Plugin Name: Suvantra einrichten
 * Description: Legt die acht Seiten von suvantra.eu an — mit Titel, Adresse, übergeordneter Seite, Seitenvorlage und Inhalt — setzt die Startseite und baut die beiden Menüs. Einmal ausführen, dann deaktivieren und löschen.
 * Version: 1.0
 * Requires at least: 6.0
 * Requires PHP: 7.4
 * Author: Suvantra
 *
 * Warum es dieses Plugin gibt: acht Seiten von Hand anzulegen heißt acht Mal
 * Titel tippen, Adresse setzen, übergeordnete Seite wählen, Vorlage wählen, in
 * den Code-Editor wechseln, einfügen, zurückwechseln, veröffentlichen. Das ist
 * nicht schwer, aber es sind rund fünfzig Handgriffe, und jeder einzelne kann
 * danebengehen — eine falsche Adresse reicht, und das Pflichtfeld im Partner
 * Center zeigt später ins Leere.
 *
 * Das Plugin ist absichtlich wiederholbar: es erkennt bereits angelegte Seiten
 * an ihrer Adresse und aktualisiert sie, statt Doppelte zu erzeugen. Wer eine
 * Seite inzwischen von Hand geändert hat, kann sie über das Häkchen unten vom
 * Überschreiben ausnehmen.
 *
 * Nach getaner Arbeit gehört es deaktiviert und gelöscht. Es ist Werkzeug,
 * nicht Ausstattung.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

define( 'SUVANTRA_SETUP_VERSION', '1.0' );

/**
 * Die Seitenstruktur. Reihenfolge zählt: übergeordnete Seiten stehen vor ihren
 * Kindern, damit die Zuordnung im selben Durchlauf gelingt.
 *
 * datei  — Name in ordner/, ohne Endung
 * eltern — Adresse der übergeordneten Seite, oder ''
 */
function suvantra_seiten() {
	return array(
		array( 'titel' => 'Suvantra',            'slug' => 'start',       'eltern' => '',             'vorlage' => '',               'datei' => '05-en-start',      'front' => true ),
		array( 'titel' => 'PureQuill Writer',    'slug' => 'purequill',   'eltern' => '',             'vorlage' => '',               'datei' => '06-en-purequill' ),
		array( 'titel' => 'Privacy',             'slug' => 'privacy',     'eltern' => 'purequill',    'vorlage' => 'page-recht.php', 'datei' => '07-en-privacy' ),
		array( 'titel' => 'Imprint',             'slug' => 'imprint',     'eltern' => '',             'vorlage' => 'page-recht.php', 'datei' => '08-en-imprint' ),
		array( 'titel' => 'Suvantra (DE)',       'slug' => 'de',          'eltern' => '',             'vorlage' => '',               'datei' => '01-de-start' ),
		array( 'titel' => 'PureQuill Writer (DE)','slug' => 'purequill',  'eltern' => 'de',           'vorlage' => '',               'datei' => '02-de-purequill' ),
		array( 'titel' => 'Datenschutz',         'slug' => 'datenschutz', 'eltern' => 'de/purequill', 'vorlage' => 'page-recht.php', 'datei' => '03-de-datenschutz' ),
		array( 'titel' => 'Impressum',           'slug' => 'impressum',   'eltern' => 'de',           'vorlage' => 'page-recht.php', 'datei' => '04-de-impressum' ),
	);
}

/* --------------------------------------------------------------- Oberfläche */

function suvantra_menue() {
	add_management_page(
		'Suvantra einrichten',
		'Suvantra einrichten',
		'manage_options',
		'suvantra-einrichten',
		'suvantra_seite'
	);
}
add_action( 'admin_menu', 'suvantra_menue' );

function suvantra_seite() {
	if ( ! current_user_can( 'manage_options' ) ) {
		wp_die( 'Dafür fehlen die Rechte.' );
	}

	$bericht = null;
	if ( isset( $_POST['suvantra_los'] ) ) {
		check_admin_referer( 'suvantra_einrichten' );
		$bericht = suvantra_ausfuehren( ! empty( $_POST['suvantra_ueberschreiben'] ) );
	}

	$fehlend = suvantra_fehlende_dateien();
	?>
	<div class="wrap">
		<h1>Suvantra einrichten</h1>

		<?php if ( 'suvantra' !== get_stylesheet() ) : ?>
			<div class="notice notice-warning"><p>
				Das Theme <strong>Suvantra</strong> ist nicht aktiv. Die Seiten lassen sich
				trotzdem anlegen, sehen aber erst mit dem Theme richtig aus.
			</p></div>
		<?php endif; ?>

		<?php if ( $fehlend ) : ?>
			<div class="notice notice-error"><p>
				Diese Inhaltsdateien fehlen im Ordner <code>wp-content/plugins/suvantra-einrichten/inhalte/</code>:
				<code><?php echo esc_html( implode( ', ', $fehlend ) ); ?></code>
			</p></div>
		<?php endif; ?>

		<?php if ( $bericht ) : ?>
			<div class="notice notice-success"><p><strong>Fertig.</strong></p>
				<ul style="list-style:disc;margin-left:22px">
					<?php foreach ( $bericht as $zeile ) : ?>
						<li><?php echo wp_kses_post( $zeile ); ?></li>
					<?php endforeach; ?>
				</ul>
			</div>
		<?php endif; ?>

		<p style="max-width:70ch">
			Legt die acht Seiten an, setzt Adressen, übergeordnete Seiten und Vorlagen,
			macht die deutsche Startseite zur Startseite der Website und baut die beiden
			Menüs. Der Aufruf lässt sich wiederholen: vorhandene Seiten werden anhand
			ihrer Adresse erkannt und nicht doppelt angelegt.
		</p>

		<form method="post">
			<?php wp_nonce_field( 'suvantra_einrichten' ); ?>
			<p>
				<label>
					<input type="checkbox" name="suvantra_ueberschreiben" value="1">
					Inhalt vorhandener Seiten überschreiben
				</label><br>
				<span class="description" style="max-width:70ch;display:inline-block">
					Ohne Häkchen bleiben bereits angelegte Seiten unangetastet — nur fehlende
					werden ergänzt. Mit Häkchen wird der Inhalt aus den Dateien neu
					eingespielt; von Hand eingetragene Anschriften gehen dabei verloren.
				</span>
			</p>
			<?php submit_button( 'Seiten anlegen', 'primary', 'suvantra_los', false ); ?>
		</form>

		<h2>Danach</h2>
		<ol style="max-width:70ch">
			<li><strong>Einstellungen → Permalinks</strong> öffnen und einmal speichern —
				sonst greifen die neuen Adressen nicht.</li>
			<li>Die Adressen prüfen, besonders
				<code><?php echo esc_html( home_url( '/de/purequill/datenschutz/' ) ); ?></code>
				— die trägst du ins Partner Center ein.</li>
			<li>Dieses Plugin deaktivieren und löschen.</li>
		</ol>
	</div>
	<?php
}

/* ------------------------------------------------------------------- Arbeit */

function suvantra_inhalt_pfad( $datei ) {
	return plugin_dir_path( __FILE__ ) . 'inhalte/' . $datei . '.txt';
}

function suvantra_fehlende_dateien() {
	$fehlt = array();
	foreach ( suvantra_seiten() as $s ) {
		if ( ! file_exists( suvantra_inhalt_pfad( $s['datei'] ) ) ) {
			$fehlt[] = $s['datei'] . '.txt';
		}
	}
	return $fehlt;
}

/**
 * Sucht eine Seite über ihren Pfad. get_page_by_path() versteht "en/purequill"
 * und ist damit genau das richtige Werkzeug, um Doppelte zu vermeiden.
 */
function suvantra_finde( $pfad ) {
	$seite = get_page_by_path( $pfad, OBJECT, 'page' );
	return $seite instanceof WP_Post ? $seite : null;
}

function suvantra_ausfuehren( $ueberschreiben ) {
	$bericht = array();
	$front   = 0;

	foreach ( suvantra_seiten() as $s ) {
		$pfad = $s['eltern'] ? $s['eltern'] . '/' . $s['slug'] : $s['slug'];
		$datei = suvantra_inhalt_pfad( $s['datei'] );
		if ( ! file_exists( $datei ) ) {
			$bericht[] = 'Übersprungen (<code>' . esc_html( $s['datei'] ) . '.txt</code> fehlt): ' . esc_html( $s['titel'] );
			continue;
		}
		$inhalt = file_get_contents( $datei );

		$eltern_id = 0;
		if ( $s['eltern'] ) {
			$e = suvantra_finde( $s['eltern'] );
			if ( ! $e ) {
				$bericht[] = 'Übersprungen (übergeordnete Seite <code>' . esc_html( $s['eltern'] ) . '</code> nicht gefunden): ' . esc_html( $s['titel'] );
				continue;
			}
			$eltern_id = $e->ID;
		}

		$vorhanden = suvantra_finde( $pfad );

		if ( $vorhanden && ! $ueberschreiben ) {
			$bericht[] = 'Vorhanden, unverändert gelassen: <code>/' . esc_html( $pfad ) . '/</code>';
			if ( ! empty( $s['front'] ) ) {
				$front = $vorhanden->ID;
			}
			continue;
		}

		$daten = array(
			'post_title'   => $s['titel'],
			'post_name'    => $s['slug'],
			'post_content' => $inhalt,
			'post_status'  => 'publish',
			'post_type'    => 'page',
			'post_parent'  => $eltern_id,
		);
		if ( $vorhanden ) {
			$daten['ID'] = $vorhanden->ID;
		}

		/* wp_insert_post filtert Blockkommentare nicht weg, solange der
		   aufrufende Benutzer unfiltered_html darf — Administratoren dürfen das
		   in einer Einzelinstallation. In einem Multisite-Netz kann es fehlen;
		   dann kämen die Blöcke beschädigt an, deshalb die Prüfung. */
		if ( ! current_user_can( 'unfiltered_html' ) ) {
			$bericht[] = '<strong>Abgebrochen:</strong> diesem Benutzer fehlt das Recht <code>unfiltered_html</code>; das Markup käme beschädigt an.';
			return $bericht;
		}

		$id = wp_insert_post( wp_slash( $daten ), true );
		if ( is_wp_error( $id ) ) {
			$bericht[] = 'Fehler bei ' . esc_html( $s['titel'] ) . ': ' . esc_html( $id->get_error_message() );
			continue;
		}

		if ( $s['vorlage'] ) {
			update_post_meta( $id, '_wp_page_template', $s['vorlage'] );
		} else {
			delete_post_meta( $id, '_wp_page_template' );
		}

		$bericht[] = ( $vorhanden ? 'Aktualisiert' : 'Angelegt' ) . ': <code>/' . esc_html( $pfad ) . '/</code>'
			. ( $s['vorlage'] ? ' (Vorlage Rechtstext)' : '' );

		if ( ! empty( $s['front'] ) ) {
			$front = $id;
		}
	}

	if ( $front ) {
		update_option( 'show_on_front', 'page' );
		update_option( 'page_on_front', $front );
		$bericht[] = 'Startseite der Website gesetzt.';
	}

	$bericht = array_merge( $bericht, suvantra_menues() );

	/* Damit die frisch gesetzten Adressen sofort greifen */
	flush_rewrite_rules( false );
	$bericht[] = 'Permalinks neu geschrieben. Zur Sicherheit unter Einstellungen → Permalinks einmal speichern.';

	return $bericht;
}

/**
 * Die beiden Menüs. Sie werden nur angelegt, wenn es sie noch nicht gibt —
 * ein bestehendes Menü zu überschreiben wäre übergriffig.
 */
function suvantra_menues() {
	$bericht = array();
	$orte    = get_theme_mod( 'nav_menu_locations', array() );

	$bauen = array(
		'haupt_en' => array(
			'name'   => 'Hauptnavigation (englisch)',
			'punkte' => array(
				array( 'titel' => 'PureQuill Writer', 'pfad' => 'purequill' ),
				array( 'titel' => 'Imprint',          'pfad' => 'imprint' ),
			),
		),
		'haupt_de' => array(
			'name'   => 'Hauptnavigation (deutsch)',
			'punkte' => array(
				array( 'titel' => 'PureQuill Writer', 'pfad' => 'de/purequill' ),
				array( 'titel' => 'Impressum',        'pfad' => 'de/impressum' ),
			),
		),
	);

	foreach ( $bauen as $ort => $def ) {
		$menue = wp_get_nav_menu_object( $def['name'] );
		if ( $menue ) {
			$bericht[] = 'Menü „' . esc_html( $def['name'] ) . '“ war schon da, unverändert gelassen.';
			$orte[ $ort ] = $menue->term_id;
			continue;
		}
		$id = wp_create_nav_menu( $def['name'] );
		if ( is_wp_error( $id ) ) {
			$bericht[] = 'Menü „' . esc_html( $def['name'] ) . '“ ließ sich nicht anlegen.';
			continue;
		}
		foreach ( $def['punkte'] as $p ) {
			if ( isset( $p['pfad'] ) ) {
				$seite = suvantra_finde( $p['pfad'] );
				if ( ! $seite ) {
					continue;
				}
				wp_update_nav_menu_item( $id, 0, array(
					'menu-item-title'     => $p['titel'],
					'menu-item-object'    => 'page',
					'menu-item-object-id' => $seite->ID,
					'menu-item-type'      => 'post_type',
					'menu-item-status'    => 'publish',
				) );
			}
		}
		$orte[ $ort ] = $id;
		$bericht[]    = 'Menü „' . esc_html( $def['name'] ) . '“ angelegt und zugeordnet.';
	}

	set_theme_mod( 'nav_menu_locations', $orte );
	return $bericht;
}
