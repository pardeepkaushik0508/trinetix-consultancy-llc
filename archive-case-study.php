<?php
/**
 * Case studies archive.
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
			'title'    => __( 'Our Work', 'trinetix' ),
			'subtitle' => __( 'Selected engagements that connect strategy, architecture and engineering to business value.', 'trinetix' ),
		)
	);
	?>
	<?php if ( have_posts() ) : ?>
		<section class="work archive-work">
			<div class="container work-grid">
				<?php
				while ( have_posts() ) :
					the_post();
					$eyebrow = (string) get_post_meta( get_the_ID(), '_trinetix_eyebrow', true );
					?>
					<a class="work-card" href="<?php the_permalink(); ?>">
						<?php if ( has_post_thumbnail() ) : ?>
							<?php the_post_thumbnail( 'trinetix-work' ); ?>
						<?php endif; ?>
						<div class="work-info">
							<?php if ( $eyebrow ) : ?>
								<small><?php echo esc_html( $eyebrow ); ?></small>
							<?php endif; ?>
							<h3><?php the_title(); ?></h3>
							<p><?php echo esc_html( has_excerpt() ? get_the_excerpt() : wp_trim_words( wp_strip_all_tags( get_the_content() ), 36 ) ); ?></p>
							<span class="circle-arrow"><?php echo trinetix_arrow_icon( 'dark' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?></span>
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
