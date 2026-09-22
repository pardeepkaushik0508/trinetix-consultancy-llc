<?php
/**
 * One-time demo content seed on theme activation.
 * Never overwrites existing content. Guarded by option trinetix_initial_content_seeded.
 *
 * @package Trinetix
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Run seed after theme switch.
 */
function trinetix_maybe_seed_demo_content(): void {
	if ( get_option( 'trinetix_initial_content_seeded' ) ) {
		return;
	}

	// Ensure CPTs are registered before inserting.
	if ( ! post_type_exists( 'service' ) ) {
		trinetix_register_post_types();
	}
	if ( ! taxonomy_exists( 'case_study_industry' ) && function_exists( 'trinetix_register_taxonomies' ) ) {
		trinetix_register_taxonomies();
	}

	trinetix_seed_pages_and_reading();
	trinetix_seed_approach_items();
	trinetix_seed_services();
	trinetix_seed_industries();
	trinetix_seed_case_studies();
	trinetix_seed_testimonials();
	trinetix_seed_partners();
	trinetix_seed_knowledge_posts();
	trinetix_seed_primary_menu();
	trinetix_seed_footer_menus();

	update_option( 'trinetix_initial_content_seeded', 1 );
	flush_rewrite_rules( false );
}
add_action( 'after_switch_theme', 'trinetix_maybe_seed_demo_content' );

/**
 * Create core pages and set static front page (without overwriting existing reading settings).
 */
function trinetix_seed_pages_and_reading(): void {
	$home_id = trinetix_seed_page(
		'Home',
		'',
		array(
			'post_content' => '<!-- Front page content is rendered by front-page.php -->',
		)
	);

	$blog_id = trinetix_seed_page( 'Knowledge Hub', 'knowledge-hub' );
	trinetix_seed_page( 'Approach', 'approach', array( 'post_content' => '<p>Learn how we engineer intelligent enterprises.</p>' ) );
	trinetix_seed_page( 'Services', 'services-overview', array( 'post_content' => '<p>Explore our services.</p>' ) );
	trinetix_seed_page( 'Industries', 'industries-overview', array( 'post_content' => '<p>Explore our industry expertise.</p>' ) );
	trinetix_seed_page( 'Our Work', 'our-work', array( 'post_content' => '<p>See selected case studies.</p>' ) );
	trinetix_seed_page(
		'Privacy Policy',
		'privacy-policy',
		array(
			'post_content' => '<p>Update this Privacy Policy with your legal copy.</p>',
		)
	);
	trinetix_seed_page(
		'Terms & Conditions',
		'terms-conditions',
		array(
			'post_content' => '<p>Update these Terms &amp; Conditions with your legal copy.</p>',
		)
	);
	trinetix_seed_page(
		'Contact',
		'contact',
		array(
			'post_content' => '<p>Reach us via the contact form on the homepage or email info@trinetixconsulting.com.</p>',
		)
	);

	if ( 'posts' === get_option( 'show_on_front' ) && $home_id ) {
		update_option( 'show_on_front', 'page' );
		update_option( 'page_on_front', $home_id );
		if ( $blog_id ) {
			update_option( 'page_for_posts', $blog_id );
		}
	}

	$settings = get_option( 'trinetix_settings', array() );
	if ( ! is_array( $settings ) ) {
		$settings = array();
	}

	$privacy = get_page_by_path( 'privacy-policy' );
	$terms   = get_page_by_path( 'terms-conditions' );

	$changed = false;
	if ( $privacy && empty( $settings['footer_privacy_url'] ) ) {
		$settings['footer_privacy_url'] = get_permalink( $privacy );
		$changed = true;
	}
	if ( $terms && empty( $settings['footer_terms_url'] ) ) {
		$settings['footer_terms_url'] = get_permalink( $terms );
		$changed = true;
	}
	if ( empty( $settings['knowledge_cta_url'] ) && $blog_id ) {
		$settings['knowledge_cta_url'] = get_permalink( $blog_id );
		$changed = true;
	}
	if ( $changed ) {
		update_option( 'trinetix_settings', $settings );
	}
}

/**
 * Find a published/draft page by title.
 *
 * @param string $title Page title.
 * @return int
 */
