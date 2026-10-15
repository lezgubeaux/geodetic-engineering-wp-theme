<?php
/**
 * Section: page-head — page opener with optional breadcrumb; tall variant is the front-page hero.
 *
 * Framework source: header.pagehead / header.pagehead.tall / .crumb
 * CSS owner: .s-page-head
 * Blocks used: buttons
 *
 * @var array{
 *   eyebrow?: string,
 *   heading: string,
 *   lede?: string,
 *   tall?: bool,
 *   crumb?: array{label: string, url: string},
 *   buttons?: array<int, array{label: string, url: string, style?: string}>
 * } $args
 *   tall   Full-height hero treatment (front page).
 *   crumb  Parent page link shown above the eyebrow.
 *
 * @package VerQuin
 */

defined( 'ABSPATH' ) || exit;

if ( empty( $args['heading'] ) ) {
	return;
}

$tall = ! empty( $args['tall'] );
?>
<section class="<?php echo verquin_classes( 's-page-head', array( $tall ? 'tall' : '' ) ); // phpcs:ignore WordPress.Security.EscapeOutput ?>">
	<div class="wrap">
		<?php if ( ! empty( $args['crumb']['label'] ) && ! empty( $args['crumb']['url'] ) ) : ?>
			<nav class="s-page-head__crumb" aria-label="<?php esc_attr_e( 'Breadcrumb', 'verquin' ); ?>">
				<a href="<?php echo esc_url( $args['crumb']['url'] ); ?>"><?php echo esc_html( $args['crumb']['label'] ); ?></a>
			</nav>
		<?php endif; ?>

		<?php if ( ! empty( $args['eyebrow'] ) ) : ?>
			<span class="eyebrow"><?php echo esc_html( $args['eyebrow'] ); ?></span>
		<?php endif; ?>

		<h1 class="s-page-head__heading"><?php echo verquin_heading( $args['heading'] ); // phpcs:ignore WordPress.Security.EscapeOutput ?></h1>

		<?php if ( ! empty( $args['lede'] ) ) : ?>
			<p class="lede s-page-head__lede"><?php echo verquin_rich_text( $args['lede'] ); // phpcs:ignore WordPress.Security.EscapeOutput ?></p>
		<?php endif; ?>

		<?php verquin_block_buttons( $args['buttons'] ?? array() ); ?>
	</div>
</section>
