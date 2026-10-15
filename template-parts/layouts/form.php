<?php
/**
 * Section: form — a band wrapping one Contact Form 7 form.
 *
 * Framework source: section > .wrap > .form / .form.wide
 * CSS owner: .s-form
 * Blocks used: none (Contact Form 7 renders the fields)
 *
 * @var array{
 *   id?: string,
 *   eyebrow?: string,
 *   heading?: string,
 *   lede?: string,
 *   form: string,
 *   title?: string,
 *   wide?: bool
 * } $args
 *   form   CF7 form hash id, e.g. '8a20e50'.
 *   title  Optional CF7 form title.
 *   wide   Widens the form column for long questionnaires (framework .form.wide).
 *
 * @package VerQuin
 */

defined( 'ABSPATH' ) || exit;

if ( empty( $args['form'] ) ) {
	return;
}

$has_intro = ! empty( $args['eyebrow'] ) || ! empty( $args['heading'] ) || ! empty( $args['lede'] );
?>
<section class="<?php echo verquin_classes( 's-form', array( empty( $args['wide'] ) ? '' : 'wide' ) ); ?>"<?php echo empty( $args['id'] ) ? '' : ' id="' . esc_attr( $args['id'] ) . '"'; ?>>
	<div class="wrap">
		<?php if ( $has_intro ) : ?>
			<div class="s-form__intro">
				<?php if ( ! empty( $args['eyebrow'] ) ) : ?>
					<span class="eyebrow"><?php echo esc_html( $args['eyebrow'] ); ?></span>
				<?php endif; ?>
				<?php if ( ! empty( $args['heading'] ) ) : ?>
					<h2><?php echo verquin_heading( $args['heading'] ); // phpcs:ignore WordPress.Security.EscapeOutput ?></h2>
				<?php endif; ?>
				<?php if ( ! empty( $args['lede'] ) ) : ?>
					<p class="lede"><?php echo verquin_rich_text( $args['lede'] ); // phpcs:ignore WordPress.Security.EscapeOutput ?></p>
				<?php endif; ?>
			</div>
		<?php endif; ?>

		<div class="s-form__form">
			<?php verquin_cf7_form( $args['form'], $args['title'] ?? '' ); ?>
		</div>
	</div>
</section>
