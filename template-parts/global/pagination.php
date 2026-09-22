<?php
/**
 * Pagination.
 *
 * @package Trinetix
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

the_posts_pagination(
	array(
		'mid_size'  => 2,
		'prev_text' => __( 'Previous', 'trinetix' ),
		'next_text' => __( 'Next', 'trinetix' ),
	)
);
