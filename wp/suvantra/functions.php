<?php
/**
 * Suvantra — theme functions.
 *
 * Deliberately thin. Everything in here has a reason; nothing is kept in stock
 * for later. The most important part is the tidying further down: the site
 * states in its privacy policy that it sets no cookies, loads nothing from
 * foreign servers and bundles no analytics. WordPress brings along a few things
 * that contradict that — those go out.
 *
 * @package Suvantra
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // No direct access.
}

/*
 * Language first: everything else asks it which language a page is in, and the
 * locale filter inside it has to be registered before anything is translated.
 */
require_once get_template_directory() . '/inc/language.php';

/* The settings, the dials for the look, and the head metadata. Separate files
   because each reads on its own — and because the head metadata is the one
   place where the theme negotiates with a plugin. */
require_once get_template_directory() . '/inc/customizer.php';
require_once get_template_directory() . '/inc/design.php';
require_once get_template_directory() . '/inc/head-meta.php';
require_once get_template_directory() . '/inc/upgrade.php';

/* -------------------------------------------------------------------- Setup */

/**
 * @return void
 */
function suvantra_setup() {
	/* The four translated strings of this theme. The site language is decided
	   per page by the locale filter in inc/language.php, so an English page
	   really does get English text — even though WordPress itself is set to
	   German. */
	load_theme_textdomain( 'suvantra', get_template_directory() . '/languages' );

	// WordPress sets <title>; the theme writes none of its own.
	add_theme_support( 'title-tag' );
	add_theme_support( 'html5', array( 'search-form', 'gallery', 'caption', 'style', 'script' ) );
	add_theme_support( 'responsive-embeds' );

	/* automatic-feed-links is deliberately NOT enabled. It puts links to /feed/
	   and to the comment feed in the head of every page — this site has neither
	   posts nor comments, and a comment feed in the head of a company site
	   looks like a forgotten blog. */

	/* The editor should show what comes out later: same typeface, same measure,
	   same colours. Otherwise you write the text in Times and are surprised by
	   the line breaks afterwards. */
	add_theme_support( 'editor-styles' );
	add_editor_style( 'assets/css/editor.css' );

	/* Limit the editor's colour choice to the brand kit. Gradients are ruled
	   out by it, so they are switched off entirely — custom colours stay
	   possible, they are just not the first thing to hand. */
	add_theme_support( 'disable-custom-gradients' );
	add_theme_support( 'editor-gradient-presets', array() );
	add_theme_support( 'editor-color-palette', array(
		array( 'name' => __( 'Ink', 'suvantra' ), 'slug' => 'ink', 'color' => '#191B1E' ),
		array( 'name' => __( 'Ink, quieter', 'suvantra' ), 'slug' => 'ink-soft', 'color' => '#5C6064' ),
		array( 'name' => __( 'Paper', 'suvantra' ), 'slug' => 'paper', 'color' => '#FCFBF8' ),
		array( 'name' => __( 'Ground', 'suvantra' ), 'slug' => 'ground', 'color' => '#F6F4EE' ),
		array( 'name' => __( 'Rule', 'suvantra' ), 'slug' => 'rule', 'color' => '#E2DFD5' ),
		array( 'name' => __( 'Company accent', 'suvantra' ), 'slug' => 'accent', 'color' => get_theme_mod( 'suvantra_color_accent_light', '#3C4147' ) ),
		array( 'name' => __( 'PureQuill accent', 'suvantra' ), 'slug' => 'accent-pq', 'color' => get_theme_mod( 'suvantra_color_accent-pq_light', '#8C3B2E' ) ),
	) );

	/* Logo in the header bar. flex-width and flex-height because a wordmark is
	   wider than tall and a picture mark square — both should fit; the
	   stylesheet caps the height. */
	add_theme_support( 'custom-logo', array(
		'height'      => 64,
		'width'       => 320,
		'flex-height' => true,
		'flex-width'  => true,
		'header-text' => array( 'marke' ),
	) );

	/* Two menu slots per language: header and footer. The list is built from
	   the language table so a third language brings its slots along instead of
	   having to be added here.

	   The language switcher is calculated by the theme instead — it should
	   point at the counterpart of the current page, and a hand-maintained menu
	   cannot know that. */
	$locations = array();
	foreach ( suvantra_languages() as $code => $language ) {
		$where = $language['prefix'] ? '/' . $language['prefix'] . '/' : __( 'root', 'suvantra' );
		/* translators: 1: language name, 2: path prefix. */
		$locations[ suvantra_menu_location( 'main', $code ) ] = sprintf( __( 'Main navigation — %1$s (%2$s)', 'suvantra' ), $language['name'], $where );
		/* translators: 1: language name, 2: path prefix. */
		$locations[ suvantra_menu_location( 'footer', $code ) ] = sprintf( __( 'Footer — %1$s (%2$s)', 'suvantra' ), $language['name'], $where );
	}
	register_nav_menus( $locations );

	/* Featured images: on pages they are the Open Graph share image. They are
	   never displayed — no template outputs them. */
	add_theme_support( 'post-thumbnails', array( 'page' ) );

	/* Header image. Wide and flat, because it sits under a 66 pixel bar and
	   should not fill half the first screen. flex-height still allows any
	   aspect ratio; the WordPress cropper only suggests what is set here.

	   'header-text' => false, because the brand is already in the bar above —
	   a second wordmark inside the image would be a duplicate. */
	add_theme_support( 'custom-header', array(
		'width'         => 2400,
		'height'        => 600,
		'flex-width'    => true,
		'flex-height'   => true,
		'header-text'   => false,
		'default-image' => '',
	) );
}
add_action( 'after_setup_theme', 'suvantra_setup' );

