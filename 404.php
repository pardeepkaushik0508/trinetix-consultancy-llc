<?php
/**
 * 404 template.
 *
 * @package Trinetix
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

get_header();
?>
<main id="top">
	<section class="error-404 not-found">
		<div class="container">
			<h1 class="page-title"><?php esc_html_e( 'Page not found', 'trinetix' ); ?></h1>
			<p><?php esc_html_e( 'The page you are looking for may have moved or no longer exists.', 'trinetix' ); ?></p>
			<?php get_search_form(); ?>
			<p><a class="btn btn-cyan" href="<?php echo esc_url( home_url( '/' ) ); ?>"><?php esc_html_e( 'Back to home', 'trinetix' ); ?></a></p>
		</div>
	</section>
</main>
<?php
get_footer();
