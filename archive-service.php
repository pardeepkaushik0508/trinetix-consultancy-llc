<?php
/**
 * Services archive.
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
			'title'    => __( 'Our Services', 'trinetix' ),
			'subtitle' => __( 'AI, data, engineering and experience capabilities built for enterprise outcomes.', 'trinetix' ),
		)
	);
	?>
	<?php if ( have_posts() ) : ?>
		<section class="services archive-services">
			<div class="container service-grid">
				<?php
				$i = 0;
				while ( have_posts() ) :
					the_post();
					++$i;
					$number = str_pad( (string) $i, 2, '0', STR_PAD_LEFT );
					?>
					<a class="service-card" href="<?php the_permalink(); ?>">
						<?php if ( has_post_thumbnail() ) : ?>
							<?php the_post_thumbnail( 'trinetix-card' ); ?>
						<?php endif; ?>
						<span class="number"><?php echo esc_html( $number ); ?></span>
						<div class="service-content">
							<div class="service-title-row">
								<h3><?php the_title(); ?></h3>
								<span class="circle-arrow"><?php echo trinetix_arrow_icon( 'dark' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?></span>
							</div>
							<p><?php echo esc_html( has_excerpt() ? get_the_excerpt() : wp_trim_words( wp_strip_all_tags( get_the_content() ), 28 ) ); ?></p>
						</div>
					</a>
				<?php endwhile; ?>
			</div>
		</section>
		<div class="container">
			<?php get_template_part( 'template-parts/global/pagination' ); ?>
		</div>
	<?php else : ?>
		<?php get_template_part( 'template-parts/content/content', 'none' ); ?>
	<?php endif; ?>
</main>
<?php
get_footer();
