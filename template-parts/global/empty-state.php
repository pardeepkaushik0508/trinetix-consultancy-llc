<?php
/**
 * Empty state.
 *
 * @package Trinetix
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
?>
<div class="empty-state">
	<h2><?php esc_html_e( 'Nothing found', 'trinetix' ); ?></h2>
	<p><?php esc_html_e( 'Try another search or return to the homepage.', 'trinetix' ); ?></p>
	<p><a class="btn btn-cyan" href="<?php echo esc_url( home_url( '/' ) ); ?>"><?php esc_html_e( 'Back to Home', 'trinetix' ); ?></a></p>
</div>
