<?php
/**
 * Template for the content pages. The body comes entirely from the editor so
 * that texts can be changed without touching PHP; the theme only contributes
 * header and footer. The sections of the product page bring their own
 * <section class="wrap"> along — which is why there is no extra wrapper here.
 *
 * @package Suvantra
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
get_header();
?>
<main id="inhalt">
<?php
while ( have_posts() ) {
	the_post();
	the_content();
}
?>
</main>
<?php
get_footer();
