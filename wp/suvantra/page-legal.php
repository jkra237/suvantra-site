<?php
/**
 * Template Name: Legal text
 *
 * For privacy and imprint: narrow column, heading from the page title. The
 * wrapper lives here so that the editor only has to hold headings and
 * paragraphs — no HTML blocks, no markup that can slip.
 *
 * @package Suvantra
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
get_header();
?>
<main id="inhalt" class="wrap recht">
<?php
while ( have_posts() ) {
	the_post();
	echo '<h1>' . esc_html( get_the_title() ) . '</h1>';
	the_content();
}
?>
</main>
<?php
get_footer();
