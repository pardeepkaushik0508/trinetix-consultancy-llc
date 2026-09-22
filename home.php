<?php
/**
 * Blog posts index (when a posts page is set).
 *
 * @package Trinetix
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

get_header();

$blog_title = __( 'Knowledge Hub', 'trinetix' );
$posts_page = (int) get_option( 'page_for_posts' );
if ( $posts_page ) {
	$blog_title = get_the_title( $posts_page );
}
?>
<main id="top" class="site-main">
	<?php
	get_template_part(
		'template-parts/global/page-hero',
		null,
		array(
			'title'    => $blog_title,
			'subtitle' => __( 'Insights on AI, data, engineering and transformation.', 'trinetix' ),
		)
	);
	?>
	<?php if ( have_posts() ) : ?>
		<div class="container archive-grid knowledge-grid">
			<?php
			while ( have_posts() ) :
				the_post();
				get_template_part( 'template-parts/content/content' );
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
