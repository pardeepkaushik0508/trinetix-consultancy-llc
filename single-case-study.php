<?php
/**
 * Single case study.
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
			$id        = get_the_ID();
			$client    = (string) get_post_meta( $id, '_trinetix_client', true );
			$eyebrow   = (string) get_post_meta( $id, '_trinetix_eyebrow', true );
			$challenge = (string) get_post_meta( $id, '_trinetix_challenge', true );
			$solution  = (string) get_post_meta( $id, '_trinetix_solution', true );
			$results   = (string) get_post_meta( $id, '_trinetix_results', true );
			?>
			<article <?php post_class( 'entry entry-case-study' ); ?>>
				<header class="entry-header">
					<?php if ( $eyebrow || $client ) : ?>
						<small class="entry-meta"><?php echo esc_html( $eyebrow ? $eyebrow : $client ); ?></small>
					<?php endif; ?>
					<h1 class="entry-title"><?php the_title(); ?></h1>
				</header>
				<?php if ( has_post_thumbnail() ) : ?>
					<figure class="entry-media"><?php the_post_thumbnail( 'large' ); ?></figure>
				<?php endif; ?>
				<div class="entry-content">
					<?php the_content(); ?>
					<?php if ( $challenge || $solution || $results ) : ?>
						<div class="case-study-details">
							<?php if ( $challenge ) : ?>
								<section>
									<h2><?php esc_html_e( 'Challenge', 'trinetix' ); ?></h2>
									<?php echo wp_kses_post( wpautop( $challenge ) ); ?>
								</section>
							<?php endif; ?>
							<?php if ( $solution ) : ?>
								<section>
									<h2><?php esc_html_e( 'Solution', 'trinetix' ); ?></h2>
									<?php echo wp_kses_post( wpautop( $solution ) ); ?>
								</section>
							<?php endif; ?>
							<?php if ( $results ) : ?>
								<section>
									<h2><?php esc_html_e( 'Results', 'trinetix' ); ?></h2>
									<?php echo wp_kses_post( wpautop( $results ) ); ?>
								</section>
							<?php endif; ?>
						</div>
					<?php endif; ?>
				</div>
				<p>
					<a class="btn btn-cyan" href="<?php echo esc_url( home_url( '/#contact' ) ); ?>">
						<?php esc_html_e( 'Start a similar conversation', 'trinetix' ); ?>
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
