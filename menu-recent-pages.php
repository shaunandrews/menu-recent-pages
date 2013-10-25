<?php
/*
Plugin Name: Menu Recent Pages
Plugin URI: http://shaunandrews.com/wordpress/menu-recent-pages/
Description: Adds a list of recently edited pages to your wp-admin navigation.
Version: 0.1
Author: Shaun Andrews
Author URI: http://automattic.com
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html

Kudos: Based on code from Ehsanul Haque's (http://ehsanIs.me/) plugin, Recently Updated Pages (http://wordpress.org/plugins/recently-updated-pages/).

*/

// Enqueue some new styles
add_action( 'admin_print_styles', 'mrp_add_wp_admin_style' );
function mrp_add_wp_admin_style() {
	wp_enqueue_style( 'menu-recent-pages', plugins_url( "style.css", __FILE__ ), array(), '20111209' );
}

add_action( 'admin_footer', 'mrp_add_recent_pages' );
function mrp_add_recent_pages() {
    GLOBAL $wpdb;

	$post_types = get_post_types( array(
		'show_ui' => true
	), 'objects' );

	$no_of_pages = 5;

	foreach ( $post_types as $post_type => $pto ) {
		if ( ! post_type_supports( $post_type, 'recent_menu' ) )
			continue;

		if ( 'page' == $post_type )
			$slug = 'pages';
		else if ( 'post' == $post_type )
			$slug = 'posts';
		else
			$slug = sprintf( 'posts-%s', $post_type );

	    $sql = $wpdb->prepare( "
			SELECT ID, post_title, post_modified
			FROM {$wpdb->posts}
			WHERE post_status = 'publish'
			AND post_type = %s
			ORDER BY post_modified DESC
			LIMIT %d
		", $post_type, $no_of_pages );

	    $page_list = (array) $wpdb->get_results($sql);

	    if ( empty( $page_list ) )
	    	continue;

	    $append = '<li class="mrp-recent-pages-header">Recently Updated</li>';

		foreach ($page_list as $page) {
		    $time_since = strtotime( $page->post_modified );
	    	$time_since = human_time_diff( $time_since, time() );
			$append .= '<li class="mrp-recent-page"><a title="Updated about ' . $time_since . ' ago" href="' . admin_url( "post.php?post={$page->ID}&action=edit" ) . '">' . esc_html( $page->post_title ) . '</a></li>';
		}

	    ?>
			<script type="text/javascript">
				(function($) {
					$( '#menu-<?php echo esc_attr( $slug ); ?> .wp-submenu' ).append( '<?php echo $append; ?>' );
				})(jQuery);
			</script>
		<?php

	}
}

function mrp_add_support() {
	add_post_type_support( 'page', 'recent_menu' );
	add_post_type_support( 'post', 'recent_menu' );
}

add_action( 'init', 'mrp_add_support' );
