<?php
/**
 * Home page section meta boxes + migration from Trinetix Settings.
 *
 * @package Trinetix
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Setting keys that live on the Home page (meta), not in Settings UI.
 *
 * @return array<int, string>
 */
function trinetix_home_section_keys(): array {
	return array(
		'hero_eyebrow',
		'hero_heading',
		'hero_subline',
		'hero_description',
		'hero_cta_text',
		'hero_cta_url',
		'hero_cta_secondary_text',
		'hero_cta_secondary_url',
		'hero_video_enabled',
		'hero_video_id',
		'hero_video_mobile_id',
		'hero_poster_id',
		'hero_playback_rate',
		'hero_capabilities_json',
		'intro_heading',
		'intro_content',
		'intro_cta_text',
		'intro_cta_url',
		'intro_video_image_id',
		'intro_video_label',
		'intro_video_url',
		'services_title',
		'industries_title',
		'work_title',
		'testimonials_title',
		'partners_title',
		'knowledge_title',
		'knowledge_cta_text',
		'knowledge_cta_url',
		'contact_heading',
		'contact_copy',
		'contact_pills',
	);
}

/**
 * Meta key for a home setting key.
 *
 * @param string $key Setting-style key.
 * @return string
 */
function trinetix_home_meta_key( string $key ): string {
	return '_trinetix_' . $key;
}

/**
 * Front page ID (static homepage).
 *
 * @return int
 */
function trinetix_front_page_id(): int {
	if ( 'page' !== get_option( 'show_on_front' ) ) {
		return 0;
	}
	return (int) get_option( 'page_on_front' );
}

/**
 * Whether a page uses the Home template.
 *
 * @param int $post_id Post ID.
 * @return bool
 */
function trinetix_is_home_template( int $post_id ): bool {
	if ( $post_id <= 0 ) {
		return false;
	}
	return 'templates/home.php' === get_page_template_slug( $post_id );
}

/**
 * Whether a page uses the Page Sections template.
 *
 * @param int $post_id Post ID.
 * @return bool
 */
function trinetix_is_page_sections_template( int $post_id ): bool {
	return 'templates/page-sections.php' === get_page_template_slug( $post_id );
}

/**
 * Pages that store/manage the full homepage section fields.
 *
 * @param int $post_id Post ID.
 * @return bool
 */
function trinetix_page_has_home_sections( int $post_id ): bool {
	if ( $post_id <= 0 ) {
		return false;
	}
	if ( trinetix_is_home_template( $post_id ) ) {
		return true;
	}
	$front = trinetix_front_page_id();
	return $front > 0 && $post_id === $front;
}

/**
 * Whether the current admin screen should show Home section boxes.
 *
 * @param int|null $post_id Optional post ID.
 * @return bool
 */
function trinetix_is_home_sections_edit( ?int $post_id = null ): bool {
	if ( null === $post_id ) {
		$post_id = isset( $_GET['post'] ) ? (int) $_GET['post'] : 0; // phpcs:ignore WordPress.Security.NonceVerification.Recommended
		if ( ! $post_id && isset( $GLOBALS['post'] ) && $GLOBALS['post'] instanceof WP_Post ) {
			$post_id = (int) $GLOBALS['post']->ID;
		}
	}
	return trinetix_page_has_home_sections( $post_id );
}

/**
 * Page ID whose meta drives section copy while rendering.
 *
 * @return int
 */
function trinetix_sections_source_page_id(): int {
	if ( is_singular( 'page' ) ) {
		$id = (int) get_queried_object_id();
		if ( trinetix_page_has_home_sections( $id ) ) {
			return $id;
		}
	}
	return trinetix_front_page_id();
}

/**
 * Whether the current screen is the front page editor.
 *
 * @param int|null $post_id Optional post ID.
 * @return bool
 */
function trinetix_is_front_page_edit( ?int $post_id = null ): bool {
	$front = trinetix_front_page_id();
	if ( ! $front ) {
		return false;
	}
	if ( null === $post_id ) {
		$post_id = isset( $_GET['post'] ) ? (int) $_GET['post'] : 0; // phpcs:ignore WordPress.Security.NonceVerification.Recommended
		if ( ! $post_id && isset( $GLOBALS['post'] ) && $GLOBALS['post'] instanceof WP_Post ) {
			$post_id = (int) $GLOBALS['post']->ID;
		}
	}
	return $post_id > 0 && $post_id === $front;
}

/**
 * Read a homepage field: current Home-layout page meta first, then settings.
 *
 * @param string $key     Setting-style key.
 * @param mixed  $default Fallback.
 * @return mixed
 */
