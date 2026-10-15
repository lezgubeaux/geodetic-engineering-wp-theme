<?php
/**
 * Row: video — a YouTube embed as its own band row.
 *
 * Framework source: .ph[data-kind="Video"] (DDBM-03, SVC-07 — both placeholders)
 * CSS owner: .r-video
 * Blocks used: video; figure for the not-yet-supplied state
 *
 * With no URL the row falls back to the figure block's placeholder, so a page can
 * be composed before the video exists and swaps over without being rebuilt.
 *
 * @var array{
 *   url?: string,
 *   ratio?: string,
 *   slot?: string,
 *   placeholder?: string,
 *   caption?: string,
 *   credit?: string
 * } $args
 *
 * @package VerQuin
 */

defined( 'ABSPATH' ) || exit;

$has_video = '' !== verquin_youtube_id( (string) ( $args['url'] ?? '' ) );

if ( ! $has_video && empty( $args['slot'] ) && empty( $args['placeholder'] ) ) {
	return;
}
?>
<div class="r-video">
	<?php if ( $has_video ) : ?>
		<?php
		verquin_block_video(
			array(
				'url'     => $args['url'] ?? '',
				'ratio'   => $args['ratio'] ?? '',
				'caption' => $args['caption'] ?? '',
				'credit'  => $args['credit'] ?? '',
			)
		);
		?>
	<?php else : ?>
		<?php
		verquin_block_figure(
			array(
				'kind'        => 'Video',
				'ratio'       => $args['ratio'] ?? '',
				'slot'        => $args['slot'] ?? '',
				'placeholder' => $args['placeholder'] ?? '',
				'caption'     => $args['caption'] ?? '',
				'credit'      => $args['credit'] ?? '',
			)
		);
		?>
	<?php endif; ?>
</div>
