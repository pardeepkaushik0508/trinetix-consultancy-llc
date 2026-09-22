<?php
/**
 * Contact form AJAX handler + admin submission UI.
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
		wp_send_json_error( array( 'message' => __( 'Invalid request. Please refresh the page and try again.', 'trinetix' ) ), 403 );
	}

	// Honeypot — pretend success for bots.
	$honeypot = isset( $_POST['website'] ) ? trim( (string) wp_unslash( $_POST['website'] ) ) : '';
	if ( '' !== $honeypot ) {
		wp_send_json_success( array( 'message' => __( 'Thank you. Your message has been sent.', 'trinetix' ) ) );
	}

	$ip = isset( $_SERVER['REMOTE_ADDR'] ) ? sanitize_text_field( wp_unslash( $_SERVER['REMOTE_ADDR'] ) ) : 'unknown';
	if ( trinetix_contact_is_rate_limited( $ip ) ) {
		wp_send_json_error( array( 'message' => __( 'Please wait a moment before sending another message.', 'trinetix' ) ), 429 );
	}

	$first    = isset( $_POST['first_name'] ) ? sanitize_text_field( wp_unslash( $_POST['first_name'] ) ) : '';
	$last     = isset( $_POST['last_name'] ) ? sanitize_text_field( wp_unslash( $_POST['last_name'] ) ) : '';
	$email    = isset( $_POST['email'] ) ? sanitize_email( wp_unslash( $_POST['email'] ) ) : '';
	$phone    = isset( $_POST['phone'] ) ? sanitize_text_field( wp_unslash( $_POST['phone'] ) ) : '';
	$company  = isset( $_POST['company'] ) ? sanitize_text_field( wp_unslash( $_POST['company'] ) ) : '';
	$message  = isset( $_POST['message'] ) ? sanitize_textarea_field( wp_unslash( $_POST['message'] ) ) : '';
	$interest = isset( $_POST['interest'] ) ? sanitize_text_field( wp_unslash( $_POST['interest'] ) ) : '';

	if ( '' === $first || '' === $last || ! is_email( $email ) || '' === $message ) {
		wp_send_json_error( array( 'message' => __( 'Please check the form and try again.', 'trinetix' ) ), 400 );
	}

	$fields = array(
		'first_name' => $first,
		'last_name'  => $last,
		'email'      => $email,
		'phone'      => $phone,
		'company'    => $company,
		'interest'   => $interest,
		'message'    => $message,
		'ip'         => $ip,
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
	$body = implode( "\n", $body_lines );

	// Persist submission first — this is the source of truth for admin.
	$submission_id = wp_insert_post(
		array(
			'post_type'    => 'contact_submission',
			'post_status'  => 'private',
			'post_title'   => trim( $first . ' ' . $last ) . ' — ' . $email,
			'post_content' => $body,
		),
		true
	);

	if ( is_wp_error( $submission_id ) || ! $submission_id ) {
		wp_send_json_error( array( 'message' => __( 'Something went wrong. Please try again.', 'trinetix' ) ), 500 );
	}

	foreach ( $fields as $key => $value ) {
		update_post_meta( (int) $submission_id, '_trinetix_contact_' . $key, $value );
	}

	// Best-effort email notification (do not fail the form if mail is unavailable).
	$sent = trinetix_contact_send_mail( $fields, $body );
	update_post_meta( (int) $submission_id, '_trinetix_mail_sent', $sent ? 1 : 0 );

	trinetix_contact_mark_rate_limit( $ip );

	wp_send_json_success(
		array(
			'message' => __( 'Thank you. Your message has been sent.', 'trinetix' ),
		)
	);
}

/**
 * Send notification email for a contact submission.
 *
 * @param array<string, string> $fields Field values.
 * @param string                $body   Plain-text body.
 * @return bool
 */
