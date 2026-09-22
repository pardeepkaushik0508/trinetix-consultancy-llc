<?php
/**
 * Breadcrumb.
 *
 * @package Trinetix
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( is_front_page() ) {
	return;
}
?>
<nav class="breadcrumb" aria-label="<?php esc_attr_e( 'Breadcrumb', 'trinetix' ); ?>">
	<a href="<?php echo esc_url( home_url( '/' ) ); ?>"><?php esc_html_e( 'Home', 'trinetix' ); ?></a>
	<span aria-hidden="true"> / </span>
	<?php if ( is_singular() ) : ?>
		<span><?php echo esc_html( get_the_title() ); ?></span>
	<?php elseif ( is_search() ) : ?>
		<span><?php esc_html_e( 'Search', 'trinetix' ); ?></span>
	<?php else : ?>
		<span><?php echo wp_kses_post( get_the_archive_title() ); ?></span>
	<?php endif; ?>
</nav>