function trinetix_find_page_by_title( string $title ): int {
	$query = new WP_Query(
		array(
			'post_type'              => 'page',
			'title'                  => $title,
			'post_status'            => array( 'publish', 'draft', 'private' ),
			'posts_per_page'         => 1,
			'fields'                 => 'ids',
			'no_found_rows'          => true,
			'update_post_meta_cache' => false,
			'update_post_term_cache' => false,
		)
	);

	if ( ! empty( $query->posts[0] ) ) {
		return (int) $query->posts[0];
	}

	return 0;
}

/**
 * Insert a page if a page with the same title/slug does not already exist.
 *
 * @param string               $title Page title.
 * @param string               $slug  Optional slug.
 * @param array<string, mixed> $args  Extra wp_insert_post args.
 * @return int Page ID or 0.
 */
function trinetix_seed_page( string $title, string $slug = '', array $args = array() ): int {
	if ( $slug ) {
		$existing = get_page_by_path( $slug );
		if ( $existing ) {
			return (int) $existing->ID;
		}
	}

	$found = trinetix_find_page_by_title( $title );
	if ( $found ) {
		return $found;
	}

	$data = array_merge(
		array(
			'post_title'   => $title,
			'post_name'    => $slug ? $slug : sanitize_title( $title ),
			'post_status'  => 'publish',
			'post_type'    => 'page',
			'post_content' => '',
		),
		$args
	);

	$id = wp_insert_post( $data, true );
	return is_wp_error( $id ) ? 0 : (int) $id;
}

/**
 * True when the CPT already has any posts (never seed over existing).
 *
 * @param string $post_type Post type.
 * @return bool
 */
function trinetix_cpt_has_content( string $post_type ): bool {
	$query = new WP_Query(
		array(
			'post_type'      => $post_type,
			'post_status'    => 'any',
			'posts_per_page' => 1,
			'fields'         => 'ids',
			'no_found_rows'  => true,
		)
	);
	return $query->have_posts();
}

/**
 * Insert a CPT item.
 *
 * @param string               $post_type Post type.
 * @param string               $title     Title.
 * @param string               $content   Content.
 * @param string               $excerpt   Excerpt.
 * @param int                  $order     Menu order.
 * @param array<string, mixed> $meta      Meta key => value.
 * @return int
 */
function trinetix_seed_cpt_item( string $post_type, string $title, string $content, string $excerpt, int $order = 0, array $meta = array() ): int {
	$id = wp_insert_post(
		array(
			'post_type'    => $post_type,
			'post_title'   => $title,
			'post_content' => $content,
			'post_excerpt' => $excerpt,
			'post_status'  => 'publish',
			'menu_order'   => $order,
		),
		true
	);

	if ( is_wp_error( $id ) ) {
		return 0;
	}

	foreach ( $meta as $key => $value ) {
		update_post_meta( (int) $id, $key, $value );
	}

	return (int) $id;
}

/**
 * Seed approach items.
 */
function trinetix_seed_approach_items(): void {
	if ( trinetix_cpt_has_content( 'approach_item' ) ) {
		return;
	}

	$items = array(
		array( 'Domain & Strategy', 'We help leaders define the right outcomes, prioritize the work that matters, and align technology investment to business value.', 1 ),
		array( 'Cognitive Architecture', 'We design how knowledge, data and AI capabilities work together so intelligence is usable across the enterprise.', 2 ),
		array( 'Harness Engineering', 'We engineer production-ready platforms and delivery systems that ship reliably and keep value compounding after launch.', 3 ),
	);

	foreach ( $items as $i => $item ) {
		trinetix_seed_cpt_item( 'approach_item', $item[0], $item[1], $item[1], $item[2] );
	}
}

/**
 * Seed services from homepage reference copy.
 */
function trinetix_seed_services(): void {
	if ( trinetix_cpt_has_content( 'service' ) ) {
		return;
	}

	$items = array(
		array( 'Artificial Intelligence', 'Applied AI, intelligent automation and enterprise copilots built around measurable business outcomes.', 1 ),
		array( 'Data & Analytics', 'Trusted data foundations and decision intelligence that turn enterprise information into action.', 2 ),
		array( 'Digital Engineering', 'Cloud-native platforms, APIs and product engineering built for speed, resilience and lower delivery risk.', 3 ),
		array( 'Experience', 'Connected customer and employee experiences across digital products, portals and enterprise platforms.', 4 ),
	);

	foreach ( $items as $item ) {
		trinetix_seed_cpt_item(
			'service',
			$item[0],
			'<p>' . $item[1] . '</p>',
			$item[1],
			$item[2],
			array(
				'_trinetix_cta_label' => 'Talk about this capability',
				'_trinetix_cta_url'   => home_url( '/#contact' ),
			)
		);
	}
}