/**
 * @return void
 */
function suvantra_styles() {
	// filemtime as the version: after every upload the cache is cold by itself.
	$file = get_template_directory() . '/style.css';
	wp_enqueue_style(
		'suvantra',
		get_stylesheet_uri(),
		array(),
		file_exists( $file ) ? filemtime( $file ) : '1.2'
	);
}
add_action( 'wp_enqueue_scripts', 'suvantra_styles' );

/* --------------------------------------------------- Product colour by slug */

/**
 * The accent belongs to the product, not the company: .p-purequill on <body>
 * turns the same page terracotta. So that this happens without manual work per
 * page, the slug travels along as a class.
 *
 * It is calculated from the path, not from the parent chain. The earlier
 * version took a sub-page's own slug when it sat under a language folder —
 * /de/purequill/datenschutz/ got p-datenschutz instead of p-purequill and lost
 * the terracotta, while /purequill/privacy/ kept it.
 *
 * The class names stay German: they also appear in the page content stored in
 * WordPress, and renaming them would mean re-importing every page.
 *
 * @param array $classes Body classes.
 * @return array
 */
function suvantra_body_class( $classes ) {
	if ( ! is_page() ) {
		return $classes;
	}

	$path   = suvantra_path();
	$prefix = trim( (string) suvantra_language_data()['prefix'], '/' );
	if ( $prefix && ( $path === $prefix || 0 === strpos( $path, $prefix . '/' ) ) ) {
		$path = ltrim( substr( $path, strlen( $prefix ) ), '/' );
	}

	$parts = array_filter( explode( '/', $path ) );
	$slug  = $parts ? reset( $parts ) : '';
	if ( $slug ) {
		$classes[] = 'p-' . sanitize_html_class( $slug );
	}

	/* So the look can also be pinned to the language without having to guess
	   at the path. */
	$classes[] = 'sprache-' . sanitize_html_class( suvantra_language_code() );
	return $classes;
}
add_filter( 'body_class', 'suvantra_body_class' );

/* ------------------------------------------------------------------ Tidying */

/**
 * WordPress loads an emoji script and replaces characters with images from
 * s.w.org. That is a request to a foreign server on every page view — and so
 * contradicts what /purequill/privacy/ promises. Modern systems draw emoji
 * themselves anyway.
 *
 * @return void
 */
function suvantra_disable_emoji() {
	remove_action( 'wp_head', 'print_emoji_detection_script', 7 );
	remove_action( 'admin_print_scripts', 'print_emoji_detection_script' );
	remove_action( 'wp_print_styles', 'print_emoji_styles' );
	remove_action( 'admin_print_styles', 'print_emoji_styles' );
	remove_filter( 'the_content_feed', 'wp_staticize_emoji' );
	remove_filter( 'comment_text_rss', 'wp_staticize_emoji' );
	remove_filter( 'wp_mail', 'wp_staticize_emoji_for_email' );
	add_filter( 'emoji_svg_url', '__return_false' );
	// The DNS prefetch to s.w.org would otherwise stay in the head.
	add_filter( 'wp_resource_hints', function ( $hints, $kind ) {
		if ( 'dns-prefetch' === $kind ) {
			$hints = array_filter( $hints, function ( $hint ) {
				return false === strpos( is_array( $hint ) ? '' : $hint, 's.w.org' );
			} );
		}
		return $hints;
	}, 10, 2 );
}
add_action( 'init', 'suvantra_disable_emoji' );

/**
 * Take the version number out of the head and out of the asset URLs. It tells
 * every scanner the exact WordPress version and helps nobody else.
 */
remove_action( 'wp_head', 'wp_generator' );
add_filter( 'the_generator', '__return_empty_string' );

/**
 * The rest of the head lines WordPress brings along for a blog and this site
 * does not have: the oEmbed discovery tags (so other sites can embed a
 * preview), the link to the REST route, the shortlink and the remains of
 * Windows Live Writer. Every one of them is a line somebody has to read in
 * order to understand what the site does.
 */
