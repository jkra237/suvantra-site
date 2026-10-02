<?php
/**
 * Language logic, kept in one file so header, footer and head meta all ask the
 * same source.
 *
 * English lives at the root, German under /de/. This is decided purely from the
 * page path — never from the WordPress language setting. On a German install
 * that setting is de_DE even though the front page is English; anything that
 * followed it would put a German footer on every English page.
 *
 * ADDING A LANGUAGE — three steps, all in this file:
 *   1. add a row to suvantra_languages(),
 *   2. add its path and footer label to each row of suvantra_page_groups(),
 *   3. create the pages in WordPress.
 * Menu locations, the language switcher, hreflang, the per-language Customizer
 * fields and the mandatory footer links all follow on their own.
 *
 * @package Suvantra
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * The languages of the site. The FIRST entry is the root language: its prefix
 * is empty and it carries hreflang="x-default".
 *
 * @return array<string,array<string,string>>
 */
function suvantra_languages() {
	return array(
		'en' => array(
			'prefix'   => '',
			'name'     => 'English',
			'locale'   => 'en_US',
			'hreflang' => 'en',
		),
		'de' => array(
			'prefix'   => 'de',
			'name'     => 'Deutsch',
			'locale'   => 'de_DE',
			'hreflang' => 'de',
		),
	);
}

/**
 * The pages that exist in every language — one row per group, one path per
 * language code. This single table feeds the language switcher, the hreflang
 * tags and the mandatory footer links; the same knowledge used to sit in three
 * places and drifted apart.
 *
 * 'footer' offers the group as an automatic footer link. Whether it is actually
 * appended is decided by the setting "Append privacy and imprint
 * automatically", which is OFF by default: the footer shows what the menu
 * holds and nothing else. Switched on, these entries are added whenever no
 * menu item points at their address.
 *
 * @return array<string,array>
 */
function suvantra_page_groups() {
	return array(
		'home' => array(
			'paths' => array( 'en' => '', 'de' => 'de' ),
		),
		'purequill' => array(
			'paths' => array( 'en' => 'purequill', 'de' => 'de/purequill' ),
		),
		/* Two privacy policies, deliberately: this one covers the website
		   (hosting, log files, contact), the one below covers the app and is
		   the address linked from the Microsoft Store — which is why that one
		   must never move. */
		'privacy-site' => array(
			'paths'  => array( 'en' => 'privacy-policy', 'de' => 'de/datenschutzerklaerung' ),
			'footer' => array( 'en' => 'Privacy Policy', 'de' => 'Datenschutzerklärung' ),
		),
		'privacy-app' => array(
			'paths'  => array( 'en' => 'purequill/privacy', 'de' => 'de/purequill/datenschutz' ),
			'footer' => array( 'en' => 'Privacy', 'de' => 'Datenschutz' ),
		),
		'imprint' => array(
			'paths'  => array( 'en' => 'imprint', 'de' => 'de/impressum' ),
			'footer' => array( 'en' => 'Imprint', 'de' => 'Impressum' ),
		),
	);
}

/**
 * Code of the root language — the first entry of the table.
 *
 * @return string
 */
function suvantra_root_language() {
	$languages = suvantra_languages();
	return (string) key( $languages );
}

/**
 * The requested path, without slashes and without the WordPress subdirectory.
 * Read from the request only, so it is available before the query is parsed —
 * the locale filter needs it that early.
 *
 * @return string
 */
function suvantra_requested_path() {
	$requested = isset( $_SERVER['REQUEST_URI'] ) ? wp_unslash( $_SERVER['REQUEST_URI'] ) : '';
	$path      = trim( (string) wp_parse_url( $requested, PHP_URL_PATH ), '/' );
	$base      = trim( (string) wp_parse_url( home_url( '/' ), PHP_URL_PATH ), '/' );
	if ( $base && ( $path === $base || 0 === strpos( $path, $base . '/' ) ) ) {
		$path = trim( substr( $path, strlen( $base ) ), '/' );
	}
	return $path;
}

/**
 * Path of the current page, '' for the front page. get_page_uri() returns the
 * front page's slug, which does not appear in the address — hence the special
 * case. Anything that is not a page (in practice: the 404) falls back to the
 * requested address, so /de/nothing-here/ still gets a German error page.
 *
 * @return string
 */
function suvantra_path() {
	if ( is_front_page() ) {
		return '';
	}
	if ( is_page() ) {
		return trim( (string) get_page_uri( get_queried_object_id() ), '/' );
	}
	return suvantra_requested_path();
}

/**
 * Language code for a path. The longest matching prefix wins, so a later
 * /de-at/ is not swallowed by /de/.
 *
 * @param string|null $path Path to test, or null for the current page.
 * @return string
 */
