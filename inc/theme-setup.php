<?php
/**
 * Theme setup.
 *
 * @package Trinetix
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Register theme supports and menus.
 */
function trinetix_theme_setup(): void {
	load_theme_textdomain( 'trinetix', TRINETIX_DIR . '/languages' );

	add_theme_support( 'title-tag' );
	add_theme_support( 'post-thumbnails' );
	add_theme_support( 'custom-logo', array(
		'height'      => 80,
		'width'       => 240,
		'flex-height' => true,
		'flex-width'  => true,
	) );
	add_theme_support(
		'html5',
		array(
			'search-form',
			'comment-form',
			'comment-list',
			'gallery',
			'caption',
			'style',
			'script',
			'navigation-widgets',
		)
	);
	add_theme_support( 'responsive-embeds' );
	add_theme_support( 'align-wide' );
	add_theme_support( 'editor-styles' );
	add_editor_style( 'assets/css/editor.css' );

	register_nav_menus(
		array(
			'primary'            => __( 'Primary Menu', 'trinetix' ),
			'footer_ai'          => __( 'Footer — Artificial Intelligence', 'trinetix' ),
			'footer_data'        => __( 'Footer — Data & Analytics', 'trinetix' ),
			'footer_engineering' => __( 'Footer — Digital Engineering', 'trinetix' ),
			'footer_experience'  => __( 'Footer — Experience', 'trinetix' ),
			'footer_company'     => __( 'Footer — Company', 'trinetix' ),
			'footer_legal'       => __( 'Footer — Legal', 'trinetix' ),
		)
	);

	add_image_size( 'trinetix-card', 1300, 900, true );
	add_image_size( 'trinetix-work', 1200, 800, true );
	add_image_size( 'trinetix-insight', 1100, 700, true );
}
add_action( 'after_setup_theme', 'trinetix_theme_setup' );

/**
 * Content width.
 */
function trinetix_content_width(): void {
	$GLOBALS['content_width'] = 1240;
}
add_action( 'after_setup_theme', 'trinetix_content_width', 0 );