function trinetix_home_setting( string $key, $default = '' ) {
	$page_id = trinetix_sections_source_page_id();
	if ( $page_id ) {
		$meta = get_post_meta( $page_id, trinetix_home_meta_key( $key ), true );
		if ( '' !== $meta && null !== $meta ) {
			return $meta;
		}
	}
	return trinetix_get_setting( $key, $default );
}

/**
 * JSON setting from home page meta (or settings fallback).
 *
 * @param string $key Key.
 * @return array<int|string, mixed>
 */
function trinetix_home_json_setting( string $key ): array {
	$raw = (string) trinetix_home_setting( $key, '' );
	if ( '' === trim( $raw ) ) {
		return array();
	}
	$data = json_decode( $raw, true );
	return is_array( $data ) ? $data : array();
}

/**
 * Section toggle keys (show/hide on Home template).
 *
 * @return array<string, string> slug => label
 */
function trinetix_home_section_toggles(): array {
	return array(
		'hero'         => __( '1. Hero / Home Banner', 'trinetix' ),
		'intro'        => __( '2. Intro + Approach cards', 'trinetix' ),
		'services'     => __( '3. Services', 'trinetix' ),
		'industries'   => __( '4. Industries', 'trinetix' ),
		'work'         => __( '5. Our Work', 'trinetix' ),
		'testimonials' => __( '6. Testimonials', 'trinetix' ),
		'partners'     => __( '7. Partners', 'trinetix' ),
		'knowledge'    => __( '8. Knowledge Hub', 'trinetix' ),
		'contact'      => __( '9. Contact', 'trinetix' ),
	);
}

/**
 * Whether a home section should render for a page.
 *
 * @param int    $page_id Page ID.
 * @param string $slug    Section slug.
 * @return bool
 */
function trinetix_page_section_enabled( int $page_id, string $slug ): bool {
	if ( $page_id <= 0 ) {
		return true;
	}
	$key = '_trinetix_show_section_' . $slug;
	$val = get_post_meta( $page_id, $key, true );
	if ( '' === $val || null === $val ) {
		return true;
	}
	return (int) $val === 1;
}

/**
 * Whether to show the global site header on this request.
 *
 * @return bool
 */
function trinetix_show_site_header(): bool {
	if ( ! is_singular( 'page' ) ) {
		return true;
	}
	$id  = (int) get_queried_object_id();
	$val = get_post_meta( $id, '_trinetix_show_header', true );
	if ( '' === $val || null === $val ) {
		return true;
	}
	return (int) $val === 1;
}

/**
 * Whether to show the global site footer on this request.
 *
 * @return bool
 */
function trinetix_show_site_footer(): bool {
	if ( ! is_singular( 'page' ) ) {
		return true;
	}
	$id  = (int) get_queried_object_id();
	$val = get_post_meta( $id, '_trinetix_show_footer', true );
	if ( '' === $val || null === $val ) {
		return true;
	}
	return (int) $val === 1;
}

/**
 * Render the homepage section stack for a page.
 *
 * @param int $page_id Page ID (for toggles).
 */
function trinetix_render_home_sections( int $page_id ): void {
	if ( trinetix_page_section_enabled( $page_id, 'hero' ) ) {
		get_template_part( 'template-parts/home/hero' );
	}
	if ( trinetix_page_section_enabled( $page_id, 'intro' ) ) {
		get_template_part( 'template-parts/home/intro' );
		get_template_part( 'template-parts/home/approach' );
	}
	if ( trinetix_page_section_enabled( $page_id, 'services' ) ) {
		get_template_part( 'template-parts/home/services' );
	}
	if ( trinetix_page_section_enabled( $page_id, 'industries' ) ) {
		get_template_part( 'template-parts/home/industries' );
	}
	if ( trinetix_page_section_enabled( $page_id, 'work' ) ) {
		get_template_part( 'template-parts/home/case-studies' );
	}
	if ( trinetix_page_section_enabled( $page_id, 'testimonials' ) ) {
		get_template_part( 'template-parts/home/testimonials' );
	}
	if ( trinetix_page_section_enabled( $page_id, 'partners' ) ) {
		get_template_part( 'template-parts/home/partners' );
	}
	if ( trinetix_page_section_enabled( $page_id, 'knowledge' ) ) {
		get_template_part( 'template-parts/home/knowledge' );
	}
	if ( trinetix_page_section_enabled( $page_id, 'contact' ) ) {
		get_template_part( 'template-parts/home/contact' );
	}
}

/**
 * Assign Home template to a page.
 *
 * @param int $page_id Page ID.
 */
