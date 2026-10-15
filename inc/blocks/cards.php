<?php
/**
 * Block: cards — a hairline grid of short labelled cells.
 *
 * Framework source: .cards > .card (index.html's NEMA tiles add figure + ul.links)
 * CSS owner: .b-cards
 * Blocks used: figure, links
 *
 * @package VerQuin
 */

defined( 'ABSPATH' ) || exit;

/**
 * Render a cards grid.
 *
 * @param array<int, array{title: string, index?: string, text?: string, note?: string, url?: string, figure?: array<string, mixed>, links?: array<int, array<string, mixed>>}> $cards
 *   title  Card heading (required).
 *   index  Small mono label above the title, e.g. '01'.
 *   text   Supporting sentence.
 *   note   Mono footnote, e.g. 'Patent protected'.
 *   url    Makes the whole card a link. Ignored when the card has its own links.
 *   figure Tile image above the title; figure-block args.
 *   links  Links listed inside the card, e.g. a PDF download.
 */
function verquin_block_cards( array $cards ): void {
	$cards = array_filter( $cards, static fn( $c ) => ! empty( $c['title'] ) );
	if ( ! $cards ) {
		return;
	}
	?>
	<div class="b-cards">
		<?php foreach ( $cards as $card ) : ?>
			<?php
			$links = $card['links'] ?? array();

			// A card holding its own links cannot also be an anchor: the browser would
			// have to nest one inside the other. The inner links win, being the specific
			// destinations; the whole-card url is dropped.
			$tag = ( empty( $card['url'] ) || $links ) ? 'div' : 'a';
			?>
			<<?php echo $tag; // phpcs:ignore WordPress.Security.EscapeOutput ?> class="b-cards__card"<?php echo $tag === 'a' ? ' href="' . esc_url( $card['url'] ) . '"' : ''; ?>>
				<?php if ( ! empty( $card['figure'] ) ) : ?>
					<div class="b-cards__figure">
						<?php verquin_block_figure( $card['figure'] ); ?>
					</div>
				<?php endif; ?>
				<?php if ( ! empty( $card['index'] ) ) : ?>
					<span class="b-cards__index"><?php echo esc_html( $card['index'] ); ?></span>
				<?php endif; ?>
				<h3 class="b-cards__title"><?php echo esc_html( $card['title'] ); ?></h3>
				<?php if ( ! empty( $card['text'] ) ) : ?>
					<p class="b-cards__text"><?php echo esc_html( $card['text'] ); ?></p>
				<?php endif; ?>
				<?php if ( ! empty( $card['note'] ) ) : ?>
					<p class="b-cards__note"><?php echo esc_html( $card['note'] ); ?></p>
				<?php endif; ?>
				<?php if ( $links ) : ?>
					<div class="b-cards__links">
						<?php verquin_block_links( $links ); ?>
					</div>
				<?php endif; ?>
			</<?php echo $tag; // phpcs:ignore WordPress.Security.EscapeOutput ?>>
		<?php endforeach; ?>
	</div>
	<?php
}
