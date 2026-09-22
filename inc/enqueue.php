<?php
/**
 * Enqueue styles and scripts.
 *
 * @package Trinetix
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Front-end assets.
 */
function trinetix_enqueue_assets(): void {
	wp_enqueue_style(
		'trinetix-poppins',
		'https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap',
		array(),
		null
	);

	$main_css = 'assets/css/main.css';
	if ( is_readable( trinetix_asset_path( $main_css ) ) ) {
		wp_enqueue_style(
			'trinetix-main',
			trinetix_asset_uri( $main_css ),
			array( 'trinetix-poppins' ),
			trinetix_asset_version( $main_css )
		);
	}

	$responsive_css = 'assets/css/responsive.css';
	if ( is_readable( trinetix_asset_path( $responsive_css ) ) ) {
		wp_enqueue_style(
			'trinetix-responsive',
			trinetix_asset_uri( $responsive_css ),
			array( 'trinetix-main' ),
			trinetix_asset_version( $responsive_css )
		);
	}

	wp_enqueue_style(
		'trinetix-style',
		get_stylesheet_uri(),
		array( 'trinetix-responsive' ),
		trinetix_asset_version( 'style.css' )
	);

	$is_front   = is_front_page();
	$needs_form = $is_front || is_page( 'contact' );

	$scripts = array(
		'trinetix-navigation' => array(
			'file' => 'assets/js/navigation.js',
			'deps' => array(),
			'when' => true,
		),
		'trinetix-main'       => array(
			'file' => 'assets/js/main.js',
			'deps' => array( 'trinetix-navigation' ),
			'when' => true,
		),
		'trinetix-hero-video' => array(
			'file' => 'assets/js/hero-video.js',
			'deps' => array(),
			'when' => $is_front,
		),
		'trinetix-sliders'    => array(
			'file' => 'assets/js/sliders.js',
			'deps' => array(),
			'when' => $is_front,
		),
		'trinetix-contact'    => array(
			'file' => 'assets/js/contact.js',
			'deps' => array(),
			'when' => $needs_form,
		),
	);

	foreach ( $scripts as $handle => $config ) {
		if ( empty( $config['when'] ) ) {
			continue;
		}

		$path = trinetix_asset_path( $config['file'] );
		if ( ! is_readable( $path ) ) {
			continue;
		}

		wp_enqueue_script(
			$handle,
			trinetix_asset_uri( $config['file'] ),
			$config['deps'],
			trinetix_asset_version( $config['file'] ),
			array(
				'in_footer' => true,
				'strategy'  => 'defer',
			)
		);
	}

	$settings = trinetix_get_settings();

	wp_localize_script(
		'trinetix-main',
		'trinetixData',
		array(
			'ajaxUrl'      => admin_url( 'admin-ajax.php' ),
			'homeUrl'      => home_url( '/' ),
			'themeUri'     => TRINETIX_URI,
			'isFrontPage'  => $is_front,
			'playbackRate' => (string) ( $settings['hero_playback_rate'] ?? '1' ),
		)
	);

	if ( $needs_form ) {
		$contact_handle = wp_script_is( 'trinetix-contact', 'enqueued' ) ? 'trinetix-contact' : 'trinetix-main';
		wp_localize_script(
			$contact_handle,
			'trinetixContact',
			array(
				'ajaxUrl' => admin_url( 'admin-ajax.php' ),
				'nonce'   => wp_create_nonce( 'trinetix_contact' ),
				'i18n'    => array(
					'success' => __( 'Thank you. Your message has been sent.', 'trinetix' ),
					'error'   => __( 'Something went wrong. Please try again.', 'trinetix' ),
					'invalid' => __( 'Please check the form and try again.', 'trinetix' ),
				),
			)
		);
	}

	// Industry + testimonial JSON for homepage interactions.
	if ( $is_front && wp_script_is( 'trinetix-sliders', 'enqueued' ) ) {
		wp_localize_script(
			'trinetix-sliders',
			'trinetixIndustries',
			trinetix_get_industries_payload()
		);
		wp_localize_script(
			'trinetix-sliders',
			'trinetixTestimonials',
			trinetix_get_testimonials_payload()
		);
	}
}
add_action( 'wp_enqueue_scripts', 'trinetix_enqueue_assets' );

/**
 * Industries payload for JS.
 *
 * @return array<int, array<string, string>>
 */
