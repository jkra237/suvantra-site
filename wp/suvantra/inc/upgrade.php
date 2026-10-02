<?php
/**
 * Carry saved settings over to the renamed keys.
 *
 * Version 1.1 of this theme used German setting names — suvantra_fuss_mail,
 * suvantra_farbe_paper_hell and so on. Version 1.2 renamed everything to
 * English. Theme mods are addressed by name, so without this file every value
 * set in the Customizer would silently fall back to its default: the Store
 * address gone, the colours back to the brand kit, the footer text lost.
 *
 * It runs once. Afterwards the old keys are deleted, a marker is set, and this
 * file does nothing for the rest of its life — it can be removed together with
 * its require in functions.php once the site has been loaded once.
 *
 * @package Suvantra
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Addresses that have moved, old path => new path (both without slashes).
 *
 * Two moves are collected here.
 *
 * On 8 September 2026 the structure was flipped: German used to sit at the
 * root and English under /en/, now it is the other way round. The old pages
 * were never removed, so every page existed twice — under both addresses,
 * both answering 200, and all of them advertised to search engines in the
 * Yoast sitemap. Duplicates like that split the ranking between the two
 * addresses and make the wrong one win about half the time.
 *
 * The website privacy policy was created at the root as well, in the English
 * branch, even though it is German: it served lang="en-US", the English footer
 * and a language switch that pointed nowhere.
 *
 * These entries only take effect once the old page has actually been deleted
 * in WordPress — while it still exists it answers 200 and nothing here runs.
 * WordPress redirects a changed slug on its own, but never a changed parent
 * and never a deleted page.
 *
 * Empty this table once the old addresses have dropped out of the search index.
 *
 * @return array<string,string>
 */
function suvantra_moved_pages() {
	return array(
		// The German branch, formerly at the root.
		'start-de'                => 'de',
		'impressum'               => 'de/impressum',
		'purequill/datenschutz'   => 'de/purequill/datenschutz',
		'datenschutzerklaerung'   => 'de/datenschutzerklaerung',

		// The English branch, formerly under /en/.
		'en'                      => '',
		'en/purequill'            => 'purequill',
		'en/imprint'              => 'imprint',
		'en/purequill/privacy'    => 'purequill/privacy',
	);
}

/**
 * Send the old address to the new one. 301, because the move is permanent —
 * anything else and search engines keep the old address in their index.
 *
 * @return void
 */
function suvantra_redirect_moved_pages() {
	if ( is_admin() || ! is_404() ) {
		return;
	}
	$path  = suvantra_requested_path();
	$moved = suvantra_moved_pages();
	if ( ! isset( $moved[ $path ] ) ) {
		return;
	}
	$target = trim( $moved[ $path ], '/' );
	wp_safe_redirect( home_url( '/' . ( $target ? $target . '/' : '' ) ), 301 );
	exit;
}
add_action( 'template_redirect', 'suvantra_redirect_moved_pages' );

/**
 * Old key => new key. Per-language and per-colour names are expanded below,
 * so the table stays readable.
 *
 * @return array<string,string>
 */
