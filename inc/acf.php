<?php
/**
 * ACF Pro integration: local JSON sync, the site Options page, option access,
 * and the Flexible Content dispatcher used by the page templates.
 *
 * Every entry point is guarded so the theme still renders when ACF Pro is
 * deactivated.
 *
 * @package VerQuin
 */

defined( 'ABSPATH' ) || exit;

/**
 * Save field-group JSON inside the theme so field definitions travel with the code.
 *
 * @param string $path Default save path.
 * @return string
 */
function verquin_acf_json_save_point( string $path ): string {
	unset( $path );

	return get_theme_file_path( '/acf-json' );
}
add_filter( 'acf/settings/save_json', 'verquin_acf_json_save_point' );

/**
 * Load field-group JSON from the theme.
 *
 * @param array<int, string> $paths Registered load paths.
 * @return array<int, string>
 */
function verquin_acf_json_load_point( array $paths ): array {
	$paths[] = get_theme_file_path( '/acf-json' );

	return $paths;
}
add_filter( 'acf/settings/load_json', 'verquin_acf_json_load_point' );

/**
 * Register the site-wide Options page that holds contact details, the CTA, and the brochure.
 */
function verquin_acf_options_page(): void {
	if ( ! function_exists( 'acf_add_options_page' ) ) {
		return;
	}

	acf_add_options_page(
		array(
			'page_title' => __( 'Site Options', 'verquin' ),
			'menu_title' => __( 'Site Options', 'verquin' ),
			'menu_slug'  => 'verquin-site-options',
			'capability' => 'manage_options',
			'position'   => 80,
			'icon_url'   => 'dashicons-admin-settings',
			'redirect'   => false,
		)
	);
}
add_action( 'acf/init', 'verquin_acf_options_page' );

/**
 * Accept mailto: and tel: in ACF url fields.
 *
 * ACF's url field requires '://' or a leading '//', so contact links are rejected
 * on save even though the browser's type="url" check passes them and esc_url()
 * allows both schemes on output. Runs only after ACF has already failed the value,
 * so malformed URLs still fail.
 *
 * A safety net rather than load-bearing: editable links use the ACF `link` field,
 * which has no such restriction. This keeps any future `url` field from breaking
 * on a contact link by default.
 *
 * @param bool|string          $valid True, or the validation error message.
 * @param mixed                $value Submitted value.
 * @param array<string, mixed> $field Field settings.
 * @param string               $input Input name.
 * @return bool|string
 */
function verquin_acf_allow_contact_schemes( bool|string $valid, mixed $value, array $field, string $input ): bool|string {
	unset( $field, $input );

	if ( true === $valid || ! is_string( $value ) || '' === $value ) {
		return $valid;
	}

	$scheme = strtolower( (string) wp_parse_url( $value, PHP_URL_SCHEME ) );

	if ( ! in_array( $scheme, array( 'mailto', 'tel' ), true ) ) {
		return $valid;
	}

	// Require something after the colon, e.g. 'mailto:' alone stays invalid.
	return '' !== trim( substr( $value, strlen( $scheme ) + 1 ) ) ? true : $valid;
}
add_filter( 'acf/validate_value/type=url', 'verquin_acf_allow_contact_schemes', 20, 4 );

/**
 * Read one value from the site Options page.
 *
 * @param string $name    Field name, e.g. 'site_phone'.
 * @param mixed  $default Returned when ACF is unavailable or the value is empty.
 * @return mixed
 */
function verquin_option( string $name, mixed $default = null ): mixed {
	if ( ! function_exists( 'get_field' ) ) {
		return $default;
	}

	$value = get_field( $name, 'option' );

	return ( null === $value || '' === $value || false === $value ) ? $default : $value;
}

/**
 * Read an ACF field that may be absent, as a string.
 *
 * @param string $name Field name.
 * @param bool   $sub  True to read a sub-field of the current row.
 */
function verquin_field_text( string $name, bool $sub = false ): string {
	if ( ! function_exists( 'get_field' ) ) {
		return '';
	}

	$value = $sub ? get_sub_field( $name ) : get_field( $name );

	return is_scalar( $value ) ? trim( (string) $value ) : '';
}

/**
 * A readable label for a link whose text was left empty.
 *
 * The picker fills the title when an existing page is chosen, but a pasted URL,
 * mailto: or tel: link stores an empty title. The buttons block drops entries with
 * no label, so without this a button disappears with no indication why.
 *
 * @param string $url Link URL.
 */
