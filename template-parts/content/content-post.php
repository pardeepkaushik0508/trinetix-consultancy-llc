<?php
/**
 * Post content.
 *
 * @package Trinetix
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
?>
<div class="entry-content">
	<?php if ( has_post_thumbnail() ) : ?>
		<figure class="entry-thumbnail">
			<?php the_post_thumbnail( 'large' ); ?>
		</figure>
	<?php endif; ?>
	<?php the_content(); ?>
</div>
