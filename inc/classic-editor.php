<?php
/**
 * Force Classic Editor for pages (non-technical editing).
 *
 * @package Trinetix
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Disable block editor for pages only.
 *
 * @param bool    $use  Whether to use block editor.
 * @param WP_Post $post Post object.
 * @return bool
 */
function trinetix_use_classic_editor_for_pages( bool $use, $post ): bool {
	if ( $post instanceof WP_Post && 'page' === $post->post_type ) {
		return false;
	}
	return $use;
}
add_filter( 'use_block_editor_for_post', 'trinetix_use_classic_editor_for_pages', 20, 2 );

/**
 * Disable block editor by post type for pages.
 *
 * @param bool   $use       Whether to use block editor.
 * @param string $post_type Post type.
 * @return bool
 */
function trinetix_use_classic_editor_for_page_type( bool $use, string $post_type ): bool {
	if ( 'page' === $post_type ) {
		return false;
	}
	return $use;
}
add_filter( 'use_block_editor_for_post_type', 'trinetix_use_classic_editor_for_page_type', 20, 2 );
