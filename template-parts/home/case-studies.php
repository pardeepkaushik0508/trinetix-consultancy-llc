<?php
/**
 * Homepage case studies / work section.
 *
 * @package Trinetix
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$title = (string) trinetix_home_setting( 'work_title', 'Our Work' );
list( $title_main, $title_accent ) = trinetix_split_accent_title( $title );

$query = trinetix_ordered_query( 'case_study', array( 'posts_per_page' => 4 ) );

$fallback = array(
	array(
		'eyebrow' => 'AI Center of Excellence',
		'title'   => 'An on-premises AI Center of Excellence that resolves complex maintenance queries in 20 to 40 seconds.',
		'excerpt' => 'Built to keep enterprise knowledge close to the business, reduce resolution time and support operational teams with trusted AI capabilities.',
		'image'   => 'https://images.unsplash.com/photo-1436491865332-7a61a109cc05?auto=format&fit=crop&w=1200&q=92',
		'url'     => '',
	),
	array(
		'eyebrow' => 'Agentic AI',
		'title'   => 'AI-driven supply chain risk intelligence with quantified business impact.',
		'excerpt' => 'Combining predictive analytics and intelligent automation to identify supply chain risks earlier and improve decision velocity.',
		'image'   => 'https://images.unsplash.com/photo-1586528116311-ad8dd3c8310d?auto=format&fit=crop&w=1200&q=92',
		'url'     => '',
	),
	array(
		'eyebrow' => 'Digital Engineering',
		'title'   => '60% faster MVP creation through AI-assisted product engineering.',
		'excerpt' => 'Accelerating ideation, prototyping and delivery with AI-enabled workflows while maintaining quality and enterprise engineering discipline.',
		'image'   => 'https://images.unsplash.com/photo-1516321318423-f06f85e504b3?auto=format&fit=crop&w=1200&q=92',
		'url'     => '',
	),
	array(
		'eyebrow' => 'Healthcare Data',
		'title'   => 'Building a modern healthcare data platform for better patient and therapy insights.',
		'excerpt' => 'Designed to unify fragmented information, improve access to insight and support better clinical and operational outcomes.',
		'image'   => 'https://images.unsplash.com/photo-1576091160399-112ba8d25d1d?auto=format&fit=crop&w=1200&q=92',
		'url'     => '',
	),
);

$items = array();
if ( $query->have_posts() ) {
	while ( $query->have_posts() ) {
		$query->the_post();
		$id = get_the_ID();
		$eyebrow = (string) get_post_meta( $id, '_trinetix_eyebrow', true );
		$items[] = array(
			'eyebrow' => $eyebrow ? $eyebrow : '',
			'title'   => get_the_title(),
			'excerpt' => has_excerpt() ? get_the_excerpt() : wp_trim_words( wp_strip_all_tags( get_the_content() ), 36 ),
			'image'   => get_the_post_thumbnail_url( $id, 'trinetix-work' ) ?: '',
			'url'     => get_permalink(),
		);
	}
	wp_reset_postdata();
} else {
	$items = $fallback;
}
?>
<section class="work" id="work">
	<div class="container">
		<div class="section-head reveal">
			<div class="section-head-left">
				<h2 class="section-title">
					<?php echo esc_html( $title_main ); ?>
					<?php if ( $title_accent ) : ?>
						<span class="accent"><?php echo esc_html( $title_accent ); ?></span>
					<?php endif; ?>
				</h2>
				<span class="circle-arrow"><?php echo trinetix_arrow_icon( 'dark' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?></span>
			</div>
		</div>

		<?php if ( empty( $items ) ) : ?>
			<p class="empty-state"><?php esc_html_e( 'Case studies will appear here once published.', 'trinetix' ); ?></p>
		<?php else : ?>
			<div class="work-grid stagger">
				<?php foreach ( $items as $item ) : ?>
					<?php
					$tag   = ! empty( $item['url'] ) ? 'a' : 'article';
					$attrs = ! empty( $item['url'] ) ? ' href="' . esc_url( $item['url'] ) . '"' : '';
					?>
					<<?php echo $tag; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?> class="work-card"<?php echo $attrs; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>>
						<?php if ( ! empty( $item['image'] ) ) : ?>
							<img src="<?php echo esc_url( $item['image'] ); ?>" alt="<?php echo esc_attr( $item['title'] ); ?>">
						<?php endif; ?>
						<div class="work-info">
							<?php if ( ! empty( $item['eyebrow'] ) ) : ?>
								<small><?php echo esc_html( $item['eyebrow'] ); ?></small>
							<?php endif; ?>
							<h3><?php echo esc_html( $item['title'] ); ?></h3>
							<?php if ( ! empty( $item['excerpt'] ) ) : ?>
								<p><?php echo esc_html( $item['excerpt'] ); ?></p>
							<?php endif; ?>
							<span class="circle-arrow"><?php echo trinetix_arrow_icon( 'dark' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?></span>
						</div>
					</<?php echo $tag; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>>
				<?php endforeach; ?>
			</div>
		<?php endif; ?>
	</div>
</section>