function trinetix_assign_home_template( int $page_id ): void {
	if ( $page_id <= 0 ) {
		return;
	}
	update_post_meta( $page_id, '_wp_page_template', 'templates/home.php' );
}

/**
 * One-time migrate settings → Home page meta + editor guide + Home template.
 */
function trinetix_maybe_migrate_home_page_meta(): void {
	if ( get_option( 'trinetix_home_meta_migrated' ) ) {
		// Still ensure front page uses Home template.
		$front = trinetix_front_page_id();
		if ( $front && ! trinetix_is_home_template( $front ) && ! get_option( 'trinetix_home_template_assigned' ) ) {
			trinetix_assign_home_template( $front );
			update_option( 'trinetix_home_template_assigned', 1, false );
		}
		return;
	}

	$page_id = trinetix_front_page_id();
	if ( ! $page_id ) {
		return;
	}

	trinetix_assign_home_template( $page_id );
	update_option( 'trinetix_home_template_assigned', 1, false );

	$settings = trinetix_get_settings();
	foreach ( trinetix_home_section_keys() as $key ) {
		$meta_key = trinetix_home_meta_key( $key );
		$existing = get_post_meta( $page_id, $meta_key, true );
		if ( '' !== $existing && null !== $existing ) {
			continue;
		}
		if ( array_key_exists( $key, $settings ) && '' !== $settings[ $key ] && null !== $settings[ $key ] ) {
			update_post_meta( $page_id, $meta_key, $settings[ $key ] );
		}
	}

	$post = get_post( $page_id );
	if ( $post instanceof WP_Post ) {
		$content = (string) $post->post_content;
		$is_placeholder = (
			'' === trim( wp_strip_all_tags( $content ) )
			|| false !== strpos( $content, 'Front page content is rendered by front-page.php' )
		);
		if ( $is_placeholder ) {
			wp_update_post(
				array(
					'ID'           => $page_id,
					'post_content' => trinetix_home_page_editor_guide(),
				)
			);
		}
	}

	update_option( 'trinetix_home_meta_migrated', 1, false );
}
add_action( 'admin_init', 'trinetix_maybe_migrate_home_page_meta' );
add_action( 'after_switch_theme', 'trinetix_maybe_migrate_home_page_meta', 20 );

/**
 * Force Home template assignment once for existing installs that already migrated.
 */
function trinetix_maybe_assign_home_template(): void {
	if ( get_option( 'trinetix_home_template_assigned' ) ) {
		return;
	}
	$front = trinetix_front_page_id();
	if ( ! $front ) {
		return;
	}
	trinetix_assign_home_template( $front );
	update_option( 'trinetix_home_template_assigned', 1, false );
}
add_action( 'admin_init', 'trinetix_maybe_assign_home_template', 25 );

/**
 * Register meta boxes.
 */
function trinetix_register_page_section_meta_boxes(): void {
	global $post;
	$post_id = 0;
	if ( isset( $_GET['post'] ) ) { // phpcs:ignore WordPress.Security.NonceVerification.Recommended
		$post_id = (int) $_GET['post']; // phpcs:ignore WordPress.Security.NonceVerification.Recommended
	} elseif ( $post instanceof WP_Post ) {
		$post_id = (int) $post->ID;
	}

	// Layout (header/footer) on every page — sidebar.
	add_meta_box(
		'trinetix_page_layout',
		__( 'Header & Footer', 'trinetix' ),
		'trinetix_render_page_layout_meta_box',
		'page',
		'side',
		'high'
	);

	if ( trinetix_is_home_sections_edit( $post_id ) ) {
		add_meta_box(
			'trinetix_home_help',
			__( 'How to edit this Home page', 'trinetix' ),
			'trinetix_render_home_help_meta_box',
			'page',
			'normal',
			'high'
		);
		add_meta_box(
			'trinetix_home_sections_toggle',
			__( 'Which sections to show', 'trinetix' ),
			'trinetix_render_home_sections_toggle_meta_box',
			'page',
			'side',
			'default'
		);
		add_meta_box(
			'trinetix_home_hero',
			__( '1. Hero / Home Banner', 'trinetix' ),
			'trinetix_render_home_hero_meta_box',
			'page',
			'normal',
			'high'
		);
		add_meta_box(
			'trinetix_home_intro',
			__( '2. Intro', 'trinetix' ),
			'trinetix_render_home_intro_meta_box',
			'page',
			'normal',
			'default'
		);
		add_meta_box(
			'trinetix_home_titles',
			__( '3. Section titles', 'trinetix' ),
			'trinetix_render_home_titles_meta_box',
			'page',
			'normal',
			'default'
		);
		add_meta_box(
			'trinetix_home_contact',
			__( '4. Contact section', 'trinetix' ),
			'trinetix_render_home_contact_meta_box',
			'page',
			'normal',
			'default'
		);
	}

	if ( $post_id && trinetix_is_page_sections_template( $post_id ) && ! trinetix_is_home_sections_edit( $post_id ) ) {
		add_meta_box(
			'trinetix_page_hero',
			__( 'Page Hero', 'trinetix' ),
			'trinetix_render_page_hero_meta_box',
			'page',
			'normal',
			'high'
		);
	}
}
add_action( 'add_meta_boxes', 'trinetix_register_page_section_meta_boxes' );

