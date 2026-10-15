<?php
/**
 * Block: buttons — a row of call-to-action links.
 *
 * Framework source: .acts > a.btn
 * CSS owner: .b-buttons
 *
 * @package VerQuin
 */

defined( 'ABSPATH' ) || exit;

/**
 * Render a button row.
 *
 * @param array<int, array{label: string, url: string, style?: string, target?: string}> $buttons
 *        style:  'solid' (default) | 'ghost'.
 *        target: '_blank' opens in a new tab and adds rel="noopener"; anything else is ignored.
 */
function verquin_block_buttons( array $buttons ): void {
	$buttons = array_filter( $buttons, static fn( $b ) => ! empty( $b['label'] ) && ! empty( $b['url'] ) );
	if ( ! $buttons ) {
		return;
	}
	?>
	<div class="b-buttons">
		<?php foreach ( $buttons as $button ) : ?>
			<?php
			$style    = ( $button['style'] ?? 'solid' ) === 'ghost' ? 'ghost' : 'solid';
			$new_tab  = '_blank' === ( $button['target'] ?? '' );
			?>
			<a class="btn btn--<?php echo esc_attr( $style ); ?>" href="<?php echo esc_url( $button['url'] ); ?>"<?php echo $new_tab ? ' target="_blank" rel="noopener"' : ''; ?>><?php echo esc_html( $button['label'] ); ?></a>
		<?php endforeach; ?>
	</div>
	<?php
}
