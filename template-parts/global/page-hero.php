<?php
/**
 * Inner page hero.
 *
 * @package Trinetix
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$title = '';
if ( is_home() && ! is_front_page() ) {
	$posts_page = (int) get_option( 'page_for_posts' );
	$title      = $posts_page ? get_the_title( $posts_page ) : __( 'Knowledge Hub', 'trinetix' );
} elseif ( is_search() ) {
	$title = sprintf(
		/* translators: %s: search query */
		__( 'Search: %s', 'trinetix' ),
		get_search_query()
	);
} elseif ( is_404() ) {
	$title = __( 'Not Found', 'trinetix' );
} elseif ( is_archive() ) {
	$title = get_the_archive_title();
} else {
	$title = get_the_title();
}
?>
<section class="page-hero section">
	<div class="container">
		<h1><?php echo wp_kses_post( $title ); ?></h1>
		<?php if ( is_singular() && has_excerpt() ) : ?>
			<p class="page-hero-excerpt"><?php echo esc_html( get_the_excerpt() ); ?></p>
		<?php endif; ?>
	</div>
</section>
