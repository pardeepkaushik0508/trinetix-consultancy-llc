<?php
/**
 * Trinetix Settings admin page.
 *
 * @package Trinetix
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Register settings menu and option.
 */
function trinetix_register_admin_settings(): void {
	register_setting(
		'trinetix_settings_group',
		'trinetix_settings',
		array(
			'type'              => 'array',
			'sanitize_callback' => 'trinetix_sanitize_settings',
			'default'           => trinetix_default_settings(),
		)
	);

	add_menu_page(
		__( 'Trinetix Settings', 'trinetix' ),
		__( 'Trinetix Settings', 'trinetix' ),
		'manage_options',
		'trinetix-settings',
		'trinetix_render_settings_page',
		'dashicons-admin-customizer',
		59
	);
}
add_action( 'admin_menu', 'trinetix_register_admin_settings' );

/**
 * Sanitize settings array.
 *
 * @param mixed $input Raw input.
 * @return array<string, mixed>
 */
function trinetix_sanitize_settings( $input ): array {
	$defaults = trinetix_default_settings();
	$existing = get_option( 'trinetix_settings', array() );
	if ( ! is_array( $existing ) ) {
		$existing = array();
	}
	// Merge so tabbed saves do not wipe other tabs.
	$output = wp_parse_args( $existing, $defaults );

	if ( ! is_array( $input ) ) {
		return $output;
	}

	$text_keys = array(
		'company_name',
		'brand_tagline',
		'header_cta_text',
		'header_cta_url',
		'hero_eyebrow',
		'hero_heading',
		'hero_subline',
		'hero_cta_text',
		'hero_cta_url',
		'hero_cta_secondary_text',
		'hero_cta_secondary_url',
		'hero_playback_rate',
		'intro_heading',
		'intro_cta_text',
		'intro_cta_url',
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
		'contact_email',
		'contact_phone',
		'contact_address',
		'contact_recipient',
		'social_linkedin',
		'social_twitter',
		'social_youtube',
		'social_facebook',
		'footer_copyright',
		'footer_privacy_url',
		'footer_terms_url',
		'seo_default_title',
		'organization_url',
	);

	$textarea_keys = array(
		'hero_description',
		'hero_capabilities_json',
		'intro_content',
		'contact_copy',
		'contact_pills',
		'footer_intro',
		'seo_default_description',
	);

	$int_keys = array(
		'hero_video_id',
		'hero_video_mobile_id',
		'hero_poster_id',
		'intro_video_image_id',
		'seo_og_image_id',
		'hero_video_enabled',
	);

	foreach ( $text_keys as $key ) {
		if ( isset( $input[ $key ] ) ) {
			$output[ $key ] = sanitize_text_field( $input[ $key ] );
		}
	}

	foreach ( $textarea_keys as $key ) {
		if ( isset( $input[ $key ] ) ) {
			$output[ $key ] = sanitize_textarea_field( $input[ $key ] );
		}
	}

	foreach ( $int_keys as $key ) {
		if ( isset( $input[ $key ] ) ) {
			$output[ $key ] = absint( $input[ $key ] );
		}
	}

	// Preserve newlines in hero heading.
	if ( isset( $input['hero_heading'] ) ) {
		$output['hero_heading'] = sanitize_textarea_field( $input['hero_heading'] );
	}

	if ( isset( $input['contact_email'] ) ) {
		$output['contact_email'] = sanitize_email( $input['contact_email'] );
	}
	if ( isset( $input['contact_recipient'] ) ) {
		$output['contact_recipient'] = sanitize_email( $input['contact_recipient'] );
	}

	// Checkbox only present when Hero tab is submitted.
	if ( array_key_exists( 'hero_heading', $input ) ) {
		$output['hero_video_enabled'] = ! empty( $input['hero_video_enabled'] ) ? 1 : 0;
	}

	$url_keys = array(
		'header_cta_url',
		'hero_cta_url',
		'hero_cta_secondary_url',
		'intro_cta_url',
		'intro_video_url',
		'knowledge_cta_url',
		'social_linkedin',
		'social_twitter',
		'social_youtube',
		'social_facebook',
		'footer_privacy_url',
		'footer_terms_url',
		'organization_url',
	);

	foreach ( $url_keys as $key ) {
		if ( ! empty( $output[ $key ] ) && ! str_starts_with( (string) $output[ $key ], '#' ) ) {
			$output[ $key ] = esc_url_raw( (string) $output[ $key ] );
		}
	}

	return $output;
}

