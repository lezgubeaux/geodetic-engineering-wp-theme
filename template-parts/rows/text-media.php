<?php
/**
 * Row: text-media — two-column text beside a figure, diagram, tables or video.
 *
 * Framework source: .block / .block.flip > .txt + figure | figure.diagram | .txt
 * CSS owner: .r-text-media
 * Blocks used: labelled-text, spec-table, callout, links, buttons, figure, diagram, video
 *
 * The text column renders in a fixed order. Where the framework puts both a
 * callout and a table in one column it always orders them callout first, so
 * that is the order here. Stability opens a column with a callout before its
 * body, which is normalised; see the registry entry.
 *
 * @var array{
 *   eyebrow?: string,
 *   heading?: string,
 *   body?: string,
 *   groups?: array<int, array{label?: string, text?: string}>,
 *   tables?: array<int, array{caption?: string, rows?: array<int, array{label: string, value: string}>}>,
 *   callout?: string,
 *   closing?: string,
 *   links?: array<int, array{label: string, url: string, external?: bool}>,
 *   buttons?: array<int, array{label: string, url: string, style?: string}>,
 *   figure?: array<string, mixed>,
 *   diagram?: string,
 *   tables_media?: bool,
 *   video?: array<string, string>,
 *   flip?: bool
 * } $args
 *   flip          true puts the media on the left.
 *   figure        Used when the media column is a figure; empty means none.
 *   diagram       Registered diagram id; used when the media column is a diagram.
 *   video         YouTube embed in the media column, which is the framework's
 *                 video figure beside text (service-design-modeling.html:469).
 *                 With no usable URL it falls back to the figure placeholder with
 *                 kind Video, as the standalone video row does, so SVC-07 composes
 *                 before the file exists and swaps over untouched.
 *   tables_media  true moves `tables` out of the text column and into the media
 *                 column, which is the framework's prose-cell-beside-table-cell
 *                 block (service-data-quality.html:453). The same repeater feeds
 *                 both placements, so a row has tables in one column or the other.
 *
 * @package VerQuin
 */

defined( 'ABSPATH' ) || exit;

$flip   = ! empty( $args['flip'] );
$tables = array_filter(
	$args['tables'] ?? array(),
	static fn( $table ) => ! empty( $table['rows'] )
);
$media  = '';

if ( ! empty( $args['tables_media'] ) && $tables ) {
	$media = 'tables';
} elseif ( ! empty( $args['video'] ) ) {
	$media = 'video';
} elseif ( ! empty( $args['diagram'] ) ) {
	$media = 'diagram';
} elseif ( ! empty( $args['figure'] ) ) {
	$media = 'figure';
}

// The repeater feeds one column or the other, never both.
$text_tables = 'tables' === $media ? array() : $tables;

$text_only = '' === $media;

// A diagram too wide to read in the media column takes the full row instead,
// stacking below the text. Decided from the diagram registry, not per page.
$diagram_wide = 'diagram' === $media && ! verquin_diagram_fits_column( $args['diagram'] );
?>
<div class="<?php echo verquin_classes( 'r-text-media', array( $flip ? 'flip' : '', $text_only ? 'text-only' : '', $diagram_wide ? 'diagram-wide' : '' ) ); // phpcs:ignore WordPress.Security.EscapeOutput ?>">
	<div class="r-text-media__text">
		<?php if ( ! empty( $args['eyebrow'] ) ) : ?>
			<span class="eyebrow"><?php echo esc_html( $args['eyebrow'] ); ?></span>
		<?php endif; ?>

		<?php if ( ! empty( $args['heading'] ) ) : ?>
			<h2><?php echo verquin_heading( $args['heading'] ); // phpcs:ignore WordPress.Security.EscapeOutput ?></h2>
		<?php endif; ?>

		<?php if ( ! empty( $args['body'] ) ) : ?>
			<div class="small"><?php echo verquin_rich_text( $args['body'] ); // phpcs:ignore WordPress.Security.EscapeOutput ?></div>
		<?php endif; ?>

		<?php verquin_block_labelled_text( $args['groups'] ?? array() ); ?>

		<?php verquin_block_callout( $args['callout'] ?? '' ); ?>

		<?php foreach ( $text_tables as $table ) : ?>
			<?php verquin_block_spec_table( $table['rows'], $table['caption'] ?? '' ); ?>
		<?php endforeach; ?>

		<?php if ( ! empty( $args['closing'] ) ) : ?>
			<div class="small"><?php echo verquin_rich_text( $args['closing'] ); // phpcs:ignore WordPress.Security.EscapeOutput ?></div>
		<?php endif; ?>

		<?php verquin_block_links( $args['links'] ?? array() ); ?>
		<?php verquin_block_buttons( $args['buttons'] ?? array() ); ?>
	</div>

	<?php if ( ! $text_only ) : ?>
		<div class="r-text-media__media">
			<?php if ( 'tables' === $media ) : ?>
				<?php foreach ( $tables as $table ) : ?>
					<?php verquin_block_spec_table( $table['rows'], $table['caption'] ?? '' ); ?>
				<?php endforeach; ?>
			<?php elseif ( 'video' === $media ) : ?>
				<?php if ( '' !== verquin_youtube_id( $args['video']['url'] ?? '' ) ) : ?>
					<?php verquin_block_video( $args['video'] ); ?>
				<?php else : ?>
					<?php verquin_block_figure( array( 'kind' => 'Video' ) + $args['video'] ); ?>
				<?php endif; ?>
			<?php elseif ( 'diagram' === $media ) : ?>
				<?php verquin_block_diagram( $args['diagram'] ); ?>
			<?php else : ?>
				<?php verquin_block_figure( $args['figure'] ); ?>
			<?php endif; ?>
		</div>
	<?php endif; ?>
</div>