function trinetix_get_industries_payload(): array {
	$query = trinetix_ordered_query( 'industry' );
	$items = array();

	if ( $query->have_posts() ) {
		while ( $query->have_posts() ) {
			$query->the_post();
			$items[] = array(
				'title' => get_the_title(),
				'text'  => has_excerpt() ? get_the_excerpt() : wp_trim_words( wp_strip_all_tags( get_the_content() ), 36 ),
				'image' => get_the_post_thumbnail_url( get_the_ID(), 'full' ) ?: '',
				'url'   => get_permalink(),
			);
		}
		wp_reset_postdata();
	}

	if ( empty( $items ) ) {
		$items = array(
			array(
				'title' => 'Healthcare',
				'text'  => 'Modernize care platforms, operations and data experiences with secure technology designed around patients, practitioners and performance.',
				'image' => 'https://images.unsplash.com/photo-1576091160399-112ba8d25d1d?auto=format&fit=crop&w=2100&q=90',
				'url'   => home_url( '/#contact' ),
			),
			array(
				'title' => 'Life Sciences',
				'text'  => 'Accelerate research, therapy and commercial operations with connected data, AI and digital platforms.',
				'image' => 'https://images.unsplash.com/photo-1532187863486-abf9dbad1b69?auto=format&fit=crop&w=2100&q=90',
				'url'   => home_url( '/#contact' ),
			),
			array(
				'title' => 'Financial Services',
				'text'  => 'Modernize customer journeys, risk operations and core platforms with secure, resilient engineering.',
				'image' => 'https://images.unsplash.com/photo-1556742049-0cfed4f6a45d?auto=format&fit=crop&w=2100&q=90',
				'url'   => home_url( '/#contact' ),
			),
			array(
				'title' => 'High Tech',
				'text'  => 'Scale product engineering, data platforms and AI capabilities for fast-moving technology businesses.',
				'image' => 'https://images.unsplash.com/photo-1518770660439-4636190af475?auto=format&fit=crop&w=2100&q=90',
				'url'   => home_url( '/#contact' ),
			),
			array(
				'title' => 'Consumer',
				'text'  => 'Connect commerce, experience and operations so brands can move faster with clearer customer insight.',
				'image' => 'https://images.unsplash.com/photo-1441986300917-64674bd600d8?auto=format&fit=crop&w=2100&q=90',
				'url'   => home_url( '/#contact' ),
			),
			array(
				'title' => 'Manufacturing',
				'text'  => 'Improve operations, supply chain visibility and industrial platforms with practical digital transformation.',
				'image' => 'https://images.unsplash.com/photo-1581091226825-a6a2a5aee158?auto=format&fit=crop&w=2100&q=90',
				'url'   => home_url( '/#contact' ),
			),
		);
	}

	return $items;
}

/**
 * Testimonials payload for JS.
 *
 * @return array<int, array<string, mixed>>
 */
function trinetix_get_testimonials_payload(): array {
	$query = trinetix_ordered_query( 'testimonial' );
	$items = array();

	if ( $query->have_posts() ) {
		while ( $query->have_posts() ) {
			$query->the_post();
			$id     = get_the_ID();
			$role   = (string) get_post_meta( $id, '_trinetix_role', true );
			$company = (string) get_post_meta( $id, '_trinetix_company', true );
			$rating = (int) get_post_meta( $id, '_trinetix_rating', true );
			$chip   = (string) get_post_meta( $id, '_trinetix_chip_label', true );

			$items[] = array(
				'name'    => get_the_title(),
				'quote'   => has_excerpt() ? get_the_excerpt() : wp_strip_all_tags( get_the_content() ),
				'role'    => $role ? $role : $company,
				'image'   => get_the_post_thumbnail_url( $id, 'large' ) ?: '',
				'chip'    => $chip ? $chip : get_the_title(),
				'rating'  => $rating > 0 ? $rating : 5,
			);
		}
		wp_reset_postdata();
	}

	if ( empty( $items ) ) {
		$items = array(
			array(
				'name'   => 'Enterprise Technology Leader',
				'quote'  => 'Trinetix combines strategic thinking with practical delivery. The team keeps the work focused, transparent and aligned to the business outcome we are trying to achieve.',
				'role'   => 'Placeholder testimonial — replace with approved client quote',
				'image'  => 'https://images.unsplash.com/photo-1560250097-0b93528c311a?auto=format&fit=crop&w=800&q=92',
				'chip'   => 'Technology',
				'rating' => 5,
			),
			array(
				'name'   => 'Operations Executive',
				'quote'  => 'They helped us move from fragmented initiatives to a clearer operating model with measurable delivery milestones.',
				'role'   => 'Placeholder testimonial — replace with approved client quote',
				'image'  => 'https://images.unsplash.com/photo-1573496359142-b8d87734a5a2?auto=format&fit=crop&w=800&q=92',
				'chip'   => 'Operations',
				'rating' => 5,
			),
			array(
				'name'   => 'Digital Transformation Lead',
				'quote'  => 'The combination of strategy, architecture and engineering discipline made execution far more predictable.',
				'role'   => 'Placeholder testimonial — replace with approved client quote',
				'image'  => 'https://images.unsplash.com/photo-1500648767791-00dcc994a43e?auto=format&fit=crop&w=800&q=92',
				'chip'   => 'Digital',
				'rating' => 5,
			),
			array(
				'name'   => 'Strategy Sponsor',
				'quote'  => 'We appreciated the clarity, pace and business-first framing across every engagement phase.',
				'role'   => 'Placeholder testimonial — replace with approved client quote',
				'image'  => 'https://images.unsplash.com/photo-1551836022-d5d88e9218df?auto=format&fit=crop&w=800&q=92',
				'chip'   => 'Strategy',
				'rating' => 5,
			),
		);
	}

	return $items;
}
