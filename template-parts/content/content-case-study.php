<?php
/**
 * Case study content.
 *
 * @package Trinetix
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$client    = (string) get_post_meta( get_the_ID(), '_trinetix_client', true );
$challenge = (string) get_post_meta( get_the_ID(), '_trinetix_challenge', true );
$solution  = (string) get_post_meta( get_the_ID(), '_trinetix_solution', true );
$results   = (string) get_post_meta( get_the_ID(), '_trinetix_results', true );
?>
<article <?php post_class( 'entry entry-case-study' ); ?>>
	<?php if ( has_post_thumbnail() ) : ?>
		<figure class="entry-thumbnail"><?php the_post_thumbnail( 'large' ); ?></figure>
	<?php endif; ?>
	<?php if ( $client ) : ?>
		<p><strong><?php esc_html_e( 'Client', 'trinetix' ); ?>:</strong> <?php echo esc_html( $client ); ?></p>
	<?php endif; ?>
	<div class="entry-content">
		<?php the_content(); ?>
	</div>
	<?php if ( $challenge || $solution || $results ) : ?>
		<div class="case-study-meta">
			<?php if ( $challenge ) : ?>
				<section>
					<h2><?php esc_html_e( 'Challenge', 'trinetix' ); ?></h2>
					<p><?php echo esc_html( $challenge ); ?></p>
				</section>
			<?php endif; ?>
			<?php if ( $solution ) : ?>
				<section>
					<h2><?php esc_html_e( 'Solution', 'trinetix' ); ?></h2>
					<p><?php echo esc_html( $solution ); ?></p>
				</section>
			<?php endif; ?>
			<?php if ( $results ) : ?>
				<section>
					<h2><?php esc_html_e( 'Results', 'trinetix' ); ?></h2>
					<p><?php echo esc_html( $results ); ?></p>
				</section>
			<?php endif; ?>
		</div>
	<?php endif; ?>
</article>
