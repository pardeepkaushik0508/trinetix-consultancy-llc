<?php
/**
 * Archive template.
 *
 * @package Trinetix
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

get_header();

$archive_title = get_the_archive_title();
$archive_desc  = get_the_archive_description();
?>
<main id="top" class="site-main">
	<?php
	get_template_part(
		'template-parts/global/page-hero',
		null,
		array(
			'title'    => wp_strip_all_tags( $archive_title ),
			'subtitle' => $archive_desc ? wp_strip_all_tags( $archive_desc ) : '',
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
