<?php
/**
 * No results content.
 *
 * @package Trinetix
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
?>
<section class="no-results not-found">
	<div class="container">
		<h1 class="page-title"><?php esc_html_e( 'Nothing found', 'trinetix' ); ?></h1>
		<p><?php esc_html_e( 'It looks like nothing was found at this location. Try a search or return home.', 'trinetix' ); ?></p>
		<?php get_search_form(); ?>
		<p><a class="btn btn-cyan" href="<?php echo esc_url( home_url( '/' ) ); ?>"><?php esc_html_e( 'Back to home', 'trinetix' ); ?></a></p>
	</div>
</section>
