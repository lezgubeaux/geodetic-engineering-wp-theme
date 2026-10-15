<?php
/**
 * Block: faq-item — one question and answer as a native disclosure.
 *
 * Framework source: details > summary + .ans (.verdict, p, table.spec, .callout, ul.links)
 * CSS owner: .b-faq-item
 *
 * Uses details/summary rather than scripted tabs, so keyboard operation, the open
 * state and in-page search come from the browser.
 *
 * @package VerQuin
 */

defined( 'ABSPATH' ) || exit;

/**
 * Render one FAQ item.
 *
 * The answer is a fixed sequence rather than a free ordering: verdict, answer,
 * table, callout, closing. The framework places a paragraph after the callout in
 * two of its ten items, which `closing` carries.
 *
 * @param array{
 *   question: string,
 *   verdict?: string,
 *   answer?: string,
 *   table?: array{caption?: string, rows?: array<int, array{label: string, value: string}>},
 *   callout?: string,
 *   closing?: string,
 *   links?: array<int, array{label: string, url: string, external?: bool}>
 * } $item
 */
function verquin_block_faq_item( array $item ): void {
	if ( empty( $item['question'] ) ) {
		return;
	}
	?>
	<details class="b-faq-item">
		<summary class="b-faq-item__question"><?php echo esc_html( $item['question'] ); ?></summary>
		<div class="b-faq-item__answer">
			<?php if ( ! empty( $item['verdict'] ) ) : ?>
				<p class="b-faq-item__verdict"><?php echo esc_html( $item['verdict'] ); ?></p>
			<?php endif; ?>

			<?php if ( ! empty( $item['answer'] ) ) : ?>
				<?php echo verquin_rich_text( $item['answer'] ); // phpcs:ignore WordPress.Security.EscapeOutput ?>
			<?php endif; ?>

			<?php if ( ! empty( $item['table']['rows'] ) ) : ?>
				<?php verquin_block_spec_table( $item['table']['rows'], $item['table']['caption'] ?? '', array( 'compact' ) ); ?>
			<?php endif; ?>

			<?php verquin_block_callout( $item['callout'] ?? '' ); ?>

			<?php if ( ! empty( $item['closing'] ) ) : ?>
				<?php echo verquin_rich_text( $item['closing'] ); // phpcs:ignore WordPress.Security.EscapeOutput ?>
			<?php endif; ?>

			<?php verquin_block_links( $item['links'] ?? array() ); ?>
		</div>
	</details>
	<?php
}
