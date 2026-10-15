<?php
/**
 * Block: diagram — a theme-owned inline SVG in a scrollable, captioned figure.
 *
 * Framework source: figure.diagram > svg, figure.quincunx > svg
 * CSS owner: .b-diagram
 *
 * SVG markup is never supplied by an editor. Each diagram is registered below
 * against a file under assets/svg/, so a layout can only name a diagram the theme
 * already ships — never an arbitrary path.
 *
 * In this published copy the registered SVG files are placeholders: the client's
 * technical drawings are not included. The registry, the render path and the
 * scroll/accessibility behaviour are unchanged.
 *
 * @package VerQuin
 */

defined( 'ABSPATH' ) || exit;

/**
 * The diagrams this theme ships.
 *
 * min_width is the diagram's own viewBox width: below it the labels stop being
 * readable, so the viewport scrolls rather than shrinking further. One shared
 * constant would be wrong for diagrams this different in size.
 *
 * compact drops the panel and the scroll viewport, for a diagram small enough to
 * need neither (framework .quincunx).
 *
 * label names the diagram for the editor's select and for the scroll region's
 * accessible name, so a keyboard user hears which diagram they have reached.
 *
 * @return array<string, array{file: string, label: string, min_width: int, compact: bool}>
 */
function verquin_diagrams(): array {
	return array(
		// Used on both monuments and stability: one diagram, one id.
		'monument-comparison'   => array(
			'file'      => 'monument-comparison.svg',
			'label'     => __( 'Monument depth comparison', 'verquin' ),
			'min_width' => 940,
			'compact'   => false,
		),
		'monument-ddbm'         => array(
			'file'      => 'monument-ddbm.svg',
			'label'     => __( 'Deep-drilled braced monument', 'verquin' ),
			'min_width' => 620,
			'compact'   => false,
		),
		'monument-sdbm'         => array(
			'file'      => 'monument-sdbm.svg',
			'label'     => __( 'Short-drilled braced monument', 'verquin' ),
			'min_width' => 620,
			'compact'   => false,
		),
		'monument-svbm'         => array(
			'file'      => 'monument-svbm.svg',
			'label'     => __( 'Short-driven braced monument', 'verquin' ),
			'min_width' => 620,
			'compact'   => false,
		),
		'quincunx'              => array(
			'file'      => 'quincunx.svg',
			'label'     => __( 'Quincunx geometry', 'verquin' ),
			'min_width' => 240,
			'compact'   => true,
		),
	);
}

/**
 * The widest diagram that still reads inside a text-media media column.
 *
 * That column is 1.1fr of a two-column grid inside .wrap: about 425px at --bp-md
 * and 600px at the 1300px maximum. A 620px diagram therefore renders at 69–97%,
 * which holds; the 940px one would fall to 45–64% and its 11px labels to 5–7px.
 */
const VERQUIN_DIAGRAM_COLUMN_MAX = 620;

/**
 * Whether a diagram still reads beside text, or needs the full row width.
 *
 * @param string $id Registered diagram id.
 */
function verquin_diagram_fits_column( string $id ): bool {
	$diagrams = verquin_diagrams();

	if ( ! isset( $diagrams[ $id ] ) ) {
		return true;
	}

	return $diagrams[ $id ]['min_width'] <= VERQUIN_DIAGRAM_COLUMN_MAX;
}

/**
 * Offer the registered diagrams as the layout select's choices.
 *
 * The registry is the single source: a diagram added to verquin_diagrams()
 * appears in the editor without touching the field JSON, and a choice can never
 * name a diagram the theme does not ship.
 *
 * @param array<string, mixed> $field Field settings.
 * @return array<string, mixed>
 */
function verquin_diagram_field_choices( array $field ): array {
	$field['choices'] = wp_list_pluck( verquin_diagrams(), 'label' );

	return $field;
}
add_filter( 'acf/load_field/key=field_verquin_diagram_diagram', 'verquin_diagram_field_choices' );
add_filter( 'acf/load_field/key=field_verquin_text_media_diagram', 'verquin_diagram_field_choices' );

/**
 * Render one registered diagram.
 *
 * The SVG carries its own accessible name; the scroll region gets a separate one
 * naming the behaviour, so a keyboard user hears that the area scrolls.
 *
 * @param string                                     $id   Registered diagram id.
 * @param array{caption?: string, credit?: string}   $args Caption and credit.
 */
function verquin_block_diagram( string $id, array $args = array() ): void {
	$diagrams = verquin_diagrams();

	if ( ! isset( $diagrams[ $id ] ) ) {
		return;
	}

	$diagram = $diagrams[ $id ];
	$path    = get_theme_file_path( '/assets/svg/' . $diagram['file'] );

	if ( ! is_readable( $path ) ) {
		return;
	}

	$svg = file_get_contents( $path ); // phpcs:ignore WordPress.WP.AlternativeFunctions.file_get_contents_file_get_contents — theme-owned file, not a remote request.

	if ( false === $svg || '' === trim( $svg ) ) {
		return;
	}

	$caption = (string) ( $args['caption'] ?? '' );
	$credit  = (string) ( $args['credit'] ?? '' );

	/* translators: %s: diagram name. */
	$region_label = sprintf( __( '%s, scrollable sideways', 'verquin' ), $diagram['label'] );
	?>
	<figure class="<?php echo verquin_classes( 'b-diagram', array( $diagram['compact'] ? 'compact' : '' ) ); // phpcs:ignore WordPress.Security.EscapeOutput ?>">
		<?php if ( $diagram['compact'] ) : ?>
			<?php echo $svg; // phpcs:ignore WordPress.Security.EscapeOutput — theme-owned SVG file. ?>
		<?php else : ?>
			<div
				class="b-diagram__viewport"
				style="--diagram-min: <?php echo (int) $diagram['min_width']; ?>px"
				role="region"
				aria-label="<?php echo esc_attr( $region_label ); ?>"
				tabindex="0"
			>
				<?php echo $svg; // phpcs:ignore WordPress.Security.EscapeOutput — theme-owned SVG file. ?>
			</div>
		<?php endif; ?>

		<?php if ( $caption || $credit ) : ?>
			<figcaption class="b-diagram__caption">
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
