<?php
/**
 * Row: cards — a cards grid as its own band row.
 *
 * Framework source: .cards (direct child of .wrap)
 * CSS owner: .r-cards
 * Blocks used: cards
 *
 * @var array{
 *   cards: array<int, array{title: string, index?: string, text?: string, note?: string, url?: string, figure?: array<string, mixed>, links?: array<int, array<string, mixed>>}>
 * } $args
 *
 * @package VerQuin
 */

defined( 'ABSPATH' ) || exit;

if ( empty( $args['cards'] ) ) {
	return;
}
?>
<div class="r-cards">
	<?php verquin_block_cards( $args['cards'] ); ?>
</div>
