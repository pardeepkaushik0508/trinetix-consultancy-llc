<?php
/**
 * Footer template.
 *
 * @package Trinetix
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$company   = (string) trinetix_get_setting( 'company_name', get_bloginfo( 'name' ) );
$tagline   = (string) trinetix_get_setting( 'brand_tagline', 'Vision Beyond Technology' );
$intro     = (string) trinetix_get_setting( 'footer_intro', '' );
$copyright = (string) trinetix_get_setting( 'footer_copyright', '' );
$privacy   = (string) trinetix_get_setting( 'footer_privacy_url', '' );
$terms     = (string) trinetix_get_setting( 'footer_terms_url', '' );
$logo_src  = trinetix_asset_uri( 'assets/images/logo.png' );

$custom_logo_id = (int) get_theme_mod( 'custom_logo' );
if ( $custom_logo_id ) {
	$custom_src = wp_get_attachment_image_url( $custom_logo_id, 'full' );
	if ( $custom_src ) {
		$logo_src = $custom_src;
	}
}
?>
<footer class="footer">
	<div class="container footer-top">
		<div class="footer-heading-row">
			<div class="footer-brand">
				<img src="<?php echo esc_url( $logo_src ); ?>" alt="<?php echo esc_attr( $company ); ?>">
				<p class="footer-tagline"><?php echo esc_html( $tagline ); ?></p>
			</div>
			<?php if ( $intro ) : ?>
				<div class="footer-intro"><?php echo esc_html( $intro ); ?></div>
			<?php endif; ?>
		</div>

		<div class="footer-directory">
			<?php
			trinetix_render_footer_row( 'footer_ai', __( 'Artificial Intelligence', 'trinetix' ) );
			trinetix_render_footer_row( 'footer_data', __( 'Data & Analytics', 'trinetix' ) );
			trinetix_render_footer_row( 'footer_engineering', __( 'Digital Engineering', 'trinetix' ) );
			trinetix_render_footer_row( 'footer_experience', __( 'Experience', 'trinetix' ) );

			if (
				! has_nav_menu( 'footer_ai' )
				&& ! has_nav_menu( 'footer_data' )
				&& ! has_nav_menu( 'footer_engineering' )
				&& ! has_nav_menu( 'footer_experience' )
			) {
				get_template_part( 'template-parts/global/footer-fallback' );
			}
			?>
		</div>

		<div class="footer-bottom">
			<span><?php echo esc_html( $copyright ? $copyright : sprintf( '© %s %s. All rights reserved.', gmdate( 'Y' ), $company ) ); ?></span>
			<span>
				<?php if ( $privacy ) : ?>
					<a href="<?php echo esc_url( $privacy ); ?>"><?php esc_html_e( 'Privacy Policy', 'trinetix' ); ?></a>
				<?php else : ?>
					<?php esc_html_e( 'Privacy Policy', 'trinetix' ); ?>
				<?php endif; ?>
				&nbsp; • &nbsp;
				<?php if ( $terms ) : ?>
					<a href="<?php echo esc_url( $terms ); ?>"><?php esc_html_e( 'Terms & Conditions', 'trinetix' ); ?></a>
				<?php else : ?>
					<?php esc_html_e( 'Terms & Conditions', 'trinetix' ); ?>
				<?php endif; ?>
			</span>
		</div>
	</div>
</footer>

<?php wp_footer(); ?>
</body>
</html>
