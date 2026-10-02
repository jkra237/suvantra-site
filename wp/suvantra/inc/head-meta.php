<?php
/**
 * Head metadata: canonical, hreflang, Open Graph, Twitter.
 *
 * Why this lives in the theme instead of a plugin: the static pages had these
 * tags in their <head>; when the content was moved into WordPress only the
 * <main> part came along, and Yoast filled the gap — wrongly, with
 * og:locale=de_DE on the English front page, og:site_name "suvantra.eu" and no
 * og:image at all. In the theme the output knows the language table from
 * inc/language.php and cannot drift; it also survives a change of plugin.
 *
 * All of it can be switched off (Customizer → Suvantra → Head metadata) should
 * the job go back to a plugin.
 *
 * @package Suvantra
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Is the theme's head metadata switched on?
 *
 * @return bool
 */
function suvantra_head_meta_enabled() {
	return (bool) get_theme_mod( 'suvantra_head_meta', true );
}

/**
 * The share image for the current page, in this order:
 *   1. the page's featured image — this is how a single page is steered
 *      without needing a field of its own,
 *   2. the matching file in /assets/, i.e. og-<group>-<language>.png,
 *   3. the image set for that language in the Customizer,
 *   4. the program icon as a last resort.
 * Always returns address, width and height — Facebook and LinkedIn otherwise
 * crop on their own, and that regularly cuts into the wordmark.
 *
 * @return array{url:string,width:int,height:int}
 */
function suvantra_share_image() {
	$code  = suvantra_language_code();
	$group = suvantra_group_for_path();

	if ( is_page() && has_post_thumbnail() ) {
		$image = wp_get_attachment_image_src( get_post_thumbnail_id(), 'full' );
		if ( $image ) {
			return array( 'url' => $image[0], 'width' => $image[1], 'height' => $image[2] );
		}
	}

	$group_file = in_array( $group, array( 'home', 'purequill' ), true ) ? $group : 'home';
	$group_file = ( 'home' === $group_file ) ? 'start' : $group_file;
	$file       = 'og-' . $group_file . '-' . $code . '.png';
	if ( file_exists( ABSPATH . 'assets/' . $file ) ) {
		return array( 'url' => suvantra_asset_url( $file ), 'width' => 1200, 'height' => 630 );
	}

	$id = (int) get_theme_mod( 'suvantra_share_image_' . $code, 0 );
	if ( $id ) {
		$image = wp_get_attachment_image_src( $id, 'full' );
		if ( $image ) {
			return array( 'url' => $image[0], 'width' => $image[1], 'height' => $image[2] );
		}
	}

	return array( 'url' => suvantra_asset_url( 'pq-icon-512.png' ), 'width' => 512, 'height' => 512 );
}

/**
 * The description: whatever the page itself has in its "Excerpt" field, else
 * the sentence set for that language in the Customizer, else the site tagline.
 * The excerpt is the right place for it — it sits next to the text in the
 * editor and does not have to be hunted for in the Customizer.
 *
 * @return string
 */
function suvantra_meta_description() {
	if ( is_page() ) {
		$page = get_queried_object();
		if ( $page instanceof WP_Post && '' !== trim( (string) $page->post_excerpt ) ) {
			return trim( wp_strip_all_tags( $page->post_excerpt ) );
		}
	}
	$fallback = get_theme_mod( 'suvantra_meta_description_' . suvantra_language_code(), '' );
	if ( $fallback ) {
		return $fallback;
	}
	return (string) get_bloginfo( 'description', 'display' );
}

/**
 * The title without the suffix WordPress appends for <title>.
 *
 * @return string
 */
function suvantra_meta_title() {
	if ( is_front_page() ) {
		$title   = get_bloginfo( 'name', 'display' );
		$tagline = get_bloginfo( 'description', 'display' );
		return $tagline ? $title . ' — ' . $tagline : $title;
	}
	if ( is_page() ) {
		return wp_strip_all_tags( get_the_title( get_queried_object_id() ) );
	}
	return wp_get_document_title();
}

/**
 * The address of the current page, without query arguments.
 *
 * @return string
 */
function suvantra_current_url() {
	if ( is_front_page() ) {
		return home_url( '/' );
	}
	if ( is_page() ) {
		return get_permalink( get_queried_object_id() );
	}
	return home_url( add_query_arg( array() ) );
}

