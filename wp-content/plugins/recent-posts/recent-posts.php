<?php
/*
Plugin Name: Recent Posts
Plugin URI: http://wordpress.org/extend/plugins/recent-posts/
Description: Returns a list of the most recent posts.
Version: 1.2
Author: Nick Momrik
Author URI: http://nickmomrik.com/
*/

function mdv_recent_posts( $no_posts = 5, $before = '<li>', $after = '</li>', $hide_pass_post = true, $skip_posts = 0, $show_excerpts = false, $include_pages = false ) {
	global $wpdb;

	$time_difference = get_settings( 'gmt_offset' );
	$now = gmdate( "Y-m-d H:i:s", time() );
	$request = "SELECT ID, post_title, post_excerpt FROM $wpdb->posts WHERE post_status = 'publish' ";
	if ( $hide_pass_post )
		$request .= "AND post_password ='' ";
	if ( $include_pages )
		$request .= "AND (post_type='post' OR post_type='page') ";
	else
		$request .= "AND post_type='post' ";
	$request .= "AND post_date_gmt < '$now' ORDER BY post_date DESC LIMIT $skip_posts, $no_posts";
	$posts = $wpdb->get_results( $request );
	$output = '';

	if ( $posts ) {
		foreach ( $posts as $post ) {
			$post_title = $post->post_title;
			$permalink = get_permalink( $post->ID );
			$output .= $before . '<a href="' . esc_url( $permalink ) . '" rel="bookmark" title="Permanent Link: ' . esc_attr( $post_title ) . '">' . esc_html( $post_title ) . '</a>';
			if ( $show_excerpts ) {
				$post_excerpt = esc_html( $post->post_excerpt );
				$output.= '<br />' . $post_excerpt;
			}
			$output .= $after;
		}
	} else {
		$output .= $before . "None found" . $after;
	}
    echo $output;
}
?>