function suvantra_renamed_settings() {
	$map = array(
		'suvantra_kopf_aufbau'        => 'suvantra_header_layout',
		'suvantra_kopf_fest'          => 'suvantra_header_sticky',
		'suvantra_kopf_hoehe'         => 'suvantra_header_height',
		'suvantra_kopf_linie'         => 'suvantra_header_rule',
		'suvantra_kopfgrafik_wo'      => 'suvantra_header_image_where',
		'suvantra_sprachlink_zeigen'  => 'suvantra_language_switcher',
		'suvantra_fuss_layout'        => 'suvantra_footer_layout',
		'suvantra_fuss_mail'          => 'suvantra_footer_email',
		'suvantra_fuss_mail_zeigen'   => 'suvantra_footer_email_show',
		'suvantra_schrift'            => 'suvantra_font_size',
		'suvantra_breite'             => 'suvantra_measure',
		'suvantra_dunkelmodus'        => 'suvantra_dark_mode',
		'suvantra_seo_an'             => 'suvantra_head_meta',
	);

	foreach ( array_keys( suvantra_languages() ) as $code ) {
		$map[ 'suvantra_fuss_copy_' . $code ]  = 'suvantra_footer_copyright_' . $code;
		$map[ 'suvantra_fuss_notiz_' . $code ] = 'suvantra_footer_note_' . $code;
		$map[ 'suvantra_seo_text_' . $code ]   = 'suvantra_meta_description_' . $code;
		$map[ 'suvantra_og_bild_' . $code ]    = 'suvantra_share_image_' . $code;
	}

	$colors = array(
		'paper'     => 'paper',
		'grund'     => 'ground',
		'chrome'    => 'chrome',
		'ink'       => 'ink',
		'ink-soft'  => 'ink-soft',
		'rule'      => 'rule',
		'akzent'    => 'accent',
		'akzent-pq' => 'accent-pq',
	);
	foreach ( $colors as $old => $new ) {
		$map[ 'suvantra_farbe_' . $old . '_hell' ]   = 'suvantra_color_' . $new . '_light';
		$map[ 'suvantra_farbe_' . $old . '_dunkel' ] = 'suvantra_color_' . $new . '_dark';
	}
	return $map;
}

/**
 * Values that changed as well, not just their key.
 *
 * @return array<string,array<string,string>>
 */
function suvantra_renamed_values() {
	return array(
		'suvantra_header_layout'     => array( 'links' => 'left', 'eng' => 'inline', 'mitte' => 'centre', 'gespiegelt' => 'mirrored' ),
		'suvantra_footer_layout'     => array( 'zeile' => 'row', 'spalten' => 'columns' ),
		'suvantra_header_image_where' => array( 'alle' => 'all', 'start' => 'home' ),
	);
}

/**
 * @return void
 */
function suvantra_upgrade_settings() {
	if ( '1.2' === get_theme_mod( 'suvantra_schema', '' ) ) {
		return;
	}

	$values = suvantra_renamed_values();

	foreach ( suvantra_renamed_settings() as $old => $new ) {
		$saved = get_theme_mod( $old, null );
		if ( null === $saved ) {
			continue; // Never set — the default applies, nothing to carry over.
		}
		if ( isset( $values[ $new ][ $saved ] ) ) {
			$saved = $values[ $new ][ $saved ];
		}
		/* The year placeholder was renamed with everything else. Anyone who
		   edited the line by hand would otherwise read %JAHR% on the live
		   site. */
		if ( is_string( $saved ) ) {
			$saved = str_replace( '%JAHR%', '%YEAR%', $saved );
		}
		set_theme_mod( $new, $saved );
		remove_theme_mod( $old );
	}

	suvantra_upgrade_menu_locations();

	set_theme_mod( 'suvantra_schema', '1.2' );
}

/**
 * The menu locations were renamed too: haupt_de became main_de, fuss_de became
 * footer_de. WordPress stores which menu sits in which location by that key —
 * without this the assigned menus would simply be gone, and the navigation
 * would vanish from the site without any error to explain it.
 *
 * @return void
 */
function suvantra_upgrade_menu_locations() {
	$assigned = get_theme_mod( 'nav_menu_locations', array() );
	if ( ! is_array( $assigned ) || ! $assigned ) {
		return;
	}

	$prefixes = array( 'haupt_' => 'main_', 'fuss_' => 'footer_' );
	$changed  = false;

	foreach ( $assigned as $location => $menu_id ) {
		foreach ( $prefixes as $old => $new ) {
			if ( 0 !== strpos( $location, $old ) ) {
				continue;
			}
			$renamed = $new . substr( $location, strlen( $old ) );
			if ( ! isset( $assigned[ $renamed ] ) ) {
				$assigned[ $renamed ] = $menu_id;
			}
			unset( $assigned[ $location ] );
			$changed = true;
		}
	}

	if ( $changed ) {
		set_theme_mod( 'nav_menu_locations', $assigned );
	}
}
add_action( 'after_setup_theme', 'suvantra_upgrade_settings', 20 );
