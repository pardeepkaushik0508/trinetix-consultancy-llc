<?php
/**
 * Industry content.
 *
 * @package Trinetix
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
?>
<article <?php post_class( 'entry entry-industry' ); ?>>
	<?php if ( has_post_thumbnail() ) : ?>
		<figure class="entry-thumbnail"><?php the_post_thumbnail( 'large' ); ?></figure>
	<?php endif; ?>
	<div class="entry-content">
		<?php the_content(); ?>
	</div>
</article>
