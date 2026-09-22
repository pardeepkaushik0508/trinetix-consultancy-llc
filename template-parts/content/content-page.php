<?php
/**
 * Page content.
 *
 * @package Trinetix
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
?>
<article <?php post_class( 'entry entry-page' ); ?>>
	<div class="entry-content">
		<?php the_content(); ?>
	</div>
</article>
