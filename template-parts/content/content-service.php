<?php
/**
 * Service content.
 *
 * @package Trinetix
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$cta_label = (string) get_post_meta( get_the_ID(), '_trinetix_cta_label', true );
$cta_url   = (string) get_post_meta( get_the_ID(), '_trinetix_cta_url', true );
?>
<article <?php post_class( 'entry entry-service' ); ?>>
	<?php if ( has_post_thumbnail() ) : ?>
		<figure class="entry-thumbnail"><?php the_post_thumbnail( 'large' ); ?></figure>
	<?php endif; ?>
	<div class="entry-content">
		<?php the_content(); ?>
	</div>
	<?php if ( $cta_label && $cta_url ) : ?>
		<p><a class="btn btn-cyan" href="<?php echo esc_url( $cta_url ); ?>"><?php echo esc_html( $cta_label ); ?></a></p>
	<?php endif; ?>
</article>
