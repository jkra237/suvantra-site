<?php
/**
 * The adjustable design values — colours, base font size, measure, dark mode,
 * and the header layout.
 *
 * Everything is emitted through CSS custom properties that style.css already
 * uses. That keeps the stylesheet the single source of the look, with the
 * Customizer only moving dials inside it; not one selector is overridden here.
 * Reset everything and you get exactly the stylesheet back — because then
 * nothing is emitted at all.
 *
 * The brand kit does settle the colours. The pickers exist because a second
 * product needs its own colour and because a company colour changes over the
 * years — not as an invitation to redo everything one evening.
 *
 * @package Suvantra
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * The adjustable colours — one value for light, one for dark.
 * 'contrast' => true means the text colour on it is calculated, not chosen.
 * 'selector' sets a different selector; without it :root applies.
 *
 * Labels and notes are wrapped in __() right here rather than where they are
 * displayed: a translation tool scans for literal strings, and __( $variable )
 * would be invisible to it.
 *
 * @return array<string,array>
 */
function suvantra_color_tokens() {
	return array(
		'paper' => array(
			'property' => '--paper',
			'light'    => '#FCFBF8',
			'dark'     => '#16181B',
			'label'    => __( 'Base surface', 'suvantra' ),
			'note'     => __( 'The background of the whole page.', 'suvantra' ),
		),
		'ground' => array(
			'property' => '--grund',
			'light'    => '#F6F4EE',
			'dark'     => '#1C1F22',
			'label'    => __( 'Offset surface', 'suvantra' ),
			'note'     => __( 'Whole sections and the footer lift off the page with it. The difference to the base surface may be small — it should not stand out, only divide.', 'suvantra' ),
		),
		'chrome' => array(
			'property' => '--chrome',
			'light'    => '#EAE8E0',
			'dark'     => '#24272B',
			'label'    => __( 'Button surface', 'suvantra' ),
			'note'     => __( 'The second, quiet button. The first one carries the accent.', 'suvantra' ),
		),
		'ink' => array(
			'property' => '--ink',
			'light'    => '#191B1E',
			'dark'     => '#F2F0EA',
			'label'    => __( 'Text', 'suvantra' ),
			'note'     => '',
		),
		'ink-soft' => array(
			'property' => '--ink-soft',
			'light'    => '#5C6064',
			'dark'     => '#A9ADB2',
			'label'    => __( 'Text, quieter', 'suvantra' ),
			'note'     => __( 'Lead paragraphs, captions, the footer.', 'suvantra' ),
		),
		'rule' => array(
			'property' => '--rule',
			'light'    => '#E2DFD5',
			'dark'     => '#2E3236',
			'label'    => __( 'Lines and borders', 'suvantra' ),
			'note'     => '',
		),
		'accent' => array(
			'property' => '--akzent',
			'light'    => '#3C4147',
			'dark'     => '#C9CDD2',
			'contrast' => true,
			'label'    => __( 'Company accent', 'suvantra' ),
			'note'     => __( 'First button, brand mark, focus ring. On a dark ground the same colour has to be lighter or it disappears.', 'suvantra' ),
		),
		'accent-pq' => array(
			'property' => '--akzent',
			'light'    => '#8C3B2E',
			'dark'     => '#D4705F',
			'contrast' => true,
			'selector' => 'body.p-purequill',
			'label'    => __( 'PureQuill accent', 'suvantra' ),
			'note'     => __( 'Applies to every page under /purequill/ and its translations.', 'suvantra' ),
		),
	);
}

/**
 * The remaining light values. Needed only when dark mode is switched off: then
 * everything style.css changes in the dark has to be pulled back, or the dark
 * band would stay dark and the shadows too hard.
 *
 * @return array<string,string>
 */
function suvantra_light_extras() {
	return array(
		'--chrome-2'       => '#F3F1EA',
		'--ink-faint'      => '#8A8D91',
		'--akzent-ink'     => '#FFFFFF',
		'--dunkel'         => '#191B1E',
		'--dunkel-ink'     => '#F4F2EC',
		'--dunkel-soft'    => '#A8ABA6',
		'--dunkel-rule'    => '#33363A',
		'--schatten'       => '0 1px 2px rgba(25,27,30,.06), 0 8px 24px -8px rgba(25,27,30,.14)',
		'--schatten-gross' => '0 2px 4px rgba(25,27,30,.05), 0 24px 60px -18px rgba(25,27,30,.28)',
	);
}

/**
 * Defaults of the numeric values — character for character what style.css says.
 *
 * @return array<string,int>
 */
