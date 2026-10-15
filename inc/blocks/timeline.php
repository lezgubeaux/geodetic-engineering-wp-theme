<?php
/**
 * Block: timeline — dated events down a ruled vertical line.
 *
 * Framework source: .tl > .ev (span.yr + h4 + p…)
 * CSS owner: .b-timeline
 *
 * @package VerQuin
 */

defined( 'ABSPATH' ) || exit;

/**
 * Render a timeline.
 *
 * @param array<int, array{year: string, heading?: string, body?: string}> $events
 *   year     Short label on the marker, e.g. '1988'. Required.
 *   heading  Event title.
 *   body     Paragraphs, bold, italic and links.
 */
function verquin_block_timeline( array $events ): void {
	$events = array_filter( $events, static fn( $e ) => ! empty( $e['year'] ) );
	if ( ! $events ) {
		return;
	}
	?>
	<div class="b-timeline">
		<?php foreach ( $events as $event ) : ?>
			<div class="b-timeline__event">
				<span class="b-timeline__year"><?php echo esc_html( $event['year'] ); ?></span>
				<?php if ( ! empty( $event['heading'] ) ) : ?>
					<h4 class="b-timeline__heading"><?php echo esc_html( $event['heading'] ); ?></h4>
				<?php endif; ?>
				<?php if ( ! empty( $event['body'] ) ) : ?>
					<?php echo verquin_rich_text( $event['body'] ); // phpcs:ignore WordPress.Security.EscapeOutput ?>
				<?php endif; ?>
			</div>
		<?php endforeach; ?>
	</div>
	<?php
}
