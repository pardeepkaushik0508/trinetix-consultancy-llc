<?php
/**
 * Homepage approach cards (continues intro section).
 *
 * @package Trinetix
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$query = trinetix_ordered_query( 'approach_item', array( 'posts_per_page' => 3 ) );

$fallback = array(
	array(
		'title' => 'Domain & Strategy',
		'image' => 'https://images.unsplash.com/photo-1497366754035-f200968a6e72?auto=format&fit=crop&w=1300&q=92',
		'url'   => '',
	),
	array(
		'title' => 'Cognitive Architecture',
		'image' => 'https://images.unsplash.com/photo-1558494949-ef010cbdcc31?auto=format&fit=crop&w=1300&q=92',
		'url'   => '',
	),
	array(
		'title' => 'Harness Engineering',
		'image' => 'https://images.unsplash.com/photo-1518770660439-4636190af475?auto=format&fit=crop&w=1300&q=92',
		'url'   => '',
	),
);

$cards = array();
if ( $query->have_posts() ) {
	while ( $query->have_posts() ) {
		$query->the_post();
		$link = (string) get_post_meta( get_the_ID(), '_trinetix_link_url', true );
		$cards[] = array(
			'title' => get_the_title(),
			'image' => get_the_post_thumbnail_url( get_the_ID(), 'trinetix-card' ) ?: '',
			'url'   => $link ? $link : get_permalink(),
		);
	}
	wp_reset_postdata();
} else {
	$cards = $fallback;
}
?>
		<?php if ( ! empty( $cards ) ) : ?>
			<div class="approach-grid stagger">
				<?php foreach ( $cards as $card ) : ?>
					<?php
					$tag   = ! empty( $card['url'] ) ? 'a' : 'article';
					$attrs = ! empty( $card['url'] ) ? ' href="' . esc_url( $card['url'] ) . '"' : '';
					?>
					<<?php echo $tag; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?> class="approach-card"<?php echo $attrs; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>>
						<?php if ( ! empty( $card['image'] ) ) : ?>
							<img src="<?php echo esc_url( $card['image'] ); ?>" alt="<?php echo esc_attr( $card['title'] ); ?>">
						<?php endif; ?>
						<div class="inner">
							<h3><?php echo esc_html( $card['title'] ); ?></h3>
							<span class="circle-arrow"><?php echo trinetix_arrow_icon( 'dark' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?></span>
						</div>
					</<?php echo $tag; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>>
				<?php endforeach; ?>
			</div>
		<?php endif; ?>
	</div>
</section>
