<?php
/**
 * Section: band — one page band: optional intro followed by any number of rows.
 *
 * Framework source: section > .wrap > (eyebrow, h2, .lede)? + rows
 * CSS owner: .s-band
 * Blocks used: none directly (rows call blocks)
 *
 * @var array{
 *   id?: string,
 *   eyebrow?: string,
 *   heading?: string,
 *   lede?: string,
 *   rows?: array<int, array{row: string, data: array<string, mixed>}>
 * } $args
 *   id    Optional anchor id (framework uses e.g. "inquiry").
 *   rows  Ordered list; each item names a row slug under template-parts/rows/ and its data.
 *
 * @package VerQuin
 */

defined( 'ABSPATH' ) || exit;

$has_intro = ! empty( $args['eyebrow'] ) || ! empty( $args['heading'] ) || ! empty( $args['lede'] );
$rows      = $args['rows'] ?? array();

// Rows are rendered first: a row may decide it has nothing to show (a child-index on a
// page with no children, a cards row whose entries were all filtered out), and an
// intro-less band of only such rows must not print an empty padded section.
ob_start();

foreach ( $rows as $row ) {
	if ( empty( $row['row'] ) ) {
		continue;
	}
	verquin_row( $row['row'], $row['data'] ?? array() );
}

$rows_html = trim( (string) ob_get_clean() );

if ( ! $has_intro && '' === $rows_html ) {
	return;
}
?>
<section class="s-band"<?php echo ! empty( $args['id'] ) ? ' id="' . esc_attr( $args['id'] ) . '"' : ''; ?>>
	<div class="wrap">
		<?php if ( $has_intro ) : ?>
			<div class="s-band__intro">
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

		<?php echo $rows_html; // phpcs:ignore WordPress.Security.EscapeOutput — row templates escape their own output. ?>
	</div>
</section>