function suvantra_language_code( $path = null ) {
	$subject = ( null === $path ) ? suvantra_path() : trim( (string) $path, '/' );
	$match   = suvantra_root_language();
	$length  = -1;

	foreach ( suvantra_languages() as $code => $language ) {
		$prefix = trim( (string) $language['prefix'], '/' );
		if ( '' === $prefix ) {
			continue; // The root language is the fallback, never a match.
		}
		if ( ( $subject === $prefix || 0 === strpos( $subject, $prefix . '/' ) ) && strlen( $prefix ) > $length ) {
			$match  = $code;
			$length = strlen( $prefix );
		}
	}
	return $match;
}

/**
 * Kept because templates and page content ask for it.
 *
 * @param string|null $path Path to test, or null for the current page.
 * @return bool
 */
function suvantra_is_german( $path = null ) {
	return 'de' === suvantra_language_code( $path );
}

/**
 * The WordPress locale for the page being requested.
 *
 * This is what makes __() work here at all. Without it every theme string
 * would follow the site setting — de_DE — and the English pages would show
 * German text, which is the exact bug this theme exists to avoid.
 *
 * Admin, AJAX, REST and CLI are left alone: the back end stays in the language
 * its user chose, no matter which page is being edited.
 *
 * @param string $locale The locale WordPress would use.
 * @return string
 */
function suvantra_locale( $locale ) {
	if ( is_admin() || wp_doing_ajax() || wp_doing_cron() ) {
		return $locale;
	}
	if ( defined( 'REST_REQUEST' ) && REST_REQUEST ) {
		return $locale;
	}
	$data = suvantra_language_data( suvantra_language_code( suvantra_requested_path() ) );
	return $data['locale'];
}
add_filter( 'locale', 'suvantra_locale' );

/**
 * The data for a language, falling back to the root language.
 *
 * @param string|null $code Language code.
 * @return array
 */
function suvantra_language_data( $code = null ) {
	$languages = suvantra_languages();
	$code      = $code ? $code : suvantra_language_code();
	if ( isset( $languages[ $code ] ) ) {
		return array_merge( $languages[ $code ], array( 'code' => $code ) );
	}
	$root = suvantra_root_language();
	return array_merge( $languages[ $root ], array( 'code' => $root ) );
}

/**
 * Address of a language's front page.
 *
 * @param string $code Language code.
 * @return string
 */
function suvantra_language_home( $code ) {
	$data   = suvantra_language_data( $code );
	$prefix = trim( (string) $data['prefix'], '/' );
	return home_url( '/' . ( $prefix ? $prefix . '/' : '' ) );
}

/**
 * Which group does a path belong to? '' when none.
 *
 * @param string|null $path Path to test, or null for the current page.
 * @return string
 */
function suvantra_group_for_path( $path = null ) {
	$subject = ( null === $path ) ? suvantra_path() : trim( (string) $path, '/' );
	foreach ( suvantra_page_groups() as $name => $group ) {
		if ( in_array( $subject, $group['paths'], true ) ) {
			return $name;
		}
	}
	return '';
}

/**
 * Address of a group in one language, '' when it does not exist there.
 *
 * @param string $group Group key.
 * @param string $code  Language code.
 * @return string
 */
function suvantra_group_url( $group, $code ) {
	$groups = suvantra_page_groups();
	if ( ! isset( $groups[ $group ]['paths'][ $code ] ) ) {
		return '';
	}
	$path = trim( (string) $groups[ $group ]['paths'][ $code ], '/' );
	return home_url( '/' . ( $path ? $path . '/' : '' ) );
}

/**
 * Every version of the current page, code => address, including the one being
 * shown. A page that belongs to no group — one added by hand later — has no
 * counterparts: better no hreflang at all than one pointing at the wrong page.
 *
 * @return array<string,string>
 */
function suvantra_translations() {
	$group = suvantra_group_for_path();
	if ( ! $group ) {
		return array();
	}
	$list = array();
	foreach ( suvantra_languages() as $code => $language ) {
		$url = suvantra_group_url( $group, $code );
		if ( $url ) {
			$list[ $code ] = $url;
		}
	}
	return $list;
}

/**
 * Targets for the language switcher: every language but the current one. When
 * the current page has no counterpart the link falls back to that language's
 * front page — better than a dead link.
 *
 * @return array<int,array<string,string>>
 */
function suvantra_language_links() {
	$current      = suvantra_language_code();
	$translations = suvantra_translations();
	$links        = array();

	foreach ( suvantra_languages() as $code => $language ) {
		if ( $code === $current ) {
			continue;
		}
		$links[] = array(
			'url'  => isset( $translations[ $code ] ) ? $translations[ $code ] : suvantra_language_home( $code ),
			'text' => $language['name'],
			'lang' => $language['hreflang'],
			'code' => $code,
		);
	}
	return $links;
}

