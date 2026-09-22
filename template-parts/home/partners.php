<?php
/**
 * Homepage partners ecosystem.
 *
 * @package Trinetix
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$title_raw = (string) trinetix_home_setting( 'partners_title', 'Our Partner Ecosystem' );
list( $title_main, $title_accent ) = trinetix_split_accent_title( $title_raw );

$query = trinetix_ordered_query( 'partner' );
$cards = array();

if ( $query->have_posts() ) {
	while ( $query->have_posts() ) {
		$query->the_post();
		$class = (string) get_post_meta( get_the_ID(), '_trinetix_card_class', true );
		$cards[] = array(
			'title' => get_the_title(),
			'text'  => has_excerpt() ? get_the_excerpt() : wp_trim_words( wp_strip_all_tags( get_the_content() ), 24 ),
			'image' => trinetix_get_card_image_url( get_the_ID(), 'medium', '' ),
			'url'   => (string) get_post_meta( get_the_ID(), '_trinetix_website', true ),
			'class' => $class ? $class : sanitize_title( get_the_title() ),
		);
	}
	wp_reset_postdata();
}

if ( empty( $cards ) ) {
	$cards = array(
		array( 'title' => 'AWS', 'text' => 'Cloud infrastructure, modernization and data platform enablement for enterprise-scale solutions.', 'image' => trinetix_asset_uri( 'assets/images/partner-aws.png' ), 'url' => '', 'class' => 'aws' ),
		array( 'title' => 'Microsoft', 'text' => 'Cloud, productivity, data and AI capabilities across the Microsoft enterprise ecosystem.', 'image' => trinetix_asset_uri( 'assets/icons/partner-microsoft.svg' ), 'url' => '', 'class' => 'microsoft' ),
		array( 'title' => 'Google Cloud', 'text' => 'Modern cloud, analytics and scalable architecture support for data-intensive businesses.', 'image' => trinetix_asset_uri( 'assets/icons/partner-google.svg' ), 'url' => '', 'class' => 'google' ),
		array( 'title' => 'Databricks', 'text' => 'Lakehouse-driven data engineering and analytics foundations for advanced enterprise use cases.', 'image' => trinetix_asset_uri( 'assets/images/partner-databricks.png' ), 'url' => '', 'class' => 'databricks' ),
		array( 'title' => 'Cprime', 'text' => 'Transformation and modern delivery support for product, platform and enterprise agility programs.', 'image' => trinetix_asset_uri( 'assets/images/partner-cprime.png' ), 'url' => '', 'class' => 'cprime' ),
		array( 'title' => 'Salesforce', 'text' => 'Connected CRM, customer experience and workflow enablement across sales and service operations.', 'image' => trinetix_asset_uri( 'assets/icons/partner-salesforce.svg' ), 'url' => '', 'class' => 'salesforce' ),
		array( 'title' => 'ServiceNow', 'text' => 'Enterprise service workflows and operational efficiency through modern digital automation.', 'image' => trinetix_asset_uri( 'assets/images/partner-servicenow.png' ), 'url' => '', 'class' => 'servicenow' ),
		array( 'title' => 'Atlassian', 'text' => 'Collaboration, engineering delivery and work management for fast-moving product and technology teams.', 'image' => trinetix_asset_uri( 'assets/icons/partner-atlassian.svg' ), 'url' => '', 'class' => 'atlassian' ),
	);
}
?>
<section class="ecosystem">
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

		<div class="partner-grid stagger">
			<?php foreach ( $cards as $card ) : ?>
				<article class="partner-card <?php echo esc_attr( $card['class'] ); ?>">
					<?php if ( ! empty( $card['url'] ) ) : ?>
						<a href="<?php echo esc_url( $card['url'] ); ?>" target="_blank" rel="noopener noreferrer">
					<?php endif; ?>
					<img src="<?php echo esc_url( $card['image'] ); ?>" alt="<?php echo esc_attr( $card['title'] ); ?>" loading="lazy" />
					<div class="partner-hover">
						<div>
							<h4><?php echo esc_html( $card['title'] ); ?></h4>
							<p><?php echo esc_html( $card['text'] ); ?></p>
						</div>
						<span class="circle-arrow"><?php echo trinetix_arrow_icon( 'dark' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?></span>
					</div>
					<?php if ( ! empty( $card['url'] ) ) : ?>
						</a>
					<?php endif; ?>
				</article>
			<?php endforeach; ?>
		</div>
	</div>
</section>
