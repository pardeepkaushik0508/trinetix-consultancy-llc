<?php
/**
 * Appearance → Header & Footer settings.
 *
 * @package Trinetix
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Default header/footer options.
 *
 * @return array<string, mixed>
 */
function trinetix_hf_defaults(): array {
	return array(
		// Header.
		'header_logo_id'         => 0,
		'header_heading'         => 'Trinetix Consulting LLC',
		'header_tagline'         => 'Vision Beyond Technology',
		'header_show_tagline'    => 1,
		'header_show_menu'       => 1,
		'header_menu_id'         => 0,
		'header_show_search'     => 1,
		'header_show_cta'        => 1,
		'header_cta_text'        => 'Contact Us',
		'header_cta_url'         => '#contact',
		'header_cta_font_size'   => '',
		'header_menu_font_size'  => '',
		'header_enabled'         => 1,
		// Footer.
		'footer_enabled'         => 1,
		'footer_logo_id'         => 0,
		'footer_heading'         => 'Trinetix Consulting LLC',
		'footer_tagline'         => 'Vision Beyond Technology',
		'footer_show_tagline'    => 1,
		'footer_intro'           => 'We engineer intelligent enterprises by connecting domain strategy, cognitive architecture, data and modern engineering into one practical transformation model.',
		'footer_show_intro'      => 1,
		'footer_show_menus'      => 1,
		'footer_menu_ai'         => 0,
		'footer_menu_data'       => 0,
		'footer_menu_engineering'=> 0,
		'footer_menu_experience' => 0,
		'footer_menu_company'    => 0,
		'footer_menu_legal'      => 0,
		'footer_copyright'       => '© 2026 Trinetix Consulting LLC. All rights reserved.',
		'footer_privacy_url'     => '',
		'footer_privacy_label'   => 'Privacy Policy',
		'footer_terms_url'       => '',
		'footer_terms_label'     => 'Terms & Conditions',
		'footer_show_legal'      => 1,
	);
}

/**
 * Get all header/footer settings (merged with defaults).
 *
 * @return array<string, mixed>
 */
function trinetix_get_hf_settings(): array {
	$stored = get_option( 'trinetix_header_footer', array() );
	if ( ! is_array( $stored ) ) {
		$stored = array();
	}
	return wp_parse_args( $stored, trinetix_hf_defaults() );
}

/**
 * Get one header/footer setting.
 *
 * @param string $key     Key.
 * @param mixed  $default Fallback.
 * @return mixed
 */
function trinetix_get_hf( string $key, $default = '' ) {
	$settings = trinetix_get_hf_settings();
	if ( array_key_exists( $key, $settings ) && '' !== $settings[ $key ] && null !== $settings[ $key ] ) {
		return $settings[ $key ];
	}
	return $default;
}

/**
 * Register Appearance submenu.
 */
function trinetix_register_header_footer_page(): void {
	add_theme_page(
		__( 'Header & Footer', 'trinetix' ),
		__( 'Header & Footer', 'trinetix' ),
		'edit_theme_options',
		'trinetix-header-footer',
		'trinetix_render_header_footer_page'
	);
}
add_action( 'admin_menu', 'trinetix_register_header_footer_page' );

/**
 * Enqueue media on this screen.
 *
 * @param string $hook Hook.
 */
function trinetix_hf_admin_assets( string $hook ): void {
	if ( 'appearance_page_trinetix-header-footer' !== $hook ) {
		return;
	}
	wp_enqueue_media();
	wp_enqueue_style(
		'trinetix-admin',
		trinetix_asset_uri( 'assets/css/editor.css' ),
		array(),
		trinetix_asset_version( 'assets/css/editor.css' )
	);
}
add_action( 'admin_enqueue_scripts', 'trinetix_hf_admin_assets' );

/**
 * One-time migrate from theme settings / customizer.
 */
