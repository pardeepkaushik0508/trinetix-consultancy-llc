<?php
/**
 * Front page template — same section stack as the Home page template.
 *
 * @package Trinetix
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

get_header();

$page_id = trinetix_front_page_id();
if ( ! $page_id ) {
	$page_id = (int) get_queried_object_id();
}
?>
<main id="top">
	<?php trinetix_render_home_sections( $page_id ); ?>
</main>
<?php
get_footer();
