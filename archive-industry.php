<?php
/**
 * Industries archive.
 *
 * @package Trinetix
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

get_header();
?>
<main id="top" class="site-main">
	<?php
	get_template_part(
		'template-parts/global/page-hero',
		null,
		array(
			'title'    => __( 'Industry Expertise', 'trinetix' ),
			'subtitle' => __( 'Domain-led technology transformation across regulated and high-velocity industries.', 'trinetix' ),
		)
	);
	?>
	<?php if ( have_posts() ) : ?>
		<div class="container archive-grid">
			<?php
			while ( have_posts() ) :
				the_post();
				get_template_part( 'template-parts/content/content', 'card' );
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
