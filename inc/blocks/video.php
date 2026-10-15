<?php
/**
 * Block: video — a privacy-enhanced YouTube embed in a captioned figure.
 *
 * Framework source: .ph[data-kind="Video"] — both framework slots are placeholders,
 * so the embed itself follows the implementation guide rather than framework markup.
 * CSS owner: .b-video
 *
 * YouTube only: the project hosts no video itself. The frame carries no width or
 * height attributes; the ratio is CSS, per the guide's rule on deprecated iframe
 * presentation attributes.
 *
 * @package VerQuin
 */

defined( 'ABSPATH' ) || exit;

/**
 * The YouTube video id in a watch, short, embed or shorts URL.
 *
 * Returns an empty string for anything else, so a mistyped or non-YouTube URL
 * renders nothing instead of a broken frame.
 *
 * @param string $url Pasted URL.
 */
function verquin_youtube_id( string $url ): string {
	$url = trim( $url );

	if ( '' === $url ) {
		return '';
	}

	$patterns = array(
		'~[?&]v=([A-Za-z0-9_-]{11})~',       // watch?v=ID
		'~youtu\.be/([A-Za-z0-9_-]{11})~',   // youtu.be/ID
		'~/embed/([A-Za-z0-9_-]{11})~',      // /embed/ID
		'~/shorts/([A-Za-z0-9_-]{11})~',     // /shorts/ID
	);

	foreach ( $patterns as $pattern ) {
		if ( preg_match( $pattern, $url, $match ) ) {
			return $match[1];
		}
	}

	return '';
}

/**
 * Render a video figure.
 *
 * @param array{
 *   url?: string,
 *   ratio?: string,
 *   caption?: string,
 *   credit?: string
 * } $args
 *   url      YouTube URL in any common form; anything unrecognised renders nothing.
 *   ratio    '' (16/9, default) | 'tall' (4/5) | 'wide' (21/9).
 *   caption  Public caption; also supplies the frame's accessible name.
 *   credit   Source credit.
 */
function verquin_block_video( array $args ): void {
	$id = verquin_youtube_id( (string) ( $args['url'] ?? '' ) );

	if ( '' === $id ) {
		return;
	}

	$ratio   = (string) ( $args['ratio'] ?? '' );
	$caption = (string) ( $args['caption'] ?? '' );
	$credit  = (string) ( $args['credit'] ?? '' );

	// An iframe needs its own accessible name; the caption is the one already written.
	$title = trim( wp_strip_all_tags( $caption ) );
	$title = '' !== $title ? $title : __( 'Video', 'verquin' );
	?>
	<figure class="<?php echo verquin_classes( 'b-video', array( $ratio ) ); // phpcs:ignore WordPress.Security.EscapeOutput ?>">
		<div class="b-video__frame">
			<iframe
				src="<?php echo esc_url( 'https://www.youtube-nocookie.com/embed/' . $id . '?rel=0' ); ?>"
				title="<?php echo esc_attr( $title ); ?>"
				loading="lazy"
				referrerpolicy="strict-origin-when-cross-origin"
				allow="accelerometer; clipboard-write; encrypted-media; gyroscope; picture-in-picture"
				allowfullscreen
			></iframe>
		</div>

		<?php if ( $caption || $credit ) : ?>
			<figcaption class="b-video__caption">
				<?php if ( $caption ) : ?>
					<em><?php echo verquin_rich_text( $caption ); // phpcs:ignore WordPress.Security.EscapeOutput ?></em>
				<?php endif; ?>
				<?php if ( $caption && $credit ) : ?>
					<br>
				<?php endif; ?>
				<?php echo esc_html( $credit ); ?>
			</figcaption>
		<?php endif; ?>
	</figure>
	<?php
}
