<?php
/**
 * Foot of every page. What goes in it is decided by suvantra_footer_content()
 * in inc/language.php — the same function feeds the Customizer preview, so the
 * markup exists only once.
 *
 * @package Suvantra
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/* The CSS class names stay German — they also appear in the page content
   stored in WordPress. The setting is English, so it is translated here
   instead of leaving a German value in the database. */
$suvantra_layout = ( 'columns' === get_theme_mod( 'suvantra_footer_layout', 'row' ) ) ? 'spalten' : 'zeile';
?>
<footer class="fuss-<?php echo esc_attr( $suvantra_layout ); ?>">
  <div class="wrap">
	<?php suvantra_footer_content(); ?>
  </div>
</footer>
<?php wp_footer(); ?>
</body>
</html>
