<?php
/**
 * Header template.
 *
 * @package Trinetix
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$cta_text   = (string) trinetix_get_setting( 'header_cta_text', 'Contact Us' );
$cta_url    = (string) trinetix_get_setting( 'header_cta_url', '#contact' );
$search_url = home_url( '/?s=' );
?><!DOCTYPE html>
<html <?php language_attributes(); ?>>
<head>
	<meta charset="<?php bloginfo( 'charset' ); ?>">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<?php wp_head(); ?>
</head>
<body <?php body_class(); ?>>
<?php wp_body_open(); ?>
<a class="skip-link" href="#top"><?php esc_html_e( 'Skip to content', 'trinetix' ); ?></a>
<div id="progress"></div>

<header class="site-header" id="header">
	<div class="container navbar">
		<?php echo trinetix_get_logo_html(); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- escaped in helper. ?>

		<nav class="nav" id="primary-menu" aria-label="<?php esc_attr_e( 'Primary', 'trinetix' ); ?>">
			<?php
			if ( has_nav_menu( 'primary' ) ) {
				wp_nav_menu(
					array(
						'theme_location' => 'primary',
						'container'      => false,
						'items_wrap'     => '%3$s',
						'depth'          => 1,
						'fallback_cb'    => false,
						'walker'         => new Trinetix_Flat_Nav_Walker(),
					)
				);
			} else {
				$home     = trailingslashit( home_url( '/' ) );
				$fallback = array(
					$home . '#approach'   => __( 'Approach', 'trinetix' ),
					$home . '#services'   => __( 'Services', 'trinetix' ),
					$home . '#industries' => __( 'Industries', 'trinetix' ),
					$home . '#work'       => __( 'Our Work', 'trinetix' ),
					$home . '#insights'   => __( 'Knowledge Hub', 'trinetix' ),
					$home . '#contact'    => __( 'Contact', 'trinetix' ),
				);
				foreach ( $fallback as $url => $label ) {
					printf( '<a href="%s">%s</a>', esc_url( $url ), esc_html( $label ) );
				}
			}
			?>
		</nav>

		<div class="header-actions">
			<div class="search" role="button" tabindex="0" aria-label="<?php esc_attr_e( 'Search', 'trinetix' ); ?>" data-search-url="<?php echo esc_url( $search_url ); ?>">
				<svg viewBox="0 0 24 24" aria-hidden="true" fill="none"><circle cx="11" cy="11" r="6.5" fill="none"></circle><path d="M16.5 16.5L21 21" fill="none"></path></svg>
			</div>
			<?php if ( $cta_text ) : ?>
				<a class="btn btn-outline" href="<?php echo esc_url( $cta_url ); ?>">
					<?php echo esc_html( $cta_text ); ?>
					<?php echo trinetix_arrow_icon( 'light' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
				</a>
			<?php endif; ?>
			<button class="menu-toggle" id="menuToggle" type="button" aria-label="<?php esc_attr_e( 'Open navigation', 'trinetix' ); ?>" aria-expanded="false" aria-controls="primary-menu">
				<svg viewBox="0 0 24 24" aria-hidden="true"><path d="M4 7h16M4 12h16M4 17h16"></path></svg>
			</button>
		</div>
	</div>
</header>