/**
 * The output. Written out by hand rather than looped over a list of field
 * names: the head is the one place where you have to be able to see at a
 * glance what actually goes out.
 *
 * @return void
 */
function suvantra_head_meta_output() {
	if ( ! suvantra_head_meta_enabled() ) {
		return;
	}

	$language     = suvantra_language_data();
	$url          = suvantra_current_url();
	$title        = suvantra_meta_title();
	$description  = suvantra_meta_description();
	$image        = suvantra_share_image();
	$translations = suvantra_translations();
	$root         = suvantra_root_language();

	echo "\n<!-- Suvantra: head metadata -->\n";

	if ( $description ) {
		printf( "<meta name=\"description\" content=\"%s\">\n", esc_attr( $description ) );
	}
	printf( "<link rel=\"canonical\" href=\"%s\">\n", esc_url( $url ) );

	foreach ( $translations as $code => $address ) {
		$data = suvantra_language_data( $code );
		printf(
			"<link rel=\"alternate\" hreflang=\"%s\" href=\"%s\">\n",
			esc_attr( $data['hreflang'] ),
			esc_url( $address )
		);
	}
	if ( isset( $translations[ $root ] ) ) {
		printf( "<link rel=\"alternate\" hreflang=\"x-default\" href=\"%s\">\n", esc_url( $translations[ $root ] ) );
	}

	echo "<meta property=\"og:type\" content=\"website\">\n";
	printf( "<meta property=\"og:locale\" content=\"%s\">\n", esc_attr( $language['locale'] ) );
	foreach ( suvantra_languages() as $code => $other ) {
		if ( $code !== $language['code'] && isset( $translations[ $code ] ) ) {
			printf( "<meta property=\"og:locale:alternate\" content=\"%s\">\n", esc_attr( $other['locale'] ) );
		}
	}
	printf( "<meta property=\"og:site_name\" content=\"%s\">\n", esc_attr( get_bloginfo( 'name', 'display' ) ) );
	printf( "<meta property=\"og:title\" content=\"%s\">\n", esc_attr( $title ) );
	if ( $description ) {
		printf( "<meta property=\"og:description\" content=\"%s\">\n", esc_attr( $description ) );
	}
	printf( "<meta property=\"og:url\" content=\"%s\">\n", esc_url( $url ) );
	printf( "<meta property=\"og:image\" content=\"%s\">\n", esc_url( $image['url'] ) );
	printf( "<meta property=\"og:image:width\" content=\"%d\">\n", (int) $image['width'] );
	printf( "<meta property=\"og:image:height\" content=\"%d\">\n", (int) $image['height'] );
	printf( "<meta property=\"og:image:alt\" content=\"%s\">\n", esc_attr( $title ) );
	echo "<meta name=\"twitter:card\" content=\"summary_large_image\">\n";
}
add_action( 'wp_head', 'suvantra_head_meta_output', 4 );

/**
 * WordPress emits a canonical of its own. While the theme emits one, that has
 * to go — two of them contradict each other when it matters.
 *
 * @return void
 */
function suvantra_remove_core_canonical() {
	if ( suvantra_head_meta_enabled() ) {
		remove_action( 'wp_head', 'rel_canonical' );
	}
}
add_action( 'wp', 'suvantra_remove_core_canonical' );

/**
 * Yoast, where installed, emits the same tags a second time — and wrongly,
 * because it takes the language from the WordPress setting rather than from
 * the path. Instead of removing the plugin (it handles titles and sitemaps
 * well) its presenters for Open Graph, Twitter and canonical are unregistered
 * here. This is the filter Yoast provides for exactly that.
 *
 * @param array $presenters Yoast's presenters.
 * @return array
 */
function suvantra_filter_yoast_presenters( $presenters ) {
	if ( ! suvantra_head_meta_enabled() ) {
		return $presenters;
	}
	$drop = array( 'Open_Graph', 'Twitter', 'Canonical' );
	return array_values( array_filter( $presenters, function ( $presenter ) use ( $drop ) {
		$name = is_object( $presenter ) ? get_class( $presenter ) : (string) $presenter;
		foreach ( $drop as $needle ) {
			if ( false !== strpos( $name, $needle ) ) {
				return false;
			}
		}
		return true;
	} ) );
}
add_filter( 'wpseo_frontend_presenters', 'suvantra_filter_yoast_presenters', 20 );

/** Older Yoast versions do not know the filter above; for those, the old way. */
add_filter( 'wpseo_opengraph_desc', 'suvantra_meta_description' );
