<?php
/**
 * Block: references — grouped numbered reference lists.
 *
 * Framework source: h3 + ol.refs, repeated
 * CSS owner: .b-references
 *
 * The heading belongs to the block rather than the row: the framework's six lists
 * on one page are only distinguishable by their headings.
 *
 * @package VerQuin
 */

defined( 'ABSPATH' ) || exit;

/**
 * Render grouped reference lists.
 *
 * @param array<int, array{heading?: string, items: array<int, string>}> $groups
 *   heading  Subject heading above the list.
 *   items    Citation strings; inline entities are rendered, markup is not expected.
 */
function verquin_block_references( array $groups ): void {
	$groups = array_filter( $groups, static fn( $g ) => ! empty( $g['items'] ) );
	if ( ! $groups ) {
		return;
	}
	?>
	<div class="b-references">
		<?php foreach ( $groups as $group ) : ?>
			<div class="b-references__group">
				<?php if ( ! empty( $group['heading'] ) ) : ?>
					<h3 class="b-references__heading"><?php echo esc_html( $group['heading'] ); ?></h3>
				<?php endif; ?>
				<ol class="b-references__list">
					<?php foreach ( $group['items'] as $item ) : ?>
						<li class="b-references__item"><?php echo verquin_rich_text( $item ); // phpcs:ignore WordPress.Security.EscapeOutput ?></li>
					<?php endforeach; ?>
				</ol>
			</div>
		<?php endforeach; ?>
	</div>
	<?php
}
