<?php
/**
 * Block: spec-table — a two-column label | value specification table.
 *
 * Framework source: table.spec > caption + tr > td, td
 * CSS owner: .b-spec-table
 *
 * Improvements over the framework: labels are row headers (th scope="row"),
 * the caption is a real <caption>, and rows stack label-over-value below --bp-sm.
 * Rows are CSS grid, so explicit ARIA table roles keep the semantics in every browser.
 *
 * @package VerQuin
 */

defined( 'ABSPATH' ) || exit;

/**
 * Render a specification table.
 *
 * @param array<int, array{label: string, value: string}> $rows    Label | value pairs; value may carry inline HTML.
 * @param string                                          $caption Optional caption shown above the rows.
 * @param array<string>                                   $modifiers Optional: 'compact' (FAQ answers).
 */
function verquin_block_spec_table( array $rows, string $caption = '', array $modifiers = array() ): void {
	$rows = array_filter( $rows, static fn( $r ) => isset( $r['label'], $r['value'] ) && '' !== trim( $r['label'] ) );
	if ( ! $rows ) {
		return;
	}
	?>
	<table class="<?php echo verquin_classes( 'b-spec-table', $modifiers ); ?>" role="table">
		<?php if ( '' !== $caption ) : ?>
			<caption class="b-spec-table__caption"><?php echo esc_html( $caption ); ?></caption>
		<?php endif; ?>
		<tbody>
			<?php foreach ( $rows as $row ) : ?>
				<tr class="b-spec-table__row" role="row">
					<th class="b-spec-table__label" scope="row" role="rowheader"><?php echo esc_html( $row['label'] ); ?></th>
					<td class="b-spec-table__value" role="cell"><?php echo verquin_rich_text( $row['value'] ); // phpcs:ignore WordPress.Security.EscapeOutput ?></td>
				</tr>
			<?php endforeach; ?>
		</tbody>
	</table>
	<?php
}
