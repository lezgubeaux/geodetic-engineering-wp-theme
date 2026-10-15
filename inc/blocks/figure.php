<?php
/**
 * Block: figure — one image slot with caption and credit.
 *
 * Renders the attachment when one is assigned; otherwise a labelled
 * placeholder carrying the handoff slot code and asset kind, so pages
 * can be assembled before media arrives.
 *
 * Framework source: figure > .ph + figcaption
 * CSS owner: .b-figure
 *
 * @package VerQuin
 */

defined( 'ABSPATH' ) || exit;

/**
 * Render a figure.
 *
 * @param array{
 *   image?: int|null,
 *   slot?: string,
 *   kind?: string,
 *   ratio?: string,
 *   fit?: string,
 *   placeholder?: string,
 *   caption?: string,
 *   credit?: string,
 *   size?: string
 * } $args
 *   image       Attachment ID, or null for placeholder.
 *   slot        Handoff slot code, e.g. 'HOM-01'.
 *   kind        Asset type badge: Photo (default) | Plot | Drawing | Map | Video | Graphic + PDF | Logo.
 *               Logo also switches the figure to the framework's white `.logocard` treatment.
 *   ratio       '' (image's own proportions, default) | 'standard' (16/10) | 'tall' (4/5) | 'wide' (21/9).
 *   fit         How the image meets a framed slot: '' (cover, default) | 'contain'. Cover crops to
 *               fill, which cuts titles off thumbnails of documents; contain fits the whole image
 *               inside the frame and letterboxes the remainder.
 *   placeholder What the image shows. Used as alt text when the attachment has none
 *               of its own, and shown inside the placeholder until the image arrives.
 *   caption     Public caption.
 *   credit      Source credit, e.g. 'VerQuin'.
 *   size        WordPress image size (default 'large').
 */
function verquin_block_figure( array $args ): void {
	$image   = (int) ( $args['image'] ?? 0 );
	$slot    = (string) ( $args['slot'] ?? '' );
	$kind    = (string) ( $args['kind'] ?? 'Photo' );
	$ratio   = (string) ( $args['ratio'] ?? '' );
	$fit     = (string) ( $args['fit'] ?? '' );
	$caption = (string) ( $args['caption'] ?? '' );
	$credit  = (string) ( $args['credit'] ?? '' );

	// The framework's .logocard is a figure with a white card instead of a media frame.
	$is_logo = 'Logo' === $kind;
	?>
	<figure class="<?php echo verquin_classes( 'b-figure', array( $ratio, 'contain' === $fit ? 'contain' : '', $is_logo ? 'logo' : '' ) ); // phpcs:ignore WordPress.Security.EscapeOutput ?>">
		<?php if ( $image && wp_attachment_is_image( $image ) ) : ?>
			<div class="b-figure__media">
				<?php
				// Alt comes from the Media Library first, then the figure's description.
				// Never the caption: it sits right here in the figcaption, and repeating it
				// makes a screen reader read the same sentence twice.
				$alt = trim( (string) get_post_meta( $image, '_wp_attachment_image_alt', true ) );

				if ( '' === $alt ) {
					$alt = trim( (string) ( $args['placeholder'] ?? '' ) );
				}

				echo wp_get_attachment_image( $image, $args['size'] ?? 'large', false, array( 'alt' => $alt ) );
				?>
			</div>
		<?php else : ?>
			<div class="b-figure__media b-figure__media--placeholder" data-kind="<?php echo esc_attr( $kind ); ?>">
				<span>
					<?php if ( $slot ) : ?>
						<?php echo esc_html( $slot ); ?><br>
					<?php endif; ?>
					<?php echo esc_html( $args['placeholder'] ?? '' ); ?>
				</span>
			</div>
		<?php endif; ?>

		<?php if ( $caption || $credit ) : ?>
			<figcaption class="b-figure__caption">
				<?php if ( $caption ) : ?>
					<em><?php echo verquin_rich_text( $caption ); // phpcs:ignore WordPress.Security.EscapeOutput ?></em>
				<?php endif; ?>
				<?php if ( $caption && $credit ) : ?>
					<br>
				<?php endif; ?>
				<?php echo esc_html( $credit ); ?>
			</figcaption>
		<?php endif; ?>
	</figure>
	<?php
}