function verquin_link_fallback_label( string $url ): string {
	$scheme = strtolower( (string) wp_parse_url( $url, PHP_URL_SCHEME ) );

	// mailto:info@example.com → info@example.com; tel:1234 → 1234.
	if ( in_array( $scheme, array( 'mailto', 'tel' ), true ) ) {
		$value = substr( $url, strlen( $scheme ) + 1 );
		return trim( (string) strtok( $value, '?' ) );
	}

	$post_id = (int) url_to_postid( $url );

	if ( $post_id > 0 ) {
		$title = trim( (string) get_the_title( $post_id ) );
		if ( '' !== $title ) {
			return $title;
		}
	}

	// Last resort: the host, so the button is at least visible and identifiable.
	return trim( (string) wp_parse_url( $url, PHP_URL_HOST ) );
}

/**
 * Normalize an ACF link field to the buttons block's shape.
 *
 * ACF returns array{title, url, target}; an empty field returns false or an empty
 * array. The style is supplied by the caller, not the link.
 *
 * @param mixed  $link  Raw link-field value.
 * @param string $style 'solid' or 'ghost'.
 * @return array{label: string, url: string, style: string, target: string}
 */
function verquin_link_to_button( mixed $link, string $style = 'solid' ): array {
	$link = is_array( $link ) ? $link : array();

	$url   = trim( (string) ( $link['url'] ?? '' ) );
	$label = trim( (string) ( $link['title'] ?? '' ) );

	if ( '' === $label && '' !== $url ) {
		$label = verquin_link_fallback_label( $url );
	}

	return array(
		'label'  => $label,
		'url'    => $url,
		'style'  => $style,
		'target' => trim( (string) ( $link['target'] ?? '' ) ),
	);
}

/**
 * Collect a buttons repeater into the buttons block's shape.
 *
 * @param string $repeater   Repeater field name.
 * @param string $link_field Link sub-field name.
 * @param string $style_field Style sub-field name.
 * @return array<int, array{label: string, url: string, style: string, target: string}>
 */
function verquin_repeater_buttons( string $repeater, string $link_field, string $style_field ): array {
	$buttons = array();

	if ( ! function_exists( 'have_rows' ) || ! have_rows( $repeater ) ) {
		return $buttons;
	}

	while ( have_rows( $repeater ) ) {
		the_row();
		$buttons[] = verquin_link_to_button(
			get_sub_field( $link_field ),
			verquin_field_text( $style_field, true )
		);
	}

	return $buttons;
}

/**
 * Normalize a figure group to the figure block's shape.
 *
 * An ACF group always returns a populated array, so an untouched group would still
 * render a placeholder box. Returns an empty array unless the editor supplied
 * something, which the row treats as "no figure".
 *
 * @param mixed  $group  Raw group value, sub-fields prefixed with the group's name.
 * @param string $prefix The group's sub-field prefix, including its trailing underscore.
 * @return array<string, mixed>
 */
function verquin_figure_args( mixed $group, string $prefix = 'text_media_figure_' ): array {
	if ( ! is_array( $group ) ) {
		return array();
	}

	$get = static fn( string $key ): string => trim( (string) ( $group[ $prefix . $key ] ?? '' ) );

	$figure = array(
		'image'       => (int) ( $group[ $prefix . 'image' ] ?? 0 ),
		'slot'        => $get( 'slot' ),
		'kind'        => $get( 'kind' ),
		'ratio'       => $get( 'ratio' ),
		'fit'         => $get( 'fit' ),
		'placeholder' => $get( 'placeholder' ),
		'caption'     => $get( 'caption' ),
		'credit'      => $get( 'credit' ),
	);

	// Kind, ratio and fit have defaults, so they do not count as editor input.
	$supplied = $figure['image'] > 0
		|| '' !== $figure['slot']
		|| '' !== $figure['placeholder']
		|| '' !== $figure['caption'];

	return $supplied ? $figure : array();
}

/**
 * Normalize a video group to the shape the video row and media column use.
 *
 * Same contract as verquin_figure_args(): an untouched ACF group still returns a
 * populated array, so this returns an empty one unless the editor supplied a URL,
 * a slot or a placeholder description. Ratio alone has a default and does not count.
 *
 * @param mixed  $group  Raw group value, sub-fields prefixed with the group's name.
 * @param string $prefix The group's sub-field prefix, including its trailing underscore.
 * @return array<string, string>
 */
