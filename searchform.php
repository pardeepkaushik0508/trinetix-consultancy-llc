<?php
/**
 * Search form.
 *
 * @package Trinetix
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
?>
<form role="search" method="get" class="search-form" action="<?php echo esc_url( home_url( '/' ) ); ?>">
	<label class="screen-reader-text" for="trinetix-search"><?php esc_html_e( 'Search for:', 'trinetix' ); ?></label>
	<input type="search" id="trinetix-search" class="search-field" placeholder="<?php esc_attr_e( 'Search…', 'trinetix' ); ?>" value="<?php echo esc_attr( get_search_query() ); ?>" name="s" />
	<button type="submit" class="btn btn-cyan search-submit"><?php esc_html_e( 'Search', 'trinetix' ); ?></button>
</form>
