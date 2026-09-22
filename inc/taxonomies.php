<?php
/**
 * Taxonomies.
 *
 * @package Trinetix
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Register taxonomies used by case studies and knowledge content.
 */
function trinetix_register_taxonomies(): void {
	register_taxonomy(
		'case_study_industry',
		array( 'case_study' ),
		array(
			'labels'            => array(
				'name'          => __( 'Case Study Industries', 'trinetix' ),
				'singular_name' => __( 'Case Study Industry', 'trinetix' ),
				'search_items'  => __( 'Search Industries', 'trinetix' ),
				'all_items'     => __( 'All Industries', 'trinetix' ),
				'edit_item'     => __( 'Edit Industry', 'trinetix' ),
				'update_item'   => __( 'Update Industry', 'trinetix' ),
				'add_new_item'  => __( 'Add New Industry', 'trinetix' ),
				'new_item_name' => __( 'New Industry Name', 'trinetix' ),
				'menu_name'     => __( 'Industries', 'trinetix' ),
			),
			'hierarchical'      => true,
			'public'            => true,
			'show_ui'           => true,
			'show_admin_column' => true,
			'show_in_rest'      => true,
			'rewrite'           => array( 'slug' => 'case-study-industry' ),
		)
	);

	register_taxonomy(
		'case_study_tag',
		array( 'case_study' ),
		array(
			'labels'            => array(
				'name'          => __( 'Case Study Tags', 'trinetix' ),
				'singular_name' => __( 'Case Study Tag', 'trinetix' ),
			),
			'hierarchical'      => false,
			'public'            => true,
			'show_ui'           => true,
			'show_admin_column' => true,
			'show_in_rest'      => true,
			'rewrite'           => array( 'slug' => 'case-tag' ),
		)
	);
}
add_action( 'init', 'trinetix_register_taxonomies' );