/**
 * Help meta box.
 */
function trinetix_render_home_help_meta_box(): void {
	echo '<div style="line-height:1.55;font-size:14px;">';
	echo '<p><strong>' . esc_html__( 'Template:', 'trinetix' ) . '</strong> ' . esc_html__( 'Page Attributes → Template must be “Home” to see these section boxes.', 'trinetix' ) . '</p>';
	echo '<p><strong>' . esc_html__( 'Logo:', 'trinetix' ) . '</strong> ' . esc_html__( 'Appearance → Customize → Site Identity.', 'trinetix' ) . '</p>';
	echo '<p><strong>' . esc_html__( 'Header & footer menus:', 'trinetix' ) . '</strong> ' . esc_html__( 'Appearance → Menus (global). Use the “Header & Footer” box in the sidebar to show/hide them on this page.', 'trinetix' ) . '</p>';
	echo '<p><strong>' . esc_html__( 'Banner & section text:', 'trinetix' ) . '</strong> ' . esc_html__( 'Use the numbered boxes below (1 → 4). Turn sections on/off in the sidebar.', 'trinetix' ) . '</p>';
	echo '<p><strong>' . esc_html__( 'Services / Industries / Case Studies cards:', 'trinetix' ) . '</strong> ' . esc_html__( 'Edit those items in the left admin menu. Shortcodes in the editor are for copying onto other pages.', 'trinetix' ) . '</p>';
	echo '<p><strong>' . esc_html__( 'Site-wide contact email & footer legal URLs:', 'trinetix' ) . '</strong> ' . esc_html__( 'Trinetix Settings (sidebar).', 'trinetix' ) . '</p>';
	echo '</div>';
}

/**
 * Sidebar: show/hide global header & footer on this page.
 *
 * @param WP_Post $post Post.
 */
function trinetix_render_page_layout_meta_box( WP_Post $post ): void {
	wp_nonce_field( 'trinetix_save_page_layout', 'trinetix_page_layout_nonce' );
	$id      = (int) $post->ID;
	$header  = get_post_meta( $id, '_trinetix_show_header', true );
	$footer  = get_post_meta( $id, '_trinetix_show_footer', true );
	$show_h  = ( '' === $header || null === $header ) ? 1 : (int) $header;
	$show_f  = ( '' === $footer || null === $footer ) ? 1 : (int) $footer;

	echo '<p class="description" style="margin-top:0;">' . esc_html__( 'Header and footer content stay global (Customizer + Menus + Trinetix Settings). These options only show or hide them on this page.', 'trinetix' ) . '</p>';
	echo '<p><label><input type="checkbox" name="_trinetix_show_header" value="1" ' . checked( $show_h, 1, false ) . ' /> ' . esc_html__( 'Show site header', 'trinetix' ) . '</label></p>';
	echo '<p><label><input type="checkbox" name="_trinetix_show_footer" value="1" ' . checked( $show_f, 1, false ) . ' /> ' . esc_html__( 'Show site footer', 'trinetix' ) . '</label></p>';
}

/**
 * Sidebar: which home sections to display.
 *
 * @param WP_Post $post Post.
 */
function trinetix_render_home_sections_toggle_meta_box( WP_Post $post ): void {
	$id = (int) $post->ID;
	echo '<p class="description" style="margin-top:0;">' . esc_html__( 'Uncheck a section to hide it on this page.', 'trinetix' ) . '</p>';
	foreach ( trinetix_home_section_toggles() as $slug => $label ) {
		$key = '_trinetix_show_section_' . $slug;
		$val = get_post_meta( $id, $key, true );
		$on  = ( '' === $val || null === $val ) ? 1 : (int) $val;
		printf(
			'<p style="margin:6px 0;"><label><input type="checkbox" name="%1$s" value="1" %2$s /> %3$s</label></p>',
			esc_attr( $key ),
			checked( $on, 1, false ),
			esc_html( $label )
		);
	}
}

