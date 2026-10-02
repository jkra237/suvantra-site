<?php
/**
 * Footer, single row layout. The default: quiet, and enough for a handful of
 * links.
 *
 * @package Suvantra
 *
 * @var array $args Passed by suvantra_footer_content().
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$suvantra_links = isset( $args['links'] ) ? $args['links'] : array();
?>
<span class="fuss-copy"><?php echo wp_kses_post( $args['copyright'] ); ?></span>
<span class="grow"></span>
<?php foreach ( $suvantra_links as $suvantra_link ) : ?>
	<a href="<?php echo esc_url( $suvantra_link['url'] ); ?>"
		<?php if ( ! empty( $suvantra_link['title'] ) ) : ?>
			title="<?php echo esc_attr( $suvantra_link['title'] ); ?>"
		<?php endif; ?>
		<?php if ( ! empty( $suvantra_link['target'] ) ) : ?>
			target="<?php echo esc_attr( $suvantra_link['target'] ); ?>" rel="noopener"
		<?php endif; ?>><?php echo esc_html( $suvantra_link['text'] ); ?></a>
<?php endforeach; ?>

<?php if ( ! empty( $args['show_mail'] ) && ! empty( $args['email'] ) ) : ?>
	<a href="mailto:<?php echo esc_attr( antispambot( $args['email'] ) ); ?>"><?php
		echo esc_html( antispambot( $args['email'] ) );
	?></a>
<?php endif; ?>

<?php if ( ! empty( $args['note'] ) ) : ?>
	<p class="fuss-notiz"><?php echo wp_kses_post( $args['note'] ); ?></p>
<?php endif; ?>