/**
 * Render settings page.
 */
function trinetix_render_settings_page(): void {
	if ( ! current_user_can( 'manage_options' ) ) {
		return;
	}

	$tab      = isset( $_GET['tab'] ) ? sanitize_key( wp_unslash( $_GET['tab'] ) ) : 'general'; // phpcs:ignore WordPress.Security.NonceVerification.Recommended
	$settings = trinetix_get_settings();
	$tabs     = array(
		'general'  => __( 'General', 'trinetix' ),
		'header'   => __( 'Header', 'trinetix' ),
		'hero'     => __( 'Hero', 'trinetix' ),
		'homepage' => __( 'Homepage', 'trinetix' ),
		'contact'  => __( 'Contact', 'trinetix' ),
		'social'   => __( 'Social', 'trinetix' ),
		'footer'   => __( 'Footer', 'trinetix' ),
		'seo'      => __( 'SEO Defaults', 'trinetix' ),
	);

	if ( ! isset( $tabs[ $tab ] ) ) {
		$tab = 'general';
	}
	?>
	<div class="wrap">
		<h1><?php esc_html_e( 'Trinetix Settings', 'trinetix' ); ?></h1>
		<nav class="nav-tab-wrapper">
			<?php foreach ( $tabs as $key => $label ) : ?>
				<a class="nav-tab <?php echo $tab === $key ? 'nav-tab-active' : ''; ?>" href="<?php echo esc_url( admin_url( 'admin.php?page=trinetix-settings&tab=' . $key ) ); ?>"><?php echo esc_html( $label ); ?></a>
			<?php endforeach; ?>
		</nav>
		<form method="post" action="options.php">
			<?php settings_fields( 'trinetix_settings_group' ); ?>
			<table class="form-table" role="presentation">
				<?php
				switch ( $tab ) {
					case 'header':
						trinetix_settings_field_text( 'brand_tagline', __( 'Brand tagline', 'trinetix' ), $settings );
						trinetix_settings_field_text( 'header_cta_text', __( 'Contact CTA text', 'trinetix' ), $settings );
						trinetix_settings_field_text( 'header_cta_url', __( 'Contact CTA URL', 'trinetix' ), $settings );
						break;
					case 'hero':
						trinetix_settings_field_text( 'hero_eyebrow', __( 'Hero eyebrow', 'trinetix' ), $settings );
						trinetix_settings_field_textarea( 'hero_heading', __( 'Hero main heading (use line breaks)', 'trinetix' ), $settings );
						trinetix_settings_field_text( 'hero_subline', __( 'Hero subline', 'trinetix' ), $settings );
						trinetix_settings_field_textarea( 'hero_description', __( 'Hero description', 'trinetix' ), $settings );
						trinetix_settings_field_text( 'hero_cta_text', __( 'Primary CTA text', 'trinetix' ), $settings );
						trinetix_settings_field_text( 'hero_cta_url', __( 'Primary CTA URL', 'trinetix' ), $settings );
						trinetix_settings_field_text( 'hero_cta_secondary_text', __( 'Secondary CTA text', 'trinetix' ), $settings );
						trinetix_settings_field_text( 'hero_cta_secondary_url', __( 'Secondary CTA URL', 'trinetix' ), $settings );
						trinetix_settings_field_checkbox( 'hero_video_enabled', __( 'Enable hero video', 'trinetix' ), $settings );
						echo '<tr><th>' . esc_html__( 'Desktop hero video', 'trinetix' ) . '</th><td>';
						trinetix_render_media_field( 'trinetix_settings[hero_video_id]', (int) $settings['hero_video_id'], '', 'video', 'hero_video_id' );
						echo '</td></tr>';
						echo '<tr><th>' . esc_html__( 'Mobile hero video', 'trinetix' ) . '</th><td>';
						trinetix_render_media_field( 'trinetix_settings[hero_video_mobile_id]', (int) $settings['hero_video_mobile_id'], '', 'video', 'hero_video_mobile_id' );
						echo '</td></tr>';
						echo '<tr><th>' . esc_html__( 'Hero poster image', 'trinetix' ) . '</th><td>';
						trinetix_render_media_field( 'trinetix_settings[hero_poster_id]', (int) $settings['hero_poster_id'], '', 'image', 'hero_poster_id' );
						echo '</td></tr>';
						trinetix_settings_field_text( 'hero_playback_rate', __( 'Playback rate (e.g. 1 or 0.9)', 'trinetix' ), $settings );
						trinetix_settings_field_textarea( 'hero_capabilities_json', __( 'Capability cards JSON (optional advanced)', 'trinetix' ), $settings );
						break;
					case 'homepage':
						trinetix_settings_field_textarea( 'intro_heading', __( 'Intro heading', 'trinetix' ), $settings );
						trinetix_settings_field_textarea( 'intro_content', __( 'Intro content (optional HTML paragraphs as plain text lines)', 'trinetix' ), $settings );
						trinetix_settings_field_text( 'intro_cta_text', __( 'Intro CTA text', 'trinetix' ), $settings );
						trinetix_settings_field_text( 'intro_cta_url', __( 'Intro CTA URL', 'trinetix' ), $settings );
						echo '<tr><th>' . esc_html__( 'Intro video card image', 'trinetix' ) . '</th><td>';
						trinetix_render_media_field( 'trinetix_settings[intro_video_image_id]', (int) $settings['intro_video_image_id'], '', 'image', 'intro_video_image_id' );
						echo '</td></tr>';
						trinetix_settings_field_textarea( 'intro_video_label', __( 'Intro video label', 'trinetix' ), $settings );
						trinetix_settings_field_text( 'intro_video_url', __( 'Intro video URL', 'trinetix' ), $settings );
						trinetix_settings_field_text( 'services_title', __( 'Services section title', 'trinetix' ), $settings );
						trinetix_settings_field_text( 'industries_title', __( 'Industries section title', 'trinetix' ), $settings );
						trinetix_settings_field_text( 'work_title', __( 'Work section title', 'trinetix' ), $settings );
						trinetix_settings_field_text( 'testimonials_title', __( 'Testimonials title', 'trinetix' ), $settings );
						trinetix_settings_field_text( 'partners_title', __( 'Partners title', 'trinetix' ), $settings );
						trinetix_settings_field_text( 'knowledge_title', __( 'Knowledge Hub title', 'trinetix' ), $settings );
						trinetix_settings_field_text( 'knowledge_cta_text', __( 'Knowledge CTA text', 'trinetix' ), $settings );
						trinetix_settings_field_text( 'knowledge_cta_url', __( 'Knowledge CTA URL', 'trinetix' ), $settings );
						break;
					case 'contact':
						trinetix_settings_field_text( 'contact_heading', __( 'Contact heading', 'trinetix' ), $settings );
						trinetix_settings_field_textarea( 'contact_copy', __( 'Contact supporting text', 'trinetix' ), $settings );
						trinetix_settings_field_text( 'contact_email', __( 'Display email', 'trinetix' ), $settings );
						trinetix_settings_field_text( 'contact_phone', __( 'Phone', 'trinetix' ), $settings );
						trinetix_settings_field_text( 'contact_address', __( 'Address', 'trinetix' ), $settings );
						trinetix_settings_field_text( 'contact_recipient', __( 'Form recipient email', 'trinetix' ), $settings );
						trinetix_settings_field_textarea( 'contact_pills', __( 'Interest pills (one per line)', 'trinetix' ), $settings );
						break;
					case 'social':
						trinetix_settings_field_text( 'social_linkedin', __( 'LinkedIn URL', 'trinetix' ), $settings );
						trinetix_settings_field_text( 'social_twitter', __( 'X / Twitter URL', 'trinetix' ), $settings );
						trinetix_settings_field_text( 'social_youtube', __( 'YouTube URL', 'trinetix' ), $settings );
						trinetix_settings_field_text( 'social_facebook', __( 'Facebook URL', 'trinetix' ), $settings );
						break;
					case 'footer':
						trinetix_settings_field_textarea( 'footer_intro', __( 'Footer introduction', 'trinetix' ), $settings );
						trinetix_settings_field_text( 'footer_copyright', __( 'Copyright text', 'trinetix' ), $settings );
						trinetix_settings_field_text( 'footer_privacy_url', __( 'Privacy Policy URL', 'trinetix' ), $settings );
						trinetix_settings_field_text( 'footer_terms_url', __( 'Terms & Conditions URL', 'trinetix' ), $settings );
						break;
					case 'seo':
						trinetix_settings_field_text( 'seo_default_title', __( 'Default SEO title fallback', 'trinetix' ), $settings );
						trinetix_settings_field_textarea( 'seo_default_description', __( 'Default meta description', 'trinetix' ), $settings );
						echo '<tr><th>' . esc_html__( 'Default OG image', 'trinetix' ) . '</th><td>';
						trinetix_render_media_field( 'trinetix_settings[seo_og_image_id]', (int) $settings['seo_og_image_id'], '', 'image', 'seo_og_image_id' );
						echo '</td></tr>';
						trinetix_settings_field_text( 'organization_url', __( 'Organization URL', 'trinetix' ), $settings );
						break;
					case 'general':
					default:
						trinetix_settings_field_text( 'company_name', __( 'Company name', 'trinetix' ), $settings );
						trinetix_settings_field_text( 'brand_tagline', __( 'Brand tagline', 'trinetix' ), $settings );
						trinetix_settings_field_text( 'organization_url', __( 'Website URL', 'trinetix' ), $settings );
						break;
				}
				?>
			</table>
			<?php submit_button(); ?>
		</form>
	</div>
	<?php
}