/**
 * Field helpers for page meta.
 *
 * @param int    $post_id Post ID.
 * @param string $key     Key.
 * @param string $label   Label.
 * @param string $type    text|textarea|checkbox.
 */
function trinetix_page_meta_field( int $post_id, string $key, string $label, string $type = 'text' ): void {
	$meta_key = trinetix_home_meta_key( $key );
	$value    = get_post_meta( $post_id, $meta_key, true );
	if ( '' === $value || null === $value ) {
		$value = trinetix_get_setting( $key, '' );
	}
	$id = 'trinetix_field_' . $key;

	echo '<p style="margin:12px 0;">';
	echo '<label for="' . esc_attr( $id ) . '"><strong>' . esc_html( $label ) . '</strong></label><br />';

	if ( 'textarea' === $type ) {
		printf(
			'<textarea class="large-text" rows="4" id="%1$s" name="%2$s">%3$s</textarea>',
			esc_attr( $id ),
			esc_attr( $meta_key ),
			esc_textarea( (string) $value )
		);
	} elseif ( 'checkbox' === $type ) {
		printf(
			'<label><input type="checkbox" id="%1$s" name="%2$s" value="1" %3$s /> %4$s</label>',
			esc_attr( $id ),
			esc_attr( $meta_key ),
			checked( ! empty( $value ), true, false ),
			esc_html( $label )
		);
	} else {
		printf(
			'<input type="text" class="large-text" id="%1$s" name="%2$s" value="%3$s" />',
			esc_attr( $id ),
			esc_attr( $meta_key ),
			esc_attr( (string) $value )
		);
	}
	echo '</p>';
}

/**
 * Hero meta box.
 *
 * @param WP_Post $post Post.
 */
function trinetix_render_home_hero_meta_box( WP_Post $post ): void {
	wp_nonce_field( 'trinetix_save_home_sections', 'trinetix_home_sections_nonce' );
	$id = (int) $post->ID;

	trinetix_page_meta_field( $id, 'hero_eyebrow', __( 'Hero eyebrow', 'trinetix' ) );
	trinetix_page_meta_field( $id, 'hero_heading', __( 'Hero main heading (line breaks allowed)', 'trinetix' ), 'textarea' );
	trinetix_page_meta_field( $id, 'hero_subline', __( 'Hero subline', 'trinetix' ) );
	trinetix_page_meta_field( $id, 'hero_description', __( 'Hero description', 'trinetix' ), 'textarea' );
	trinetix_page_meta_field( $id, 'hero_cta_text', __( 'Primary CTA text', 'trinetix' ) );
	trinetix_page_meta_field( $id, 'hero_cta_url', __( 'Primary CTA URL', 'trinetix' ) );
	trinetix_page_meta_field( $id, 'hero_cta_secondary_text', __( 'Secondary CTA text', 'trinetix' ) );
	trinetix_page_meta_field( $id, 'hero_cta_secondary_url', __( 'Secondary CTA URL', 'trinetix' ) );

	$video_on = get_post_meta( $id, trinetix_home_meta_key( 'hero_video_enabled' ), true );
	if ( '' === $video_on || null === $video_on ) {
		$video_on = trinetix_get_setting( 'hero_video_enabled', 1 );
	}
	echo '<p><label><input type="checkbox" name="' . esc_attr( trinetix_home_meta_key( 'hero_video_enabled' ) ) . '" value="1" ' . checked( ! empty( $video_on ), true, false ) . ' /> ' . esc_html__( 'Enable hero video', 'trinetix' ) . '</label></p>';

	trinetix_render_media_field(
		trinetix_home_meta_key( 'hero_video_id' ),
		(int) ( get_post_meta( $id, trinetix_home_meta_key( 'hero_video_id' ), true ) ?: trinetix_get_setting( 'hero_video_id', 0 ) ),
		__( 'Desktop hero video', 'trinetix' ),
		'video',
		'hero_video_id'
	);
	trinetix_render_media_field(
		trinetix_home_meta_key( 'hero_video_mobile_id' ),
		(int) ( get_post_meta( $id, trinetix_home_meta_key( 'hero_video_mobile_id' ), true ) ?: trinetix_get_setting( 'hero_video_mobile_id', 0 ) ),
		__( 'Mobile hero video', 'trinetix' ),
		'video',
		'hero_video_mobile_id'
	);
	trinetix_render_media_field(
		trinetix_home_meta_key( 'hero_poster_id' ),
		(int) ( get_post_meta( $id, trinetix_home_meta_key( 'hero_poster_id' ), true ) ?: trinetix_get_setting( 'hero_poster_id', 0 ) ),
		__( 'Hero poster image', 'trinetix' ),
		'image',
		'hero_poster_id'
	);
	trinetix_page_meta_field( $id, 'hero_playback_rate', __( 'Playback rate (e.g. 1 or 0.9)', 'trinetix' ) );
	trinetix_page_meta_field( $id, 'hero_capabilities_json', __( 'Capability cards JSON (optional advanced)', 'trinetix' ), 'textarea' );
}

