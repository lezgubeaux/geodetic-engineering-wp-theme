<?php
/**
 * Document head and site header.
 *
 * Framework source: nav.site
 * CSS owner: .site-header (global shell), .brandmark
 * Blocks used: menu
 * Behaviour: assets/js/theme.js (mobile panel, submenu toggles)
 *
 * @package VerQuin
 */

defined( 'ABSPATH' ) || exit;
?>
<!DOCTYPE html>
<html <?php language_attributes(); ?>>
<head>
<meta charset="<?php bloginfo( 'charset' ); ?>">
<meta name="viewport" content="width=device-width, initial-scale=1">
<?php wp_head(); ?>
</head>
<body <?php body_class(); ?>>
<?php wp_body_open(); ?>
<a class="skip-link" href="#main"><?php esc_html_e( 'Skip to content', 'verquin' ); ?></a>

<header class="site-header">
	<div class="site-header__row">
		<a class="brandmark" href="<?php echo esc_url( home_url( '/' ) ); ?>" rel="home">
			<?php verquin_brandmark(); ?>
		</a>

		<button class="site-header__toggle" type="button" aria-expanded="false" aria-controls="site-nav" data-label-open="<?php esc_attr_e( 'Menu', 'verquin' ); ?>" data-label-close="<?php esc_attr_e( 'Close menu', 'verquin' ); ?>">
			<span class="screen-reader-text"><?php esc_html_e( 'Menu', 'verquin' ); ?></span>
			<span class="site-header__toggle-bar" aria-hidden="true"></span>
		</button>

		<nav id="site-nav" class="site-header__nav" aria-label="<?php esc_attr_e( 'Primary', 'verquin' ); ?>">
			<?php verquin_block_menu( 'primary' ); ?>
			<a class="btn btn--solid site-header__cta" href="<?php echo esc_url( home_url( '/contact/' ) ); ?>"><?php esc_html_e( 'Contact', 'verquin' ); ?></a>
		</nav>
	</div>
</header>

<main id="main" class="site-main">
