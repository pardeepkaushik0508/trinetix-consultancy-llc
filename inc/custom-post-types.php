<?php
/**
 * Custom post types.
 *
 * @package Trinetix
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Register CPTs.
 */
function trinetix_register_post_types(): void {
	$common = array(
		'public'             => true,
		'show_in_rest'       => true,
		'has_archive'        => true,
		'show_in_nav_menus'  => true,
		'menu_position'      => 20,
		'supports'           => array( 'title', 'editor', 'excerpt', 'thumbnail', 'page-attributes', 'revisions' ),
	);

	register_post_type(
		'service',
		array_merge(
			$common,
			array(
				'labels'       => trinetix_cpt_labels( 'Service', 'Services' ),
				'menu_icon'    => 'dashicons-admin-generic',
				'rewrite'      => array( 'slug' => 'services' ),
				'has_archive'  => 'services',
			)
		)
	);

	register_post_type(
		'industry',
		array_merge(
			$common,
			array(
				'labels'      => trinetix_cpt_labels( 'Industry', 'Industries' ),
				'menu_icon'   => 'dashicons-building',
				'rewrite'     => array( 'slug' => 'industries' ),
				'has_archive' => 'industries',
			)
		)
	);

	register_post_type(
		'case_study',
		array_merge(
			$common,
			array(
				'labels'      => trinetix_cpt_labels( 'Case Study', 'Case Studies' ),
				'menu_icon'   => 'dashicons-portfolio',
				'rewrite'     => array( 'slug' => 'case-studies' ),
				'has_archive' => 'case-studies',
			)
		)
	);

	register_post_type(
		'testimonial',
		array(
			'labels'              => trinetix_cpt_labels( 'Testimonial', 'Testimonials' ),
			'public'              => true,
			'publicly_queryable'  => false,
			'show_ui'             => true,
			'show_in_rest'        => true,
			'exclude_from_search' => true,
			'has_archive'         => false,
			'menu_icon'           => 'dashicons-format-quote',
			'supports'            => array( 'title', 'editor', 'excerpt', 'thumbnail', 'page-attributes' ),
		)
	);

	register_post_type(
		'partner',
		array(
			'labels'              => trinetix_cpt_labels( 'Partner', 'Partners' ),
			'public'              => true,
			'publicly_queryable'  => false,
			'show_ui'             => true,
			'show_in_rest'        => true,
			'exclude_from_search' => true,
			'has_archive'         => false,
			'menu_icon'           => 'dashicons-groups',
			'supports'            => array( 'title', 'editor', 'excerpt', 'thumbnail', 'page-attributes' ),
		)
	);

	register_post_type(
		'approach_item',
		array(
			'labels'              => trinetix_cpt_labels( 'Approach Item', 'Approach Items' ),
			'public'              => true,
			'publicly_queryable'  => true,
			'show_ui'             => true,
			'show_in_rest'        => true,
			'exclude_from_search' => false,
			'has_archive'         => false,
			'menu_icon'           => 'dashicons-share-alt',
			'supports'            => array( 'title', 'editor', 'excerpt', 'thumbnail', 'page-attributes' ),
			'rewrite'             => array( 'slug' => 'approach' ),
		)
	);

	register_post_type(
		'contact_submission',
		array(
			'labels'              => trinetix_cpt_labels( 'Contact Submission', 'Contact Submissions' ),
			'public'              => false,
			'show_ui'             => true,
			'show_in_menu'        => true,
			'exclude_from_search' => true,
			'has_archive'         => false,
			'menu_icon'           => 'dashicons-email-alt',
			'capability_type'     => 'post',
			'capabilities'        => array(
				'create_posts' => 'do_not_allow',
			),
			'map_meta_cap'        => true,
			'supports'            => array( 'title', 'editor' ),
		)
	);
}
add_action( 'init', 'trinetix_register_post_types' );

/**
 * CPT labels helper.
 *
 * @param string $singular Singular.
 * @param string $plural   Plural.
 * @return array<string, string>
 */
function trinetix_cpt_labels( string $singular, string $plural ): array {
	return array(
		'name'               => $plural,
		'singular_name'      => $singular,
		'add_new'            => __( 'Add New', 'trinetix' ),
		'add_new_item'       => sprintf( __( 'Add New %s', 'trinetix' ), $singular ),
		'edit_item'          => sprintf( __( 'Edit %s', 'trinetix' ), $singular ),
		'new_item'           => sprintf( __( 'New %s', 'trinetix' ), $singular ),
		'view_item'          => sprintf( __( 'View %s', 'trinetix' ), $singular ),
		'search_items'       => sprintf( __( 'Search %s', 'trinetix' ), $plural ),
		'not_found'          => sprintf( __( 'No %s found', 'trinetix' ), strtolower( $plural ) ),
		'not_found_in_trash' => sprintf( __( 'No %s found in Trash', 'trinetix' ), strtolower( $plural ) ),
		'all_items'          => sprintf( __( 'All %s', 'trinetix' ), $plural ),
		'menu_name'          => $plural,
	);
}