function trinetix_maybe_migrate_hf_settings(): void {
	if ( get_option( 'trinetix_hf_migrated' ) ) {
		return;
	}

	$hf       = trinetix_hf_defaults();
	$settings = function_exists( 'trinetix_get_settings' ) ? trinetix_get_settings() : array();

	$hf['header_heading']       = (string) ( $settings['company_name'] ?? $hf['header_heading'] );
	$hf['header_tagline']       = (string) ( $settings['brand_tagline'] ?? $hf['header_tagline'] );
	$hf['header_cta_text']      = (string) ( $settings['header_cta_text'] ?? $hf['header_cta_text'] );
	$hf['header_cta_url']       = (string) ( $settings['header_cta_url'] ?? $hf['header_cta_url'] );
	$hf['footer_heading']       = $hf['header_heading'];
	$hf['footer_tagline']       = $hf['header_tagline'];
	$hf['footer_intro']         = (string) ( $settings['footer_intro'] ?? $hf['footer_intro'] );
	$hf['footer_copyright']     = (string) ( $settings['footer_copyright'] ?? $hf['footer_copyright'] );
	$hf['footer_privacy_url']   = (string) ( $settings['footer_privacy_url'] ?? '' );
	$hf['footer_terms_url']     = (string) ( $settings['footer_terms_url'] ?? '' );

	$logo_id = (int) get_theme_mod( 'custom_logo' );
	if ( $logo_id ) {
		$hf['header_logo_id'] = $logo_id;
		$hf['footer_logo_id'] = $logo_id;
	}

	$locations = get_nav_menu_locations();
	if ( ! empty( $locations['primary'] ) ) {
		$hf['header_menu_id'] = (int) $locations['primary'];
	}
	$map = array(
		'footer_ai'          => 'footer_menu_ai',
		'footer_data'        => 'footer_menu_data',
		'footer_engineering' => 'footer_menu_engineering',
		'footer_experience'  => 'footer_menu_experience',
		'footer_company'     => 'footer_menu_company',
		'footer_legal'       => 'footer_menu_legal',
	);
	foreach ( $map as $location => $key ) {
		if ( ! empty( $locations[ $location ] ) ) {
			$hf[ $key ] = (int) $locations[ $location ];
		}
	}

	update_option( 'trinetix_header_footer', $hf, false );
	update_option( 'trinetix_hf_migrated', 1, false );
}
add_action( 'admin_init', 'trinetix_maybe_migrate_hf_settings' );

/**
 * Handle form save.
 */
