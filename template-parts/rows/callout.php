<?php
/**
 * Row: callout — an emphasised statement as its own band row.
 *
 * Framework source: .callout as the only content of a band
 * CSS owner: .r-callout
 * Blocks used: callout
 *
 * @var array{html: string} $args
 *
 * @package VerQuin
 */

defined( 'ABSPATH' ) || exit;

if ( empty( $args['html'] ) ) {
	return;
}
?>
<div class="r-callout">
	<?php verquin_block_callout( $args['html'] ); ?>
</div>
