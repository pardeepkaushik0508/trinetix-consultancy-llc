<?php
/**
 * Footer template.
 *
 * @package Trinetix
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$company   = (string) trinetix_get_hf( 'footer_heading', trinetix_get_setting( 'company_name', get_bloginfo( 'name' ) ) );
$tagline   = (string) trinetix_get_hf( 'footer_tagline', trinetix_get_setting( 'brand_tagline', 'Vision Beyond Technology' ) );
$intro     = (string) trinetix_get_hf( 'footer_intro', trinetix_get_setting( 'footer_intro', '' ) );
$copyright = (string) trinetix_get_hf( 'footer_copyright', trinetix_get_setting( 'footer_copyright', '' ) );
$privacy   = (string) trinetix_get_hf( 'footer_privacy_url', trinetix_get_setting( 'footer_privacy_url', '' ) );
$terms     = (string) trinetix_get_hf( 'footer_terms_url', trinetix_get_setting( 'footer_terms_url', '' ) );
$privacy_l = (string) trinetix_get_hf( 'footer_privacy_label', 'Privacy Policy' );
$terms_l   = (string) trinetix_get_hf( 'footer_terms_label', 'Terms & Conditions' );
$show_tag  = (int) trinetix_get_hf( 'footer_show_tagline', 1 ) === 1;
$show_intro = (int) trinetix_get_hf( 'footer_show_intro', 1 ) === 1;
$show_menus = (int) trinetix_get_hf( 'footer_show_menus', 1 ) === 1;
$show_legal = (int) trinetix_get_hf( 'footer_show_legal', 1 ) === 1;

$logo_id = (int) trinetix_get_hf( 'footer_logo_id', 0 );
if ( ! $logo_id ) {
	$logo_id = (int) trinetix_get_hf( 'header_logo_id', 0 );
}
if ( ! $logo_id ) {
	$logo_id = (int) get_theme_mod( 'custom_logo' );
}
$logo_src = $logo_id ? (string) wp_get_attachment_image_url( $logo_id, 'full' ) : '';
if ( ! $logo_src ) {
	$logo_src = trinetix_asset_uri( 'assets/images/logo.png' );
}

$menu_ai   = (int) trinetix_get_hf( 'footer_menu_ai', 0 );
$menu_data = (int) trinetix_get_hf( 'footer_menu_data', 0 );
$menu_eng  = (int) trinetix_get_hf( 'footer_menu_engineering', 0 );
$menu_exp  = (int) trinetix_get_hf( 'footer_menu_experience', 0 );
$menu_co   = (int) trinetix_get_hf( 'footer_menu_company', 0 );
$menu_leg  = (int) trinetix_get_hf( 'footer_menu_legal', 0 );
?>
<?php if ( trinetix_show_site_footer() ) : ?>
<footer class="footer">
	<div class="container footer-top">
		<div class="footer-heading-row">
			<div class="footer-brand">
				<img src="<?php echo esc_url( $logo_src ); ?>" alt="<?php echo esc_attr( $company ); ?>">
				<?php if ( $show_tag && $tagline ) : ?>
					<p class="footer-tagline"><?php echo esc_html( $tagline ); ?></p>
				<?php endif; ?>
			</div>
			<?php if ( $show_intro && $intro ) : ?>
				<div class="footer-intro"><?php echo esc_html( $intro ); ?></div>
			<?php endif; ?>
		</div>

		<?php if ( $show_menus ) : ?>
		<div class="footer-directory">
			<?php
			trinetix_render_footer_row( 'footer_ai', __( 'Artificial Intelligence', 'trinetix' ), $menu_ai );
			trinetix_render_footer_row( 'footer_data', __( 'Data & Analytics', 'trinetix' ), $menu_data );
			trinetix_render_footer_row( 'footer_engineering', __( 'Digital Engineering', 'trinetix' ), $menu_eng );
			trinetix_render_footer_row( 'footer_experience', __( 'Experience', 'trinetix' ), $menu_exp );
			trinetix_render_footer_row( 'footer_company', __( 'Company', 'trinetix' ), $menu_co );

			if (
				$menu_ai <= 0 && $menu_data <= 0 && $menu_eng <= 0 && $menu_exp <= 0
				&& ! has_nav_menu( 'footer_ai' )
				&& ! has_nav_menu( 'footer_data' )
				&& ! has_nav_menu( 'footer_engineering' )
				&& ! has_nav_menu( 'footer_experience' )
			) {
				get_template_part( 'template-parts/global/footer-fallback' );
			}
			?>
		</div>
		<?php endif; ?>

		<div class="footer-bottom">
			<span><?php echo esc_html( $copyright ? $copyright : sprintf( '© %s %s. All rights reserved.', gmdate( 'Y' ), $company ) ); ?></span>
			<?php if ( $show_legal ) : ?>
			<span class="footer-legal">
				<?php
				if ( $menu_leg > 0 || has_nav_menu( 'footer_legal' ) ) {
					$legal_args = array(
						'container'   => false,
						'depth'       => 1,
						'fallback_cb' => false,
						'items_wrap'  => '%3$s',
						'walker'      => new Trinetix_Flat_Nav_Walker(),
					);
					if ( $menu_leg > 0 ) {
						$legal_args['menu'] = $menu_leg;
					} else {
						$legal_args['theme_location'] = 'footer_legal';
					}
					wp_nav_menu( $legal_args );
				} else {
					?>
					<?php if ( $privacy ) : ?>
						<a href="<?php echo esc_url( $privacy ); ?>"><?php echo esc_html( $privacy_l ); ?></a>
					<?php else : ?>
						<?php echo esc_html( $privacy_l ); ?>
					<?php endif; ?>
					&nbsp; • &nbsp;
					<?php if ( $terms ) : ?>
						<a href="<?php echo esc_url( $terms ); ?>"><?php echo esc_html( $terms_l ); ?></a>
					<?php else : ?>
						<?php echo esc_html( $terms_l ); ?>
					<?php endif; ?>
					<?php
				}
				?>
			</span>
			<?php endif; ?>
		</div>
	</div>
</footer>
<?php endif; ?>

<?php wp_footer(); ?>
</body>
</html>
