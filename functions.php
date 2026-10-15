<?php
/**
 * Theme bootstrap.
 *
 * Thin loader only; all behaviour lives in inc/.
 *
 * @package VerQuin
 */

defined( 'ABSPATH' ) || exit;

define( 'VERQUIN_VERSION', wp_get_theme()->get( 'Version' ) );

require_once get_theme_file_path( '/inc/setup.php' );
require_once get_theme_file_path( '/inc/enqueue.php' );
require_once get_theme_file_path( '/inc/helpers.php' );
require_once get_theme_file_path( '/inc/admin.php' );
require_once get_theme_file_path( '/inc/acf.php' );
require_once get_theme_file_path( '/inc/forms.php' );
require_once get_theme_file_path( '/inc/charts.php' );

foreach ( glob( get_theme_file_path( '/inc/blocks/*.php' ) ) as $verquin_block_file ) {
	require_once $verquin_block_file;
}
unset( $verquin_block_file );
