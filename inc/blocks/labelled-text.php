<?php
/**
 * Block: labelled-text — short labelled passages stacked in a text column.
 *
 * Framework source: .sublabel + p.small groups inside .txt
 * CSS owner: .b-labelled-text
 *
 * The label reuses the global .sublabel type; the block owns only the rhythm
 * between groups, which the framework repeated as inline margin-top:34px.
 *
 * The label is an h3, not the framework's span: these are real subheadings under
 * the row's or band's h2, and a screen reader should find them in the outline.
 * .sublabel outranks the h3 element rule, so the type is unchanged.
 *
 * @package VerQuin
 */

defined( 'ABSPATH' ) || exit;

/**
 * Render labelled text groups.
 *
 * @param array<int, array{label?: string, text?: string}> $groups
 *   label  Short mono label above the passage.
 *   text   Paragraphs, bold, italic and links.
 */
function verquin_block_labelled_text( array $groups ): void {
	$groups = array_filter(
		$groups,
		static fn( $g ) => ! empty( $g['label'] ) || ! empty( $g['text'] )
	);

	if ( ! $groups ) {
		return;
	}
	?>
	<div class="b-labelled-text">
		<?php foreach ( $groups as $group ) : ?>
			<div class="b-labelled-text__group">
				<?php if ( ! empty( $group['label'] ) ) : ?>
					<h3 class="sublabel"><?php echo esc_html( $group['label'] ); ?></h3>
				<?php endif; ?>
				<?php if ( ! empty( $group['text'] ) ) : ?>
					<div class="small"><?php echo verquin_rich_text( $group['text'] ); // phpcs:ignore WordPress.Security.EscapeOutput ?></div>
				<?php endif; ?>
			</div>
		<?php endforeach; ?>
	</div>
	<?php
}
