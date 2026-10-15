<?php
/**
 * Row: faq — an accordion of questions as its own band row.
 *
 * Framework source: .faq (direct child of .wrap)
 * CSS owner: .r-faq
 * Blocks used: faq-item (which uses spec-table, callout, links)
 *
 * The framework splits its ten questions across four bands, each with its own
 * intro heading; the row carries one group, not the whole page.
 *
 * @var array{
 *   items?: array<int, array<string, mixed>>
 * } $args
 *
 * @package VerQuin
 */

defined( 'ABSPATH' ) || exit;

if ( empty( $args['items'] ) ) {
	return;
}
?>
<div class="r-faq">
	<?php foreach ( $args['items'] as $item ) : ?>
		<?php verquin_block_faq_item( $item ); ?>
	<?php endforeach; ?>
</div>