/**
 * Intro meta box.
 *
 * @param WP_Post $post Post.
 */
function trinetix_render_home_intro_meta_box( WP_Post $post ): void {
	$id = (int) $post->ID;
	trinetix_page_meta_field( $id, 'intro_heading', __( 'Intro heading', 'trinetix' ), 'textarea' );
	trinetix_page_meta_field( $id, 'intro_content', __( 'Intro content (plain text; paragraphs OK)', 'trinetix' ), 'textarea' );
	trinetix_page_meta_field( $id, 'intro_cta_text', __( 'Intro CTA text', 'trinetix' ) );
	trinetix_page_meta_field( $id, 'intro_cta_url', __( 'Intro CTA URL', 'trinetix' ) );
	trinetix_render_media_field(
		trinetix_home_meta_key( 'intro_video_image_id' ),
		(int) ( get_post_meta( $id, trinetix_home_meta_key( 'intro_video_image_id' ), true ) ?: trinetix_get_setting( 'intro_video_image_id', 0 ) ),
		__( 'Intro video card image', 'trinetix' ),
		'image',
		'intro_video_image_id'
	);
	trinetix_page_meta_field( $id, 'intro_video_label', __( 'Intro video label', 'trinetix' ), 'textarea' );
	trinetix_page_meta_field( $id, 'intro_video_url', __( 'Intro video URL', 'trinetix' ) );
}

/**
 * Section titles meta box.
 *
 * @param WP_Post $post Post.
 */
function trinetix_render_home_titles_meta_box( WP_Post $post ): void {
	$id = (int) $post->ID;
	trinetix_page_meta_field( $id, 'services_title', __( 'Services section title', 'trinetix' ) );
	trinetix_page_meta_field( $id, 'industries_title', __( 'Industries section title', 'trinetix' ) );
	trinetix_page_meta_field( $id, 'work_title', __( 'Our Work section title', 'trinetix' ) );
	trinetix_page_meta_field( $id, 'testimonials_title', __( 'Testimonials title', 'trinetix' ) );
	trinetix_page_meta_field( $id, 'partners_title', __( 'Partners title', 'trinetix' ) );
	trinetix_page_meta_field( $id, 'knowledge_title', __( 'Knowledge Hub title', 'trinetix' ) );
	trinetix_page_meta_field( $id, 'knowledge_cta_text', __( 'Knowledge CTA text', 'trinetix' ) );
	trinetix_page_meta_field( $id, 'knowledge_cta_url', __( 'Knowledge CTA URL', 'trinetix' ) );
}

/**
 * Contact section meta box (display copy; recipient stays in Settings).
 *
 * @param WP_Post $post Post.
 */
function trinetix_render_home_contact_meta_box( WP_Post $post ): void {
	$id = (int) $post->ID;
	trinetix_page_meta_field( $id, 'contact_heading', __( 'Contact heading', 'trinetix' ) );
	trinetix_page_meta_field( $id, 'contact_copy', __( 'Contact supporting text', 'trinetix' ), 'textarea' );
	trinetix_page_meta_field( $id, 'contact_pills', __( 'Interest pills (one per line)', 'trinetix' ), 'textarea' );
	echo '<p class="description">' . esc_html__( 'Display email, phone, and form recipient are under Trinetix Settings → Contact.', 'trinetix' ) . '</p>';
}

/**
 * Generic page hero for Page Sections template.
 *
 * @param WP_Post $post Post.
 */
function trinetix_render_page_hero_meta_box( WP_Post $post ): void {
	wp_nonce_field( 'trinetix_save_page_hero', 'trinetix_page_hero_nonce' );
	$id      = (int) $post->ID;
	$title   = (string) get_post_meta( $id, '_trinetix_page_hero_title', true );
	$subline = (string) get_post_meta( $id, '_trinetix_page_hero_subline', true );
	$image   = (int) get_post_meta( $id, '_trinetix_page_hero_image_id', true );

	echo '<p><label for="trinetix_page_hero_title"><strong>' . esc_html__( 'Hero title', 'trinetix' ) . '</strong></label><br />';
	echo '<input type="text" class="large-text" id="trinetix_page_hero_title" name="_trinetix_page_hero_title" value="' . esc_attr( $title ) . '" /></p>';
	echo '<p><label for="trinetix_page_hero_subline"><strong>' . esc_html__( 'Hero subline', 'trinetix' ) . '</strong></label><br />';
	echo '<textarea class="large-text" rows="3" id="trinetix_page_hero_subline" name="_trinetix_page_hero_subline">' . esc_textarea( $subline ) . '</textarea></p>';
	trinetix_render_media_field( '_trinetix_page_hero_image_id', $image, __( 'Hero image', 'trinetix' ), 'image', 'page_hero_image_id' );
	echo '<p class="description">' . esc_html__( 'Add more content (and shortcodes like [trinetix_work]) in the editor above.', 'trinetix' ) . '</p>';
}

