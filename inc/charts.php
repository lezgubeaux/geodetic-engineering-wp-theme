<?php
/**
 * GNSS data charts: station registry, data locations, and asset loading.
 *
 * The charts are the legacy site's D3 visualisations, carried over intact. The
 * library lives in assets/js/vis/ and draws from CSVs in the uploads folder, so
 * re-exported data never requires a theme deploy. Only the surrounding markup,
 * surface and scrolling belong to this theme.
 *
 * @package VerQuin
 */

defined( 'ABSPATH' ) || exit;

/**
 * Narrowest width at which a three-panel chart stays readable.
 *
 * Below it the row's viewport scrolls sideways rather than shrinking the chart,
 * as a wide diagram does. The legacy test pages drew at roughly 860px of inner
 * width; tune here, in one place, not per row.
 */
const VERQUIN_CHART_MIN_WIDTH = 860;

/** Path, relative to the uploads base, holding the station folders. */
const VERQUIN_CHART_DATA_DIR = 'data';

/**
 * Registered stations and the chart kinds each one can draw.
 *
 * `label` is the station's full name as the charts title it. `kinds` names the
 * chart types whose CSVs exist for that station, and `comments` records whether
 * an annotations file accompanies them.
 *
 * The real station identifiers are redacted in this published copy; the shape,
 * the mix of chart kinds and the annotation flags are unchanged.
 *
 * @return array<string, array{label: string, kinds: array<int, string>, comments: bool}>
 */
function verquin_charts(): array {
	return array(
		'XXXX1' => array(
			'label'    => 'Station XXXX1',
			'kinds'    => array( 'positions' ),
			'comments' => true,
		),
		'XXXX2' => array(
			'label'    => 'Station XXXX2',
			'kinds'    => array( 'positions' ),
			'comments' => false,
		),
		'XXXX3' => array(
			'label'    => 'Station XXXX3',
			'kinds'    => array( 'positions' ),
			'comments' => false,
		),
		'XXXX4' => array(
			'label'    => 'Station XXXX4',
			'kinds'    => array( 'positions' ),
			'comments' => false,
		),
		'XXXX5' => array(
			'label'    => 'Station XXXX5',
			'kinds'    => array( 'qc' ),
			'comments' => true,
		),
		'XXXX6' => array(
			'label'    => 'Station XXXX6',
			'kinds'    => array( 'positions' ),
			'comments' => true,
		),
		'XXXX7' => array(
			'label'    => 'Station XXXX7',
			'kinds'    => array( 'positions' ),
			'comments' => false,
		),
		'XXXX8' => array(
			'label'    => 'Station XXXX8',
			'kinds'    => array( 'positions', 'qc' ),
			'comments' => true,
		),
	);
}

/**
 * The chart kinds, as the editor names them.
 *
 * @return array<string, string>
 */
function verquin_chart_kinds(): array {
	return array(
		'positions' => __( 'Position time series', 'verquin' ),
		'qc'        => __( 'QC parameters', 'verquin' ),
	);
}

/**
 * Supply the station select's choices from the registry.
 *
 * @param array<string, mixed> $field Field settings.
 * @return array<string, mixed>
 */
function verquin_chart_field_choices( array $field ): array {
	$field['choices'] = wp_list_pluck( verquin_charts(), 'label' );

	return $field;
}
add_filter( 'acf/load_field/key=field_verquin_data_chart_station', 'verquin_chart_field_choices' );

/**
 * Whether a station can draw a chart of this kind.
 */
function verquin_chart_exists( string $station, string $kind ): bool {
	$charts = verquin_charts();

	return isset( $charts[ $station ] ) && in_array( $kind, $charts[ $station ]['kinds'], true );
}

/**
 * The CSV URLs for one chart, keyed as the vis library's constructors expect.
 *
 * Returns an empty array when the station or kind is not registered, so the row
 * renders nothing rather than a chart that fetches missing files.
 *
 * @return array<string, string>
 */
function verquin_chart_data_urls( string $station, string $kind ): array {
	if ( ! verquin_chart_exists( $station, $kind ) ) {
		return array();
	}

	$charts = verquin_charts();
	$base   = trailingslashit( wp_get_upload_dir()['baseurl'] ) . VERQUIN_CHART_DATA_DIR . '/' . $station . '/';
	$file   = static fn( string $series ): string => $base . $station . '_' . $series . '.csv';

	$urls = 'qc' === $kind
		? array(
			'slipsDataUrl'     => $file( 'Slips' ),
			'multipathDataUrl' => $file( 'Multipath' ),
			'snrDataUrl'       => $file( 'SNRs' ),
		)
		: array( 'positionsDataUrl' => $file( 'Positions' ) );

	if ( $charts[ $station ]['comments'] ) {
		$urls['commentsDataUrl'] = $file( 'Comments' );
	}

	return $urls;
}

/**
 * Enqueue D3, the vis library and the initialiser.
 *
 * Called by the row as it renders, so the 280KB of D3 loads only on pages that
 * actually carry a chart. Scripts enqueued during the template still print in
 * wp_footer. The library's five files define classes referenced at construction
 * time, not at load time, so the chain only has to guarantee all of them run
 * before the initialiser.
 */
function verquin_chart_enqueue_assets(): void {
	if ( wp_script_is( 'verquin-vis-init', 'enqueued' ) ) {
		return;
	}

	wp_enqueue_script(
		'verquin-d3',
		get_theme_file_uri( '/assets/js/vendor/d3.v7.min.js' ),
		array(),
		'7.9.0',
		array( 'strategy' => 'defer' )
	);

	$previous = 'verquin-d3';

	// vis-color is this theme's own addition and must precede the library files,
	// which call visColor() for their canvas-drawn colours.
	foreach ( array( 'color', 'base-chart', 'tooltip', 'annotations', 'positions-charts', 'qc-charts' ) as $part ) {
		$handle = 'verquin-vis-' . $part;
		$path   = '/assets/js/vis/vis-' . $part . '.js';

		wp_enqueue_script(
			$handle,
			get_theme_file_uri( $path ),
			array( $previous ),
			verquin_asset_version( $path ),
			array( 'strategy' => 'defer' )
		);

		$previous = $handle;
	}

	wp_enqueue_script(
		'verquin-vis-init',
		get_theme_file_uri( '/assets/js/vis-init.js' ),
		array( $previous ),
		verquin_asset_version( '/assets/js/vis-init.js' ),
		array( 'strategy' => 'defer' )
	);
}
