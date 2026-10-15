<?php
/**
 * Edit-screen tweaks for the classic editor.
 *
 * Pages use the classic edit screen (see inc/setup.php); ACF Flexible Content
 * makes that screen long, so the Publish box follows the viewport.
 *
 * @package VerQuin
 */

defined( 'ABSPATH' ) || exit;

/**
 * Keep the Publish box in view while scrolling a long edit screen.
 *
 * Sticky, not fixed: the box stays in the flow, so it keeps its column width
 * and does not overlap the boxes below it.
 *
 * Sticky only travels inside its containing block, and #submitdiv's is
 * #side-sortables — as tall as the sidebar boxes, while ACF's field groups sit
 * in #postbox-container-2 and make the other column several screens long. So
 * the two columns are laid out as a grid in which the sidebar spans both rows:
 * a stretched grid item has a definite height, which a floated one does not,
 * and that height passes down to #side-sortables. Sticking the sidebar column
 * itself would be less CSS but did not hold on this screen.
 *
 * Limited to the two-column layout — in one-column mode the box sits inline
 * with the content and has nothing to stick to.
 */
function verquin_admin_sticky_publish_box(): void {
	$screen = get_current_screen();

	if ( ! $screen instanceof WP_Screen || 'page' !== $screen->post_type ) {
		return;
	}

	?>
	<style id="verquin-sticky-publish">
		/* 851px is WordPress's own two-column breakpoint; 32px admin bar + 10px gap. */
		@media screen and (min-width: 851px) {
			/* Replaces WordPress's float layout: the sidebar spans both rows. */
			#poststuff #post-body.columns-2 {
				display: grid;
				grid-template-columns: minmax(0, 1fr) 280px;
				grid-template-areas:
					"content side"
					"boxes side";
				gap: 0 20px;
				margin-right: 0;
			}

			#post-body.columns-2 #post-body-content {
				grid-area: content;
				float: none;
				width: auto;
				margin: 0;
			}

			#post-body.columns-2 #postbox-container-2 {
				grid-area: boxes;
				float: none;
				width: auto;
			}

			#post-body.columns-2 #postbox-container-1 {
				grid-area: side;
				float: none;
				width: auto;
				margin-right: 0;
			}

			/* Hands the row height down, so #submitdiv can travel the full screen. */
			#post-body.columns-2 #side-sortables {
				height: 100%;
			}

			/* The float-clearing break would claim a grid row of its own. */
			#post-body.columns-2 > br.clear {
				display: none;
			}

			#post-body.columns-2 #submitdiv {
				position: sticky;
				top: 42px;
				z-index: 9; /* above sortable metabox placeholders */
			}
		}
	</style>
	<?php
}
add_action( 'admin_head-post.php', 'verquin_admin_sticky_publish_box' );
add_action( 'admin_head-post-new.php', 'verquin_admin_sticky_publish_box' );
