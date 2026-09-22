<?php
/**
 * Theme helpers.
 *
 * @package Trinetix
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Get all theme settings.
 *
 * @return array<string, mixed>
 */
function trinetix_get_settings(): array {
	$defaults = trinetix_default_settings();
	$stored   = get_option( 'trinetix_settings', array() );

	if ( ! is_array( $stored ) ) {
		$stored = array();
	}

	return wp_parse_args( $stored, $defaults );
}

/**
 * Get a single setting value.
 *
 * @param string $key     Setting key.
 * @param mixed  $default Fallback.
 * @return mixed
 */
function trinetix_get_setting( string $key, $default = '' ) {
	$settings = trinetix_get_settings();

	if ( array_key_exists( $key, $settings ) && '' !== $settings[ $key ] && null !== $settings[ $key ] ) {
		return $settings[ $key ];
	}

	return $default;
}

/**
 * Default settings values.
 *
 * @return array<string, mixed>
 */
function trinetix_default_settings(): array {
	return array(
		'company_name'           => 'Trinetix Consulting LLC',
		'brand_tagline'          => 'Vision Beyond Technology',
		'header_cta_text'        => 'Contact Us',
		'header_cta_url'         => '#contact',
		'hero_eyebrow'           => '',
		'hero_heading'           => "We engineer\nintelligent enterprises",
		'hero_subline'           => 'for a world that keeps moving.',
		'hero_description'       => 'The terrain is shifting faster than most companies can react. We help leaders connect strategy, AI, data and engineering so transformation moves from ambition to measurable business value.',
		'hero_cta_text'          => 'See how intelligent enterprises are engineered',
		'hero_cta_url'           => '#approach',
		'hero_cta_secondary_text'=> '',
		'hero_cta_secondary_url' => '',
		'hero_video_enabled'     => 1,
		'hero_video_id'          => 0,
		'hero_video_mobile_id'   => 0,
		'hero_poster_id'         => 0,
		'hero_playback_rate'     => '1',
		'hero_capabilities_json' => '',
		'intro_heading'          => 'AI creates opportunity. We make sure it creates business value.',
		'intro_content'          => '',
		'intro_cta_text'         => 'Explore Our Capabilities',
		'intro_cta_url'          => '#services',
		'intro_video_image_id'   => 0,
		'intro_video_label'      => "From strategy\nto measurable outcomes",
		'intro_video_url'        => '',
		'services_title'         => 'Our Services',
		'industries_title'       => 'Our Industry Expertise',
		'work_title'             => 'Our Work',
		'testimonials_title'     => 'What Our Customers Say',
		'partners_title'         => 'Our Partner Ecosystem',
		'knowledge_title'        => 'Knowledge Hub',
		'knowledge_cta_text'     => 'Explore More Insights',
		'knowledge_cta_url'      => '',
		'contact_heading'        => 'How can we help you?',
		'contact_copy'           => 'Tell us what you are trying to improve, modernize or build. We can help shape the right approach from strategy and architecture through engineering and delivery.',
		'contact_email'          => 'info@trinetixconsulting.com',
		'contact_phone'          => '',
		'contact_address'        => '',
		'contact_recipient'      => 'info@trinetixconsulting.com',
		'contact_pills'          => "Artificial Intelligence\nData & Analytics\nDigital Engineering\nExperience",
		'social_linkedin'        => '',
		'social_twitter'         => '',
		'social_youtube'         => '',
		'social_facebook'        => '',
		'footer_intro'           => 'We engineer intelligent enterprises by connecting domain strategy, cognitive architecture, data and modern engineering into one practical transformation model.',
		'footer_copyright'       => '© 2026 Trinetix Consulting LLC. All rights reserved.',
		'footer_privacy_url'     => '',
		'footer_terms_url'       => '',
		'seo_default_title'      => '',
		'seo_default_description'=> '',
		'seo_og_image_id'        => 0,
		'organization_url'       => 'https://trinetixconsulting.com/',
	);
}

/**
 * Theme asset URI helper.
 *
 * @param string $relative Relative path under theme.
 * @return string
 */
function trinetix_asset_uri( string $relative ): string {
	return trailingslashit( TRINETIX_URI ) . ltrim( $relative, '/' );
}

/**
 * Theme asset path helper.
 *
 * @param string $relative Relative path under theme.
 * @return string
 */
function trinetix_asset_path( string $relative ): string {
	return trailingslashit( TRINETIX_DIR ) . ltrim( $relative, '/' );
}

/**
 * Filemtime-based version for cache busting.
 *
 * @param string $relative Relative asset path.
 * @return string
 */
