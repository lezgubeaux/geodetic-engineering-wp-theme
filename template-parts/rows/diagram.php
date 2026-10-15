<?php
/**
 * Row: diagram — a theme-owned diagram as its own band row.
 *
 * Framework source: figure.diagram / figure.quincunx under .wrap
 * CSS owner: .r-diagram
 * Blocks used: diagram
 *
 * @var array{
 *   diagram?: string,
 *   caption?: string,
 *   credit?: string
 * } $args
 *   diagram  Registered diagram id; an unknown id renders nothing.
 *
 * @package VerQuin
 */

defined( 'ABSPATH' ) || exit;

if ( empty( $args['diagram'] ) ) {
	return;
}
?>
<div class="r-diagram">
	<?php
	verquin_block_diagram(
		$args['diagram'],
		array(
			'caption' => $args['caption'] ?? '',
			'credit'  => $args['credit'] ?? '',
		)
	);
	?>
</div>