/**
 * Text setting row.
 *
 * @param string               $key      Key.
 * @param string               $label    Label.
 * @param array<string, mixed> $settings Settings.
 */
function trinetix_settings_field_text( string $key, string $label, array $settings ): void {
	$value = isset( $settings[ $key ] ) ? (string) $settings[ $key ] : '';
	printf(
		'<tr><th scope="row"><label for="%1$s">%2$s</label></th><td><input name="trinetix_settings[%1$s]" id="%1$s" type="text" class="regular-text" value="%3$s" /></td></tr>',
		esc_attr( $key ),
		esc_html( $label ),
		esc_attr( $value )
	);
}

/**
 * Textarea setting row.
 *
 * @param string               $key      Key.
 * @param string               $label    Label.
 * @param array<string, mixed> $settings Settings.
 */
function trinetix_settings_field_textarea( string $key, string $label, array $settings ): void {
	$value = isset( $settings[ $key ] ) ? (string) $settings[ $key ] : '';
	printf(
		'<tr><th scope="row"><label for="%1$s">%2$s</label></th><td><textarea name="trinetix_settings[%1$s]" id="%1$s" class="large-text" rows="5">%3$s</textarea></td></tr>',
		esc_attr( $key ),
		esc_html( $label ),
		esc_textarea( $value )
	);
}

/**
 * Checkbox setting row.
 *
 * @param string               $key      Key.
 * @param string               $label    Label.
 * @param array<string, mixed> $settings Settings.
 */
function trinetix_settings_field_checkbox( string $key, string $label, array $settings ): void {
	$checked = ! empty( $settings[ $key ] );
	printf(
		'<tr><th scope="row">%1$s</th><td><label><input type="checkbox" name="trinetix_settings[%2$s]" value="1" %3$s /> %1$s</label></td></tr>',
		esc_html( $label ),
		esc_attr( $key ),
		checked( $checked, true, false )
	);
}