function trinetix_asset_version( string $relative ): string {
	$path = trinetix_asset_path( $relative );
	if ( is_readable( $path ) ) {
		return (string) filemtime( $path );
	}

	return TRINETIX_VERSION;
}

/**
 * Render site logo HTML with fallback to theme logo.
 *
 * @param string $class CSS class for the img.
 * @return string
 */
function trinetix_get_logo_html( string $class = '' ): string {
	$company = function_exists( 'trinetix_get_hf' )
		? (string) trinetix_get_hf( 'header_heading', trinetix_get_setting( 'company_name', get_bloginfo( 'name' ) ) )
		: (string) trinetix_get_setting( 'company_name', get_bloginfo( 'name' ) );
	$tagline = function_exists( 'trinetix_get_hf' )
		? (string) trinetix_get_hf( 'header_tagline', trinetix_get_setting( 'brand_tagline', 'Vision Beyond Technology' ) )
		: (string) trinetix_get_setting( 'brand_tagline', 'Vision Beyond Technology' );
	$show_tagline = function_exists( 'trinetix_get_hf' ) ? (int) trinetix_get_hf( 'header_show_tagline', 1 ) === 1 : true;

	$logo_id = 0;
	if ( function_exists( 'trinetix_get_hf' ) ) {
		$logo_id = (int) trinetix_get_hf( 'header_logo_id', 0 );
	}
	if ( ! $logo_id ) {
		$logo_id = (int) get_theme_mod( 'custom_logo' );
	}
	$img = '';

	if ( $logo_id ) {
		$img = wp_get_attachment_image( $logo_id, 'full', false, array( 'alt' => $company, 'class' => $class ) );
	}

	if ( ! $img ) {
		$img = sprintf(
			'<img src="%1$s" alt="%2$s"%3$s />',
			esc_url( trinetix_asset_uri( 'assets/images/logo.png' ) ),
			esc_attr( $company ),
			$class ? ' class="' . esc_attr( $class ) . '"' : ''
		);
	}

	$tagline_html = $show_tagline && $tagline
		? '<span class="brand-tagline">' . esc_html( $tagline ) . '</span>'
		: '';

	return sprintf(
		'<a class="brand" href="%1$s" aria-label="%2$s">%3$s%4$s</a>',
		esc_url( home_url( '/' ) ),
		esc_attr( $company ),
		$img,
		$tagline_html
	);
}

/**
 * Arrow icon markup.
 *
 * @param string $variant light|dark.
 * @return string
 */
function trinetix_arrow_icon( string $variant = 'light' ): string {
	$file = ( 'dark' === $variant ) ? 'assets/icons/arrow-dark.svg' : 'assets/icons/arrow-light.svg';
	$path = trinetix_asset_path( $file );

	if ( ! is_readable( $path ) ) {
		$file = 'assets/icons/arrow-light.svg';
	}

	return sprintf(
		'<img class="arrow-img" src="%s" alt="" />',
		esc_url( trinetix_asset_uri( $file ) )
	);
}

/**
 * Ordered CPT query helper.
 *
 * @param string               $post_type Post type.
 * @param array<string, mixed> $args      Extra args.
 * @return WP_Query
 */
function trinetix_ordered_query( string $post_type, array $args = array() ): WP_Query {
	$defaults = array(
		'post_type'              => $post_type,
		'post_status'            => 'publish',
		'posts_per_page'         => -1,
		'orderby'                => array(
			'menu_order' => 'ASC',
			'title'      => 'ASC',
		),
		'no_found_rows'          => true,
		'update_post_meta_cache' => true,
		'update_post_term_cache' => false,
	);

	return new WP_Query( wp_parse_args( $args, $defaults ) );
}

/**
 * Attachment image or placeholder URL.
 *
 * @param int    $attachment_id Attachment ID.
 * @param string $size          Image size.
 * @param string $fallback      Fallback URL.
 * @return string
 */
function trinetix_image_url( int $attachment_id, string $size = 'large', string $fallback = '' ): string {
	if ( $attachment_id > 0 ) {
		$url = wp_get_attachment_image_url( $attachment_id, $size );
		if ( $url ) {
			return $url;
		}
	}

	return $fallback;
}

/**
 * Decode JSON setting safely.
 *
 * @param string $key Setting key.
 * @return array<int, mixed>
 */
function trinetix_get_json_setting( string $key ): array {
	$raw = (string) trinetix_get_setting( $key, '' );
	if ( '' === trim( $raw ) ) {
		return array();
	}

	$data = json_decode( $raw, true );
	return is_array( $data ) ? $data : array();
}

/**
 * Default hero capability cards.
 *
 * @return array<int, array<string, string>>
 */
