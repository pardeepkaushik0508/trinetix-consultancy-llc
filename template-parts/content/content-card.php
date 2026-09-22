<?php
/**
 * CPT card for archives.
 *
 * @package Trinetix
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$type    = get_post_type();
$eyebrow = '';
if ( 'case_study' === $type ) {
	$eyebrow = (string) get_post_meta( get_the_ID(), '_trinetix_eyebrow', true );
}
?>
<article <?php post_class( 'archive-card' ); ?>>
	<a class="archive-card-link" href="<?php the_permalink(); ?>">
		<?php if ( has_post_thumbnail() ) : ?>
			<div class="archive-card-media"><?php the_post_thumbnail( 'trinetix-card' ); ?></div>
		<?php endif; ?>
		<div class="archive-card-body">
			<?php if ( $eyebrow ) : ?>
				<small><?php echo esc_html( $eyebrow ); ?></small>
			<?php endif; ?>
			<h2><?php the_title(); ?></h2>
			<?php if ( has_excerpt() || get_the_content() ) : ?>
				<p><?php echo esc_html( has_excerpt() ? get_the_excerpt() : wp_trim_words( wp_strip_all_tags( get_the_content() ), 28 ) ); ?></p>
			<?php endif; ?>
			<span class="circle-arrow"><?php echo trinetix_arrow_icon( 'dark' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?></span>
		</div>
	</a>
</article>
