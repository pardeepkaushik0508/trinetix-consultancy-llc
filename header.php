<?php
/**
 * Header template.
 *
 * @package Trinetix
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$cta_text     = (string) trinetix_get_hf( 'header_cta_text', trinetix_get_setting( 'header_cta_text', 'Contact Us' ) );
$cta_url      = (string) trinetix_get_hf( 'header_cta_url', trinetix_get_setting( 'header_cta_url', '#contact' ) );
$show_menu    = (int) trinetix_get_hf( 'header_show_menu', 1 ) === 1;
$show_search  = (int) trinetix_get_hf( 'header_show_search', 1 ) === 1;
$show_cta     = (int) trinetix_get_hf( 'header_show_cta', 1 ) === 1;
$header_menu  = (int) trinetix_get_hf( 'header_menu_id', 0 );
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

<?php if ( trinetix_show_site_header() ) : ?>
<header class="site-header" id="header">
	<div class="container navbar">
		<?php echo trinetix_get_logo_html(); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- escaped in helper. ?>

		<?php if ( $show_menu ) : ?>
		<nav class="nav" id="primary-menu" aria-label="<?php esc_attr_e( 'Primary', 'trinetix' ); ?>">
			<?php
			$menu_args = array(
				'container'   => false,
				'items_wrap'  => '%3$s',
				'depth'       => 1,
				'fallback_cb' => false,
				'walker'      => new Trinetix_Flat_Nav_Walker(),
			);
			if ( $header_menu > 0 ) {
				$menu_args['menu'] = $header_menu;
				wp_nav_menu( $menu_args );
			} elseif ( has_nav_menu( 'primary' ) ) {
				$menu_args['theme_location'] = 'primary';
				wp_nav_menu( $menu_args );
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
		<?php endif; ?>

		<div class="header-actions">
			<?php if ( $show_search ) : ?>
			<button type="button" class="search" id="trinetixSearchToggle" aria-label="<?php esc_attr_e( 'Open search', 'trinetix' ); ?>" aria-expanded="false" aria-controls="trinetixSearchOverlay">
				<svg viewBox="0 0 24 24" aria-hidden="true" fill="none"><circle cx="11" cy="11" r="6.5" fill="none"></circle><path d="M16.5 16.5L21 21" fill="none"></path></svg>
			</button>
			<?php endif; ?>
			<?php if ( $show_cta && $cta_text ) : ?>
				<a class="btn btn-outline" href="<?php echo esc_url( $cta_url ); ?>">
					<?php echo esc_html( $cta_text ); ?>
					<?php echo trinetix_arrow_icon( 'light' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
				</a>
			<?php endif; ?>
			<?php if ( $show_menu ) : ?>
			<button class="menu-toggle" id="menuToggle" type="button" aria-label="<?php esc_attr_e( 'Open navigation', 'trinetix' ); ?>" aria-expanded="false" aria-controls="primary-menu">
				<svg viewBox="0 0 24 24" aria-hidden="true"><path d="M4 7h16M4 12h16M4 17h16"></path></svg>
			</button>
			<?php endif; ?>
		</div>
	</div>
</header>

<?php if ( $show_search ) : ?>
<div class="trinetix-search-overlay" id="trinetixSearchOverlay" hidden>
	<div class="trinetix-search-overlay__panel" role="dialog" aria-modal="true" aria-label="<?php esc_attr_e( 'Search the site', 'trinetix' ); ?>">
		<button type="button" class="trinetix-search-overlay__close" id="trinetixSearchClose" aria-label="<?php esc_attr_e( 'Close search', 'trinetix' ); ?>">
			<span aria-hidden="true">&times;</span>
		</button>
		<form role="search" method="get" class="trinetix-search-overlay__form" action="<?php echo esc_url( home_url( '/' ) ); ?>">
			<label class="screen-reader-text" for="trinetix-overlay-search"><?php esc_html_e( 'Search for:', 'trinetix' ); ?></label>
			<input type="search" id="trinetix-overlay-search" class="trinetix-search-overlay__input" placeholder="<?php esc_attr_e( 'Search pages, articles…', 'trinetix' ); ?>" value="<?php echo esc_attr( get_search_query() ); ?>" name="s" autocomplete="off" />
			<button type="submit" class="btn btn-cyan trinetix-search-overlay__submit"><?php esc_html_e( 'Search', 'trinetix' ); ?></button>
		</form>
	</div>
</div>
<?php endif; ?>
<?php endif; ?>
