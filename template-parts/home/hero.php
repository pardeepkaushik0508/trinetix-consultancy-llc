<?php
/**
 * Homepage hero section.
 *
 * @package Trinetix
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$eyebrow      = (string) trinetix_home_setting( 'hero_eyebrow', '' );
$heading      = (string) trinetix_home_setting( 'hero_heading', "We engineer\nintelligent enterprises" );
$subline      = (string) trinetix_home_setting( 'hero_subline', 'for a world that keeps moving.' );
$description  = (string) trinetix_home_setting( 'hero_description', '' );
$cta_text     = (string) trinetix_home_setting( 'hero_cta_text', '' );
$cta_url      = (string) trinetix_home_setting( 'hero_cta_url', '#approach' );
$cta2_text    = (string) trinetix_home_setting( 'hero_cta_secondary_text', '' );
$cta2_url     = (string) trinetix_home_setting( 'hero_cta_secondary_url', '' );
$video_on     = (int) trinetix_home_setting( 'hero_video_enabled', 1 );
$video_id     = (int) trinetix_home_setting( 'hero_video_id', 0 );
$video_m_id   = (int) trinetix_home_setting( 'hero_video_mobile_id', 0 );
$poster_id    = (int) trinetix_home_setting( 'hero_poster_id', 0 );
$playback     = (string) trinetix_home_setting( 'hero_playback_rate', '1' );

$video_url = $video_id ? (string) wp_get_attachment_url( $video_id ) : '';
if ( ! $video_url ) {
	$video_url = trinetix_asset_uri( 'assets/video/hero.mp4' );
}

$video_mobile = $video_m_id ? (string) wp_get_attachment_url( $video_m_id ) : '';
$poster_url   = trinetix_image_url( $poster_id, 'full', trinetix_asset_uri( 'assets/images/hero-poster.jpg' ) );

$capabilities = trinetix_home_json_setting( 'hero_capabilities_json' );
if ( empty( $capabilities ) ) {
	$capabilities = trinetix_default_hero_capabilities();
}

$heading_html = nl2br( esc_html( $heading ), false );
?>
<section class="hero">
	<div class="hero-media" id="heroBg">
		<?php if ( $video_on ) : ?>
			<video
				class="hero-video"
				autoplay
				muted
				loop
				playsinline
				webkit-playsinline
				preload="metadata"
				disablepictureinpicture
				controlslist="nodownload noplaybackrate nofullscreen"
				poster="<?php echo esc_url( $poster_url ); ?>"
				aria-hidden="true"
				data-playback-rate="<?php echo esc_attr( $playback ); ?>"
				data-src="<?php echo esc_url( $video_url ); ?>"
				data-src-desktop="<?php echo esc_url( $video_url ); ?>"
				<?php if ( $video_mobile ) : ?>
					data-src-mobile="<?php echo esc_url( $video_mobile ); ?>"
				<?php endif; ?>
			>
				<source src="<?php echo esc_url( $video_url ); ?>" type="video/mp4">
			</video>
		<?php else : ?>
			<img class="hero-poster-fallback" src="<?php echo esc_url( $poster_url ); ?>" alt="" />
		<?php endif; ?>
		<div class="hero-tech-grid" aria-hidden="true"></div>
		<div class="hero-tech-lines" aria-hidden="true"></div>
		<div class="hero-tech-glow" aria-hidden="true"></div>
	</div>

	<div class="hero-star" aria-hidden="true"></div>

	<div class="container hero-reference-layout">
		<div class="hero-copy hero-reference-copy reveal">
			<?php if ( $eyebrow ) : ?>
				<div class="hero-eyebrow"><?php echo esc_html( $eyebrow ); ?></div>
			<?php endif; ?>
			<h1><?php echo $heading_html; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- escaped above. ?></h1>
			<?php if ( $subline ) : ?>
				<div class="hero-subline"><?php echo esc_html( $subline ); ?></div>
			<?php endif; ?>
			<?php if ( $description ) : ?>
				<p><?php echo esc_html( $description ); ?></p>
			<?php endif; ?>
			<?php if ( $cta_text || $cta2_text ) : ?>
				<div class="hero-actions">
					<?php if ( $cta_text ) : ?>
						<a href="<?php echo esc_url( $cta_url ); ?>" class="btn hero-primary-cta"><?php echo esc_html( $cta_text ); ?></a>
					<?php endif; ?>
					<?php if ( $cta2_text ) : ?>
						<a href="<?php echo esc_url( $cta2_url ); ?>" class="btn btn-outline"><?php echo esc_html( $cta2_text ); ?></a>
					<?php endif; ?>
				</div>
			<?php endif; ?>
		</div>

		<?php if ( ! empty( $capabilities ) ) : ?>
			<div class="hero-capability-list reveal" aria-label="<?php esc_attr_e( 'Core capabilities', 'trinetix' ); ?>">
				<?php foreach ( $capabilities as $cap ) : ?>
					<?php
					$cap      = is_array( $cap ) ? $cap : array();
					$number   = (string) ( $cap['number'] ?? '' );
					$title    = (string) ( $cap['title'] ?? '' );
					$url      = (string) ( $cap['url'] ?? '#approach' );
					$class    = (string) ( $cap['class'] ?? '' );
					$icon     = (string) ( $cap['icon'] ?? 'star' );
					$cap_cls  = trim( 'hero-capability ' . $class );
					?>
					<a class="<?php echo esc_attr( $cap_cls ); ?>" href="<?php echo esc_url( $url ); ?>">
						<span class="capability-copy">
							<?php if ( $number ) : ?><small><?php echo esc_html( $number ); ?></small><?php endif; ?>
							<strong><?php echo esc_html( $title ); ?></strong>
						</span>
						<?php trinetix_capability_icon( $icon ); ?>
					</a>
				<?php endforeach; ?>
			</div>
		<?php endif; ?>
	</div>

	<div class="hero-edge-glow" aria-hidden="true"></div>
</section>
