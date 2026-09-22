<?php
/**
 * Single service.
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
			$cta_label = (string) get_post_meta( get_the_ID(), '_trinetix_cta_label', true );
			$cta_url   = (string) get_post_meta( get_the_ID(), '_trinetix_cta_url', true );
			if ( ! $cta_label ) {
				$cta_label = __( 'Talk to us', 'trinetix' );
			}
			if ( ! $cta_url ) {
				$cta_url = home_url( '/#contact' );
			}
			?>
			<article <?php post_class( 'entry entry-service' ); ?>>
				<header class="entry-header">
					<small class="entry-meta"><?php esc_html_e( 'Service', 'trinetix' ); ?></small>
					<h1 class="entry-title"><?php the_title(); ?></h1>
				</header>
				<?php if ( has_post_thumbnail() ) : ?>
					<figure class="entry-media"><?php the_post_thumbnail( 'large' ); ?></figure>
				<?php endif; ?>
				<div class="entry-content">
					<?php the_content(); ?>
				</div>
				<p>
					<a class="btn btn-cyan" href="<?php echo esc_url( $cta_url ); ?>">
						<?php echo esc_html( $cta_label ); ?>
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
