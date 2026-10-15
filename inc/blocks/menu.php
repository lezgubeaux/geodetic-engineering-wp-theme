<?php
/**
 * Block: menu — a WordPress menu rendered as the framework nav list.
 *
 * Framework source: nav.site > ul > li.has-sub > ul
 * CSS owner: .b-menu
 *
 * @package VerQuin
 */

defined( 'ABSPATH' ) || exit;

/**
 * Render a menu location as a two-level list.
 *
 * @param string        $location  Registered nav menu location.
 * @param int           $depth     Levels to render (1 = flat list).
 * @param array<string> $modifiers Block modifiers, e.g. array( 'plain' ) → 'b-menu--plain'.
 *                                 'plain' drops the header's mono uppercase type, link
 *                                 padding, and item hairlines; used by the site footer.
 */
function verquin_block_menu( string $location, int $depth = 2, array $modifiers = array() ): void {
	if ( ! has_nav_menu( $location ) ) {
		return;
	}
	wp_nav_menu(
		array(
			'theme_location' => $location,
			'container'      => false,
			'menu_class'     => verquin_classes( 'b-menu', $modifiers ),
			'depth'          => $depth,
			'fallback_cb'    => false,
			'walker'         => new Verquin_Menu_Walker(),
		)
	);
}

/**
 * Walker producing framework-shaped markup: a.b-menu__link, li.b-menu__item--has-sub,
 * button.b-menu__toggle (keyboard/touch submenu control), ul.b-menu__sub.
 */
class Verquin_Menu_Walker extends Walker_Nav_Menu {

	// phpcs:disable WordPress.NamingConventions.ValidVariableName -- WordPress core signature.

	/** ID of the parent item whose submenu is being opened. */
	private int $parent_id = 0;

	public function start_lvl( &$output, $depth = 0, $args = null ) {
		$output .= '<ul class="b-menu__sub" id="b-menu-sub-' . (int) $this->parent_id . '">';
	}

	public function end_lvl( &$output, $depth = 0, $args = null ) {
		$output .= '</ul>';
	}

	public function start_el( &$output, $item, $depth = 0, $args = null, $id = 0 ) {
		$classes = array( 'b-menu__item' );

		/* A submenu is only announced when this menu's depth will actually render it:
		   depth 0 is unlimited, otherwise children stop at depth - 1. Without the guard a
		   flat menu emits a toggle and an aria-controls pointing at a list never output. */
		$max_depth = isset( $args->depth ) ? (int) $args->depth : 0;
		$has_sub   = in_array( 'menu-item-has-children', (array) $item->classes, true )
			&& ( 0 === $max_depth || $max_depth > $depth + 1 );
		if ( $has_sub ) {
			$classes[]       = 'b-menu__item--has-sub';
			$this->parent_id = (int) $item->ID;
		}
		$current = in_array( 'current-menu-item', (array) $item->classes, true ) ? ' aria-current="page"' : '';

		$output .= '<li class="' . esc_attr( implode( ' ', $classes ) ) . '">';
		$output .= '<a class="b-menu__link" href="' . esc_url( $item->url ) . '"' . $current . '>' . esc_html( $item->title ) . '</a>';

		if ( $has_sub ) {
			/* translators: %s: parent menu item title. */
			$label   = sprintf( __( 'Show submenu for %s', 'verquin' ), $item->title );
			$output .= '<button class="b-menu__toggle" type="button" aria-expanded="false" aria-controls="b-menu-sub-' . (int) $item->ID . '" aria-label="' . esc_attr( $label ) . '">' . verquin_icon( 'chevron-down', array( 'small' ) ) . '</button>';
		}
	}

	public function end_el( &$output, $item, $depth = 0, $args = null ) {
		$output .= '</li>';
	}

	// phpcs:enable
}