function verquin_video_args( mixed $group, string $prefix = 'text_media_video_' ): array {
	if ( ! is_array( $group ) ) {
		return array();
	}

	$get = static fn( string $key ): string => trim( (string) ( $group[ $prefix . $key ] ?? '' ) );

	$video = array(
		'url'         => $get( 'url' ),
		'ratio'       => $get( 'ratio' ),
		'slot'        => $get( 'slot' ),
		'placeholder' => $get( 'placeholder' ),
		'caption'     => $get( 'caption' ),
		'credit'      => $get( 'credit' ),
	);

	$supplied = '' !== $video['url'] || '' !== $video['slot'] || '' !== $video['placeholder'];

	return $supplied ? $video : array();
}

/**
 * The parent-page breadcrumb, derived rather than entered.
 *
 * The framework shows a single parent link on child pages and nothing on top-level
 * pages. Deriving it from post_parent keeps it correct when pages are renamed or
 * moved, and removes 17 chances to mistype it.
 *
 * @param int $post_id Page being rendered.
 * @return array{label: string, url: string}|array{}
 */
function verquin_page_head_crumb( int $post_id ): array {
	$parent_id = (int) wp_get_post_parent_id( $post_id );

	if ( $parent_id <= 0 || 'publish' !== get_post_status( $parent_id ) ) {
		return array();
	}

	$label = trim( (string) get_the_title( $parent_id ) );
	$url   = (string) get_permalink( $parent_id );

	if ( '' === $label || '' === $url ) {
		return array();
	}

	return array(
		'label' => $label,
		'url'   => $url,
	);
}

/**
 * Build the page head's $args from its field group.
 *
 * The heading falls back to the page title, so a page always has an h1 even when
 * the field is left empty.
 *
 * @param int $post_id Page being rendered.
 * @return array<string, mixed>
 */
function verquin_page_head_args( int $post_id ): array {
	$heading = verquin_field_text( 'page_head_heading' );

	$buttons = verquin_repeater_buttons( 'page_head_buttons', 'page_head_buttons_link', 'page_head_buttons_style' );

	return array(
		'eyebrow' => verquin_field_text( 'page_head_eyebrow' ),
		'heading' => '' !== $heading ? $heading : (string) get_the_title( $post_id ),
		'lede'    => verquin_field_text( 'page_head_lede' ),
		'tall'    => (bool) get_field( 'page_head_tall' ),
		'crumb'   => verquin_page_head_crumb( $post_id ),
		'buttons' => $buttons,
	);
}

/**
 * Render the page head.
 *
 * Rendered by the template rather than chosen as a layout: every page has exactly
 * one, always first, so there is nothing for an editor to order or omit.
 *
 * @param int|null $post_id Defaults to the current post.
 */
function verquin_render_page_head( ?int $post_id = null ): void {
	$post_id = $post_id ?? (int) get_the_ID();

	if ( ! $post_id ) {
		return;
	}

	verquin_section( 'page-head', verquin_page_head_args( $post_id ) );
}

/**
 * Collect the spec-tables repeater into the row's tables shape.
 *
 * Nested repeaters: the outer loop sets each table as the current row, so the inner
 * have_rows() resolves against that table rather than the row layout.
 *
 * @return array<int, array{caption: string, rows: array<int, array{label: string, value: string}>}>
 */
function verquin_repeater_tables( string $repeater, string $prefix ): array {
	$tables = array();

	if ( ! function_exists( 'have_rows' ) || ! have_rows( $repeater ) ) {
		return $tables;
	}

	while ( have_rows( $repeater ) ) {
		the_row();

		$rows = array();

		if ( have_rows( $prefix . 'rows' ) ) {
			while ( have_rows( $prefix . 'rows' ) ) {
				the_row();
				$rows[] = array(
					'label' => verquin_field_text( $prefix . 'rows_label', true ),
					'value' => verquin_field_text( $prefix . 'rows_value', true ),
				);
			}
		}

		$tables[] = array(
			'caption' => verquin_field_text( $prefix . 'caption', true ),
			'rows'    => $rows,
		);
	}

	return $tables;
}

