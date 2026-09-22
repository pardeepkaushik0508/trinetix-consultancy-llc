<?php
/**
 * Homepage contact section.
 *
 * @package Trinetix
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$heading = (string) trinetix_home_setting( 'contact_heading', 'How can we help you?' );
$copy    = (string) trinetix_home_setting( 'contact_copy', '' );
$email   = (string) trinetix_get_setting( 'contact_email', 'info@trinetixconsulting.com' );
$pills   = (string) trinetix_home_setting( 'contact_pills', "Artificial Intelligence\nData & Analytics\nDigital Engineering\nExperience" );
$pill_list = array_values( array_filter( array_map( 'trim', preg_split( '/\r\n|\r|\n/', $pills ) ?: array() ) ) );
?>
<section class="contact" id="contact">
	<div class="container contact-grid">
		<div class="reveal">
			<?php if ( $heading ) : ?>
				<h2><?php echo esc_html( $heading ); ?></h2>
			<?php endif; ?>
			<?php if ( ! empty( $pill_list ) ) : ?>
				<div class="pills" role="group" aria-label="<?php esc_attr_e( 'Areas of interest', 'trinetix' ); ?>">
					<?php foreach ( $pill_list as $i => $pill ) : ?>
						<button class="pill<?php echo 0 === $i ? ' active' : ''; ?>" type="button" data-interest="<?php echo esc_attr( $pill ); ?>">
							<?php echo esc_html( $pill ); ?>
						</button>
					<?php endforeach; ?>
				</div>
			<?php endif; ?>
			<?php if ( $copy ) : ?>
				<p class="contact-copy"><?php echo esc_html( $copy ); ?></p>
			<?php endif; ?>
			<?php if ( $email ) : ?>
				<div class="email-row">
					<span class="email-icon" aria-hidden="true">✉</span>
					<a href="mailto:<?php echo esc_attr( $email ); ?>"><?php echo esc_html( $email ); ?></a>
				</div>
			<?php endif; ?>
		</div>

		<form class="form reveal" id="trinetixContactForm" method="post" action="<?php echo esc_url( admin_url( 'admin-ajax.php' ) ); ?>" novalidate>
			<input type="hidden" name="action" value="trinetix_contact" />
			<input type="hidden" name="nonce" value="<?php echo esc_attr( wp_create_nonce( 'trinetix_contact' ) ); ?>" />
			<input type="hidden" name="interest" id="trinetixInterest" value="<?php echo esc_attr( $pill_list[0] ?? '' ); ?>" />
			<p class="hp-field" style="position:absolute;left:-9999px;opacity:0;height:0;overflow:hidden;" aria-hidden="true">
				<label for="trinetixWebsite"><?php esc_html_e( 'Website', 'trinetix' ); ?></label>
				<input type="text" name="website" id="trinetixWebsite" tabindex="-1" autocomplete="off" />
			</p>
			<div class="form-row">
				<input type="text" name="first_name" required placeholder="<?php esc_attr_e( 'First Name *', 'trinetix' ); ?>" autocomplete="given-name" />
				<input type="text" name="last_name" required placeholder="<?php esc_attr_e( 'Last Name *', 'trinetix' ); ?>" autocomplete="family-name" />
			</div>
			<input type="email" name="email" required placeholder="<?php esc_attr_e( 'Business Email *', 'trinetix' ); ?>" autocomplete="email" />
			<input type="tel" name="phone" placeholder="<?php esc_attr_e( 'Direct Phone Number', 'trinetix' ); ?>" autocomplete="tel" />
			<input type="text" name="company" placeholder="<?php esc_attr_e( 'Company / Organization', 'trinetix' ); ?>" autocomplete="organization" />
			<textarea name="message" required placeholder="<?php esc_attr_e( 'Question or Message *', 'trinetix' ); ?>" rows="5"></textarea>
			<div class="form-end">
				<button type="submit" class="btn btn-light">
					<?php esc_html_e( 'Submit', 'trinetix' ); ?>
					<?php echo trinetix_arrow_icon( 'dark' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
				</button>
			</div>
			<div class="form-status" id="contactFormStatus" role="status" aria-live="polite"></div>
		</form>
	</div>
</section>
