<?php
/**
 * Template helpers shared by sections and blocks.
 *
 * @package VerQuin
 */

defined( 'ABSPATH' ) || exit;

/**
 * Render a section (ACF Flexible Content layout) from template-parts/layouts/.
 *
 * @param string               $name Layout slug, e.g. 'hero'.
 * @param array<string, mixed> $data Section data, available as $args in the template.
 */
function verquin_section( string $name, array $data = array() ): void {
	get_template_part( 'template-parts/layouts/' . $name, null, $data );
}

/**
 * Render a row (nested Flexible Content layout inside a band) from template-parts/rows/.
 *
 * @param string               $name Row slug, e.g. 'text-media'.
 * @param array<string, mixed> $data Row data, available as $args in the template.
 */
function verquin_row( string $name, array $data = array() ): void {
	get_template_part( 'template-parts/rows/' . $name, null, $data );
}

/**
 * Sanitize heading text that may carry the framework's inline <br> and <em>.
 *
 * @param string $text Raw heading text.
 */
function verquin_heading( string $text ): string {
	return wp_kses(
		$text,
		array(
			'br' => array(),
			'em' => array(),
		)
	);
}

/**
 * Sanitize body copy: paragraphs and inline formatting only.
 *
 * @param string $html Raw body HTML.
 */
function verquin_rich_text( string $html ): string {
	return wp_kses(
		$html,
		array(
			'p'      => array(),
			'br'     => array(),
			'em'     => array(),
			'strong' => array(),
			'a'      => array(
				'href'   => true,
				'rel'    => true,
				'target' => true,
			),
		)
	);
}

/**
 * Build a space-separated class attribute value from a base class and modifiers.
 *
 * @param string        $base      Owner class, e.g. 's-page-head'.
 * @param array<string> $modifiers Modifier suffixes, e.g. array( 'tall' ) → 's-page-head--tall'.
 * @param array<string> $extra     Additional classes appended verbatim.
 */
function verquin_classes( string $base, array $modifiers = array(), array $extra = array() ): string {
	$classes = array( $base );
	foreach ( array_filter( $modifiers ) as $modifier ) {
		$classes[] = $base . '--' . $modifier;
	}
	return esc_attr( implode( ' ', array_merge( $classes, array_filter( $extra ) ) ) );
}

/**
 * Inline Lucide icon (ISC) per docs/lucide-icon-workflow.md; reference copies live in assets/icons/.
 *
 * @param string        $name      Icon name, one of: 'chevron-down'.
 * @param array<string> $modifiers Icon modifier suffixes, e.g. array( 'small' ) → 'icon--small'.
 */
function verquin_icon( string $name, array $modifiers = array() ): string {
	$paths = array(
		'chevron-down' => '<path d="m6 9 6 6 6-6"/>',
	);
	if ( ! isset( $paths[ $name ] ) ) {
		return '';
	}
	return '<svg class="' . verquin_classes( 'icon', $modifiers ) . '" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">' . $paths[ $name ] . '</svg>';
}

/**
 * The footer menu columns, in display order: nav menu location => column heading.
 *
 * Headings are framework copy; the links come from the menus assigned in
 * Appearance → Menus. A location with no menu assigned renders no column.
 *
 * @return array<string, string>
 */
function verquin_footer_columns(): array {
	return array(
		'footer-services'   => __( 'Services', 'verquin' ),
		'footer-monuments'  => __( 'Monuments', 'verquin' ),
		'footer-networks'   => __( 'Monitoring Networks', 'verquin' ),
		'footer-reflectors' => __( 'Corner Reflectors', 'verquin' ),
		'footer-about'      => __( 'About', 'verquin' ),
		'footer-contact'    => __( 'Contact', 'verquin' ),
	);
}

/**
 * Render the brandmark: the Customizer site logo (Site Identity → Logo) when one is set,
 * otherwise the framework's text wordmark.
 *
 * The image alt is the only SEO carrier: the Media Library alt when filled in,
 * else "Site Title – Tagline". No title attribute, no hidden duplicate text.
 *
 * @param bool $show_bm_sub Whether to append the "Geodetic Engineering" sub-line.
 *                          The header shows it; the footer suppresses it.
 */
function verquin_brandmark( bool $show_bm_sub = true ): void {
	$logo_id = (int) get_theme_mod( 'custom_logo' );
	$image   = '';

	if ( $logo_id > 0 ) {
		$alt = trim( (string) get_post_meta( $logo_id, '_wp_attachment_image_alt', true ) );
		if ( '' === $alt ) {
			$alt = implode( ' – ', array_filter( array( get_bloginfo( 'name' ), get_bloginfo( 'description' ) ) ) );
		}

		$image = wp_get_attachment_image(
			$logo_id,
			'full',
			false,
			array(
				'class'         => 'brandmark__logo',
				'alt'           => $alt,
				'loading'       => 'eager',
				'fetchpriority' => 'high',
			)
		);
	}

	if ( '' !== $image ) {
		echo $image; // phpcs:ignore WordPress.Security.EscapeOutput -- wp_get_attachment_image() returns escaped markup.
	} else {
		echo '<span class="brandmark__wordmark">VER<span>QUIN</span></span>';
	}

	if ( $show_bm_sub ) {
		echo '<span class="brandmark__sub">' . esc_html__( 'Geodetic Engineering', 'verquin' ) . '</span>';
	}
}
