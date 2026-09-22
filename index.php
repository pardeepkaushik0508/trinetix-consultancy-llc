<?php
/**
 * Main index template.
 *
 * @package Trinetix
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

get_header();
?>
<main id="top" class="site-main">
	<?php if ( have_posts() ) : ?>
		<?php
		get_template_part(
			'template-parts/global/page-hero',
			null,
			array(
				'title' => is_home() && ! is_front_page() ? get_the_title( (int) get_option( 'page_for_posts' ) ) : __( 'Latest', 'trinetix' ),
			)
		);
		?>
		<div class="container archive-grid">
			<?php
			while ( have_posts() ) :
				the_post();
				get_template_part( 'template-parts/content/content', get_post_type() );
			endwhile;
			?>
		</div>
		<div class="container">
			<?php get_template_part( 'template-parts/global/pagination' ); ?>
		</div>
	<?php else : ?>
		<?php get_template_part( 'template-parts/content/content', 'none' ); ?>
	<?php endif; ?>
</main>
<?php
get_footer();