/**
 * Save home section meta.
 *
 * @param int $post_id Post ID.
 */
function trinetix_save_home_section_meta( int $post_id ): void {
	if ( ! isset( $_POST['trinetix_home_sections_nonce'] ) || ! wp_verify_nonce( sanitize_text_field( wp_unslash( $_POST['trinetix_home_sections_nonce'] ) ), 'trinetix_save_home_sections' ) ) {
		return;
	}
	if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
		return;
	}
	if ( ! current_user_can( 'edit_page', $post_id ) ) {
		return;
	}

	// Allow save when Home template is selected on this request, or page already has home sections.
	$posted_template = isset( $_POST['page_template'] ) ? sanitize_text_field( wp_unslash( $_POST['page_template'] ) ) : '';
	$is_home_tpl     = ( 'templates/home.php' === $posted_template ) || trinetix_page_has_home_sections( $post_id );
	if ( ! $is_home_tpl ) {
		return;
	}

	$text_keys = array(
		'hero_eyebrow',
		'hero_subline',
		'hero_cta_text',
		'hero_cta_url',
		'hero_cta_secondary_text',
		'hero_cta_secondary_url',
		'hero_playback_rate',
		'intro_cta_text',
		'intro_cta_url',
		'intro_video_url',
		'services_title',
		'industries_title',
		'work_title',
		'testimonials_title',
		'partners_title',
		'knowledge_title',
		'knowledge_cta_text',
		'knowledge_cta_url',
		'contact_heading',
	);

	$textarea_keys = array(
		'hero_heading',
		'hero_description',
		'hero_capabilities_json',
		'intro_heading',
		'intro_content',
		'intro_video_label',
		'contact_copy',
		'contact_pills',
	);

	$int_keys = array(
		'hero_video_id',
		'hero_video_mobile_id',
		'hero_poster_id',
		'intro_video_image_id',
	);

	foreach ( $text_keys as $key ) {
		$meta_key = trinetix_home_meta_key( $key );
		if ( isset( $_POST[ $meta_key ] ) ) {
			$val = sanitize_text_field( wp_unslash( $_POST[ $meta_key ] ) );
			if ( in_array( $key, array( 'hero_cta_url', 'hero_cta_secondary_url', 'intro_cta_url', 'intro_video_url', 'knowledge_cta_url' ), true )
				&& $val
				&& ! str_starts_with( $val, '#' )
			) {
				$val = esc_url_raw( $val );
			}
			update_post_meta( $post_id, $meta_key, $val );
		}
	}

	foreach ( $textarea_keys as $key ) {
		$meta_key = trinetix_home_meta_key( $key );
		if ( isset( $_POST[ $meta_key ] ) ) {
			update_post_meta( $post_id, $meta_key, sanitize_textarea_field( wp_unslash( $_POST[ $meta_key ] ) ) );
		}
	}

	foreach ( $int_keys as $key ) {
		$meta_key = trinetix_home_meta_key( $key );
		if ( isset( $_POST[ $meta_key ] ) ) {
			update_post_meta( $post_id, $meta_key, absint( wp_unslash( $_POST[ $meta_key ] ) ) );
		}
	}

	$video_key = trinetix_home_meta_key( 'hero_video_enabled' );
	update_post_meta( $post_id, $video_key, ! empty( $_POST[ $video_key ] ) ? 1 : 0 );

	foreach ( array_keys( trinetix_home_section_toggles() ) as $slug ) {
		$key = '_trinetix_show_section_' . $slug;
		update_post_meta( $post_id, $key, ! empty( $_POST[ $key ] ) ? 1 : 0 );
	}
}
add_action( 'save_post_page', 'trinetix_save_home_section_meta' );

/**
 * Save header/footer visibility.
 *
 * @param int $post_id Post ID.
 */
