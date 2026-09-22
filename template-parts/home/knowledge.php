<?php
/**
 * Homepage Knowledge Hub.
 *
 * @package Trinetix
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$title_raw = (string) trinetix_home_setting( 'knowledge_title', 'Knowledge Hub' );
list( $title_main, $title_accent ) = trinetix_split_accent_title( $title_raw );
$cta_text = (string) trinetix_home_setting( 'knowledge_cta_text', 'Explore More Insights' );
$cta_url  = (string) trinetix_home_setting( 'knowledge_cta_url', '' );
if ( ! $cta_url ) {
	$blog = (int) get_option( 'page_for_posts' );
	$cta_url = $blog ? get_permalink( $blog ) : home_url( '/' );
}

$query = new WP_Query(
	array(
		'post_type'              => 'post',
		'post_status'            => 'publish',
		'posts_per_page'         => 5,
		'no_found_rows'          => true,
		'ignore_sticky_posts'    => true,
		'update_post_meta_cache' => true,
		'update_post_term_cache' => true,
	)
);

$cards = array();
if ( $query->have_posts() ) {
	$i = 0;
	while ( $query->have_posts() ) {
		$query->the_post();
		$cats = get_the_category();
		$cards[] = array(
			'title'    => get_the_title(),
			'label'    => ! empty( $cats[0] ) ? $cats[0]->name : __( 'Insights', 'trinetix' ),
			'image'    => trinetix_get_card_image_url( get_the_ID(), 'trinetix-insight', 'https://images.unsplash.com/photo-1518770660439-4636190af475?auto=format&fit=crop&w=1100&q=92' ),
			'url'      => get_permalink(),
			'featured' => 1 === $i,
			'wide'     => 4 === $i,
		);
		++$i;
	}
	wp_reset_postdata();
}

if ( empty( $cards ) ) {
	$cards = array(
		array( 'title' => 'Building the right foundation for enterprise AI.', 'label' => 'Insights', 'image' => 'https://images.unsplash.com/photo-1518770660439-4636190af475?auto=format&fit=crop&w=1100&q=92', 'url' => $cta_url, 'featured' => false, 'wide' => false ),
		array( 'title' => 'How technology leaders move from isolated initiatives to connected business outcomes.', 'label' => 'Transformation', 'image' => 'https://images.unsplash.com/photo-1552664730-d307ca884978?auto=format&fit=crop&w=1200&q=92', 'url' => $cta_url, 'featured' => true, 'wide' => false ),
		array( 'title' => 'Making enterprise data trusted, usable and actionable.', 'label' => 'Data & Analytics', 'image' => 'https://images.unsplash.com/photo-1551288049-bebda4e38f71?auto=format&fit=crop&w=1100&q=92', 'url' => $cta_url, 'featured' => false, 'wide' => false ),
		array( 'title' => 'A practical approach to legacy modernization.', 'label' => 'Engineering', 'image' => 'https://images.unsplash.com/photo-1498050108023-c5249f4df085?auto=format&fit=crop&w=1100&q=92', 'url' => $cta_url, 'featured' => false, 'wide' => false ),
		array( 'title' => 'Aligning people, process and technology for sustainable change.', 'label' => 'Leadership', 'image' => 'https://images.unsplash.com/photo-1521737711867-e3b97375f902?auto=format&fit=crop&w=1100&q=92', 'url' => $cta_url, 'featured' => false, 'wide' => true ),
	);
}
?>
<section class="knowledge" id="insights">
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
			<?php if ( $cta_text ) : ?>
				<a href="<?php echo esc_url( $cta_url ); ?>" class="btn btn-cyan">
					<?php echo esc_html( $cta_text ); ?>
					<?php echo trinetix_arrow_icon( 'dark' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
				</a>
			<?php endif; ?>
		</div>

		<div class="knowledge-grid stagger">
			<?php foreach ( $cards as $card ) : ?>
				<?php
				$classes = 'insight';
				if ( ! empty( $card['featured'] ) ) {
					$classes .= ' featured';
				}
				if ( ! empty( $card['wide'] ) ) {
					$classes .= ' wide';
				}
				?>
				<article class="<?php echo esc_attr( $classes ); ?>">
					<a href="<?php echo esc_url( $card['url'] ); ?>">
						<img src="<?php echo esc_url( $card['image'] ); ?>" alt="" loading="lazy" />
						<div class="insight-info">
							<small><?php echo esc_html( $card['label'] ); ?></small>
							<h3><?php echo esc_html( $card['title'] ); ?></h3>
						</div>
					</a>
				</article>
			<?php endforeach; ?>
		</div>
	</div>
</section>