/**
 * Seed industries.
 */
function trinetix_seed_industries(): void {
	if ( trinetix_cpt_has_content( 'industry' ) ) {
		return;
	}

	$items = array(
		array( 'Healthcare', 'Modernize care platforms, operations and data experiences with secure technology designed around patients, practitioners and performance.', 1 ),
		array( 'Life Sciences', 'Accelerate research, therapy and commercial operations with connected data, AI and digital platforms.', 2 ),
		array( 'Financial Services', 'Modernize customer journeys, risk operations and core platforms with secure, resilient engineering.', 3 ),
		array( 'High Tech', 'Scale product engineering, data platforms and AI capabilities for fast-moving technology businesses.', 4 ),
		array( 'Consumer', 'Connect commerce, experience and operations so brands can move faster with clearer customer insight.', 5 ),
		array( 'Manufacturing', 'Improve operations, supply chain visibility and industrial platforms with practical digital transformation.', 6 ),
	);

	foreach ( $items as $item ) {
		trinetix_seed_cpt_item( 'industry', $item[0], '<p>' . $item[1] . '</p>', $item[1], $item[2] );
	}
}

/**
 * Seed case studies.
 */
function trinetix_seed_case_studies(): void {
	if ( trinetix_cpt_has_content( 'case_study' ) ) {
		return;
	}

	$items = array(
		array(
			'title'   => 'An on-premises AI Center of Excellence that resolves complex maintenance queries in 20 to 40 seconds.',
			'excerpt' => 'Built to keep enterprise knowledge close to the business, reduce resolution time and support operational teams with trusted AI capabilities.',
			'eyebrow' => 'AI Center of Excellence',
			'order'   => 1,
		),
		array(
			'title'   => 'AI-driven supply chain risk intelligence with quantified business impact.',
			'excerpt' => 'Combining predictive analytics and intelligent automation to identify supply chain risks earlier and improve decision velocity.',
			'eyebrow' => 'Agentic AI',
			'order'   => 2,
		),
		array(
			'title'   => '60% faster MVP creation through AI-assisted product engineering.',
			'excerpt' => 'Accelerating ideation, prototyping and delivery with AI-enabled workflows while maintaining quality and enterprise engineering discipline.',
			'eyebrow' => 'Digital Engineering',
			'order'   => 3,
		),
		array(
			'title'   => 'Building a modern healthcare data platform for better patient and therapy insights.',
			'excerpt' => 'Designed to unify fragmented information, improve access to insight and support better clinical and operational outcomes.',
			'eyebrow' => 'Healthcare Data',
			'order'   => 4,
		),
	);

	foreach ( $items as $item ) {
		trinetix_seed_cpt_item(
			'case_study',
			$item['title'],
			'<p>' . $item['excerpt'] . '</p>',
			$item['excerpt'],
			$item['order'],
			array(
				'_trinetix_eyebrow' => $item['eyebrow'],
			)
		);
	}
}

/**
 * Seed testimonials.
 */
function trinetix_seed_testimonials(): void {
	if ( trinetix_cpt_has_content( 'testimonial' ) ) {
		return;
	}

	$items = array(
		array( 'Enterprise Technology Leader', 'Trinetix combines strategic thinking with practical delivery. The team keeps the work focused, transparent and aligned to the business outcome we are trying to achieve.', 'Placeholder testimonial — replace with approved client quote', 'Technology', 1 ),
		array( 'Operations Executive', 'They helped us move from fragmented initiatives to a clearer operating model with measurable delivery milestones.', 'Placeholder testimonial — replace with approved client quote', 'Operations', 2 ),
		array( 'Digital Transformation Lead', 'The combination of strategy, architecture and engineering discipline made execution far more predictable.', 'Placeholder testimonial — replace with approved client quote', 'Digital', 3 ),
		array( 'Strategy Sponsor', 'We appreciated the clarity, pace and business-first framing across every engagement phase.', 'Placeholder testimonial — replace with approved client quote', 'Strategy', 4 ),
	);

	foreach ( $items as $item ) {
		trinetix_seed_cpt_item(
			'testimonial',
			$item[0],
			$item[1],
			$item[1],
			$item[4],
			array(
				'_trinetix_role'       => $item[2],
				'_trinetix_chip_label' => $item[3],
				'_trinetix_rating'     => '5',
			)
		);
	}
}

