<?php
/**
 * Block: pending — a dashed marker where client copy has not been supplied.
 *
 * Framework source: .pending
 * CSS owner: .b-pending
 *
 * Development scaffolding: every instance is removed once the copy arrives
 * (see docs/post-beta-release-cleanup.md).
 *
 * @package VerQuin
 */

defined( 'ABSPATH' ) || exit;

/**
 * Render a pending-copy marker.
 *
 * @param string $note Optional note replacing the default wording.
 */
function verquin_block_pending( string $note = '' ): void {
	$note = '' !== trim( $note ) ? $note : __( 'Copy to be supplied.', 'verquin' );
	?>
	<p class="b-pending"><?php echo esc_html( $note ); ?></p>
	<?php
}