function suvantra_design_defaults() {
	return array(
		'suvantra_font_size'     => 17,
		'suvantra_header_height' => 66,
		'suvantra_measure'       => 1180,
	);
}

/**
 * Black or white text on a colour? Calculated from WCAG relative luminance —
 * nothing is guessed here, because legible button labels hang on it exactly.
 *
 * @param string $hex Colour.
 * @return string
 */
function suvantra_contrast_color( $hex ) {
	$hex = ltrim( (string) $hex, '#' );
	if ( 3 === strlen( $hex ) ) {
		$hex = $hex[0] . $hex[0] . $hex[1] . $hex[1] . $hex[2] . $hex[2];
	}
	if ( 6 !== strlen( $hex ) || ! ctype_xdigit( $hex ) ) {
		return '#FFFFFF';
	}
	$channels = array(
		hexdec( substr( $hex, 0, 2 ) ) / 255,
		hexdec( substr( $hex, 2, 2 ) ) / 255,
		hexdec( substr( $hex, 4, 2 ) ) / 255,
	);
	foreach ( $channels as $i => $c ) {
		$channels[ $i ] = ( $c <= 0.03928 ) ? $c / 12.92 : pow( ( $c + 0.055 ) / 1.055, 2.4 );
	}
	$luminance = 0.2126 * $channels[0] + 0.7152 * $channels[1] + 0.0722 * $channels[2];
	return ( $luminance > 0.4 ) ? '#16181B' : '#FFFFFF';
}

/**
 * The chosen colour, when it differs from the default.
 *
 * @param string $key  Token key.
 * @param string $mode 'light' or 'dark'.
 * @return string|null
 */
function suvantra_color_override( $key, $mode ) {
	$tokens = suvantra_color_tokens();
	if ( ! isset( $tokens[ $key ][ $mode ] ) ) {
		return null;
	}
	$value = get_theme_mod( 'suvantra_color_' . $key . '_' . $mode, $tokens[ $key ][ $mode ] );
	$value = strtoupper( (string) $value );
	return ( $value && $value !== strtoupper( $tokens[ $key ][ $mode ] ) ) ? $value : null;
}

/**
 * A numeric value, when it differs from the default.
 *
 * @param string $name Setting name.
 * @return int|null
 */
function suvantra_number_override( $name ) {
	$defaults = suvantra_design_defaults();
	if ( ! isset( $defaults[ $name ] ) ) {
		return null;
	}
	$value = (int) get_theme_mod( $name, $defaults[ $name ] );
	return ( $value && $value !== (int) $defaults[ $name ] ) ? $value : null;
}

/**
 * The rules for one mode. Returns selector => declarations, so light and dark
 * run through the same machinery.
 *
 * @param string $mode 'light' or 'dark'.
 * @return array<string,string>
 */
function suvantra_color_rules( $mode ) {
	$rules = array();

	foreach ( suvantra_color_tokens() as $key => $token ) {
		$value = suvantra_color_override( $key, $mode );
		if ( ! $value ) {
			continue;
		}
		$where = isset( $token['selector'] ) ? $token['selector'] : ':root';
		if ( ! isset( $rules[ $where ] ) ) {
			$rules[ $where ] = '';
		}
		$rules[ $where ] .= $token['property'] . ':' . $value . ';';
		if ( ! empty( $token['contrast'] ) ) {
			$rules[ $where ] .= '--akzent-ink:' . suvantra_contrast_color( $value ) . ';';
		}
	}

	/* Two values are not chosen but mixed: the lighter button surface and the
	   faintest text. Making them settings of their own would be one more dial
	   nobody ever wants set differently — derived, they always match. */
	$paper  = suvantra_color_override( 'paper', $mode );
	$chrome = suvantra_color_override( 'chrome', $mode );
	$soft   = suvantra_color_override( 'ink-soft', $mode );

	if ( $paper || $chrome ) {
		$rules[':root'] = ( isset( $rules[':root'] ) ? $rules[':root'] : '' )
			. '--chrome-2:color-mix(in srgb,' . ( $chrome ? $chrome : 'var(--chrome)' )
			. ' 55%,' . ( $paper ? $paper : 'var(--paper)' ) . ');';
	}
	if ( $paper || $soft ) {
		$rules[':root'] = ( isset( $rules[':root'] ) ? $rules[':root'] : '' )
			. '--ink-faint:color-mix(in srgb,' . ( $soft ? $soft : 'var(--ink-soft)' )
			. ' 72%,' . ( $paper ? $paper : 'var(--paper)' ) . ');';
	}
	return $rules;
}

/**
 * A rule set to CSS.
 *
 * @param array<string,string> $rules Selector => declarations.
 * @return string
 */