/**
 * Collect the cards repeater into the cards block's shape.
 *
 * Only the link's url is used: the card's visible text is its own title, and the
 * block wraps the whole card in the anchor rather than rendering a labelled link.
 *
 * The optional figure and download links serve index.html's NEMA tiles, where each
 * card carries a branded graphic and its own PDF link. The figure reuses the shared
 * figure args so a card cell placeholders exactly as any other figure does.
 *
 * @return array<int, array{title: string, index: string, text: string, note: string, url: string, figure: array<string, mixed>, links: array<int, array{label: string, url: string, external: bool}>}>
 */
function verquin_repeater_cards(): array {
	$cards = array();

	if ( ! function_exists( 'have_rows' ) || ! have_rows( 'cards_card' ) ) {
		return $cards;
	}

	while ( have_rows( 'cards_card' ) ) {
		the_row();

		$link  = get_sub_field( 'cards_card_link' );
		$links = verquin_repeater_links( 'cards_card_links', 'cards_card_links_' );

		$cards[] = array(
			'title'  => verquin_field_text( 'cards_card_title', true ),
			'index'  => verquin_field_text( 'cards_card_index', true ),
			'text'   => verquin_field_text( 'cards_card_text', true ),
			'note'   => verquin_field_text( 'cards_card_note', true ),
			'url'    => is_array( $link ) ? trim( (string) ( $link['url'] ?? '' ) ) : '',
			'figure' => verquin_figure_args( get_sub_field( 'cards_card_figure' ), 'cards_card_figure_' ),
			'links'  => $links,
		);
	}

	return $cards;
}

/**
 * Resolve the child-index row to an ordered list of pages.
 *
 * Automatic mode lists the current page's published children in menu order, which
 * is how the six framework hub pages are arranged. Chosen mode keeps the editor's
 * order and silently skips anything unpublished or deleted.
 *
 * @param int $post_id Page being rendered.
 * @return array<int, array{title: string, url: string}>
 */
function verquin_child_index_items( int $post_id ): array {
	$mode = verquin_field_text( 'child_index_mode', true );

	if ( 'selected' === $mode ) {
		$ids = get_sub_field( 'child_index_pages' );
		$ids = is_array( $ids ) ? array_map( 'intval', $ids ) : array();

		$pages = array();
		foreach ( $ids as $id ) {
			if ( 'publish' === get_post_status( $id ) ) {
				$pages[] = get_post( $id );
			}
		}
	} else {
		$pages = get_posts(
			array(
				'post_type'        => 'page',
				'post_parent'      => $post_id,
				'post_status'      => 'publish',
				'orderby'          => array(
					'menu_order' => 'ASC',
					'title'      => 'ASC',
				),
				'numberposts'      => -1,
				'suppress_filters' => false,
			)
		);
	}

	$items = array();
	foreach ( $pages as $page ) {
		$items[] = array(
			'title' => trim( (string) get_the_title( $page ) ),
			'url'   => (string) get_permalink( $page ),
		);
	}

	return $items;
}

/**
 * Collect the gallery repeater into a list of figure args.
 *
 * The gallery's figures are repeater rows rather than a group, so each row is
 * assembled into the prefixed shape verquin_figure_args() expects; that keeps one
 * definition of what counts as an empty figure.
 *
 * @return array<int, array<string, mixed>>
 */
function verquin_repeater_gallery(): array {
	$figures = array();

	if ( ! function_exists( 'have_rows' ) || ! have_rows( 'gallery_figure' ) ) {
		return $figures;
	}

	$keys = array( 'image', 'slot', 'kind', 'ratio', 'placeholder', 'caption', 'credit' );

	while ( have_rows( 'gallery_figure' ) ) {
		the_row();

		$group = array();
		foreach ( $keys as $key ) {
			$group[ 'gallery_figure_' . $key ] = get_sub_field( 'gallery_figure_' . $key );
		}

		$figure = verquin_figure_args( $group, 'gallery_figure_' );

		if ( $figure ) {
			$figures[] = $figure;
		}
	}

	return $figures;
}

/**
 * Collect the links repeater into the links block's shape.
 *
 * Reuses the button link's label fallback: a pasted URL stores an empty title, and
 * the links block drops entries with no label.
 *
 * @return array<int, array{label: string, url: string, external: bool}>
 */
