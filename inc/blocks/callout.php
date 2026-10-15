<?php
/**
 * Block: callout — an emphasised statement with a left rule.
 *
 * Framework source: .callout
 * CSS owner: .b-callout
 *
 * @package VerQuin
 */

defined( 'ABSPATH' ) || exit;

/**
 * Render a callout.
 *
 * @param string $html Body copy; plain text or <p> markup.
 */
function verquin_block_callout( string $html ): void {
	$html = trim( $html );
	if ( '' === $html ) {
		return;
	}
	if ( ! str_starts_with( $html, '<p' ) ) {
		$html = '<p>' . $html . '</p>';
	}
	?>
	<div class="b-callout"><?php echo verquin_rich_text( $html ); // phpcs:ignore WordPress.Security.EscapeOutput ?></div>
	<?php
}