function trinetix_handle_hf_save(): void {
	if ( ! isset( $_POST['trinetix_hf_nonce'] ) || ! wp_verify_nonce( sanitize_text_field( wp_unslash( $_POST['trinetix_hf_nonce'] ) ), 'trinetix_save_hf' ) ) {
		return;
	}
	if ( ! current_user_can( 'edit_theme_options' ) ) {
		return;
	}
	if ( ! isset( $_POST['trinetix_hf'] ) || ! is_array( $_POST['trinetix_hf'] ) ) {
		return;
	}

	$raw = wp_unslash( $_POST['trinetix_hf'] ); // phpcs:ignore WordPress.Security.ValidatedSanitizedInput.InputNotSanitized
	$out = trinetix_get_hf_settings();

	$text = array(
		'header_heading',
		'header_tagline',
		'header_cta_text',
		'header_cta_font_size',
		'header_menu_font_size',
		'footer_heading',
		'footer_tagline',
		'footer_copyright',
		'footer_privacy_label',
		'footer_terms_label',
	);
	foreach ( $text as $key ) {
		if ( isset( $raw[ $key ] ) ) {
			$out[ $key ] = sanitize_text_field( $raw[ $key ] );
		}
	}

	$textarea = array( 'footer_intro' );
	foreach ( $textarea as $key ) {
		if ( isset( $raw[ $key ] ) ) {
			$out[ $key ] = sanitize_textarea_field( $raw[ $key ] );
		}
	}

	$urls = array( 'header_cta_url', 'footer_privacy_url', 'footer_terms_url' );
	foreach ( $urls as $key ) {
		if ( isset( $raw[ $key ] ) ) {
			$val = sanitize_text_field( $raw[ $key ] );
			if ( $val && ! str_starts_with( $val, '#' ) ) {
				$val = esc_url_raw( $val );
			}
			$out[ $key ] = $val;
		}
	}

	$ints = array(
		'header_logo_id',
		'header_menu_id',
		'footer_logo_id',
		'footer_menu_ai',
		'footer_menu_data',
		'footer_menu_engineering',
		'footer_menu_experience',
		'footer_menu_company',
		'footer_menu_legal',
	);
	foreach ( $ints as $key ) {
		if ( isset( $raw[ $key ] ) ) {
			$out[ $key ] = absint( $raw[ $key ] );
		}
	}

	$checks = array(
		'header_enabled',
		'header_show_tagline',
		'header_show_menu',
		'header_show_search',
		'header_show_cta',
		'footer_enabled',
		'footer_show_tagline',
		'footer_show_intro',
		'footer_show_menus',
		'footer_show_legal',
	);
	foreach ( $checks as $key ) {
		$out[ $key ] = ! empty( $raw[ $key ] ) ? 1 : 0;
	}

	update_option( 'trinetix_header_footer', $out, false );

	// Keep Customizer logo in sync when header logo set.
	if ( ! empty( $out['header_logo_id'] ) ) {
		set_theme_mod( 'custom_logo', (int) $out['header_logo_id'] );
	}

	// Sync selected menus to theme locations.
	$locations = get_theme_mod( 'nav_menu_locations', array() );
	if ( ! is_array( $locations ) ) {
		$locations = array();
	}
	if ( ! empty( $out['header_menu_id'] ) ) {
		$locations['primary'] = (int) $out['header_menu_id'];
	}
	$loc_map = array(
		'footer_ai'          => 'footer_menu_ai',
		'footer_data'        => 'footer_menu_data',
		'footer_engineering' => 'footer_menu_engineering',
		'footer_experience'  => 'footer_menu_experience',
		'footer_company'     => 'footer_menu_company',
		'footer_legal'       => 'footer_menu_legal',
	);
	foreach ( $loc_map as $location => $key ) {
		if ( ! empty( $out[ $key ] ) ) {
			$locations[ $location ] = (int) $out[ $key ];
		} else {
			unset( $locations[ $location ] );
		}
	}
	set_theme_mod( 'nav_menu_locations', $locations );

	// Keep slim Trinetix Settings in sync for backwards compatibility.
	$settings = get_option( 'trinetix_settings', array() );
	if ( ! is_array( $settings ) ) {
		$settings = array();
	}
	$settings['company_name']       = $out['header_heading'];
	$settings['brand_tagline']      = $out['header_tagline'];
	$settings['header_cta_text']    = $out['header_cta_text'];
	$settings['header_cta_url']     = $out['header_cta_url'];
	$settings['footer_intro']       = $out['footer_intro'];
	$settings['footer_copyright']   = $out['footer_copyright'];
	$settings['footer_privacy_url'] = $out['footer_privacy_url'];
	$settings['footer_terms_url']   = $out['footer_terms_url'];
	update_option( 'trinetix_settings', $settings, false );

	add_settings_error( 'trinetix_hf', 'trinetix_hf_saved', __( 'Header & Footer settings saved.', 'trinetix' ), 'updated' );
}
add_action( 'admin_init', 'trinetix_handle_hf_save' );

/**
 * Nav menu dropdown options.
 *
 * @param int $selected Selected menu term ID.
 * @return string HTML options.
 */
function trinetix_hf_menu_options( int $selected = 0 ): string {
	$menus   = wp_get_nav_menus();
	$options = '<option value="0">' . esc_html__( '— Select a menu —', 'trinetix' ) . '</option>';
	foreach ( $menus as $menu ) {
		$options .= sprintf(
			'<option value="%1$d" %2$s>%3$s</option>',
			(int) $menu->term_id,
			selected( $selected, (int) $menu->term_id, false ),
			esc_html( $menu->name )
		);
	}
	return $options;
}

/**
 * Render the settings page.
 */