function verquin_repeater_links( string $repeater = 'links_item', string $prefix = 'links_item_' ): array {
	$links = array();

	if ( ! function_exists( 'have_rows' ) || ! have_rows( $repeater ) ) {
		return $links;
	}

	while ( have_rows( $repeater ) ) {
		the_row();

		$button = verquin_link_to_button( get_sub_field( $prefix . 'link' ), '' );

		if ( '' === $button['url'] ) {
			continue;
		}

		$links[] = array(
			'label'    => $button['label'],
			'url'      => $button['url'],
			'external' => (bool) get_sub_field( $prefix . 'external' ),
		);
	}

	return $links;
}

/**
 * Collect a labelled-passages repeater into the labelled-text block's shape.
 *
 * @param string $repeater Repeater field name.
 * @param string $prefix   Sub-field prefix, including its trailing underscore.
 * @return array<int, array{label: string, text: string}>
 */
function verquin_repeater_labelled_text( string $repeater, string $prefix ): array {
	$groups = array();

	if ( ! function_exists( 'have_rows' ) || ! have_rows( $repeater ) ) {
		return $groups;
	}

	while ( have_rows( $repeater ) ) {
		the_row();

		$groups[] = array(
			'label' => verquin_field_text( $prefix . 'label', true ),
			'text'  => verquin_field_text( $prefix . 'text', true ),
		);
	}

	return $groups;
}

/**
 * Collect the timeline repeater into the timeline block's shape.
 *
 * @return array<int, array{year: string, heading: string, body: string}>
 */
function verquin_repeater_timeline(): array {
	$events = array();

	if ( ! function_exists( 'have_rows' ) || ! have_rows( 'timeline_event' ) ) {
		return $events;
	}

	while ( have_rows( 'timeline_event' ) ) {
		the_row();

		$events[] = array(
			'year'    => verquin_field_text( 'timeline_event_year', true ),
			'heading' => verquin_field_text( 'timeline_event_heading', true ),
			'body'    => verquin_field_text( 'timeline_event_body', true ),
		);
	}

	return $events;
}

/**
 * Collect the references repeater into the references block's shape.
 *
 * Nested repeaters: the outer loop sets each group as the current row, so the inner
 * have_rows() resolves against that group rather than the row layout.
 *
 * @return array<int, array{heading: string, items: array<int, string>}>
 */
function verquin_repeater_references(): array {
	$groups = array();

	if ( ! function_exists( 'have_rows' ) || ! have_rows( 'references_group' ) ) {
		return $groups;
	}

	while ( have_rows( 'references_group' ) ) {
		the_row();

		$items = array();

		if ( have_rows( 'references_group_item' ) ) {
			while ( have_rows( 'references_group_item' ) ) {
				the_row();
				$text = verquin_field_text( 'references_group_item_text', true );
				if ( '' !== $text ) {
					$items[] = $text;
				}
			}
		}

		$groups[] = array(
			'heading' => verquin_field_text( 'references_group_heading', true ),
			'items'   => $items,
		);
	}

	return $groups;
}

/**
 * Collect the FAQ repeater into the faq-item block's shape.
 *
 * Each item carries three nested structures — a table group with its own rows
 * repeater, and a links repeater — so the sub-loops run while the item is the
 * current row.
 *
 * @return array<int, array<string, mixed>>
 */
function verquin_repeater_faq(): array {
	$items = array();

	if ( ! function_exists( 'have_rows' ) || ! have_rows( 'faq_item' ) ) {
		return $items;
	}

	while ( have_rows( 'faq_item' ) ) {
		the_row();

		// A repeater inside a group is not a have_rows() context: read the group
		// array and take its rows, which arrive keyed by their sub-field names.
		$table = get_sub_field( 'faq_item_table' );
		$table = is_array( $table ) ? $table : array();

		$rows = array();
		foreach ( (array) ( $table['faq_item_table_rows'] ?? array() ) as $row ) {
			$rows[] = array(
				'label' => trim( (string) ( $row['faq_item_table_rows_label'] ?? '' ) ),
				'value' => trim( (string) ( $row['faq_item_table_rows_value'] ?? '' ) ),
			);
		}

		$links = array();
		if ( have_rows( 'faq_item_link' ) ) {
			while ( have_rows( 'faq_item_link' ) ) {
				the_row();
				$button = verquin_link_to_button( get_sub_field( 'faq_item_link_link' ), '' );
				if ( '' !== $button['url'] ) {
					$links[] = array(
						'label'    => $button['label'],
						'url'      => $button['url'],
						'external' => (bool) get_sub_field( 'faq_item_link_external' ),
					);
				}
			}
		}

		$items[] = array(
			'question' => verquin_field_text( 'faq_item_question', true ),
			'verdict'  => verquin_field_text( 'faq_item_verdict', true ),
			'answer'   => verquin_field_text( 'faq_item_answer', true ),
			'table'    => array(
				'caption' => trim( (string) ( $table['faq_item_table_caption'] ?? '' ) ),
				'rows'    => $rows,
			),
			'callout'  => verquin_field_text( 'faq_item_callout', true ),
			'closing'  => verquin_field_text( 'faq_item_closing', true ),
			'links'    => $links,
		);
	}

	return $items;
}

