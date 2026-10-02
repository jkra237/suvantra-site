<?php
/**
 * The error page. It follows the requested address: ask for /de/nothing-here/
 * and you get German text and the German navigation. The strings run through
 * __(), and the locale filter in inc/language.php makes that work per page
 * rather than per site setting.
 *
 * @package Suvantra
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
get_header();
?>
<main id="inhalt" class="wrap recht">
  <h1><?php esc_html_e( 'Page not found', 'suvantra' ); ?></h1>
  <p><?php esc_html_e( 'This address does not exist.', 'suvantra' ); ?>
    <a href="<?php echo esc_url( suvantra_language_home( suvantra_language_code() ) ); ?>"><?php
      esc_html_e( 'Back to the home page', 'suvantra' ); ?></a>.</p>
</main>
<?php
get_footer();