function suvantra_rules_to_css( $rules ) {
	$css = '';
	foreach ( $rules as $where => $declarations ) {
		if ( $declarations ) {
			$css .= $where . '{' . $declarations . '}';
		}
	}
	return $css;
}

/**
 * Is dark mode on?
 *
 * @return bool
 */
function suvantra_dark_mode_enabled() {
	return (bool) get_theme_mod( 'suvantra_dark_mode', true );
}

/**
 * The stylesheet of deviations. Empty as long as nothing is changed.
 *
 * @return string
 */
function suvantra_design_css() {
	$css = suvantra_rules_to_css( suvantra_color_rules( 'light' ) );

	$root = '';
	$font = suvantra_number_override( 'suvantra_font_size' );
	if ( $font ) {
		$root .= '--schrift:' . $font . 'px;';
	}
	$measure = suvantra_number_override( 'suvantra_measure' );
	if ( $measure ) {
		$root .= '--breit:' . $measure . 'px;';
	}
	$header = suvantra_number_override( 'suvantra_header_height' );
	if ( $header ) {
		$root .= '--kopf-hoehe:' . $header . 'px;';
	}
	if ( $root ) {
		$css .= ':root{' . $root . '}';
	}

	if ( suvantra_dark_mode_enabled() ) {
		$dark = suvantra_rules_to_css( suvantra_color_rules( 'dark' ) );
		if ( $dark ) {
			$css .= '@media (prefers-color-scheme:dark){' . $dark . '}';
		}
		return $css;
	}

	/* Dark mode off: take back everything style.css changes in the dark, or
	   the dark band would stay dark and the shadows too hard. */
	$light = array();
	foreach ( suvantra_color_tokens() as $key => $token ) {
		$value = suvantra_color_override( $key, 'light' );
		$value = $value ? $value : $token['light'];
		$where = isset( $token['selector'] ) ? $token['selector'] : ':root';
		if ( ! isset( $light[ $where ] ) ) {
			$light[ $where ] = '';
		}
		$light[ $where ] .= $token['property'] . ':' . $value . ';';
		if ( ! empty( $token['contrast'] ) ) {
			$light[ $where ] .= '--akzent-ink:' . suvantra_contrast_color( $value ) . ';';
		}
	}
	foreach ( suvantra_light_extras() as $property => $value ) {
		$light[':root'] .= $property . ':' . $value . ';';
	}
	$css .= '@media (prefers-color-scheme:dark){' . suvantra_rules_to_css( $light ) . '}';
	return $css;
}

/**
 * Appended to the theme stylesheet rather than emitted as its own <style>
 * block: one file fewer in the head, and the order is right by itself — the
 * deviations come after what they deviate from.
 *
 * @return void
 */
function suvantra_enqueue_design_css() {
	$css = suvantra_design_css();
	if ( $css ) {
		wp_add_inline_style( 'suvantra', $css );
	}
}
add_action( 'wp_enqueue_scripts', 'suvantra_enqueue_design_css', 11 );

/* ------------------------------------------------------------ Header layout */

/**
 * The classes for <header class="…">. Classes only, no styling: how the four
 * layouts look is in the stylesheet — here is only which one applies. Anything
 * left at its default emits no class, so the sticky bar with its rule needs
 * none at all.
 *
 * CSS class names stay German: they also appear in the page content stored in
 * WordPress, and renaming them would mean re-importing every page.
 *
 * @return string
 */
function suvantra_header_classes() {
	$classes = array( 'top' );

	$layout = get_theme_mod( 'suvantra_header_layout', 'left' );
	$map    = array(
		'inline'   => 'kopf-eng',
		'centre'   => 'kopf-mitte',
		'mirrored' => 'kopf-gespiegelt',
	);
	if ( isset( $map[ $layout ] ) ) {
		$classes[] = $map[ $layout ];
	}
	if ( ! get_theme_mod( 'suvantra_header_sticky', true ) ) {
		$classes[] = 'kopf-lose';
	}
	if ( ! get_theme_mod( 'suvantra_header_rule', true ) ) {
		$classes[] = 'kopf-ohne-linie';
	}
	return implode( ' ', $classes );
}

/**
 * Should the header image show on this page? WordPress supplies the image, the
 * Customizer decides where. Without an uploaded image nothing happens at all —
 * the templates then look exactly as they would without this function.
 *
 * @return bool
 */
function suvantra_show_header_image() {
	if ( ! has_header_image() ) {
		return false;
	}
	if ( 'home' === get_theme_mod( 'suvantra_header_image_where', 'all' ) ) {
		return 'home' === suvantra_group_for_path();
	}
	return true;
}
