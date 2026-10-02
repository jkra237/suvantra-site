<?php
/**
 * Footer, column layout. Worth it from roughly five links onwards; below that
 * the single row is calmer.
 *
 * @package Suvantra
 *
 * @var array $args Passed by suvantra_footer_columns().
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$suvantra_columns = isset( $args['columns'] ) ? $args['columns'] : array();
$suvantra_loose   = isset( $args['loose'] ) ? $args['loose'] : array();
?>
<div class="fuss-spalten">

	<div class="fuss-marke">
		<span class="fuss-copy"><?php echo wp_kses_post( $args['copyright'] ); ?></span>
		<?php if ( ! empty( $args['show_mail'] ) && ! empty( $args['email'] ) ) : ?>
			<a href="mailto:<?php echo esc_attr( antispambot( $args['email'] ) ); ?>"><?php
				echo esc_html( antispambot( $args['email'] ) );
			?></a>
		<?php endif; ?>
	</div>

	<?php foreach ( $suvantra_columns as $suvantra_column ) : ?>
		<div class="fuss-spalte">
			<h2><?php echo esc_html( $suvantra_column['text'] ); ?></h2>
			<ul>
				<?php foreach ( $suvantra_column['children'] as $suvantra_child ) : ?>
					<li><a href="<?php echo esc_url( $suvantra_child['url'] ); ?>"
						<?php if ( ! empty( $suvantra_child['target'] ) ) : ?>
							target="<?php echo esc_attr( $suvantra_child['target'] ); ?>" rel="noopener"
						<?php endif; ?>><?php echo esc_html( $suvantra_child['text'] ); ?></a></li>
				<?php endforeach; ?>
			</ul>
		</div>
	<?php endforeach; ?>

	<?php if ( $suvantra_loose ) : ?>
		<div class="fuss-spalte">
			<h2 class="screen-reader-text"><?php esc_html_e( 'Legal', 'suvantra' ); ?></h2>
			<ul>
				<?php foreach ( $suvantra_loose as $suvantra_link ) : ?>
					<li><a href="<?php echo esc_url( $suvantra_link['url'] ); ?>"
						<?php if ( ! empty( $suvantra_link['target'] ) ) : ?>
							target="<?php echo esc_attr( $suvantra_link['target'] ); ?>" rel="noopener"
						<?php endif; ?>><?php echo esc_html( $suvantra_link['text'] ); ?></a></li>
				<?php endforeach; ?>
			</ul>
		</div>
	<?php endif; ?>

</div>

<?php if ( ! empty( $args['note'] ) ) : ?>
	<p class="fuss-notiz"><?php echo wp_kses_post( $args['note'] ); ?></p>
<?php endif; ?>
