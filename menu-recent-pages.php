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

	$no_of_pages = 5;
	$include_posts = false;

    if ($include_posts == 1) {
        $post_types      = "post_type IN ('page', 'post')";
    } else {
        $post_types      = "post_type = 'page'";
    }
    $sql = "SELECT ID, post_title, post_modified FROM
	       {$wpdb->posts} WHERE
	       post_status = 'publish' AND
	       {$post_types}
	       ORDER BY post_modified DESC
	       LIMIT {$no_of_pages}";

    $page_list = (array) $wpdb->get_results($sql);

    if (!empty($list)) {
        foreach ($list as $key => $val) {
            $val->uri = get_permalink($val->ID);
        }
    } ?>
		<script type="text/javascript">
			(function($) {
				$( '#menu-pages .wp-submenu' ).append('<li class="mrp-recent-pages-header">Recently Updated</li>');
			})(jQuery);
		</script>
	<?php

	foreach ($page_list as $page) {
	    $time_since = strtotime( $page->post_modified );
    	$time_since = human_time_diff( $time_since, time() ); ?>
		<script type="text/javascript">
			(function($) {
				$( '#menu-pages .wp-submenu' ).append('<li class="mrp-recent-page"><a title="Updated about <?php echo $time_since; ?> ago" href="<?php echo admin_url( "post.php?post={$page->ID}&action=edit" ); ?>"><?php echo esc_html( $page->post_title ); ?></a></li>');
			})(jQuery);
		</script>
	<?php
	}
}