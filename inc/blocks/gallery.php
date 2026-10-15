<?php
/**
 * Block: gallery — an auto-fit grid of figures.
 *
 * Framework source: .gallery > figure
 * CSS owner: .b-gallery
 *
 * @package VerQuin
 */

defined( 'ABSPATH' ) || exit;

/**
 * Render a gallery grid.
 *
 * Entries are figure args; each is delegated to the figure block unchanged, so a
 * gallery cell placeholders exactly as a standalone figure does.
 *
 * @param array<int, array<string, mixed>> $figures Figure args, in order.
 */
function verquin_block_gallery( array $figures ): void {
	$figures = array_filter( $figures );

	if ( ! $figures ) {
		return;
	}
	?>
	<div class="b-gallery">
		<?php foreach ( $figures as $figure ) : ?>
			<?php verquin_block_figure( $figure ); ?>
		<?php endforeach; ?>
	</div>
	<?php
}
