<?php
/**
 * Row: links — a links list as its own band row.
 *
 * Framework source: ul.links under .wrap or after a gallery/table
 * CSS owner: .r-links
 * Blocks used: links
 *
 * @var array{links: array<int, array{label: string, url: string, external?: bool}>} $args
 *
 * @package VerQuin
 */

defined( 'ABSPATH' ) || exit;

if ( empty( $args['links'] ) ) {
	return;
}
?>
<div class="r-links">
	<?php verquin_block_links( $args['links'] ); ?>
</div>