/**
 * Name of a menu location, e.g. 'main_de' or 'footer_en'.
 *
 * @param string      $kind 'main' or 'footer'.
 * @param string|null $code Language code.
 * @return string
 */
function suvantra_menu_location( $kind, $code = null ) {
	return $kind . '_' . ( $code ? $code : suvantra_language_code() );
}

/**
 * The mandatory footer links, derived from the page table.
 *
 * @param string|null $code Language code.
 * @return array<int,array<string,string>>
 */
function suvantra_legal_links( $code = null ) {
	$code  = $code ? $code : suvantra_language_code();
	$links = array();

	foreach ( suvantra_page_groups() as $name => $group ) {
		if ( empty( $group['footer'][ $code ] ) ) {
			continue;
		}
		$url = suvantra_group_url( $name, $code );
		if ( $url ) {
			$links[] = array( 'url' => $url, 'text' => $group['footer'][ $code ] );
		}
	}
	return $links;
}

/**
 * Make addresses comparable: no scheme, no host, no trailing slash.
 *
 * @param string $url Address.
 * @return string
 */
function suvantra_url_key( $url ) {
	$parts = wp_parse_url( (string) $url );
	$path  = isset( $parts['path'] ) ? $parts['path'] : (string) $url;
	return strtolower( trim( $path, '/' ) );
}

/**
 * The footer links: the menu of that language first, then the mandatory links
 * — but only those not already in the menu. So the menu decides the order
 * without privacy and imprint ever being able to disappear.
 *
 * @param string|null $code Language code.
 * @return array<int,array>
 */
function suvantra_footer_links( $code = null ) {
	$code  = $code ? $code : suvantra_language_code();
	$links = array();
	$keys  = array();

	foreach ( suvantra_menu_items( suvantra_menu_location( 'footer', $code ) ) as $item ) {
		$links[] = $item;
		$keys[]  = suvantra_url_key( $item['url'] );
	}

	if ( ! get_theme_mod( 'suvantra_footer_legal_auto', false ) ) {
		return $links;
	}

	foreach ( suvantra_legal_links( $code ) as $legal ) {
		if ( ! in_array( suvantra_url_key( $legal['url'] ), $keys, true ) ) {
			$links[] = array(
				'url'      => $legal['url'],
				'text'     => $legal['text'],
				'title'    => '',
				'target'   => '',
				'children' => array(),
			);
		}
	}
	return $links;
}

/**
 * A menu as a plain list, two levels deep. wp_nav_menu() would return markup
 * that does not fit here: the footer puts bare <a> elements side by side, and
 * in the column layout a heading plus a list. Building both from the same data
 * is simpler than bending someone else's markup twice.
 *
 * @param string $location Menu location name.
 * @return array<int,array>
 */
function suvantra_menu_items( $location ) {
	if ( ! has_nav_menu( $location ) ) {
		return array();
	}
	$locations = get_nav_menu_locations();
	if ( empty( $locations[ $location ] ) ) {
		return array();
	}
	$items = wp_get_nav_menu_items( $locations[ $location ] );
	if ( ! $items ) {
		return array();
	}

	$by_id = array();
	$top   = array();

	foreach ( $items as $item ) {
		$by_id[ $item->ID ] = array(
			'id'       => (int) $item->ID,
			'parent'   => (int) $item->menu_item_parent,
			'url'      => $item->url,
			'text'     => $item->title,
			'title'    => $item->attr_title,
			'target'   => $item->target,
			'children' => array(),
		);
	}
	foreach ( $by_id as $id => $item ) {
		if ( $item['parent'] && isset( $by_id[ $item['parent'] ] ) ) {
			$by_id[ $item['parent'] ]['children'][] = $id;
		}
	}
	foreach ( $by_id as $item ) {
		if ( $item['parent'] && isset( $by_id[ $item['parent'] ] ) ) {
			continue;
		}
		$children = array();
		foreach ( $item['children'] as $child_id ) {
			$child             = $by_id[ $child_id ];
			$child['children'] = array();
			$children[]        = $child;
		}
		$item['children'] = $children;
		$top[]            = $item;
	}
	return $top;
}

/**
 * The contents of the footer. A function rather than markup inside footer.php,
 * because the Customizer asks for the same fragment when refreshing its
 * preview — maintaining the same markup twice would go wrong sooner or later.
 *
 * @return void
 */
