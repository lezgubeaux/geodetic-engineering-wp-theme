<?php
/**
 * Row: gallery — a grid of figures as its own band row.
 *
 * Framework source: .gallery (direct child of .wrap)
 * CSS owner: .r-gallery
 * Blocks used: gallery
 *
 * @var array{
 *   figures?: array<int, array<string, mixed>>
 * } $args
 *   figures  Figure args in order; empty renders nothing.
 *
 * @package VerQuin
 */

defined( 'ABSPATH' ) || exit;

if ( empty( $args['figures'] ) ) {
	return;
}
?>
<div class="r-gallery">
	<?php verquin_block_gallery( $args['figures'] ); ?>
</div>