/**
 * Seed partners.
 */
function trinetix_seed_partners(): void {
	if ( trinetix_cpt_has_content( 'partner' ) ) {
		return;
	}

	$items = array(
		array( 'AWS', 'Cloud infrastructure, modernization and data platform enablement for enterprise-scale solutions.', 'aws', 1 ),
		array( 'Microsoft', 'Cloud, productivity, data and AI capabilities across the Microsoft enterprise ecosystem.', 'microsoft', 2 ),
		array( 'Google Cloud', 'Modern cloud, analytics and scalable architecture support for data-intensive businesses.', 'google', 3 ),
		array( 'Databricks', 'Lakehouse-driven data engineering and analytics foundations for advanced enterprise use cases.', 'databricks', 4 ),
		array( 'Cprime', 'Transformation and modern delivery support for product, platform and enterprise agility programs.', 'cprime', 5 ),
		array( 'Salesforce', 'Connected CRM, customer experience and workflow enablement across sales and service operations.', 'salesforce', 6 ),
		array( 'ServiceNow', 'Enterprise service workflows and operational efficiency through modern digital automation.', 'servicenow', 7 ),
		array( 'Atlassian', 'Collaboration, engineering delivery and work management for fast-moving product and technology teams.', 'atlassian', 8 ),
	);

	foreach ( $items as $item ) {
		trinetix_seed_cpt_item(
			'partner',
			$item[0],
			'<p>' . $item[1] . '</p>',
			$item[1],
			$item[3],
			array(
				'_trinetix_card_class' => $item[2],
			)
		);
	}
}

/**
 * Seed knowledge hub posts.
 */
function trinetix_seed_knowledge_posts(): void {
	$existing = new WP_Query(
		array(
			'post_type'      => 'post',
			'post_status'    => 'any',
			'posts_per_page' => 1,
			'fields'         => 'ids',
			'no_found_rows'  => true,
		)
	);
	if ( $existing->have_posts() ) {
		return;
	}

	$cats = array(
		'Insights'         => 0,
		'Transformation'   => 0,
		'Data & Analytics' => 0,
		'Engineering'      => 0,
		'Leadership'       => 0,
	);

	foreach ( array_keys( $cats ) as $name ) {
		$term = term_exists( $name, 'category' );
		if ( ! $term ) {
			$term = wp_insert_term( $name, 'category' );
		}
		if ( ! is_wp_error( $term ) ) {
			$cats[ $name ] = (int) ( is_array( $term ) ? $term['term_id'] : $term );
		}
	}

	$posts = array(
		array( 'Building the right foundation for enterprise AI.', 'Insights' ),
		array( 'How technology leaders move from isolated initiatives to connected business outcomes.', 'Transformation' ),
		array( 'Making enterprise data trusted, usable and actionable.', 'Data & Analytics' ),
		array( 'A practical approach to legacy modernization.', 'Engineering' ),
		array( 'Aligning people, process and technology for sustainable change.', 'Leadership' ),
	);

	foreach ( $posts as $i => $post ) {
		$id = wp_insert_post(
			array(
				'post_title'   => $post[0],
				'post_content' => '<p>' . $post[0] . '</p><p>Replace this placeholder article with your published insight.</p>',
				'post_excerpt' => $post[0],
				'post_status'  => 'publish',
				'post_type'    => 'post',
				'menu_order'   => $i + 1,
			),
			true
		);
		if ( ! is_wp_error( $id ) && ! empty( $cats[ $post[1] ] ) ) {
			wp_set_post_categories( (int) $id, array( $cats[ $post[1] ] ) );
		}
	}
}

/**
 * Primary navigation.
 */
function trinetix_seed_primary_menu(): void {
	$locations = get_theme_mod( 'nav_menu_locations', array() );
	if ( ! empty( $locations['primary'] ) ) {
		return;
	}

	$menu_name = 'Primary';
	$menu_id   = wp_create_nav_menu( $menu_name );
	if ( is_wp_error( $menu_id ) ) {
		return;
	}

	$home = trailingslashit( home_url( '/' ) );
	$items = array(
		'Approach'      => $home . '#approach',
		'Services'      => $home . '#services',
		'Industries'    => $home . '#industries',
		'Our Work'      => $home . '#work',
		'Knowledge Hub' => $home . '#insights',
		'Contact'       => $home . '#contact',
	);

	foreach ( $items as $label => $url ) {
		wp_update_nav_menu_item(
			(int) $menu_id,
			0,
			array(
				'menu-item-title'  => $label,
				'menu-item-url'    => $url,
				'menu-item-status' => 'publish',
				'menu-item-type'   => 'custom',
			)
		);
	}

	$locations['primary'] = (int) $menu_id;
	set_theme_mod( 'nav_menu_locations', $locations );
}

