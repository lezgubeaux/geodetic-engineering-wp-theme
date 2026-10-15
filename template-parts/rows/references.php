<?php
/**
 * Row: references — grouped reading lists as their own band row.
 *
 * Framework source: h3 + ol.refs under .wrap, repeated
 * CSS owner: .r-references
 * Blocks used: references
 *
 * @var array{
 *   groups?: array<int, array{heading?: string, items: array<int, string>}>
 * } $args
 *
 * @package VerQuin
 */

defined( 'ABSPATH' ) || exit;

if ( empty( $args['groups'] ) ) {
	return;
}
?>
<div class="r-references">
	<?php verquin_block_references( $args['groups'] ); ?>
</div>
