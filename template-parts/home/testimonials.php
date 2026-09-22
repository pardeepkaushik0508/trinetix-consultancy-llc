<?php
/**
 * Homepage testimonials section.
 * Interactive data from localized trinetixTestimonials.
 *
 * @package Trinetix
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$title = (string) trinetix_home_setting( 'testimonials_title', 'What Our Customers Say' );
$items = trinetix_get_testimonials_payload();
$first = $items[0] ?? null;

if ( empty( $items ) || ! $first ) {
	return;
}

$rating = (int) ( $first['rating'] ?? 5 );
$stars  = str_repeat( '★', max( 1, min( 5, $rating ) ) );
?>
<section class="testimonials">
	<div class="container testimonial-wrap">
		<div class="testimonial-heading-row">
			<h2 class="reveal"><?php echo esc_html( $title ); ?></h2>
			<div class="testimonial-top-controls" aria-label="<?php esc_attr_e( 'Testimonial controls', 'trinetix' ); ?>">
				<button class="testimonial-arrow testimonial-prev" id="testimonialPrev" type="button" aria-label="<?php esc_attr_e( 'Previous testimonial', 'trinetix' ); ?>"></button>
				<button class="testimonial-arrow testimonial-next" id="testimonialNext" type="button" aria-label="<?php esc_attr_e( 'Next testimonial', 'trinetix' ); ?>"></button>
			</div>
		</div>

		<div class="testimonial-stage">
			<div class="testimonial-main reveal" id="testimonialMain">
				<div class="testimonial-photo">
					<img id="tImg" src="<?php echo esc_url( (string) ( $first['image'] ?? '' ) ); ?>" alt="<?php esc_attr_e( 'Client testimonial', 'trinetix' ); ?>">
				</div>
				<div class="testimonial-content">
					<div class="quote-mark">“</div>
					<div class="quote-text" id="tQuote"><?php echo esc_html( (string) ( $first['quote'] ?? '' ) ); ?></div>
					<div class="person-name" id="tName"><?php echo esc_html( (string) ( $first['name'] ?? '' ) ); ?></div>
					<div class="person-role" id="tRole"><?php echo esc_html( (string) ( $first['role'] ?? '' ) ); ?></div>
					<div class="stars" id="tStars"><?php echo esc_html( $stars ); ?></div>
				</div>
			</div>
		</div>

		<div class="testimonial-nav">
			<?php foreach ( $items as $i => $item ) : ?>
				<button class="person-chip<?php echo 0 === $i ? ' active' : ''; ?>" type="button" data-t="<?php echo esc_attr( (string) $i ); ?>">
					<img src="<?php echo esc_url( (string) ( $item['image'] ?? '' ) ); ?>" alt="">
					<span><?php echo esc_html( (string) ( $item['chip'] ?? $item['name'] ?? '' ) ); ?></span>
				</button>
			<?php endforeach; ?>
		</div>
	</div>
</section>
