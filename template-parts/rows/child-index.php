<?php
/**
 * Row: child-index — links to this page's children, or to a chosen set of pages.
 *
 * Framework source: .children (direct child of .wrap)
 * CSS owner: .r-child-index
 * Blocks used: children
 *
 * @var array{
 *   items?: array<int, array{title: string, url: string}>
 * } $args
 *   items  Already resolved by verquin_child_index_items(); empty renders nothing.
 *
 * @package VerQuin
 */

defined( 'ABSPATH' ) || exit;

if ( empty( $args['items'] ) ) {
	return;
}
?>
<div class="r-child-index">
	<?php verquin_block_children( $args['items'] ); ?>
</div>