/**
 * Footer directory menus (hierarchical columns).
 */
function trinetix_seed_footer_menus(): void {
	$locations = get_theme_mod( 'nav_menu_locations', array() );

	$trees = array(
		'footer_ai'          => array(
			'name'  => 'Footer — Artificial Intelligence',
			'title' => 'Artificial Intelligence',
			'cols'  => array(
				'AI Strategy'    => array( 'AI readiness & roadmap', 'Enterprise AI use cases', 'AI operating model' ),
				'Automation'     => array( 'Intelligent workflows', 'Process automation', 'AI-assisted operations' ),
				'Generative AI'  => array( 'Copilots & assistants', 'Knowledge automation', 'Enterprise search' ),
				'AI Engineering' => array( 'Model integration', 'AI platform engineering', 'Responsible AI controls' ),
			),
		),
		'footer_data'        => array(
			'name' => 'Footer — Data & Analytics',
			'cols' => array(
				'Data Strategy'  => array( 'Data modernization', 'Governance & quality', 'Data operating model' ),
				'Analytics'      => array( 'Business intelligence', 'Decision intelligence', 'Advanced analytics' ),
				'Data Platforms' => array( 'Lakehouse architecture', 'Cloud data platforms', 'Data engineering' ),
				'Real-Time Data' => array( 'Streaming analytics', 'Operational analytics', 'Data APIs' ),
			),
		),
		'footer_engineering' => array(
			'name' => 'Footer — Digital Engineering',
			'cols' => array(
				'Application Engineering' => array( 'Cloud-native applications', 'API & microservices', 'Application modernization' ),
				'Cloud'                   => array( 'Cloud architecture', 'Migration & modernization', 'Platform engineering' ),
				'Quality Engineering'     => array( 'Test automation', 'Performance engineering', 'Release quality' ),
				'DevSecOps'               => array( 'CI/CD modernization', 'Cloud security', 'Engineering enablement' ),
			),
		),
		'footer_experience'  => array(
			'name' => 'Footer — Experience',
			'cols' => array(
				'Digital Experience'    => array( 'Web experience', 'Customer portals', 'Experience design' ),
				'Commerce'              => array( 'Digital commerce', 'B2B portals', 'Content & product experience' ),
				'Enterprise Platforms'  => array( 'Microsoft ecosystem', 'Salesforce', 'ServiceNow' ),
				'Company'               => array(
					array( 'Our approach', home_url( '/#approach' ) ),
					array( 'Our work', home_url( '/#work' ) ),
					array( 'Knowledge hub', home_url( '/#insights' ) ),
					array( 'Contact', home_url( '/#contact' ) ),
				),
			),
		),
	);

	$services_url = home_url( '/#services' );

	foreach ( $trees as $location => $config ) {
		if ( ! empty( $locations[ $location ] ) ) {
			continue;
		}

		$menu_id = wp_create_nav_menu( $config['name'] );
		if ( is_wp_error( $menu_id ) ) {
			continue;
		}

		foreach ( $config['cols'] as $col_title => $links ) {
			$parent_id = wp_update_nav_menu_item(
				(int) $menu_id,
				0,
				array(
					'menu-item-title'  => $col_title,
					'menu-item-url'    => '#',
					'menu-item-status' => 'publish',
					'menu-item-type'   => 'custom',
				)
			);

			if ( is_wp_error( $parent_id ) ) {
				continue;
			}

			foreach ( $links as $link ) {
				if ( is_array( $link ) ) {
					$label = $link[0];
					$url   = $link[1];
				} else {
					$label = $link;
					$url   = $services_url;
				}

				wp_update_nav_menu_item(
					(int) $menu_id,
					0,
					array(
						'menu-item-title'     => $label,
						'menu-item-url'       => $url,
						'menu-item-status'    => 'publish',
						'menu-item-type'      => 'custom',
						'menu-item-parent-id' => (int) $parent_id,
					)
				);
			}
		}

		$locations[ $location ] = (int) $menu_id;
	}

	set_theme_mod( 'nav_menu_locations', $locations );
}
