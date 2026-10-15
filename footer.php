<?php
/**
 * Contact CTA band, site footer, and document close.
 *
 * Framework source: section.cta + footer > .cols / h4 / ul / .fine
 * CSS owner: .cta-band, .site-footer (globals)
 * Blocks used: buttons, menu
 *
 * Both are global: they render on every page and cannot be varied per page.
 * The CTA copy, the footer tagline, and the footer contact line come from the
 * ACF Options page (Settings → Site Options); the columns come from the footer
 * nav menus. The tagline is deliberately its own field rather than the
 * Customizer site description, which is already spoken for as the brandmark's
 * fallback alt text and is different copy.
 *
 * @package VerQuin
 */

defined( 'ABSPATH' ) || exit;

$verquin_cta = array(
	'eyebrow' => (string) verquin_option( 'site_cta_eyebrow', '' ),
	'heading' => (string) verquin_option( 'site_cta_heading', '' ),
	'lede'    => (string) verquin_option( 'site_cta_lede', '' ),
	'buttons' => array(
		verquin_link_to_button( verquin_option( 'site_cta_primary_link' ), 'solid' ),
		verquin_link_to_button( verquin_option( 'site_cta_secondary_link' ), 'ghost' ),
	),
);

$verquin_has_cta = '' !== $verquin_cta['eyebrow'] || '' !== $verquin_cta['heading'] || '' !== $verquin_cta['lede'];

$verquin_brochure       = verquin_option( 'site_brochure' );
$verquin_footer_tagline = (string) verquin_option( 'site_footer_tagline', '' );

// The fine line merges several values, so each one is wrapped rather than
// imploded into a single string: a bare join lets the line break inside a value,
// which splits a phone number across two lines. Address lines stay breakable —
// one of them can be too long to hold on a narrow screen, and forcing it would
// overflow the viewport instead of wrapping. Phone and email are short and must
// never split, so only they are marked.
$verquin_contact = array_merge(
	array_map(
		static fn( string $line ): array => array(
			'text'   => $line,
			'nowrap' => false,
		),
		preg_split( '/\R+/', (string) verquin_option( 'site_address', '' ), -1, PREG_SPLIT_NO_EMPTY ) ?: array()
	),
	array_map(
		static fn( string $value ): array => array(
			'text'   => $value,
			'nowrap' => true,
		),
		array_filter(
			array(
				(string) verquin_option( 'site_phone', '' ),
				(string) verquin_option( 'site_email', '' ),
			)
		)
	)
);

$verquin_contact_html = implode(
	' · ',
	array_map(
		static fn( array $item ): string => sprintf(
			'<span class="%1$s">%2$s</span>',
			$item['nowrap'] ? 'site-footer__fine-item site-footer__fine-item--nowrap' : 'site-footer__fine-item',
			esc_html( $item['text'] )
		),
		$verquin_contact
	)
);

$verquin_cta_heading = verquin_heading( $verquin_cta['heading'] );
$verquin_cta_lede    = verquin_rich_text( $verquin_cta['lede'] );
?>
</main>

<?php if ( $verquin_has_cta ) : ?>
<section class="cta-band">
	<div class="wrap">
		<?php if ( '' !== $verquin_cta['eyebrow'] ) : ?>
			<span class="eyebrow"><?php echo esc_html( $verquin_cta['eyebrow'] ); ?></span>
		<?php endif; ?>
		<?php if ( '' !== $verquin_cta_heading ) : ?>
			<h2><?php echo $verquin_cta_heading; // phpcs:ignore WordPress.Security.EscapeOutput -- verquin_heading() returns sanitized markup. ?></h2>
		<?php endif; ?>
		<?php if ( '' !== $verquin_cta_lede ) : ?>
			<p class="lede"><?php echo $verquin_cta_lede; // phpcs:ignore WordPress.Security.EscapeOutput -- verquin_rich_text() returns sanitized markup. ?></p>
		<?php endif; ?>
		<?php verquin_block_buttons( $verquin_cta['buttons'] ); ?>
	</div>
</section>
<?php endif; ?>

<footer class="site-footer">
	<div class="wrap">
		<div class="site-footer__cols">
			<div class="site-footer__brand">
				<span class="brandmark"><?php verquin_brandmark( false ); ?></span>
				<?php if ( '' !== $verquin_footer_tagline ) : ?>
					<p class="site-footer__tagline"><?php echo esc_html( $verquin_footer_tagline ); ?></p>
				<?php endif; ?>
			</div>

			<?php foreach ( verquin_footer_columns() as $verquin_location => $verquin_heading ) : ?>
				<?php if ( has_nav_menu( $verquin_location ) ) : ?>
					<div class="site-footer__col">
						<h2 class="site-footer__heading"><?php echo esc_html( $verquin_heading ); ?></h2>
						<?php verquin_block_menu( $verquin_location, 1, array( 'plain' ) ); ?>
					</div>
				<?php endif; ?>
			<?php endforeach; ?>
		</div>

		<?php if ( is_array( $verquin_brochure ) && ! empty( $verquin_brochure['url'] ) ) : ?>
			<p class="site-footer__fine">
				<a href="<?php echo esc_url( $verquin_brochure['url'] ); ?>"><?php echo esc_html( $verquin_brochure['title'] ?: __( 'Brochure', 'verquin' ) ); ?></a>
			</p>
		<?php endif; ?>

		<p class="site-footer__fine">
			<?php if ( '' !== $verquin_contact_html ) : ?>
				<?php echo $verquin_contact_html; // phpcs:ignore WordPress.Security.EscapeOutput -- each value is escaped as it is wrapped above. ?><br>
			<?php endif; ?>
			<?php
			printf(
				/* translators: 1: site name, 2: current year. */
				esc_html__( '%1$s® © %2$s', 'verquin' ),
				esc_html( get_bloginfo( 'name' ) ),
				esc_html( wp_date( 'Y' ) )
			);
			?>
		</p>
	</div>
</footer>

<?php wp_footer(); ?>
</body>
</html>