function suvantra_footer_content() {
	$code      = suvantra_language_code();
	$layout    = get_theme_mod( 'suvantra_footer_layout', 'row' );
	$copyright = get_theme_mod( 'suvantra_footer_copyright_' . $code, '&copy; %YEAR% Suvantra' );
	$note      = get_theme_mod( 'suvantra_footer_note_' . $code, '' );
	$email     = get_theme_mod( 'suvantra_footer_email', 'support@suvantra.eu' );
	$show_mail = get_theme_mod( 'suvantra_footer_email_show', true );
	$links     = suvantra_footer_links( $code );

	$copyright = str_replace( '%YEAR%', gmdate( 'Y' ), (string) $copyright );

	if ( 'columns' === $layout ) {
		suvantra_footer_columns( $links, $copyright, $email, $show_mail, $note );
		return;
	}

	get_template_part( 'template-parts/footer', 'row', array(
		'links'     => $links,
		'copyright' => $copyright,
		'email'     => $email,
		'show_mail' => $show_mail,
		'note'      => $note,
	) );
}

/**
 * The column layout. Top level menu items become column headings and their
 * children the links below; items without children — the appended mandatory
 * links among them — collect in a final column so they do not stand there as
 * lonely headings.
 *
 * @param array  $links     Footer links.
 * @param string $copyright Copyright line.
 * @param string $email     Contact address.
 * @param bool   $show_mail Whether to show it.
 * @param string $note      Second, quieter line.
 * @return void
 */
function suvantra_footer_columns( $links, $copyright, $email, $show_mail, $note ) {
	$columns = array();
	$loose   = array();
	foreach ( $links as $link ) {
		if ( ! empty( $link['children'] ) ) {
			$columns[] = $link;
		} else {
			$loose[] = $link;
		}
	}

	get_template_part( 'template-parts/footer', 'columns', array(
		'columns'   => $columns,
		'loose'     => $loose,
		'copyright' => $copyright,
		'email'     => $email,
		'show_mail' => $show_mail,
		'note'      => $note,
	) );
}

/**
 * Structured data. In WordPress this belongs in the theme rather than in the
 * page content: a <script> inside an HTML block usually survives, but it
 * depends on user capabilities and is easily broken while editing.
 *
 * Deliberately without offers and without aggregateRating — the price is not
 * settled, and an invented rating violates Google's structured data policies,
 * which is not a small thing.
 *
 * @return array|null
 */
function suvantra_structured_data() {
	$group  = suvantra_group_for_path();
	$code   = suvantra_language_code();
	$german = 'de' === $code;

	if ( 'home' === $group ) {
		return array(
			'@context'     => 'https://schema.org',
			'@type'        => 'Organization',
			'name'         => 'Suvantra',
			'url'          => home_url( '/' ),
			'logo'         => suvantra_asset_url( 'pq-icon-512.png' ),
			'email'        => 'support@suvantra.eu',
			'contactPoint' => array(
				'@type'             => 'ContactPoint',
				'contactType'       => 'customer support',
				'email'             => 'support@suvantra.eu',
				'availableLanguage' => array_values( wp_list_pluck( suvantra_languages(), 'hreflang' ) ),
			),
		);
	}

	if ( 'purequill' === $group ) {
		return array(
			'@context'               => 'https://schema.org',
			'@type'                  => 'SoftwareApplication',
			'name'                   => 'PureQuill Writer',
			'applicationCategory'    => 'https://schema.org/BusinessApplication',
			'applicationSubCategory' => $german ? 'Textverarbeitung' : 'Word processor',
			'operatingSystem'        => 'Windows 10, Windows 11',
			'url'                    => suvantra_group_url( 'purequill', $code ),
			'image'                  => suvantra_asset_url( 'pq-en-schreiben.png' ),
			'inLanguage'             => array( 'de', 'en', 'fr', 'es', 'it', 'pl', 'pt' ),
			'description'            => $german
				? 'Ein Schreibprogramm, das offline arbeitet. Der Text bleibt auf dem Rechner, gesichert wird laufend und mit Versionsverlauf. Schreibziel, Wortwiederholungen, Rechtschreibprüfung in sieben Sprachen, siebzehn Farbschemata.'
				: 'A word processor that works offline. Your text stays on your machine, saved continuously and with a version history. Word goal, repeated words, spell check in seven languages, seventeen colour schemes.',
			'publisher'              => array( '@type' => 'Organization', 'name' => 'Suvantra', 'url' => home_url( '/' ) ),
		);
	}
	return null;
}

/**
 * Absolute address of a file in /assets/.
 *
 * @param string $file File name.
 * @return string
 */
function suvantra_asset_url( $file ) {
	return home_url( '/assets/' . ltrim( (string) $file, '/' ) );
}
