<?php
/**
 * Page template: the page head, then the page's ACF Flexible Content sections in order.
 *
 * @package VerQuin
 */

defined( 'ABSPATH' ) || exit;

get_header();

while ( have_posts() ) {
	the_post();
	verquin_render_page_head();
	verquin_render_sections();
}

get_footer();
