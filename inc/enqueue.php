<?php
/**
 * Front-end asset registration.
 *
 * framework-reference.css is the untouched supplied stylesheet, loaded
 * temporarily while components are migrated into theme.css. It is
 * dequeued at CSS close-out.
 *
 * @package VerQuin
 */

defined( 'ABSPATH' ) || exit;

/**
 * Return a cache-busting version for a theme asset.
 */
function verquin_asset_version( string $relative_path ): string {
	$file = get_theme_file_path( $relative_path );
	return file_exists( $file ) ? (string) filemtime( $file ) : VERQUIN_VERSION;
}

/**
 * Enqueue front-end styles and scripts.
 */
function verquin_enqueue_assets(): void {
	// Stand-in webfonts (Figtree for Proxima Nova, IBM Plex Mono); revisit once the licence is settled.
	wp_enqueue_style(
		'verquin-fonts',
		'https://fonts.googleapis.com/css2?family=Figtree:wght@300;400;500;600;800;900&family=IBM+Plex+Mono:wght@400;500;600&display=swap',
		array(),
		null // phpcs:ignore WordPress.WP.EnqueuedResourceParameters.MissingVersion -- external URL carries its own cache key.
	);

	wp_enqueue_style(
		'verquin-framework-reference',
		get_theme_file_uri( '/assets/css/framework-reference.css' ),
		array( 'verquin-fonts' ),
		verquin_asset_version( '/assets/css/framework-reference.css' )
	);

	wp_enqueue_style(
		'verquin-theme',
		get_theme_file_uri( '/assets/css/theme.css' ),
		array( 'verquin-framework-reference' ),
		verquin_asset_version( '/assets/css/theme.css' )
	);

	// Site-wide behaviour (header panel, submenu toggles, …), one file per build-conventions.
	wp_enqueue_script(
		'verquin-theme',
		get_theme_file_uri( '/assets/js/theme.js' ),
		array(),
		verquin_asset_version( '/assets/js/theme.js' ),
		array( 'strategy' => 'defer' )
	);
}
add_action( 'wp_enqueue_scripts', 'verquin_enqueue_assets' );
