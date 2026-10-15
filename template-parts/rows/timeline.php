<?php
/**
 * Row: timeline — a dated event list as its own band row.
 *
 * Framework source: .tl (direct child of .wrap)
 * CSS owner: .r-timeline
 * Blocks used: timeline
 *
 * @var array{
 *   events?: array<int, array{year: string, heading?: string, body?: string}>
 * } $args
 *
 * @package VerQuin
 */

defined( 'ABSPATH' ) || exit;

if ( empty( $args['events'] ) ) {
	return;
}
?>
<div class="r-timeline">
	<?php verquin_block_timeline( $args['events'] ); ?>
</div>
