<?php
/**
 * Row: buttons — a call-to-action group as its own band row.
 *
 * Framework source: .acts as a direct child of .wrap
 * CSS owner: .r-buttons
 * Blocks used: buttons
 *
 * @var array{buttons: array<int, array{label: string, url: string, style?: string}>} $args
 *
 * @package VerQuin
 */

defined( 'ABSPATH' ) || exit;

if ( empty( $args['buttons'] ) ) {
	return;
}
?>
<div class="r-buttons">
	<?php verquin_block_buttons( $args['buttons'] ); ?>
</div>
