<?php
/**
 * Meta boxes for CPTs and SEO fallback fields.
 *
 * @package Trinetix
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Register meta boxes.
 */
function trinetix_register_meta_boxes(): void {
	$types = array( 'service', 'industry', 'case_study', 'testimonial', 'partner', 'approach_item', 'post', 'page' );
	foreach ( $types as $type ) {
		add_meta_box(
			'trinetix_details',
			__( 'Trinetix Details', 'trinetix' ),
			'trinetix_render_details_meta_box',
			$type,
			'normal',
			'high'
		);
		add_meta_box(
			'trinetix_seo',
			__( 'SEO Fallback', 'trinetix' ),
			'trinetix_render_seo_meta_box',
			$type,
			'side',
			'default'
		);
	}
}
add_action( 'add_meta_boxes', 'trinetix_register_meta_boxes' );

/**
 * Details meta box.
 *
 * @param WP_Post $post Post.
 */
function trinetix_render_details_meta_box( WP_Post $post ): void {
	wp_nonce_field( 'trinetix_save_meta', 'trinetix_meta_nonce' );
	$type = $post->post_type;

	if ( 'service' === $type ) {
		trinetix_meta_text( $post->ID, '_trinetix_cta_label', __( 'CTA Label', 'trinetix' ) );
		trinetix_meta_text( $post->ID, '_trinetix_cta_url', __( 'CTA URL', 'trinetix' ) );
		trinetix_meta_text( $post->ID, '_trinetix_icon_class', __( 'Card CSS modifier (optional)', 'trinetix' ) );
		trinetix_render_media_field( '_trinetix_icon_id', (int) get_post_meta( $post->ID, '_trinetix_icon_id', true ), __( 'Icon Image', 'trinetix' ), 'image', 'trinetix_icon_id' );
	}

	if ( 'industry' === $type ) {
		trinetix_meta_textarea( $post->ID, '_trinetix_tab_label', __( 'Tab short label (optional)', 'trinetix' ) );
	}

	if ( 'case_study' === $type ) {
		trinetix_meta_text( $post->ID, '_trinetix_client', __( 'Client name', 'trinetix' ) );
		trinetix_meta_text( $post->ID, '_trinetix_eyebrow', __( 'Card eyebrow / category', 'trinetix' ) );
		trinetix_meta_textarea( $post->ID, '_trinetix_challenge', __( 'Challenge', 'trinetix' ) );
		trinetix_meta_textarea( $post->ID, '_trinetix_solution', __( 'Solution', 'trinetix' ) );
		trinetix_meta_textarea( $post->ID, '_trinetix_results', __( 'Results', 'trinetix' ) );
	}

	if ( 'testimonial' === $type ) {
		trinetix_meta_text( $post->ID, '_trinetix_role', __( 'Role / designation', 'trinetix' ) );
		trinetix_meta_text( $post->ID, '_trinetix_company', __( 'Company', 'trinetix' ) );
		trinetix_meta_text( $post->ID, '_trinetix_chip_label', __( 'Chip label', 'trinetix' ) );
		trinetix_meta_text( $post->ID, '_trinetix_rating', __( 'Rating (1-5)', 'trinetix' ) );
	}

	if ( 'partner' === $type ) {
		trinetix_meta_text( $post->ID, '_trinetix_website', __( 'Website URL', 'trinetix' ) );
		trinetix_meta_text( $post->ID, '_trinetix_card_class', __( 'Card CSS class (e.g. aws, microsoft)', 'trinetix' ) );
	}

	if ( 'approach_item' === $type ) {
		trinetix_meta_text( $post->ID, '_trinetix_link_url', __( 'Link URL', 'trinetix' ) );
	}
}

/**
 * SEO meta box.
 *
 * @param WP_Post $post Post.
 */
function trinetix_render_seo_meta_box( WP_Post $post ): void {
	trinetix_meta_text( $post->ID, '_trinetix_seo_title', __( 'SEO Title', 'trinetix' ) );
	trinetix_meta_textarea( $post->ID, '_trinetix_seo_description', __( 'Meta Description', 'trinetix' ) );
	trinetix_render_media_field( '_trinetix_seo_image_id', (int) get_post_meta( $post->ID, '_trinetix_seo_image_id', true ), __( 'OG Image', 'trinetix' ), 'image', 'trinetix_seo_image_' . $post->ID );
}

/**
 * Text field.
 *
 * @param int    $post_id Post ID.
 * @param string $key     Meta key.
 * @param string $label   Label.
 */
function trinetix_meta_text( int $post_id, string $key, string $label ): void {
	$value = (string) get_post_meta( $post_id, $key, true );
	printf(
		'<p><label for="%1$s"><strong>%2$s</strong></label><br /><input type="text" class="widefat" id="%1$s" name="%1$s" value="%3$s" /></p>',
		esc_attr( $key ),
		esc_html( $label ),
		esc_attr( $value )
	);
}

/**
 * Textarea field.
 *
 * @param int    $post_id Post ID.
 * @param string $key     Meta key.
 * @param string $label   Label.
 */
function trinetix_meta_textarea( int $post_id, string $key, string $label ): void {
	$value = (string) get_post_meta( $post_id, $key, true );
	printf(
		'<p><label for="%1$s"><strong>%2$s</strong></label><br /><textarea class="widefat" rows="4" id="%1$s" name="%1$s">%3$s</textarea></p>',
		esc_attr( $key ),
		esc_html( $label ),
		esc_textarea( $value )
	);
}

/**
 * Save meta.
 *
 * @param int $post_id Post ID.
 */
function trinetix_save_meta_boxes( int $post_id ): void {
	if ( ! isset( $_POST['trinetix_meta_nonce'] ) || ! wp_verify_nonce( sanitize_text_field( wp_unslash( $_POST['trinetix_meta_nonce'] ) ), 'trinetix_save_meta' ) ) {
		return;
	}

	if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
		return;
	}

	if ( ! current_user_can( 'edit_post', $post_id ) ) {
		return;
	}

	$text_keys = array(
		'_trinetix_cta_label',
		'_trinetix_cta_url',
		'_trinetix_icon_class',
		'_trinetix_tab_label',
		'_trinetix_client',
		'_trinetix_eyebrow',
		'_trinetix_role',
		'_trinetix_company',
		'_trinetix_chip_label',
		'_trinetix_rating',
		'_trinetix_website',
		'_trinetix_card_class',
		'_trinetix_link_url',
		'_trinetix_seo_title',
	);

	$textarea_keys = array(
		'_trinetix_challenge',
		'_trinetix_solution',
		'_trinetix_results',
		'_trinetix_seo_description',
	);

	$int_keys = array(
		'_trinetix_icon_id',
		'_trinetix_seo_image_id',
	);

	foreach ( $text_keys as $key ) {
		if ( isset( $_POST[ $key ] ) ) {
			update_post_meta( $post_id, $key, sanitize_text_field( wp_unslash( $_POST[ $key ] ) ) );
		}
	}

	foreach ( $textarea_keys as $key ) {
		if ( isset( $_POST[ $key ] ) ) {
			update_post_meta( $post_id, $key, sanitize_textarea_field( wp_unslash( $_POST[ $key ] ) ) );
		}
	}

	foreach ( $int_keys as $key ) {
		if ( isset( $_POST[ $key ] ) ) {
			update_post_meta( $post_id, $key, absint( $_POST[ $key ] ) );
		}
	}
}
add_action( 'save_post', 'trinetix_save_meta_boxes' );
