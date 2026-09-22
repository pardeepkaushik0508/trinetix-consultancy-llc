<?php
/**
 * Template Name: Page Sections
 * Description: Classic page with optional Page Hero fields. Add shortcodes in the editor for Industries, Our Work, etc.
 *
 * @package Trinetix
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

get_header();

while ( have_posts() ) :
	the_post();

	$hero_title   = (string) get_post_meta( get_the_ID(), '_trinetix_page_hero_title', true );
	$hero_subline = (string) get_post_meta( get_the_ID(), '_trinetix_page_hero_subline', true );
	$hero_image   = (int) get_post_meta( get_the_ID(), '_trinetix_page_hero_image_id', true );
	$hero_url     = $hero_image ? trinetix_image_url( $hero_image, 'large', '' ) : '';

	if ( ! $hero_title ) {
		$hero_title = get_the_title();
	}
	?>
	<main id="top" class="page-sections">
		<section class="page-hero section">
			<div class="container">
				<?php if ( $hero_url ) : ?>
					<div class="page-hero-media reveal">
						<img src="<?php echo esc_url( $hero_url ); ?>" alt="" />
					</div>
				<?php endif; ?>
				<div class="page-hero-copy reveal">
					<h1><?php echo esc_html( $hero_title ); ?></h1>
					<?php if ( $hero_subline ) : ?>
						<p><?php echo esc_html( $hero_subline ); ?></p>
					<?php endif; ?>
				</div>
			</div>
		</section>

		<section class="section page-sections-content">
			<div class="container entry-content">
				<?php the_content(); ?>
			</div>
		</section>
	</main>
	<?php
endwhile;

get_footer();
