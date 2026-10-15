<?php
/**
 * Theme setup: supports, menus, text domain.
 *
 * @package VerQuin
 */

defined( 'ABSPATH' ) || exit;

/**
 * Register theme supports and navigation menus.
 */
function verquin_setup(): void {
	load_theme_textdomain( 'verquin', get_template_directory() . '/languages' );

	add_theme_support( 'title-tag' );
	add_theme_support( 'post-thumbnails' );
	add_theme_support( 'responsive-embeds' );
	// Site logo from Customizer → Site Identity; rendered by verquin_brandmark().
	add_theme_support(
		'custom-logo',
		array(
			'flex-width'      => true,
			'flex-height'     => true,
			'unlink-homepage' => false,
		)
	);
	add_theme_support(
		'html5',
		array( 'search-form', 'gallery', 'caption', 'style', 'script', 'navigation-widgets' )
	);

	register_nav_menus(
		array(
			'primary'           => __( 'Primary', 'verquin' ),
			'footer-services'   => __( 'Footer: Services', 'verquin' ),
			'footer-monuments'  => __( 'Footer: Monuments', 'verquin' ),
			'footer-networks'   => __( 'Footer: Monitoring Networks', 'verquin' ),
			'footer-reflectors' => __( 'Footer: Corner Reflectors', 'verquin' ),
			'footer-about'      => __( 'Footer: About', 'verquin' ),
			'footer-contact'    => __( 'Footer: Contact', 'verquin' ),
		)
	);
}
add_action( 'after_setup_theme', 'verquin_setup' );

/**
 * Pages are composed with ACF Flexible Content; use the classic edit screen for them.
 * Other post types keep the default editor.
 */
function verquin_use_block_editor( bool $use_block_editor, string $post_type ): bool {
	return 'page' === $post_type ? false : $use_block_editor;
}
add_filter( 'use_block_editor_for_post_type', 'verquin_use_block_editor', 10, 2 );

/**
 * Pages carry no post_content; everything is composed through ACF.
 * Dropping editor support removes the unused content area from the edit screen.
 */
function verquin_remove_page_editor(): void {
	remove_post_type_support( 'page', 'editor' );
}
add_action( 'init', 'verquin_remove_page_editor' );
