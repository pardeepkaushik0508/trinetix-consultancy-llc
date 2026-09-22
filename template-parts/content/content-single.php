<?php
/**
 * Single post content.
 *
 * @package Trinetix
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
?>
<article <?php post_class( 'entry entry-single' ); ?> id="post-<?php the_ID(); ?>">
	<header class="entry-header">
		<?php
		$cats = get_the_category();
		if ( ! empty( $cats[0] ) ) :
			?>
			<small class="entry-meta"><?php echo esc_html( $cats[0]->name ); ?></small>
		<?php endif; ?>
		<h1 class="entry-title"><?php the_title(); ?></h1>
		<div class="entry-byline">
			<time datetime="<?php echo esc_attr( get_the_date( DATE_W3C ) ); ?>"><?php echo esc_html( get_the_date() ); ?></time>
		</div>
	</header>
	<?php if ( has_post_thumbnail() ) : ?>
		<figure class="entry-media"><?php the_post_thumbnail( 'large' ); ?></figure>
	<?php endif; ?>
	<div class="entry-content">
		<?php
		the_content();
		wp_link_pages(
			array(
				'before' => '<div class="page-links">' . esc_html__( 'Pages:', 'trinetix' ),
				'after'  => '</div>',
			)
		);
		?>
	</div>
	<footer class="entry-footer">
		<?php the_tags( '<div class="entry-tags">', ' ', '</div>' ); ?>
	</footer>
</article>
