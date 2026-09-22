<?php
/**
 * Trinetix Consultancy LLC theme bootstrap.
 *
 * @package Trinetix
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

define( 'TRINETIX_VERSION', '1.0.0' );
define( 'TRINETIX_DIR', get_template_directory() );
define( 'TRINETIX_URI', get_template_directory_uri() );

$trinetix_includes = array(
	'/inc/helpers.php',
	'/inc/theme-setup.php',
	'/inc/enqueue.php',
	'/inc/custom-post-types.php',
	'/inc/taxonomies.php',
	'/inc/meta-boxes.php',
	'/inc/media-fields.php',
	'/inc/classic-editor.php',
	'/inc/page-sections.php',
	'/inc/shortcodes.php',
	'/inc/header-footer-settings.php',
	'/inc/admin-settings.php',
	'/inc/contact-handler.php',
	'/inc/seo.php',
	'/inc/schema.php',
	'/inc/security.php',
	'/inc/demo-content.php',
);

foreach ( $trinetix_includes as $trinetix_file ) {
	$trinetix_path = TRINETIX_DIR . $trinetix_file;
	if ( is_readable( $trinetix_path ) ) {
		require_once $trinetix_path;
	}
}
