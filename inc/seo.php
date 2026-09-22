<?php
/**
 * Fallback SEO meta (skipped when Yoast or Rank Math is active).
 *
 * @package Trinetix
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Whether a major SEO plugin is active.
 */
function trinetix_seo_plugin_active(): bool {
	return defined( 'WPSEO_VERSION' )
		|| defined( 'RANK_MATH_VERSION' )
		|| class_exists( 'WPSEO_Options', false )
		|| class_exists( 'RankMath', false );
}

/**
 * Output fallback meta tags.
 */
function trinetix_output_seo_meta(): void {
	if ( trinetix_seo_plugin_active() || is_admin() ) {
		return;
	}

	$description = '';
	$og_image    = '';
	$title       = wp_get_document_title();

	if ( is_singular() ) {
		$post_id = get_queried_object_id();
		$custom_title = (string) get_post_meta( $post_id, '_trinetix_seo_title', true );
		$custom_desc  = (string) get_post_meta( $post_id, '_trinetix_seo_description', true );
		$custom_image = (int) get_post_meta( $post_id, '_trinetix_seo_image_id', true );

		if ( $custom_title ) {
			$title = $custom_title;
		}

		$description = $custom_desc ? $custom_desc : (string) get_the_excerpt( $post_id );
		if ( '' === $description ) {
			$description = wp_trim_words( wp_strip_all_tags( (string) get_post_field( 'post_content', $post_id ) ), 30 );
		}

		if ( $custom_image ) {
			$og_image = (string) wp_get_attachment_image_url( $custom_image, 'full' );
		} elseif ( has_post_thumbnail( $post_id ) ) {
			$og_image = (string) get_the_post_thumbnail_url( $post_id, 'full' );
		}
	} else {
		$description = (string) trinetix_get_setting( 'seo_default_description', get_bloginfo( 'description' ) );
		$og_id       = (int) trinetix_get_setting( 'seo_og_image_id', 0 );
		if ( $og_id ) {
			$og_image = (string) wp_get_attachment_image_url( $og_id, 'full' );
		}
	}

	if ( '' === $description ) {
		$description = (string) trinetix_get_setting( 'seo_default_description', get_bloginfo( 'description' ) );
	}

	$description = wp_strip_all_tags( $description );
	$url         = is_singular() ? get_permalink() : home_url( add_query_arg( array(), $GLOBALS['wp']->request ?? '' ) );

	if ( $description ) {
		echo '<meta name="description" content="' . esc_attr( $description ) . '" />' . "\n";
	}

	echo '<meta property="og:type" content="' . esc_attr( is_singular( 'post' ) ? 'article' : 'website' ) . '" />' . "\n";
	echo '<meta property="og:title" content="' . esc_attr( $title ) . '" />' . "\n";
	if ( $description ) {
		echo '<meta property="og:description" content="' . esc_attr( $description ) . '" />' . "\n";
	}
	echo '<meta property="og:url" content="' . esc_url( is_singular() ? (string) get_permalink() : home_url( '/' ) ) . '" />' . "\n";
	echo '<meta property="og:site_name" content="' . esc_attr( get_bloginfo( 'name' ) ) . '" />' . "\n";
	if ( $og_image ) {
		echo '<meta property="og:image" content="' . esc_url( $og_image ) . '" />' . "\n";
	}

	echo '<meta name="twitter:card" content="summary_large_image" />' . "\n";
	echo '<meta name="twitter:title" content="' . esc_attr( $title ) . '" />' . "\n";
	if ( $description ) {
		echo '<meta name="twitter:description" content="' . esc_attr( $description ) . '" />' . "\n";
	}
	if ( $og_image ) {
		echo '<meta name="twitter:image" content="' . esc_url( $og_image ) . '" />' . "\n";
	}

	unset( $url );
}
add_action( 'wp_head', 'trinetix_output_seo_meta', 5 );
