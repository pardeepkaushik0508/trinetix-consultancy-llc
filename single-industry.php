<?php
/**
 * Single industry.
 *
 * @package Trinetix
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

get_header();
?>
<main id="top" class="site-main">
	<div class="container content-wrap">
		<?php
		while ( have_posts() ) :
			the_post();
			?>
			<article <?php post_class( 'entry entry-industry' ); ?>>
				<header class="entry-header">
					<small class="entry-meta"><?php esc_html_e( 'Industry', 'trinetix' ); ?></small>
					<h1 class="entry-title"><?php the_title(); ?></h1>
				</header>
				<?php if ( has_post_thumbnail() ) : ?>
					<figure class="entry-media"><?php the_post_thumbnail( 'large' ); ?></figure>
				<?php endif; ?>
				<div class="entry-content">
					<?php the_content(); ?>
				</div>
				<p>
					<a class="btn btn-cyan" href="<?php echo esc_url( home_url( '/#contact' ) ); ?>">
						<?php esc_html_e( 'Explore this industry with us', 'trinetix' ); ?>
						<?php echo trinetix_arrow_icon( 'dark' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
					</a>
				</p>
			</article>
			<?php
		endwhile;
		?>
	</div>
</main>
<?php
get_footer();