function trinetix_save_page_layout_meta( int $post_id ): void {
	if ( ! isset( $_POST['trinetix_page_layout_nonce'] ) || ! wp_verify_nonce( sanitize_text_field( wp_unslash( $_POST['trinetix_page_layout_nonce'] ) ), 'trinetix_save_page_layout' ) ) {
		return;
	}
	if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
		return;
	}
	if ( ! current_user_can( 'edit_page', $post_id ) ) {
		return;
	}

	update_post_meta( $post_id, '_trinetix_show_header', ! empty( $_POST['_trinetix_show_header'] ) ? 1 : 0 );
	update_post_meta( $post_id, '_trinetix_show_footer', ! empty( $_POST['_trinetix_show_footer'] ) ? 1 : 0 );
}
add_action( 'save_post_page', 'trinetix_save_page_layout_meta' );

/**
 * Save generic page hero meta.
 *
 * @param int $post_id Post ID.
 */
function trinetix_save_page_hero_meta( int $post_id ): void {
	if ( ! isset( $_POST['trinetix_page_hero_nonce'] ) || ! wp_verify_nonce( sanitize_text_field( wp_unslash( $_POST['trinetix_page_hero_nonce'] ) ), 'trinetix_save_page_hero' ) ) {
		return;
	}
	if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
		return;
	}
	if ( ! current_user_can( 'edit_page', $post_id ) ) {
		return;
	}

	if ( isset( $_POST['_trinetix_page_hero_title'] ) ) {
		update_post_meta( $post_id, '_trinetix_page_hero_title', sanitize_text_field( wp_unslash( $_POST['_trinetix_page_hero_title'] ) ) );
	}
	if ( isset( $_POST['_trinetix_page_hero_subline'] ) ) {
		update_post_meta( $post_id, '_trinetix_page_hero_subline', sanitize_textarea_field( wp_unslash( $_POST['_trinetix_page_hero_subline'] ) ) );
	}
	if ( isset( $_POST['_trinetix_page_hero_image_id'] ) ) {
		update_post_meta( $post_id, '_trinetix_page_hero_image_id', absint( wp_unslash( $_POST['_trinetix_page_hero_image_id'] ) ) );
	}
}
add_action( 'save_post_page', 'trinetix_save_page_hero_meta' );

/**
 * When user switches template to Home in the editor, seed empty editor guide.
 *
 * @param int $post_id Post ID.
 */
function trinetix_maybe_seed_home_editor_on_template_switch( int $post_id ): void {
	if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
		return;
	}
	if ( ! current_user_can( 'edit_page', $post_id ) ) {
		return;
	}
	$posted_template = isset( $_POST['page_template'] ) ? sanitize_text_field( wp_unslash( $_POST['page_template'] ) ) : ''; // phpcs:ignore WordPress.Security.NonceVerification.Missing
	if ( 'templates/home.php' !== $posted_template ) {
		return;
	}
	$post = get_post( $post_id );
	if ( ! $post instanceof WP_Post ) {
		return;
	}
	if ( '' !== trim( wp_strip_all_tags( (string) $post->post_content ) ) ) {
		return;
	}
	if ( ! function_exists( 'trinetix_home_page_editor_guide' ) ) {
		return;
	}
	remove_action( 'save_post_page', 'trinetix_maybe_seed_home_editor_on_template_switch' );
	wp_update_post(
		array(
			'ID'           => $post_id,
			'post_content' => trinetix_home_page_editor_guide(),
		)
	);
	add_action( 'save_post_page', 'trinetix_maybe_seed_home_editor_on_template_switch' );
}
add_action( 'save_post_page', 'trinetix_maybe_seed_home_editor_on_template_switch', 20 );

/**
 * Remind editors to pick the Home template for full section boxes.
 *
 * @param WP_Post $post Post.
 */
function trinetix_home_template_admin_notice(): void {
	$screen = function_exists( 'get_current_screen' ) ? get_current_screen() : null;
	if ( ! $screen || 'page' !== $screen->id ) {
		return;
	}
	$post_id = isset( $_GET['post'] ) ? (int) $_GET['post'] : 0; // phpcs:ignore WordPress.Security.NonceVerification.Recommended
	if ( ! $post_id ) {
		return;
	}
	$front = trinetix_front_page_id();
	if ( $front && $post_id === $front && ! trinetix_is_home_template( $post_id ) ) {
		echo '<div class="notice notice-warning"><p>';
		echo esc_html__( 'This is your site front page. Set Page Attributes → Template to “Home” to edit all homepage sections (Hero, Intro, titles, Contact).', 'trinetix' );
		echo '</p></div>';
	}
}
add_action( 'admin_notices', 'trinetix_home_template_admin_notice' );
