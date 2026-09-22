<?php
/**
 * Homepage intro (Approach) section — copy + video card.
 * Closes in approach.php after approach-grid.
 *
 * @package Trinetix
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$heading     = (string) trinetix_home_setting( 'intro_heading', 'AI creates opportunity. We make sure it creates business value.' );
$content     = (string) trinetix_home_setting( 'intro_content', '' );
$cta_text    = (string) trinetix_home_setting( 'intro_cta_text', 'Explore Our Capabilities' );
$cta_url     = (string) trinetix_home_setting( 'intro_cta_url', '#services' );
$video_label = (string) trinetix_home_setting( 'intro_video_label', "From strategy\nto measurable outcomes" );
$video_url   = (string) trinetix_home_setting( 'intro_video_url', '' );
$image_id    = (int) trinetix_home_setting( 'intro_video_image_id', 0 );
$image_url   = trinetix_image_url(
	$image_id,
	'large',
	'https://images.unsplash.com/photo-1552664730-d307ca884978?auto=format&fit=crop&w=1500&q=92'
);

$default_content = '<p>AI has made it possible to build solutions most enterprises couldn\'t dream of previously. It has also made the underlying market conditions unstable enough that many solutions can become obsolete before launch or remain stuck in a pilot phase.</p>
<p><strong>We fix both.</strong></p>
<p>Our domain &amp; strategy consultants make sure you are building the thing that matters. Our cognitive architects put your organization\'s knowledge and data to work. Our harness engineering ships it at enterprise scale and keeps it running in production.</p>
<p>That is what we mean when we say <strong>we engineer intelligent enterprises</strong> — and our approach makes the difference.</p>';
?>
<section class="section intro" id="approach">
	<div class="container">
		<div class="intro-grid">
			<div class="intro-copy reveal">
				<?php if ( $heading ) : ?>
					<h2><?php echo esc_html( $heading ); ?></h2>
				<?php endif; ?>
				<?php
				if ( $content ) {
					echo wp_kses_post( wpautop( $content ) );
				} else {
					echo wp_kses_post( $default_content );
				}
				?>
				<?php if ( $cta_text ) : ?>
					<a href="<?php echo esc_url( $cta_url ); ?>" class="btn btn-cyan">
						<?php echo esc_html( $cta_text ); ?>
						<?php echo trinetix_arrow_icon( 'dark' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
					</a>
				<?php endif; ?>
			</div>

			<div class="video-card reveal"<?php echo $video_url ? ' data-video-url="' . esc_url( $video_url ) . '"' : ''; ?>>
				<img src="<?php echo esc_url( $image_url ); ?>" alt="<?php esc_attr_e( 'Enterprise strategy workshop', 'trinetix' ); ?>">
				<div class="play" aria-hidden="true">▶</div>
				<?php if ( $video_label ) : ?>
					<div class="video-label"><?php echo nl2br( esc_html( $video_label ), false ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?></div>
				<?php endif; ?>
			</div>
		</div>