function trinetix_render_header_footer_page(): void {
	if ( ! current_user_can( 'edit_theme_options' ) ) {
		return;
	}

	settings_errors( 'trinetix_hf' );
	$hf  = trinetix_get_hf_settings();
	$tab = isset( $_GET['tab'] ) ? sanitize_key( wp_unslash( $_GET['tab'] ) ) : 'header'; // phpcs:ignore WordPress.Security.NonceVerification.Recommended
	if ( ! in_array( $tab, array( 'header', 'footer' ), true ) ) {
		$tab = 'header';
	}
	$base = admin_url( 'themes.php?page=trinetix-header-footer' );
	?>
	<div class="wrap">
		<h1><?php esc_html_e( 'Header & Footer', 'trinetix' ); ?></h1>
		<p class="description">
			<?php esc_html_e( 'Manage logo, menus, buttons, and text for the site header and footer. Menu items themselves are edited under Appearance → Menus.', 'trinetix' ); ?>
			<a href="<?php echo esc_url( admin_url( 'nav-menus.php' ) ); ?>"><?php esc_html_e( 'Edit menus', 'trinetix' ); ?></a>
		</p>

		<nav class="nav-tab-wrapper" style="margin-bottom:16px;">
			<a class="nav-tab <?php echo 'header' === $tab ? 'nav-tab-active' : ''; ?>" href="<?php echo esc_url( $base . '&tab=header' ); ?>"><?php esc_html_e( 'Header', 'trinetix' ); ?></a>
			<a class="nav-tab <?php echo 'footer' === $tab ? 'nav-tab-active' : ''; ?>" href="<?php echo esc_url( $base . '&tab=footer' ); ?>"><?php esc_html_e( 'Footer', 'trinetix' ); ?></a>
		</nav>

		<form method="post" action="<?php echo esc_url( $base . '&tab=' . $tab ); ?>">
			<?php wp_nonce_field( 'trinetix_save_hf', 'trinetix_hf_nonce' ); ?>
			<table class="form-table" role="presentation">
				<?php if ( 'header' === $tab ) : ?>
					<tr>
						<th><?php esc_html_e( 'Show header site-wide', 'trinetix' ); ?></th>
						<td><label><input type="checkbox" name="trinetix_hf[header_enabled]" value="1" <?php checked( ! empty( $hf['header_enabled'] ) ); ?> /> <?php esc_html_e( 'Display the header on all pages (individual pages can still hide it)', 'trinetix' ); ?></label></td>
					</tr>
					<tr>
						<th><?php esc_html_e( 'Logo', 'trinetix' ); ?></th>
						<td>
							<?php
							trinetix_render_media_field(
								'trinetix_hf[header_logo_id]',
								(int) $hf['header_logo_id'],
								__( 'Header logo', 'trinetix' ),
								'image',
								'hf_header_logo'
							);
							?>
							<p class="description"><?php esc_html_e( 'Also updates Appearance → Customize → Site Identity logo.', 'trinetix' ); ?></p>
						</td>
					</tr>
					<tr>
						<th><label for="hf_header_heading"><?php esc_html_e( 'Brand / heading name', 'trinetix' ); ?></label></th>
						<td><input type="text" class="regular-text" id="hf_header_heading" name="trinetix_hf[header_heading]" value="<?php echo esc_attr( (string) $hf['header_heading'] ); ?>" /></td>
					</tr>
					<tr>
						<th><label for="hf_header_tagline"><?php esc_html_e( 'Tagline', 'trinetix' ); ?></label></th>
						<td>
							<input type="text" class="regular-text" id="hf_header_tagline" name="trinetix_hf[header_tagline]" value="<?php echo esc_attr( (string) $hf['header_tagline'] ); ?>" />
							<p><label><input type="checkbox" name="trinetix_hf[header_show_tagline]" value="1" <?php checked( ! empty( $hf['header_show_tagline'] ) ); ?> /> <?php esc_html_e( 'Show tagline next to logo', 'trinetix' ); ?></label></p>
						</td>
					</tr>
					<tr>
						<th><?php esc_html_e( 'Navigation menu', 'trinetix' ); ?></th>
						<td>
							<select name="trinetix_hf[header_menu_id]" id="hf_header_menu">
								<?php echo trinetix_hf_menu_options( (int) $hf['header_menu_id'] ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
							</select>
							<p><label><input type="checkbox" name="trinetix_hf[header_show_menu]" value="1" <?php checked( ! empty( $hf['header_show_menu'] ) ); ?> /> <?php esc_html_e( 'Show menu in header', 'trinetix' ); ?></label></p>
							<p class="description">
								<?php esc_html_e( 'Choose which menu appears in the header. Create or edit items in', 'trinetix' ); ?>
								<a href="<?php echo esc_url( admin_url( 'nav-menus.php' ) ); ?>"><?php esc_html_e( 'Appearance → Menus', 'trinetix' ); ?></a>.
							</p>
						</td>
					</tr>
					<tr>
						<th><label for="hf_header_menu_font"><?php esc_html_e( 'Menu font size', 'trinetix' ); ?></label></th>
						<td>
							<input type="text" class="small-text" id="hf_header_menu_font" name="trinetix_hf[header_menu_font_size]" value="<?php echo esc_attr( (string) $hf['header_menu_font_size'] ); ?>" placeholder="15px" />
							<p class="description"><?php esc_html_e( 'Optional (e.g. 15px or 1rem). Leave blank for theme default.', 'trinetix' ); ?></p>
						</td>
					</tr>
					<tr>
						<th><?php esc_html_e( 'Search icon', 'trinetix' ); ?></th>
						<td><label><input type="checkbox" name="trinetix_hf[header_show_search]" value="1" <?php checked( ! empty( $hf['header_show_search'] ) ); ?> /> <?php esc_html_e( 'Show search button', 'trinetix' ); ?></label></td>
					</tr>
					<tr>
						<th><?php esc_html_e( 'Header button (CTA)', 'trinetix' ); ?></th>
						<td>
							<p><label><input type="checkbox" name="trinetix_hf[header_show_cta]" value="1" <?php checked( ! empty( $hf['header_show_cta'] ) ); ?> /> <?php esc_html_e( 'Show button', 'trinetix' ); ?></label></p>
							<p>
								<label for="hf_cta_text"><strong><?php esc_html_e( 'Button text', 'trinetix' ); ?></strong></label><br />
								<input type="text" class="regular-text" id="hf_cta_text" name="trinetix_hf[header_cta_text]" value="<?php echo esc_attr( (string) $hf['header_cta_text'] ); ?>" />
							</p>
							<p>
								<label for="hf_cta_url"><strong><?php esc_html_e( 'Button link', 'trinetix' ); ?></strong></label><br />
								<input type="text" class="regular-text" id="hf_cta_url" name="trinetix_hf[header_cta_url]" value="<?php echo esc_attr( (string) $hf['header_cta_url'] ); ?>" placeholder="#contact or https://" />
							</p>
							<p>
								<label for="hf_cta_font"><strong><?php esc_html_e( 'Button font size', 'trinetix' ); ?></strong></label><br />
								<input type="text" class="small-text" id="hf_cta_font" name="trinetix_hf[header_cta_font_size]" value="<?php echo esc_attr( (string) $hf['header_cta_font_size'] ); ?>" placeholder="14px" />
							</p>
						</td>
					</tr>
				<?php else : ?>
					<tr>
						<th><?php esc_html_e( 'Show footer site-wide', 'trinetix' ); ?></th>
						<td><label><input type="checkbox" name="trinetix_hf[footer_enabled]" value="1" <?php checked( ! empty( $hf['footer_enabled'] ) ); ?> /> <?php esc_html_e( 'Display the footer on all pages (individual pages can still hide it)', 'trinetix' ); ?></label></td>
					</tr>
					<tr>
						<th><?php esc_html_e( 'Footer logo', 'trinetix' ); ?></th>
						<td>
							<?php
							trinetix_render_media_field(
								'trinetix_hf[footer_logo_id]',
								(int) $hf['footer_logo_id'],
								__( 'Footer logo', 'trinetix' ),
								'image',
								'hf_footer_logo'
							);
							?>
						</td>
					</tr>
					<tr>
						<th><label for="hf_footer_heading"><?php esc_html_e( 'Footer heading / company name', 'trinetix' ); ?></label></th>
						<td><input type="text" class="regular-text" id="hf_footer_heading" name="trinetix_hf[footer_heading]" value="<?php echo esc_attr( (string) $hf['footer_heading'] ); ?>" /></td>
					</tr>
					<tr>
						<th><label for="hf_footer_tagline"><?php esc_html_e( 'Footer tagline', 'trinetix' ); ?></label></th>
						<td>
							<input type="text" class="regular-text" id="hf_footer_tagline" name="trinetix_hf[footer_tagline]" value="<?php echo esc_attr( (string) $hf['footer_tagline'] ); ?>" />
							<p><label><input type="checkbox" name="trinetix_hf[footer_show_tagline]" value="1" <?php checked( ! empty( $hf['footer_show_tagline'] ) ); ?> /> <?php esc_html_e( 'Show tagline', 'trinetix' ); ?></label></p>
						</td>
					</tr>
					<tr>
						<th><label for="hf_footer_intro"><?php esc_html_e( 'Footer introduction', 'trinetix' ); ?></label></th>
						<td>
							<textarea class="large-text" rows="4" id="hf_footer_intro" name="trinetix_hf[footer_intro]"><?php echo esc_textarea( (string) $hf['footer_intro'] ); ?></textarea>
							<p><label><input type="checkbox" name="trinetix_hf[footer_show_intro]" value="1" <?php checked( ! empty( $hf['footer_show_intro'] ) ); ?> /> <?php esc_html_e( 'Show introduction text', 'trinetix' ); ?></label></p>
						</td>
					</tr>
					<tr>
						<th><?php esc_html_e( 'Footer menus', 'trinetix' ); ?></th>
						<td>
							<p><label><input type="checkbox" name="trinetix_hf[footer_show_menus]" value="1" <?php checked( ! empty( $hf['footer_show_menus'] ) ); ?> /> <?php esc_html_e( 'Show footer menu columns', 'trinetix' ); ?></label></p>
							<p class="description"><?php esc_html_e( 'Pick which menu appears in each column. Edit links under Appearance → Menus.', 'trinetix' ); ?></p>
							<p>
								<label><strong><?php esc_html_e( 'Artificial Intelligence', 'trinetix' ); ?></strong></label><br />
								<select name="trinetix_hf[footer_menu_ai]"><?php echo trinetix_hf_menu_options( (int) $hf['footer_menu_ai'] ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?></select>
							</p>
							<p>
								<label><strong><?php esc_html_e( 'Data & Analytics', 'trinetix' ); ?></strong></label><br />
								<select name="trinetix_hf[footer_menu_data]"><?php echo trinetix_hf_menu_options( (int) $hf['footer_menu_data'] ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?></select>
							</p>
							<p>
								<label><strong><?php esc_html_e( 'Digital Engineering', 'trinetix' ); ?></strong></label><br />
								<select name="trinetix_hf[footer_menu_engineering]"><?php echo trinetix_hf_menu_options( (int) $hf['footer_menu_engineering'] ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?></select>
							</p>
							<p>
								<label><strong><?php esc_html_e( 'Experience', 'trinetix' ); ?></strong></label><br />
								<select name="trinetix_hf[footer_menu_experience]"><?php echo trinetix_hf_menu_options( (int) $hf['footer_menu_experience'] ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?></select>
							</p>
							<p>
								<label><strong><?php esc_html_e( 'Company', 'trinetix' ); ?></strong></label><br />
								<select name="trinetix_hf[footer_menu_company]"><?php echo trinetix_hf_menu_options( (int) $hf['footer_menu_company'] ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?></select>
							</p>
							<p>
								<label><strong><?php esc_html_e( 'Legal (bottom bar)', 'trinetix' ); ?></strong></label><br />
								<select name="trinetix_hf[footer_menu_legal]"><?php echo trinetix_hf_menu_options( (int) $hf['footer_menu_legal'] ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?></select>
							</p>
						</td>
					</tr>
					<tr>
						<th><label for="hf_footer_copyright"><?php esc_html_e( 'Copyright text', 'trinetix' ); ?></label></th>
						<td><input type="text" class="large-text" id="hf_footer_copyright" name="trinetix_hf[footer_copyright]" value="<?php echo esc_attr( (string) $hf['footer_copyright'] ); ?>" /></td>
					</tr>
					<tr>
						<th><?php esc_html_e( 'Legal links (fallback)', 'trinetix' ); ?></th>
						<td>
							<p><label><input type="checkbox" name="trinetix_hf[footer_show_legal]" value="1" <?php checked( ! empty( $hf['footer_show_legal'] ) ); ?> /> <?php esc_html_e( 'Show privacy / terms links when no Legal menu is selected', 'trinetix' ); ?></label></p>
							<p>
								<label><strong><?php esc_html_e( 'Privacy label', 'trinetix' ); ?></strong></label><br />
								<input type="text" class="regular-text" name="trinetix_hf[footer_privacy_label]" value="<?php echo esc_attr( (string) $hf['footer_privacy_label'] ); ?>" />
							</p>
							<p>
								<label><strong><?php esc_html_e( 'Privacy URL', 'trinetix' ); ?></strong></label><br />
								<input type="text" class="regular-text" name="trinetix_hf[footer_privacy_url]" value="<?php echo esc_attr( (string) $hf['footer_privacy_url'] ); ?>" />
							</p>
							<p>
								<label><strong><?php esc_html_e( 'Terms label', 'trinetix' ); ?></strong></label><br />
								<input type="text" class="regular-text" name="trinetix_hf[footer_terms_label]" value="<?php echo esc_attr( (string) $hf['footer_terms_label'] ); ?>" />
							</p>
							<p>
								<label><strong><?php esc_html_e( 'Terms URL', 'trinetix' ); ?></strong></label><br />
								<input type="text" class="regular-text" name="trinetix_hf[footer_terms_url]" value="<?php echo esc_attr( (string) $hf['footer_terms_url'] ); ?>" />
							</p>
						</td>
					</tr>
				<?php endif; ?>
			</table>
			<?php
			// Preserve other tab fields on save.
			if ( 'header' === $tab ) {
				foreach ( array( 'footer_enabled', 'footer_show_tagline', 'footer_show_intro', 'footer_show_menus', 'footer_show_legal' ) as $ck ) {
					printf( '<input type="hidden" name="trinetix_hf[%1$s]" value="%2$s" />', esc_attr( $ck ), esc_attr( ! empty( $hf[ $ck ] ) ? '1' : '0' ) );
				}
				foreach ( array( 'footer_logo_id', 'footer_menu_ai', 'footer_menu_data', 'footer_menu_engineering', 'footer_menu_experience', 'footer_menu_company', 'footer_menu_legal' ) as $ik ) {
					printf( '<input type="hidden" name="trinetix_hf[%1$s]" value="%2$d" />', esc_attr( $ik ), (int) $hf[ $ik ] );
				}
				foreach ( array( 'footer_heading', 'footer_tagline', 'footer_intro', 'footer_copyright', 'footer_privacy_url', 'footer_privacy_label', 'footer_terms_url', 'footer_terms_label' ) as $tk ) {
					printf( '<input type="hidden" name="trinetix_hf[%1$s]" value="%2$s" />', esc_attr( $tk ), esc_attr( (string) $hf[ $tk ] ) );
				}
			} else {
				foreach ( array( 'header_enabled', 'header_show_tagline', 'header_show_menu', 'header_show_search', 'header_show_cta' ) as $ck ) {
					printf( '<input type="hidden" name="trinetix_hf[%1$s]" value="%2$s" />', esc_attr( $ck ), esc_attr( ! empty( $hf[ $ck ] ) ? '1' : '0' ) );
				}
				foreach ( array( 'header_logo_id', 'header_menu_id' ) as $ik ) {
					printf( '<input type="hidden" name="trinetix_hf[%1$s]" value="%2$d" />', esc_attr( $ik ), (int) $hf[ $ik ] );
				}
				foreach ( array( 'header_heading', 'header_tagline', 'header_cta_text', 'header_cta_url', 'header_cta_font_size', 'header_menu_font_size' ) as $tk ) {
					printf( '<input type="hidden" name="trinetix_hf[%1$s]" value="%2$s" />', esc_attr( $tk ), esc_attr( (string) $hf[ $tk ] ) );
				}
			}
			submit_button( __( 'Save Header & Footer', 'trinetix' ) );
			?>
		</form>
	</div>
	<?php
}

/**
 * Global header enabled (site-wide switch).
 *
 * @return bool
 */
function trinetix_hf_header_globally_enabled(): bool {
	return (int) trinetix_get_hf( 'header_enabled', 1 ) === 1;
}

/**
 * Global footer enabled (site-wide switch).
 *
 * @return bool
 */
function trinetix_hf_footer_globally_enabled(): bool {
	return (int) trinetix_get_hf( 'footer_enabled', 1 ) === 1;
}

/**
 * Inline CSS for optional header font sizes.
 */
function trinetix_hf_front_css(): void {
	$menu_size = (string) trinetix_get_hf( 'header_menu_font_size', '' );
	$cta_size  = (string) trinetix_get_hf( 'header_cta_font_size', '' );
	$rules     = array();
	if ( $menu_size && preg_match( '/^\d+(\.\d+)?(px|rem|em|%)$/', $menu_size ) ) {
		$rules[] = '.site-header .nav a{font-size:' . esc_attr( $menu_size ) . ' !important;}';
	}
	if ( $cta_size && preg_match( '/^\d+(\.\d+)?(px|rem|em|%)$/', $cta_size ) ) {
		$rules[] = '.site-header .header-actions .btn{font-size:' . esc_attr( $cta_size ) . ' !important;}';
	}
	if ( $rules ) {
		wp_add_inline_style( 'trinetix-main', implode( '', $rules ) );
	}
}
add_action( 'wp_enqueue_scripts', 'trinetix_hf_front_css', 30 );