/**
 * Map one row layout's sub-fields to the row's $args shape.
 *
 * Each row step adds its own case here; a layout with no case renders with no data,
 * which its template treats as empty input.
 *
 * @param string $layout Row layout name as registered in ACF.
 * @return array<string, mixed>
 */
function verquin_row_args( string $layout ): array {
	if ( 'placeholder' === $layout ) {
		return array( 'note' => verquin_field_text( 'placeholder_note', true ) );
	}

	if ( 'text-media' === $layout ) {
		$kind = verquin_field_text( 'text_media_media_kind', true );

		return array(
			'eyebrow'      => verquin_field_text( 'text_media_eyebrow', true ),
			'heading'      => verquin_field_text( 'text_media_heading', true ),
			'body'         => verquin_field_text( 'text_media_body', true ),
			'groups'       => verquin_repeater_labelled_text( 'text_media_group', 'text_media_group_' ),
			'tables'       => verquin_repeater_tables( 'text_media_table', 'text_media_table_' ),
			'callout'      => verquin_field_text( 'text_media_callout', true ),
			'closing'      => verquin_field_text( 'text_media_closing', true ),
			'links'        => verquin_repeater_links( 'text_media_link', 'text_media_link_' ),
			'buttons'      => verquin_repeater_buttons( 'text_media_buttons', 'text_media_buttons_link', 'text_media_buttons_style' ),
			'figure'       => in_array( $kind, array( 'diagram', 'tables', 'video' ), true ) ? array() : verquin_figure_args( get_sub_field( 'text_media_figure' ) ),
			'diagram'      => 'diagram' === $kind ? verquin_field_text( 'text_media_diagram', true ) : '',
			'tables_media' => 'tables' === $kind,
			'video'        => 'video' === $kind ? verquin_video_args( get_sub_field( 'text_media_video' ), 'text_media_video_' ) : array(),
			'flip'         => (bool) get_sub_field( 'text_media_flip' ),
		);
	}

	if ( 'spec-tables' === $layout ) {
		$args = array(
			'tables' => verquin_repeater_tables( 'spec_tables_table', 'spec_tables_table_' ),
			'narrow' => (bool) get_sub_field( 'spec_tables_narrow' ),
		);

		// Only 'paired' changes the rendering; off, the row's own default stacks them.
		if ( get_sub_field( 'spec_tables_paired' ) ) {
			$args['layout'] = 'paired';
		}

		return $args;
	}

	if ( 'data-chart' === $layout ) {
		return array(
			'station'    => verquin_field_text( 'data_chart_station', true ),
			'kind'       => verquin_field_text( 'data_chart_kind', true ),
			'title'      => verquin_field_text( 'data_chart_title', true ),
			'footer'     => verquin_field_text( 'data_chart_footer', true ),
			'error_bars' => (bool) get_sub_field( 'data_chart_error_bars' ),
		);
	}

	if ( 'cards' === $layout ) {
		return array( 'cards' => verquin_repeater_cards() );
	}

	if ( 'child-index' === $layout ) {
		return array( 'items' => verquin_child_index_items( (int) get_the_ID() ) );
	}

	if ( 'gallery' === $layout ) {
		return array( 'figures' => verquin_repeater_gallery() );
	}

	if ( 'figure' === $layout ) {
		return array(
			'figure' => verquin_figure_args( get_sub_field( 'figure_figure' ), 'figure_figure_' ),
			'narrow' => (bool) get_sub_field( 'figure_narrow' ),
		);
	}

	if ( 'callout' === $layout ) {
		return array( 'html' => verquin_field_text( 'callout_html', true ) );
	}

	if ( 'links' === $layout ) {
		return array( 'links' => verquin_repeater_links() );
	}

	if ( 'text' === $layout ) {
		return array(
			'html'  => verquin_field_text( 'text_html', true ),
			'style' => verquin_field_text( 'text_style', true ),
		);
	}

	if ( 'buttons' === $layout ) {
		return array(
			'buttons' => verquin_repeater_buttons( 'buttons_button', 'buttons_button_link', 'buttons_button_style' ),
		);
	}

	if ( 'pending' === $layout ) {
		return array( 'note' => verquin_field_text( 'pending_note', true ) );
	}

	if ( 'timeline' === $layout ) {
		return array( 'events' => verquin_repeater_timeline() );
	}

	if ( 'references' === $layout ) {
		return array( 'groups' => verquin_repeater_references() );
	}

	if ( 'faq' === $layout ) {
		return array( 'items' => verquin_repeater_faq() );
	}

	if ( 'diagram' === $layout ) {
		return array(
			'diagram' => verquin_field_text( 'diagram_diagram', true ),
			'caption' => verquin_field_text( 'diagram_caption', true ),
			'credit'  => verquin_field_text( 'diagram_credit', true ),
		);
	}

	if ( 'video' === $layout ) {
		return array(
			'url'         => verquin_field_text( 'video_url', true ),
			'ratio'       => verquin_field_text( 'video_ratio', true ),
			'slot'        => verquin_field_text( 'video_slot', true ),
			'placeholder' => verquin_field_text( 'video_placeholder', true ),
			'caption'     => verquin_field_text( 'video_caption', true ),
			'credit'      => verquin_field_text( 'video_credit', true ),
		);
	}

	return array();
}

