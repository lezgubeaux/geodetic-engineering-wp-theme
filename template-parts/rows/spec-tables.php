<?php
/**
 * Row: spec-tables — one to four specification tables, stacked or paired.
 *
 * Framework source: table.spec standalone or grouped under .block
 * CSS owner: .r-spec-tables
 * Blocks used: spec-table
 *
 * @var array{
 *   tables: array<int, array{caption?: string, rows: array<int, array{label: string, value: string}>}>,
 *   layout?: 'single'|'stacked'|'paired',
 *   narrow?: bool
 * } $args
 *   layout  paired = two columns from --bp-md (stacked below); single/stacked = one column. Defaults by count.
 *   narrow  Caps the row at --measure (framework's 520/620px cases).
 *
 * @package VerQuin
 */

defined( 'ABSPATH' ) || exit;

$tables = array_filter( $args['tables'] ?? array(), static fn( $t ) => ! empty( $t['rows'] ) );
if ( ! $tables ) {
	return;
}

$layout = $args['layout'] ?? ( count( $tables ) > 1 ? 'stacked' : 'single' );
?>
<div class="<?php echo verquin_classes( 'r-spec-tables', array( 'paired' === $layout ? 'paired' : '', ! empty( $args['narrow'] ) ? 'narrow' : '' ) ); ?>">
	<?php foreach ( $tables as $table ) : ?>
		<?php verquin_block_spec_table( $table['rows'], $table['caption'] ?? '' ); ?>
	<?php endforeach; ?>
</div>