function trinetix_default_hero_capabilities(): array {
	return array(
		array(
			'number' => '01',
			'title'  => 'Domain & Strategy',
			'url'    => '#approach',
			'class'  => 'capability-strategy',
			'icon'   => 'star',
		),
		array(
			'number' => '02',
			'title'  => 'Cognitive Architecture',
			'url'    => '#approach',
			'class'  => 'capability-cognitive',
			'icon'   => 'compass',
		),
		array(
			'number' => '03',
			'title'  => 'Harness Engineering',
			'url'    => '#services',
			'class'  => 'capability-engineering',
			'icon'   => 'harness',
		),
	);
}

/**
 * Render capability icon SVG.
 *
 * @param string $icon Icon key.
 * @return void
 */
function trinetix_capability_icon( string $icon ): void {
	$icons = array(
		'star'    => '<path d="M16 2v28M2 16h28M6.1 6.1l19.8 19.8M25.9 6.1L6.1 25.9"></path>',
		'compass' => '<circle cx="16" cy="16" r="12.25"></circle><path d="M20.7 11.3l-3.1 6.3-6.3 3.1 3.1-6.3 6.3-3.1z"></path>',
		'harness' => '<path d="M10.3 5.4c-3 2.7-3.3 7.3-.6 10.3l6.9 7.7c2.1 2.3 5.7 2.5 8 .4 2.3-2.1 2.5-5.7.4-8l-2.1-2.3"></path><path d="M21.7 26.6c3-2.7 3.3-7.3.6-10.3l-6.9-7.7c-2.1-2.3-5.7-2.5-8-.4-2.3 2.1-2.5 5.7-.4 8l2.1 2.3"></path>',
	);

	$path = $icons[ $icon ] ?? $icons['star'];
	$class = 'capability-icon capability-icon-' . sanitize_html_class( $icon );

	echo '<span class="' . esc_attr( $class ) . '" aria-hidden="true"><svg viewBox="0 0 32 32">' . $path . '</svg></span>'; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- static SVG paths.
}

/**
 * Featured image URL with external meta / fallback.
 *
 * @param int    $post_id  Post ID.
 * @param string $size     Image size.
 * @param string $fallback Fallback URL.
 * @return string
 */
function trinetix_get_card_image_url( int $post_id, string $size = 'large', string $fallback = '' ): string {
	if ( $post_id > 0 && has_post_thumbnail( $post_id ) ) {
		$url = get_the_post_thumbnail_url( $post_id, $size );
		if ( $url ) {
			return $url;
		}
	}

	$external = get_post_meta( $post_id, '_trinetix_external_image', true );
	if ( is_string( $external ) && '' !== $external ) {
		return $external;
	}

	return $fallback;
}

/**
 * Split title for accent span (last word).
 *
 * @param string $title Full title.
 * @return array{0:string,1:string}
 */
function trinetix_split_accent_title( string $title ): array {
	$title = trim( $title );
	$parts = preg_split( '/\s+/', $title );
	if ( ! is_array( $parts ) || count( $parts ) < 2 ) {
		return array( $title, '' );
	}

	$accent = array_pop( $parts );
	return array( implode( ' ', $parts ), (string) $accent );
}

/**
 * Flat nav walker — outputs only anchor tags (no ul/li).
 */
