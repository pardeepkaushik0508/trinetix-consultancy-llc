<?php
/**
 * Homepage industries section.
 * Interactive data comes from localized trinetixIndustries (see enqueue.php).
 *
 * @package Trinetix
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$title = (string) trinetix_home_setting( 'industries_title', 'Our Industry Expertise' );
list( $title_main, $title_accent ) = trinetix_split_accent_title( $title );

$industries = trinetix_get_industries_payload();
$first      = $industries[0] ?? array(
	'title' => '',
	'text'  => '',
	'image' => '',
	'url'   => home_url( '/#contact' ),
);
?>
<section class="industries" id="industries">
	<div class="industry-bg" id="industryBg"<?php echo ! empty( $first['image'] ) ? ' style="background-image:url(' . esc_url( $first['image'] ) . ')"' : ''; ?>></div>
	<div class="container industry-content">
		<div class="section-head reveal">
			<div class="section-head-left">
				<h2 class="section-title">
					<?php echo esc_html( $title_main ); ?>
					<?php if ( $title_accent ) : ?>
						<span class="accent"><?php echo esc_html( $title_accent ); ?></span>
					<?php endif; ?>
				</h2>
				<span class="circle-arrow outline"><?php echo trinetix_arrow_icon( 'light' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?></span>
			</div>
			<div class="industry-nav">
				<button class="circle-arrow outline" id="industryPrev" type="button" aria-label="<?php esc_attr_e( 'Previous industry', 'trinetix' ); ?>">
					<img class="arrow-img" src="<?php echo esc_url( trinetix_asset_uri( 'assets/icons/arrow-light.svg' ) ); ?>" alt="" style="transform:rotate(180deg)">
				</button>
				<button class="circle-arrow outline" id="industryNext" type="button" aria-label="<?php esc_attr_e( 'Next industry', 'trinetix' ); ?>">
					<?php echo trinetix_arrow_icon( 'light' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
				</button>
			</div>
		</div>

		<div class="industry-copy reveal">
			<h3 id="industryTitle"><?php echo esc_html( (string) ( $first['title'] ?? '' ) ); ?></h3>
			<p id="industryText"><?php echo esc_html( (string) ( $first['text'] ?? '' ) ); ?></p>
			<a href="<?php echo esc_url( (string) ( $first['url'] ?? home_url( '/#contact' ) ) ); ?>" class="btn btn-light" id="industryCta">
				<?php esc_html_e( 'Explore Industry Expertise', 'trinetix' ); ?>
				<?php echo trinetix_arrow_icon( 'dark' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
			</a>
		</div>
	</div>

	<?php if ( ! empty( $industries ) ) : ?>
		<div class="industry-tabs-wrap">
			<div class="industry-tabs" role="tablist" aria-label="<?php esc_attr_e( 'Industries', 'trinetix' ); ?>">
				<?php foreach ( $industries as $i => $industry ) : ?>
					<?php $num = str_pad( (string) ( $i + 1 ), 2, '0', STR_PAD_LEFT ); ?>
					<button class="industry-tab<?php echo 0 === $i ? ' active' : ''; ?>" type="button" data-i="<?php echo esc_attr( (string) $i ); ?>" role="tab" aria-selected="<?php echo 0 === $i ? 'true' : 'false'; ?>">
						<span><?php echo esc_html( $num ); ?></span><?php echo esc_html( (string) ( $industry['title'] ?? '' ) ); ?>
					</button>
				<?php endforeach; ?>
			</div>
		</div>
	<?php endif; ?>
</section>
