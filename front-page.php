<?php
/**
 * Front page template.
 *
 * @package Trinetix
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

get_header();
?>
<main id="top">
	<?php
	get_template_part( 'template-parts/home/hero' );
	get_template_part( 'template-parts/home/intro' );
	get_template_part( 'template-parts/home/approach' );
	get_template_part( 'template-parts/home/services' );
	get_template_part( 'template-parts/home/industries' );
	get_template_part( 'template-parts/home/case-studies' );
	get_template_part( 'template-parts/home/testimonials' );
	get_template_part( 'template-parts/home/partners' );
	get_template_part( 'template-parts/home/knowledge' );
	get_template_part( 'template-parts/home/contact' );
	?>
</main>
<?php
get_footer();
