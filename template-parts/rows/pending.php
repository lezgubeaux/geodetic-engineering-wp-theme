<?php
/**
 * Row: pending — a copy-to-be-supplied marker holding a band's place.
 *
 * Framework source: .pending
 * CSS owner: .r-pending
 * Blocks used: pending
 *
 * @var array{note?: string} $args
 *
 * @package VerQuin
 */

defined( 'ABSPATH' ) || exit;
?>
<div class="r-pending">
	<?php verquin_block_pending( $args['note'] ?? '' ); ?>
</div>
