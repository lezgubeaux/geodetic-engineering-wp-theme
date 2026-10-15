<?php
/**
 * Row: data-chart — one interactive GNSS chart drawn from the station CSVs.
 *
 * Framework source: none — the legacy site's D3 visualisation, carried over
 * CSS owner: .r-data-chart
 * Blocks used: none (the vis library builds the chart inside the canvas)
 *
 * The legacy site wrapped each chart in an iframe. Here the canvas sits in the
 * page: the data is same-origin and the library's CSS is namespaced under .vis,
 * so the chart needs no document of its own. Dense charts keep their readable
 * floor and scroll sideways inside __viewport, as a wide diagram does, instead
 * of shrinking below legibility.
 *
 * The chart draws on a light surface of its own: its axes, labels and the text
 * halo in vis.css were built for a pale background and cannot read on the
 * site's dark bands.
 *
 * @var array{
 *   station?: string,
 *   kind?: string,
 *   title?: string,
 *   footer?: string,
 *   error_bars?: bool
 * } $args
 *   station     Registered station id, e.g. XXXX8.
 *   kind        'positions' or 'qc'; must exist for that station.
 *   title       Chart heading; the station's registered label when empty.
 *   footer      Explanatory copy under the chart, as the legacy pages carried.
 *   error_bars  Positions charts only: draw the daily 1-sigma bars.
 *
 * @package VerQuin
 */

defined( 'ABSPATH' ) || exit;

$station = (string) ( $args['station'] ?? '' );
$kind    = (string) ( $args['kind'] ?? 'positions' );
$urls    = verquin_chart_data_urls( $station, $kind );

if ( ! $urls ) {
	return;
}

$charts = verquin_charts();
$title  = (string) ( $args['title'] ?? '' );
$title  = '' !== $title ? $title : $charts[ $station ]['label'];
$footer = (string) ( $args['footer'] ?? '' );

$config = array(
	'kind'      => $kind,
	'title'     => $title,
	'urls'      => $urls,
	'errorBars' => ! empty( $args['error_bars'] ),
);

if ( '' !== $footer ) {
	$config['footer'] = verquin_rich_text( $footer );
}

/* translators: %s: chart title. */
$region_label = sprintf( __( '%s, scrollable sideways', 'verquin' ), $title );

verquin_chart_enqueue_assets();
?>
<div class="r-data-chart">
	<div
		class="r-data-chart__viewport"
		style="--chart-min: <?php echo (int) VERQUIN_CHART_MIN_WIDTH; ?>px"
		role="region"
		aria-label="<?php echo esc_attr( $region_label ); ?>"
		tabindex="0"
	>
		<div
			class="r-data-chart__canvas"
			data-verquin-chart="<?php echo esc_attr( (string) wp_json_encode( $config ) ); ?>"
		>
			<noscript><?php esc_html_e( 'This chart needs JavaScript. The underlying data is available on request.', 'verquin' ); ?></noscript>
		</div>
	</div>
</div>