/**
 * Collect the rows of the band currently being walked.
 *
 * @return array<int, array{row: string, data: array<string, mixed>}>
 */
function verquin_band_rows(): array {
	$rows = array();

	if ( ! function_exists( 'have_rows' ) || ! have_rows( 'band_rows' ) ) {
		return $rows;
	}

	while ( have_rows( 'band_rows' ) ) {
		the_row();

		$layout = (string) get_row_layout();

		if ( '' === $layout ) {
			continue;
		}

		$rows[] = array(
			'row'  => $layout,
			'data' => verquin_row_args( $layout ),
		);
	}

	return $rows;
}

/**
 * Map one Flexible Content layout's sub-fields to the section's $args shape.
 *
 * Each section step adds its own case here; a layout with no case renders with
 * no data, which its template treats as empty input.
 *
 * @param string $layout Layout name as registered in ACF.
 * @return array<string, mixed>
 */
function verquin_section_args( string $layout ): array {
	if ( 'band' === $layout ) {
		return array(
			'id'      => verquin_field_text( 'band_id', true ),
			'eyebrow' => verquin_field_text( 'band_eyebrow', true ),
			'heading' => verquin_field_text( 'band_heading', true ),
			'lede'    => verquin_field_text( 'band_lede', true ),
			'rows'    => verquin_band_rows(),
		);
	}

	if ( 'form' === $layout ) {
		return array(
			'id'      => verquin_field_text( 'form_anchor', true ),
			'eyebrow' => verquin_field_text( 'form_eyebrow', true ),
			'heading' => verquin_field_text( 'form_heading', true ),
			'lede'    => verquin_field_text( 'form_lede', true ),
			'form'    => verquin_field_text( 'form_id', true ),
			'wide'    => (bool) get_sub_field( 'form_wide' ),
		);
	}

	return array();
}

/**
 * Render every Flexible Content section of a page in order.
 *
 * @param int|null $post_id Defaults to the current post.
 */
function verquin_render_sections( ?int $post_id = null ): void {
	if ( ! function_exists( 'have_rows' ) ) {
		return;
	}

	$post_id = $post_id ?? get_the_ID();

	if ( ! $post_id || ! have_rows( 'page_sections', $post_id ) ) {
		return;
	}

	while ( have_rows( 'page_sections', $post_id ) ) {
		the_row();

		$layout = (string) get_row_layout();

		if ( '' === $layout ) {
			continue;
		}

		verquin_section( $layout, verquin_section_args( $layout ) );
	}
}
