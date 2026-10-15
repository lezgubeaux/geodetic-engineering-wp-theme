<?php
/**
 * Not-found template.
 *
 * @package VerQuin
 */

defined( 'ABSPATH' ) || exit;

get_header();
?>
<h1><?php esc_html_e( 'Page not found', 'verquin' ); ?></h1>
<p><a href="<?php echo esc_url( home_url( '/' ) ); ?>"><?php esc_html_e( 'Return to the home page', 'verquin' ); ?></a></p>
<?php
get_footer();
