<?php
/**
 * Schema.org JSON-LD.
 *
 * @package Trinetix
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Output schema markup.
 */
function trinetix_output_schema(): void {
	if ( is_admin() ) {
		return;
	}

	$graphs = array();

	$org_name = (string) trinetix_get_setting( 'company_name', get_bloginfo( 'name' ) );
	$org_url  = (string) trinetix_get_setting( 'organization_url', home_url( '/' ) );
	$logo_id  = (int) get_theme_mod( 'custom_logo' );
	$logo_url = $logo_id ? (string) wp_get_attachment_image_url( $logo_id, 'full' ) : trinetix_asset_uri( 'assets/images/logo.png' );

	$organization = array(
		'@type' => 'Organization',
		'@id'   => trailingslashit( $org_url ) . '#organization',
		'name'  => $org_name,
		'url'   => $org_url,
		'logo'  => array(
			'@type' => 'ImageObject',
			'url'   => $logo_url,
		),
	);

	$email = (string) trinetix_get_setting( 'contact_email', '' );
	if ( $email ) {
		$organization['email'] = $email;
	}

	$same_as = array_filter(
		array(
			(string) trinetix_get_setting( 'social_linkedin', '' ),
			(string) trinetix_get_setting( 'social_twitter', '' ),
			(string) trinetix_get_setting( 'social_youtube', '' ),
			(string) trinetix_get_setting( 'social_facebook', '' ),
		)
	);
	if ( $same_as ) {
		$organization['sameAs'] = array_values( $same_as );
	}

	$graphs[] = $organization;

	$graphs[] = array(
		'@type'     => 'WebSite',
		'@id'       => trailingslashit( home_url( '/' ) ) . '#website',
		'url'       => home_url( '/' ),
		'name'      => get_bloginfo( 'name' ),
		'publisher' => array( '@id' => trailingslashit( $org_url ) . '#organization' ),
		'potentialAction' => array(
			'@type'       => 'SearchAction',
			'target'      => home_url( '/?s={search_term_string}' ),
			'query-input' => 'required name=search_term_string',
		),
	);

	if ( is_singular( 'post' ) ) {
		$post_id = get_queried_object_id();
		$article = array(
			'@type'            => 'BlogPosting',
			'@id'              => get_permalink( $post_id ) . '#article',
			'headline'         => get_the_title( $post_id ),
			'datePublished'    => get_the_date( DATE_W3C, $post_id ),
			'dateModified'     => get_the_modified_date( DATE_W3C, $post_id ),
			'mainEntityOfPage' => get_permalink( $post_id ),
			'author'           => array(
				'@type' => 'Person',
				'name'  => get_the_author_meta( 'display_name', (int) get_post_field( 'post_author', $post_id ) ),
			),
			'publisher'        => array( '@id' => trailingslashit( $org_url ) . '#organization' ),
		);

		if ( has_post_thumbnail( $post_id ) ) {
			$article['image'] = get_the_post_thumbnail_url( $post_id, 'full' );
		}

		$desc = has_excerpt( $post_id ) ? get_the_excerpt( $post_id ) : wp_trim_words( wp_strip_all_tags( (string) get_post_field( 'post_content', $post_id ) ), 30 );
		if ( $desc ) {
			$article['description'] = wp_strip_all_tags( $desc );
		}

		$graphs[] = $article;
	}

	if ( empty( $graphs ) ) {
		return;
	}

	$payload = array(
		'@context' => 'https://schema.org',
		'@graph'   => $graphs,
	);

	echo '<script type="application/ld+json">' . wp_json_encode( $payload, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE ) . '</script>' . "\n"; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
}
add_action( 'wp_head', 'trinetix_output_schema', 20 );