function trinetix_contact_send_mail( array $fields, string $body ): bool {
	$recipient = (string) trinetix_get_setting( 'contact_recipient', get_option( 'admin_email' ) );
	if ( ! is_email( $recipient ) ) {
		$recipient = (string) get_option( 'admin_email' );
	}
	if ( ! is_email( $recipient ) ) {
		return false;
	}

	$subject = sprintf(
		/* translators: %s: site name */
		__( '[%s] New contact form message', 'trinetix' ),
		wp_specialchars_decode( get_bloginfo( 'name' ), ENT_QUOTES )
	);

	$headers = array(
		'Content-Type: text/plain; charset=UTF-8',
		'Reply-To: ' . $fields['first_name'] . ' ' . $fields['last_name'] . ' <' . $fields['email'] . '>',
	);

	return (bool) wp_mail( $recipient, $subject, $body, $headers );
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

/**
 * Admin list columns for contact submissions.
 *
 * @param array<string, string> $columns Columns.
 * @return array<string, string>
 */
function trinetix_contact_submission_columns( array $columns ): array {
	return array(
		'cb'        => $columns['cb'] ?? '<input type="checkbox" />',
		'title'     => __( 'Name', 'trinetix' ),
		'email'     => __( 'Email', 'trinetix' ),
		'phone'     => __( 'Phone', 'trinetix' ),
		'company'   => __( 'Company', 'trinetix' ),
		'mail_sent' => __( 'Email sent', 'trinetix' ),
		'date'      => __( 'Date', 'trinetix' ),
	);
}
add_filter( 'manage_contact_submission_posts_columns', 'trinetix_contact_submission_columns' );

/**
 * Render custom columns.
 *
 * @param string $column  Column key.
 * @param int    $post_id Post ID.
 */
function trinetix_contact_submission_column_content( string $column, int $post_id ): void {
	$map = array(
		'email'   => '_trinetix_contact_email',
		'phone'   => '_trinetix_contact_phone',
		'company' => '_trinetix_contact_company',
	);

	if ( isset( $map[ $column ] ) ) {
		$value = (string) get_post_meta( $post_id, $map[ $column ], true );
		if ( 'email' === $column && $value ) {
			echo '<a href="mailto:' . esc_attr( $value ) . '">' . esc_html( $value ) . '</a>';
			return;
		}
		echo $value ? esc_html( $value ) : '—';
		return;
	}

	if ( 'mail_sent' === $column ) {
		$sent = (int) get_post_meta( $post_id, '_trinetix_mail_sent', true );
		echo $sent ? esc_html__( 'Yes', 'trinetix' ) : esc_html__( 'No', 'trinetix' );
	}
}
add_action( 'manage_contact_submission_posts_custom_column', 'trinetix_contact_submission_column_content', 10, 2 );

/**
 * Meta box: full submission details.
 */
function trinetix_contact_submission_meta_boxes(): void {
	add_meta_box(
		'trinetix_contact_details',
		__( 'Submission details', 'trinetix' ),
		'trinetix_render_contact_submission_meta_box',
		'contact_submission',
		'normal',
		'high'
	);
}
add_action( 'add_meta_boxes', 'trinetix_contact_submission_meta_boxes' );

/**
 * Render submission details meta box.
 *
 * @param WP_Post $post Post.
 */
function trinetix_render_contact_submission_meta_box( WP_Post $post ): void {
	$keys = array(
		'first_name' => __( 'First name', 'trinetix' ),
		'last_name'  => __( 'Last name', 'trinetix' ),
		'email'      => __( 'Email', 'trinetix' ),
		'phone'      => __( 'Phone', 'trinetix' ),
		'company'    => __( 'Company', 'trinetix' ),
		'interest'   => __( 'Interest', 'trinetix' ),
		'message'    => __( 'Message', 'trinetix' ),
		'ip'         => __( 'IP address', 'trinetix' ),
	);

	$mail_sent = (int) get_post_meta( $post->ID, '_trinetix_mail_sent', true );

	echo '<table class="form-table" role="presentation"><tbody>';
	foreach ( $keys as $key => $label ) {
		$value = (string) get_post_meta( $post->ID, '_trinetix_contact_' . $key, true );
		if ( '' === $value && 'message' === $key ) {
			$value = (string) $post->post_content;
		}
		echo '<tr><th scope="row">' . esc_html( $label ) . '</th><td>';
		if ( 'email' === $key && is_email( $value ) ) {
			echo '<a href="mailto:' . esc_attr( $value ) . '">' . esc_html( $value ) . '</a>';
		} elseif ( 'message' === $key ) {
			echo '<div style="white-space:pre-wrap;max-width:640px;">' . esc_html( $value ) . '</div>';
		} else {
			echo $value ? esc_html( $value ) : '—';
		}
		echo '</td></tr>';
	}
	echo '<tr><th scope="row">' . esc_html__( 'Notification email', 'trinetix' ) . '</th><td>';
	echo $mail_sent ? esc_html__( 'Sent', 'trinetix' ) : esc_html__( 'Not sent (submission still saved)', 'trinetix' );
	echo '</td></tr>';
	echo '</tbody></table>';
}

/**
 * Hide title/editor chrome for submissions — details live in the meta box.
 */
function trinetix_contact_submission_admin_styles(): void {
	$screen = function_exists( 'get_current_screen' ) ? get_current_screen() : null;
	if ( ! $screen || 'contact_submission' !== $screen->post_type ) {
		return;
	}
	echo '<style>
		.post-type-contact_submission #postdivrich,
		.post-type-contact_submission #titlediv .inside { display: none !important; }
		.post-type-contact_submission #titlediv input#title { background: #f6f7f7; pointer-events: none; }
		.post-type-contact_submission #trinetix_contact_details .form-table th { width: 160px; }
	</style>';
}
add_action( 'admin_head', 'trinetix_contact_submission_admin_styles' );
