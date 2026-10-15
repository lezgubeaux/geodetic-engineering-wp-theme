<?php
/**
 * Block: children — a hairline grid of links to related pages.
 *
 * Framework source: .children > a (span.idx + h3)
 * CSS owner: .b-children
 *
 * @package VerQuin
 */

defined( 'ABSPATH' ) || exit;

/**
 * Render a child-page index grid.
 *
 * The index number is positional, not stored: the framework numbers the cells
 * 01, 02, … in document order, so reordering the pages renumbers them.
 *
 * @param array<int, array{title: string, url: string}> $items Ordered pages; entries missing either value are dropped.
 */
function verquin_block_children( array $items ): void {
	$items = array_values(
		array_filter(
			$items,
			static fn( $i ) => ! empty( $i['title'] ) && ! empty( $i['url'] )
		)
	);

	if ( ! $items ) {
		return;
	}
	?>
	<div class="b-children">
		<?php foreach ( $items as $position => $item ) : ?>
			<a class="b-children__item" href="<?php echo esc_url( $item['url'] ); ?>">
				<span class="b-children__index"><?php echo esc_html( sprintf( '%02d', $position + 1 ) ); ?></span>
				<h3 class="b-children__title"><?php echo esc_html( $item['title'] ); ?></h3>
			</a>
		<?php endforeach; ?>
	</div>
	<?php
}
