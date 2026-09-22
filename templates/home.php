<?php
/**
 * Template Name: Home
 * Description: Full homepage layout. Edit all sections in the numbered boxes below the editor. Choose this template on any page to manage the same sections.
 *
 * @package Trinetix
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

get_header();

$page_id = (int) get_queried_object_id();
?>
<main id="top">
	<?php trinetix_render_home_sections( $page_id ); ?>
</main>
<?php
get_footer();
