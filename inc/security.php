<?php
/**
 * Light theme hardening.
 *
 * @package Trinetix
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Remove unnecessary head noise.
 */
function trinetix_security_cleanup(): void {
	remove_action( 'wp_head', 'wp_generator' );
	remove_action( 'wp_head', 'wlwmanifest_link' );
	remove_action( 'wp_head', 'rsd_link' );
	remove_action( 'wp_head', 'wp_shortlink_wp_head' );
}
add_action( 'init', 'trinetix_security_cleanup' );

/**
 * Disable XML-RPC for non-authenticated public use when filter allows.
 *
 * @param mixed $enabled Current.
 * @return mixed
 */
function trinetix_disable_xmlrpc( $enabled ) {
	return apply_filters( 'trinetix_enable_xmlrpc', false ) ? $enabled : false;
}
add_filter( 'xmlrpc_enabled', 'trinetix_disable_xmlrpc' );

/**
 * Hide WordPress version from scripts/styles query args where possible.
 *
 * @param string $src Source URL.
 * @return string
 */
function trinetix_remove_version_query( string $src ): string {
	if ( strpos( $src, 'ver=' ) ) {
		$src = remove_query_arg( 'ver', $src );
	}
	return $src;
}
// Keep filemtime versions for theme assets; only strip core generator elsewhere.

/**
 * Disallow file edit in admin when not already defined.
 */
function trinetix_suggest_disallow_file_edit(): void {
	if ( ! defined( 'DISALLOW_FILE_EDIT' ) && is_admin() ) {
		// Cannot redefine constants; documented in README instead.
	}
}
add_action( 'admin_init', 'trinetix_suggest_disallow_file_edit' );

/**
 * Add security-related headers on front-end responses when possible.
 *
 * @param array<string, string> $headers Headers.
 * @return array<string, string>
 */
function trinetix_security_headers( array $headers ): array {
	if ( is_admin() ) {
		return $headers;
	}

	$headers['X-Content-Type-Options'] = 'nosniff';
	$headers['Referrer-Policy']        = 'strict-origin-when-cross-origin';
	$headers['X-Frame-Options']        = 'SAMEORIGIN';

	return $headers;
}
add_filter( 'wp_headers', 'trinetix_security_headers' );
