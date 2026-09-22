<?php
/**
 * Contact form AJAX handler.
 *
 * @package Trinetix
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Register AJAX actions.
 */
function trinetix_register_contact_ajax(): void {
	add_action( 'wp_ajax_trinetix_contact', 'trinetix_handle_contact_submission' );
	add_action( 'wp_ajax_nopriv_trinetix_contact', 'trinetix_handle_contact_submission' );
}
add_action( 'init', 'trinetix_register_contact_ajax' );

/**
 * Handle contact form.
 */
function trinetix_handle_contact_submission(): void {
	if ( ! isset( $_POST['nonce'] ) || ! wp_verify_nonce( sanitize_text_field( wp_unslash( $_POST['nonce'] ) ), 'trinetix_contact' ) ) {
		wp_send_json_error( array( 'message' => __( 'Invalid request.', 'trinetix' ) ), 403 );
	}

	// Honeypot.
	$honeypot = isset( $_POST['website'] ) ? trim( (string) wp_unslash( $_POST['website'] ) ) : '';
	if ( '' !== $honeypot ) {
		wp_send_json_success( array( 'message' => __( 'Thank you. Your message has been sent.', 'trinetix' ) ) );
	}

	$ip = isset( $_SERVER['REMOTE_ADDR'] ) ? sanitize_text_field( wp_unslash( $_SERVER['REMOTE_ADDR'] ) ) : 'unknown';
	if ( trinetix_contact_is_rate_limited( $ip ) ) {
		wp_send_json_error( array( 'message' => __( 'Please wait a moment before sending another message.', 'trinetix' ) ), 429 );
	}

	$first   = isset( $_POST['first_name'] ) ? sanitize_text_field( wp_unslash( $_POST['first_name'] ) ) : '';
	$last    = isset( $_POST['last_name'] ) ? sanitize_text_field( wp_unslash( $_POST['last_name'] ) ) : '';
	$email   = isset( $_POST['email'] ) ? sanitize_email( wp_unslash( $_POST['email'] ) ) : '';
	$phone   = isset( $_POST['phone'] ) ? sanitize_text_field( wp_unslash( $_POST['phone'] ) ) : '';
	$company = isset( $_POST['company'] ) ? sanitize_text_field( wp_unslash( $_POST['company'] ) ) : '';
	$message = isset( $_POST['message'] ) ? sanitize_textarea_field( wp_unslash( $_POST['message'] ) ) : '';
	$interest = isset( $_POST['interest'] ) ? sanitize_text_field( wp_unslash( $_POST['interest'] ) ) : '';

	if ( '' === $first || '' === $last || ! is_email( $email ) || '' === $message ) {
		wp_send_json_error( array( 'message' => __( 'Please check the form and try again.', 'trinetix' ) ), 400 );
	}

	$recipient = (string) trinetix_get_setting( 'contact_recipient', get_option( 'admin_email' ) );
	if ( ! is_email( $recipient ) ) {
		$recipient = (string) get_option( 'admin_email' );
	}

	$subject = sprintf(
		/* translators: %s: site name */
		__( '[%s] New contact form message', 'trinetix' ),
		wp_specialchars_decode( get_bloginfo( 'name' ), ENT_QUOTES )
	);

	$body_lines = array(
		'First name: ' . $first,
		'Last name: ' . $last,
		'Email: ' . $email,
		'Phone: ' . $phone,
		'Company: ' . $company,
		'Interest: ' . $interest,
		'',
		'Message:',
		$message,
	);

	$headers = array(
		'Content-Type: text/plain; charset=UTF-8',
		'Reply-To: ' . $first . ' ' . $last . ' <' . $email . '>',
	);

	$sent = wp_mail( $recipient, $subject, implode( "\n", $body_lines ), $headers );

	// Store private submission.
	$submission_id = wp_insert_post(
		array(
			'post_type'    => 'contact_submission',
			'post_status'  => 'private',
			'post_title'   => $first . ' ' . $last . ' — ' . $email,
			'post_content' => implode( "\n", $body_lines ),
		),
		true
	);

	if ( ! is_wp_error( $submission_id ) ) {
		update_post_meta( $submission_id, '_trinetix_contact_email', $email );
		update_post_meta( $submission_id, '_trinetix_contact_interest', $interest );
		update_post_meta( $submission_id, '_trinetix_mail_sent', $sent ? 1 : 0 );
	}

	trinetix_contact_mark_rate_limit( $ip );

	if ( ! $sent ) {
		wp_send_json_error( array( 'message' => __( 'Something went wrong. Please try again.', 'trinetix' ) ), 500 );
	}

	wp_send_json_success( array( 'message' => __( 'Thank you. Your message has been sent.', 'trinetix' ) ) );
}

/**
 * Rate limit check (1 submission / 60s per IP).
 *
 * @param string $ip IP address.
 * @return bool
 */
function trinetix_contact_is_rate_limited( string $ip ): bool {
	$key = 'trinetix_contact_' . md5( $ip );
	return (bool) get_transient( $key );
}

/**
 * Mark rate limit.
 *
 * @param string $ip IP address.
 */
function trinetix_contact_mark_rate_limit( string $ip ): void {
	$key = 'trinetix_contact_' . md5( $ip );
	set_transient( $key, 1, MINUTE_IN_SECONDS );
}
