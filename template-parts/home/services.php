<?php
/**
 * Homepage services section.
 *
 * @package Trinetix
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$title = (string) trinetix_home_setting( 'services_title', 'Our Services' );
list( $title_main, $title_accent ) = trinetix_split_accent_title( $title );

$query = trinetix_ordered_query( 'service' );

$fallback = array(
	array(
		'title'   => 'Artificial Intelligence',
		'excerpt' => 'Applied AI, intelligent automation and enterprise copilots built around measurable business outcomes.',
		'image'   => 'https://images.unsplash.com/photo-1677442136019-21780ecad995?auto=format&fit=crop&w=1300&q=92',
		'url'     => '',
	),
	array(
		'title'   => 'Data & Analytics',
		'excerpt' => 'Trusted data foundations and decision intelligence that turn enterprise information into action.',
		'image'   => 'https://images.unsplash.com/photo-1551288049-bebda4e38f71?auto=format&fit=crop&w=1300&q=92',
		'url'     => '',
	),
	array(
		'title'   => 'Digital Engineering',
		'excerpt' => 'Cloud-native platforms, APIs and product engineering built for speed, resilience and lower delivery risk.',
		'image'   => 'https://images.unsplash.com/photo-1498050108023-c5249f4df085?auto=format&fit=crop&w=1300&q=92',
		'url'     => '',
	),
	array(
		'title'   => 'Experience',
		'excerpt' => 'Connected customer and employee experiences across digital products, portals and enterprise platforms.',
		'image'   => 'https://images.unsplash.com/photo-1521737604893-d14cc237f11d?auto=format&fit=crop&w=1300&q=92',
		'url'     => '',
	),
);

$items = array();
if ( $query->have_posts() ) {
	while ( $query->have_posts() ) {
		$query->the_post();
		$items[] = array(
			'title'   => get_the_title(),
			'excerpt' => has_excerpt() ? get_the_excerpt() : wp_trim_words( wp_strip_all_tags( get_the_content() ), 28 ),
			'image'   => get_the_post_thumbnail_url( get_the_ID(), 'trinetix-card' ) ?: '',
			'url'     => get_permalink(),
		);
	}
	wp_reset_postdata();
} else {
	$items = $fallback;
}
?>
<section class="services" id="services">
	<div class="container">
		<div class="section-head reveal">
			<div class="section-head-left">
				<h2 class="section-title">
					<?php echo esc_html( $title_main ); ?>
					<?php if ( $title_accent ) : ?>
						<span class="accent"><?php echo esc_html( $title_accent ); ?></span>
					<?php endif; ?>
				</h2>
			</div>
		</div>

		<?php if ( empty( $items ) ) : ?>
			<p class="empty-state"><?php esc_html_e( 'Services will appear here once published.', 'trinetix' ); ?></p>
		<?php else : ?>
			<div class="service-grid stagger">
				<?php foreach ( $items as $index => $item ) : ?>
					<?php
					$number = str_pad( (string) ( $index + 1 ), 2, '0', STR_PAD_LEFT );
					$tag    = ! empty( $item['url'] ) ? 'a' : 'article';
					$attrs  = ! empty( $item['url'] ) ? ' href="' . esc_url( $item['url'] ) . '"' : '';
					?>
					<<?php echo $tag; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?> class="service-card"<?php echo $attrs; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>>
						<?php if ( ! empty( $item['image'] ) ) : ?>
							<img src="<?php echo esc_url( $item['image'] ); ?>" alt="<?php echo esc_attr( $item['title'] ); ?>">
						<?php endif; ?>
						<span class="number"><?php echo esc_html( $number ); ?></span>
						<div class="service-content">
							<div class="service-title-row">
								<h3><?php echo esc_html( $item['title'] ); ?></h3>
								<span class="circle-arrow"><?php echo trinetix_arrow_icon( 'dark' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?></span>
							</div>
							<?php if ( ! empty( $item['excerpt'] ) ) : ?>
								<p><?php echo esc_html( $item['excerpt'] ); ?></p>
							<?php endif; ?>
						</div>
					</<?php echo $tag; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>>
				<?php endforeach; ?>
			</div>
		<?php endif; ?>
	</div>
</section>
