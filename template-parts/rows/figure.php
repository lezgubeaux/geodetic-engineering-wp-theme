<?php
/**
 * Row: figure — one standalone figure as a band row.
 *
 * Framework source: figure directly under .wrap (photo, plot, video, logo card)
 * CSS owner: .r-figure
 * Blocks used: figure
 *
 * @var array{
 *   figure?: array<string, mixed>,
 *   narrow?: bool
 * } $args
 *   figure  Figure args; empty renders nothing.
 *   narrow  Caps the row at --measure. The framework's two inline cases are
 *           520px and 620px; one reading-width cap covers both.
 *
 * @package VerQuin
 */

defined( 'ABSPATH' ) || exit;

if ( empty( $args['figure'] ) ) {
	return;
}
?>
<div class="<?php echo verquin_classes( 'r-figure', array( ! empty( $args['narrow'] ) ? 'narrow' : '' ) ); // phpcs:ignore WordPress.Security.EscapeOutput ?>">
	<?php verquin_block_figure( $args['figure'] ); ?>
</div>
