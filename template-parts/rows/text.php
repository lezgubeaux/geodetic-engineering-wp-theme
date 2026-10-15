<?php
/**
 * Row: text — a standalone paragraph that follows another row.
 *
 * Framework source: p.lede or p.small under .wrap after another row
 * CSS owner: .r-text
 * Blocks used: none
 *
 * @var array{html: string, style?: 'lede'|'body'|'small'} $args
 *   style  Type-scale global applied to the paragraph; defaults to 'body'.
 *
 * @package VerQuin
 */

defined( 'ABSPATH' ) || exit;

if ( empty( $args['html'] ) ) {
	return;
}

$style   = $args['style'] ?? 'body';
$type    = in_array( $style, array( 'lede', 'small' ), true ) ? $style : '';
$classes = verquin_classes( 'r-text', array(), array( $type ) );
?>
<div class="<?php echo $classes; ?>">
	<?php echo verquin_rich_text( $args['html'] ); // phpcs:ignore WordPress.Security.EscapeOutput ?>
</div>
