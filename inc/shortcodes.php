<?php
/**
 * Reusable homepage section shortcodes.
 *
 * @package Trinetix
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Capture a template part into a string.
 *
 * @param string $slug Template slug under template-parts/.
 * @param string $name Optional name.
 * @return string
 */
function trinetix_get_template_part_html( string $slug, string $name = '' ): string {
	ob_start();
	get_template_part( $slug, $name );
	return (string) ob_get_clean();
}

/**
 * Register section shortcodes.
 */
function trinetix_register_shortcodes(): void {
	$map = array(
		'trinetix_hero'         => 'home/hero',
		'trinetix_intro'        => 'home/intro',
		'trinetix_approach'     => 'home/approach',
		'trinetix_services'     => 'home/services',
		'trinetix_industries'   => 'home/industries',
		'trinetix_work'         => 'home/case-studies',
		'trinetix_testimonials' => 'home/testimonials',
		'trinetix_partners'     => 'home/partners',
		'trinetix_knowledge'    => 'home/knowledge',
		'trinetix_contact'      => 'home/contact',
	);

	foreach ( $map as $tag => $partial ) {
		add_shortcode(
			$tag,
			static function () use ( $partial ): string {
				return trinetix_get_template_part_html( 'template-parts/' . $partial );
			}
		);
	}
}
add_action( 'init', 'trinetix_register_shortcodes' );

/**
 * Default Classic Editor guide + shortcodes for the Home page.
 *
 * @return string
 */
function trinetix_home_page_editor_guide(): string {
	$lines = array(
		'<p><strong>' . esc_html__( 'Homepage layout', 'trinetix' ) . '</strong></p>',
		'<p>' . esc_html__( 'Edit banner text and section titles in the numbered boxes below this editor. Logo: Appearance → Customize. Menus: Appearance → Menus.', 'trinetix' ) . '</p>',
		'<p>' . esc_html__( 'These shortcodes show dynamic lists (Industries, Our Work, etc.). You can copy them onto other pages. On the Home page the theme already displays these sections in order — leave the shortcodes here so you can see and reuse them.', 'trinetix' ) . '</p>',
		'<p>[trinetix_industries]</p>',
		'<p>[trinetix_work]</p>',
		'<p>[trinetix_services]</p>',
		'<p>[trinetix_testimonials]</p>',
		'<p>[trinetix_partners]</p>',
		'<p>[trinetix_knowledge]</p>',
	);
	return implode( "\n", $lines );
}
