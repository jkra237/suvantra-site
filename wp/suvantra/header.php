<?php
/**
 * Head of every page. The main navigation comes from one of the menu
 * locations — one per language, chosen by the page path. The language switch
 * is calculated here instead of taken from a menu: it should point at the
 * COUNTERPART of the current page, and a menu cannot know that.
 *
 * @package Suvantra
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$suvantra_code     = suvantra_language_code();
$suvantra_german   = 'de' === $suvantra_code;
$suvantra_location = suvantra_menu_location( 'main', $suvantra_code );
$suvantra_links    = get_theme_mod( 'suvantra_language_switcher', true ) ? suvantra_language_links() : array();
?>
<!doctype html>
<?php
/* Not language_attributes(): that takes the WordPress language setting, which
   is de_DE even though the front page is English — every English page would
   have declared itself German. Screen readers pronounce by it, search engines
   read it. */
?>
<html lang="<?php echo esc_attr( str_replace( '_', '-', suvantra_language_data()['locale'] ) ); ?>">
<head>
<meta charset="<?php bloginfo( 'charset' ); ?>">
<meta name="viewport" content="width=device-width,initial-scale=1">
<?php
/* Tells the browser which modes the page knows — form fields and scrollbars
   are coloured by it before the stylesheet arrives. */
$suvantra_dark = suvantra_dark_mode_enabled();
?>
<meta name="color-scheme" content="<?php echo $suvantra_dark ? 'light dark' : 'light'; ?>">
<meta name="theme-color" media="(prefers-color-scheme: light)" content="<?php echo esc_attr( get_theme_mod( 'suvantra_color_paper_light', '#FCFBF8' ) ); ?>">
<?php if ( $suvantra_dark ) : ?>
<meta name="theme-color" media="(prefers-color-scheme: dark)" content="<?php echo esc_attr( get_theme_mod( 'suvantra_color_paper_dark', '#16181B' ) ); ?>">
<?php endif; ?>
<?php /* Only while no site icon is set in the Customizer — otherwise there would be two. */ ?>
<?php if ( ! has_site_icon() ) : ?>
<link rel="icon" href="<?php echo esc_url( suvantra_asset_url( 'favicon-32.png' ) ); ?>" sizes="32x32">
<?php endif; ?>
<?php wp_head(); ?>
<?php $suvantra_schema = suvantra_structured_data(); if ( $suvantra_schema ) : ?>
<script type="application/ld+json">
<?php echo wp_json_encode( $suvantra_schema, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT ); ?>
</script>
<?php endif; ?>
</head>
<body <?php body_class(); ?>>
<?php wp_body_open(); ?>
<?php
/* Skip link. Invisible until it takes focus — anyone navigating by keyboard
   would otherwise have to walk through the header bar on every page. */
?>
<a class="sprung" href="#inhalt"><?php esc_html_e( 'Skip to content', 'suvantra' ); ?></a>

<header class="<?php echo esc_attr( suvantra_header_classes() ); ?>">
  <div class="wrap">
    <?php
    /* With a logo in the Customizer it takes the place of dot and wordmark —
       otherwise both would stand side by side. the_custom_logo() links to the
       front page itself; for the German version the link should go to /de/,
       hence the frame of our own. */
    $suvantra_home = esc_url( home_url( $suvantra_german ? '/de/' : '/' ) );
    if ( has_custom_logo() ) :
      $suvantra_logo = wp_get_attachment_image(
        get_theme_mod( 'custom_logo' ), 'full', false,
        array( 'class' => 'marke-logo', 'alt' => 'Suvantra' )
      );
      ?><a class="marke" href="<?php echo $suvantra_home; ?>"><?php echo $suvantra_logo; ?></a><?php
    else :
      ?><a class="marke" href="<?php echo $suvantra_home; ?>"><span class="m-punkt"></span>Suvantra</a><?php
    endif;
    ?>
    <?php
    if ( has_nav_menu( $suvantra_location ) ) {
      wp_nav_menu( array(
        'theme_location' => $suvantra_location,
        'container'      => 'nav',
        'depth'          => 1,
      ) );
    }
    ?>
    <?php
    /* With two languages a single button, from three on a row — so nothing
       has to change here when a language is added. */
    foreach ( $suvantra_links as $suvantra_link ) :
      ?><a class="lang" href="<?php echo esc_url( $suvantra_link['url'] ); ?>"
       hreflang="<?php echo esc_attr( $suvantra_link['lang'] ); ?>"><?php echo esc_html( $suvantra_link['text'] ); ?></a><?php
    endforeach;
    ?>
  </div>
</header>

<?php
/* Header image, if one has been uploaded. It sits under the bar, not behind
   it: the bar is translucent and would be unreadable over a light image.
   Without an image nothing at all is emitted here. */
if ( suvantra_show_header_image() ) :
  ?>
  <div class="kopfgrafik">
    <?php the_header_image_tag( array( 'alt' => '', 'loading' => false ) ); ?>
  </div>
  <?php
endif;
?>
