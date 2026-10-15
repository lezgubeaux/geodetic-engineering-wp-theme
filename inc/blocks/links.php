<?php
/**
 * Block: links — a row of small mono links with a hairline underline.
 *
 * Framework source: ul.links > li > a
 * CSS owner: .b-links
 *
 * @package VerQuin
 */

defined( 'ABSPATH' ) || exit;

/**
 * Render a links list.
 *
 * @param array<int, array{label: string, url: string, external?: bool}> $links
 *        external: opens in a new tab with a screen-reader hint.
 */
function verquin_block_links( array $links ): void {
	$links = array_filter( $links, static fn( $l ) => ! empty( $l['label'] ) && ! empty( $l['url'] ) );
	if ( ! $links ) {
		return;
	}
	?>
	<ul class="b-links">
		<?php foreach ( $links as $link ) : ?>
			<li class="b-links__item">
				<a class="b-links__link" href="<?php echo esc_url( $link['url'] ); ?>"<?php echo empty( $link['external'] ) ? '' : ' target="_blank" rel="noopener"'; ?>>
					<?php echo esc_html( $link['label'] ); ?>
					<?php if ( ! empty( $link['external'] ) ) : ?>
						<span class="screen-reader-text"><?php esc_html_e( '(opens in a new tab)', 'verquin' ); ?></span>
					<?php endif; ?>
				</a>
			</li>
		<?php endforeach; ?>
	</ul>
	<?php
}
