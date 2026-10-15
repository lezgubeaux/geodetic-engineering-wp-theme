<?php
/**
 * Form integrations.
 *
 * @package VerQuin
 */

defined( 'ABSPATH' ) || exit;

/**
 * Whether Contact Form 7's reCAPTCHA integration has keys configured.
 */
function verquin_wpcf7_recaptcha_active(): bool {
	if ( ! class_exists( 'WPCF7_RECAPTCHA' ) ) {
		return false;
	}

	return WPCF7_RECAPTCHA::get_instance()->is_active();
}

/**
 * Append the Google reCAPTCHA v3 legal notice to every Contact Form 7 form.
 *
 * Required by Google's terms when the reCAPTCHA badge is hidden. Emitted only
 * when the integration is configured, so the statement is never untrue.
 *
 * @param string $content Rendered form elements.
 */
function verquin_wpcf7_recaptcha_notice( string $content ): string {
	if ( ! verquin_wpcf7_recaptcha_active() ) {
		return $content;
	}

	$notice = sprintf(
		'<p class="wpcf7-recaptcha-disclaimer">%s</p>',
		wp_kses(
			sprintf(
				/* translators: 1: privacy policy link, 2: terms of service link */
				__( 'This site is protected by reCAPTCHA and the Google %1$s and %2$s apply.', 'verquin' ),
				'<a href="https://policies.google.com/privacy" target="_blank" rel="noopener">' . esc_html__( 'Privacy Policy', 'verquin' ) . '</a>',
				'<a href="https://policies.google.com/terms" target="_blank" rel="noopener">' . esc_html__( 'Terms of Service', 'verquin' ) . '</a>'
			),
			array(
				'a' => array(
					'href'   => true,
					'target' => true,
					'rel'    => true,
				),
			)
		)
	);

	return $content . $notice;
}
add_filter( 'wpcf7_form_elements', 'verquin_wpcf7_recaptcha_notice' );

/**
 * The Contact Form 7 forms defined on this site, as id => title.
 *
 * CF7 5.8 moved the shortcode's id attribute to a hash rather than the post id,
 * so the value comes from the form object rather than from the post. Older
 * releases without hash() fall back to the post id, which CF7 still accepts.
 *
 * @return array<string, string>
 */
function verquin_cf7_forms(): array {
	if ( ! class_exists( 'WPCF7_ContactForm' ) ) {
		return array();
	}

	$choices = array();

	foreach ( WPCF7_ContactForm::find( array( 'posts_per_page' => -1 ) ) as $form ) {
		$id    = method_exists( $form, 'hash' ) ? (string) $form->hash() : (string) $form->id();
		$title = trim( (string) $form->title() );

		$choices[ $id ] = '' !== $title ? $title : $id;
	}

	return $choices;
}

/**
 * Offer the site's Contact Form 7 forms as the form layout's choices.
 *
 * Nothing is stored in the field JSON: the forms are site data, so a stored list
 * would be stale the moment a form is added, renamed or deleted.
 *
 * @param array<string, mixed> $field Field settings.
 * @return array<string, mixed>
 */
function verquin_cf7_field_choices( array $field ): array {
	$field['choices'] = verquin_cf7_forms();

	return $field;
}
add_filter( 'acf/load_field/key=field_verquin_form_id', 'verquin_cf7_field_choices' );

/**
 * Render a Contact Form 7 form by id.
 *
 * Outputs nothing when the plugin is inactive or the id is empty, so a raw
 * shortcode can never appear as text on a page.
 *
 * @param string $id    CF7 form hash id, e.g. '8a20e50'.
 * @param string $title Optional form title, passed through to the shortcode.
 */
function verquin_cf7_form( string $id, string $title = '' ): void {
	$id = trim( $id );

	if ( '' === $id || ! shortcode_exists( 'contact-form-7' ) ) {
		return;
	}

	$shortcode = sprintf(
		'[contact-form-7 id="%s"%s]',
		esc_attr( $id ),
		'' !== $title ? sprintf( ' title="%s"', esc_attr( $title ) ) : ''
	);

	echo do_shortcode( $shortcode ); // phpcs:ignore WordPress.Security.EscapeOutput -- Contact Form 7 escapes its own output.
}
