<?php
/**
 * Comments template.
 *
 * @package Trinetix
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( post_password_required() ) {
	return;
}
?>
<div id="comments" class="comments-area">
	<?php if ( have_comments() ) : ?>
		<h2 class="comments-title">
			<?php
			$trinetix_count = get_comments_number();
			printf(
				/* translators: 1: comment count, 2: post title */
				esc_html( _n( '%1$s comment on “%2$s”', '%1$s comments on “%2$s”', $trinetix_count, 'trinetix' ) ),
				esc_html( number_format_i18n( $trinetix_count ) ),
				esc_html( get_the_title() )
			);
			?>
		</h2>
		<ol class="comment-list">
			<?php
			wp_list_comments(
				array(
					'style'      => 'ol',
					'short_ping' => true,
					'avatar_size'=> 48,
				)
			);
			?>
		</ol>
		<?php the_comments_navigation(); ?>
	<?php endif; ?>

	<?php if ( ! comments_open() && get_comments_number() && post_type_supports( get_post_type(), 'comments' ) ) : ?>
		<p class="no-comments"><?php esc_html_e( 'Comments are closed.', 'trinetix' ); ?></p>
	<?php endif; ?>

	<?php
	comment_form(
		array(
			'title_reply' => __( 'Leave a comment', 'trinetix' ),
			'class_form'  => 'comment-form form',
		)
	);
	?>
</div>