remove_action( 'wp_head', 'wp_oembed_add_discovery_links' );
remove_action( 'wp_head', 'wp_oembed_add_host_js' );
remove_action( 'wp_head', 'rest_output_link_wp_head' );
remove_action( 'wp_head', 'wp_shortlink_wp_head' );
remove_action( 'wp_head', 'rsd_link' );
remove_action( 'wp_head', 'wlwmanifest_link' );
remove_action( 'template_redirect', 'wp_shortlink_header', 11 );

/**
 * Disable XML-RPC. This site has no app using it, and it has been the most
 * popular barn door for login attempts for years.
 */
add_filter( 'xmlrpc_enabled', '__return_false' );

/**
 * Close the user list of the REST API to anyone not logged in.
 *
 * By default /wp-json/wp/v2/users answers everybody and returns the display
 * name and the slug of every author. On this site that meant handing out the
 * owner's email address — it was the display name — to any passer-by: personal
 * data nobody meant to publish, a target for spam, and very probably half of
 * the login credentials, since the username tends to be the same string.
 *
 * Logged-in users keep the endpoint; the block editor needs it to fill the
 * author box and to mention people in comments.
 *
 * @param array $endpoints REST endpoints.
 * @return array
 */
function suvantra_hide_rest_users( $endpoints ) {
	if ( is_user_logged_in() ) {
		return $endpoints;
	}
	unset( $endpoints['/wp/v2/users'] );
	unset( $endpoints['/wp/v2/users/(?P<id>[\d]+)'] );
	return $endpoints;
}
add_filter( 'rest_endpoints', 'suvantra_hide_rest_users' );

/**
 * The other way to the same name: /?author=1 redirects to the author archive,
 * whose address contains the slug. The archive itself already answers 404
 * through index.php, but the redirect would still reveal the slug in the
 * Location header — so the query is refused outright.
 *
 * This site has no blog and therefore no author pages to lose.
 *
 * @return void
 */
function suvantra_block_author_probing() {
	if ( is_admin() || ! isset( $_GET['author'] ) ) { // phpcs:ignore WordPress.Security.NonceVerification
		return;
	}
	wp_safe_redirect( home_url( '/' ), 301 );
	exit;
}
add_action( 'template_redirect', 'suvantra_block_author_probing', 0 );

/**
 * The editor's block library only ships unused CSS on a page without blocks.
 * It stays loaded as soon as a page really uses blocks — only the global style
 * declarations, which would override our colours, go out.
 *
 * @return void
 */
function suvantra_dequeue_global_styles() {
	wp_dequeue_style( 'global-styles' );
	wp_dequeue_style( 'classic-theme-styles' );
}
add_action( 'wp_enqueue_scripts', 'suvantra_dequeue_global_styles', 20 );

/**
 * The stylesheet expects aria-current for the link to the page being shown
 * (`.top nav a[aria-current]`). wp_nav_menu only sets a class, so highlighting
 * the current page never took effect. Screen readers need the attribute
 * anyway; a class tells them nothing.
 *
 * @param array   $attr Link attributes.
 * @param WP_Post $item Menu item.
 * @return array
 */
function suvantra_menu_aria_current( $attr, $item ) {
	$classes = isset( $item->classes ) ? (array) $item->classes : array();
	if ( in_array( 'current-menu-item', $classes, true ) || in_array( 'current_page_item', $classes, true ) ) {
		$attr['aria-current'] = 'page';
	}
	return $attr;
}
add_filter( 'nav_menu_link_attributes', 'suvantra_menu_aria_current', 10, 2 );

/* -------------------------------------------------- Addresses in page content */

/**
 * The placeholders %STORE_URL% and %DOWNLOAD_URL% are replaced with the
 * Customizer values on output. Keeping them there rather than once per
 * language in the page content has a practical reason: when the app ships you
 * enter them once, not four times — and do not forget the fourth.
 *
 * A shortcode would not work here: the product pages are one HTML block, and
 * shortcodes are not evaluated inside those.
 *
 * @param string $content Page content.
 * @return string
 */
function suvantra_replace_urls( $content ) {
	$map = array(
		'%STORE_URL%'    => get_theme_mod( 'suvantra_store_url', '' ),
		'%DOWNLOAD_URL%' => get_theme_mod( 'suvantra_download_url', '' ),
	);
	foreach ( $map as $placeholder => $url ) {
		if ( false !== strpos( $content, $placeholder ) ) {
			$content = str_replace( $placeholder, $url ? esc_url( $url ) : '#', $content );
		}
	}
	return $content;
}
add_filter( 'the_content', 'suvantra_replace_urls', 20 );