if ( ! class_exists( 'Trinetix_Flat_Nav_Walker' ) ) {
	/**
	 * Walker that prints bare <a> elements for primary nav CSS.
	 */
	class Trinetix_Flat_Nav_Walker extends Walker_Nav_Menu {
		/**
		 * @param string   $output Used to append additional content (passed by reference).
		 * @param int      $depth  Depth of menu item.
		 * @param stdClass $args   An object of wp_nav_menu() arguments.
		 */
		public function start_lvl( &$output, $depth = 0, $args = null ) {} // phpcs:ignore Generic.CodeAnalysis.UnusedFunctionParameter

		/**
		 * @param string   $output Used to append additional content (passed by reference).
		 * @param int      $depth  Depth of menu item.
		 * @param stdClass $args   An object of wp_nav_menu() arguments.
		 */
		public function end_lvl( &$output, $depth = 0, $args = null ) {} // phpcs:ignore Generic.CodeAnalysis.UnusedFunctionParameter

		/**
		 * @param string   $output Used to append additional content (passed by reference).
		 * @param WP_Post  $item   Menu item data object.
		 * @param int      $depth  Depth of menu item.
		 * @param stdClass $args   An object of wp_nav_menu() arguments.
		 * @param int      $id     Current item ID.
		 */
		public function start_el( &$output, $item, $depth = 0, $args = null, $id = 0 ) { // phpcs:ignore Generic.CodeAnalysis.UnusedFunctionParameter
			$title = isset( $item->title ) ? $item->title : '';
			$url   = isset( $item->url ) ? $item->url : '';
			$class = empty( $item->classes ) ? array() : (array) $item->classes;
			$class = array_filter( array_map( 'sanitize_html_class', $class ) );

			$atts = array(
				'href' => $url ? $url : '#',
			);

			if ( in_array( 'current-menu-item', (array) $item->classes, true ) ) {
				$atts['aria-current'] = 'page';
			}

			$attributes = '';
			foreach ( $atts as $attr => $value ) {
				if ( is_scalar( $value ) && '' !== $value ) {
					$value       = ( 'href' === $attr ) ? esc_url( (string) $value ) : esc_attr( (string) $value );
					$attributes .= ' ' . $attr . '="' . $value . '"';
				}
			}

			$class_attr = $class ? ' class="' . esc_attr( implode( ' ', $class ) ) . '"' : '';
			$output    .= '<a' . $class_attr . $attributes . '>' . esc_html( $title ) . '</a>';
		}

		/**
		 * @param string   $output Used to append additional content (passed by reference).
		 * @param WP_Post  $item   Page data object. Not used.
		 * @param int      $depth  Depth of page. Not Used.
		 * @param stdClass $args   An object of wp_nav_menu() arguments.
		 */
		public function end_el( &$output, $item, $depth = 0, $args = null ) {} // phpcs:ignore Generic.CodeAnalysis.UnusedFunctionParameter
	}
}

/**
 * Render a footer directory row from a nav menu location or explicit menu ID.
 *
 * Top-level items become column headings (h4); children become links.
 * If items are flat (no children), they render as links under a single column.
 *
 * @param string $location Menu location slug.
 * @param string $title    Row title.
 * @param int    $menu_id  Optional explicit menu term ID (from Header & Footer settings).
 * @return void
 */
function trinetix_render_footer_row( string $location, string $title, int $menu_id = 0 ): void {
	if ( $menu_id <= 0 ) {
		$locations = get_nav_menu_locations();
		if ( empty( $locations[ $location ] ) ) {
			return;
		}
		$menu_id = (int) $locations[ $location ];
	}

	$items = wp_get_nav_menu_items( $menu_id );
	if ( empty( $items ) || ! is_array( $items ) ) {
		return;
	}

	$by_parent = array();
	foreach ( $items as $item ) {
		$parent = (int) $item->menu_item_parent;
		if ( ! isset( $by_parent[ $parent ] ) ) {
			$by_parent[ $parent ] = array();
		}
		$by_parent[ $parent ][] = $item;
	}

	$top = $by_parent[0] ?? array();
	if ( empty( $top ) ) {
		return;
	}

	echo '<div class="footer-row">';
	echo '<div class="footer-row-title">' . esc_html( $title ) . '</div>';
	echo '<div class="footer-columns">';

	$has_children = false;
	foreach ( $top as $parent_item ) {
		if ( ! empty( $by_parent[ (int) $parent_item->ID ] ) ) {
			$has_children = true;
			break;
		}
	}

	if ( $has_children ) {
		foreach ( $top as $parent_item ) {
			echo '<div class="footer-col">';
			echo '<h4>' . esc_html( $parent_item->title ) . '</h4>';
			$children = $by_parent[ (int) $parent_item->ID ] ?? array();
			if ( empty( $children ) && ! empty( $parent_item->url ) && '#' !== $parent_item->url ) {
				printf( '<a href="%s">%s</a>', esc_url( $parent_item->url ), esc_html( $parent_item->title ) );
			}
			foreach ( $children as $child ) {
				printf( '<a href="%s">%s</a>', esc_url( $child->url ), esc_html( $child->title ) );
			}
			echo '</div>';
		}
	} else {
		echo '<div class="footer-col">';
		foreach ( $top as $item ) {
			printf( '<a href="%s">%s</a>', esc_url( $item->url ), esc_html( $item->title ) );
		}
		echo '</div>';
	}

	echo '</div></div>';
}

/**
 * Primary menu fallback links.
 */
function trinetix_primary_menu_fallback(): void {
	$home  = trailingslashit( home_url( '/' ) );
	$links = array(
		'Approach'      => $home . '#approach',
		'Services'      => $home . '#services',
		'Industries'    => $home . '#industries',
		'Our Work'      => $home . '#work',
		'Knowledge Hub' => $home . '#insights',
		'Contact'       => $home . '#contact',
	);
	foreach ( $links as $label => $url ) {
		echo '<a href="' . esc_url( $url ) . '">' . esc_html( $label ) . '</a>';
	}
}

