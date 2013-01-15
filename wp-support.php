<?php
/*
Plugin Name: WP Support
Plugin URI: http://www.stomptheweb.co.uk
Description: Support Area for WordPress
Version: 0.1
Author: Steven Jones
Author URI: http://www.stomptheweb.co.uk
Contributors: stompweb
*/

// Plugin version
if( !defined( 'WPS_VERSION' ) )
	define( 'WPS_VERSION', '0.1' );

// Plugin Folder URL
if( !defined( 'WPS_PLUGIN_URL' ) )
	define( 'WPS_PLUGIN_URL', plugin_dir_url( __FILE__ ) );

// Plugin Folder Path
if( !defined( 'WPS_PLUGIN_DIR' ) )
	define( 'WPS_PLUGIN_DIR', plugin_dir_path( __FILE__ ) );

// Plugin Root File
if( !defined( 'WPS_PLUGIN_FILE' ) )
	define( 'WPS_PLUGIN_FILE', __FILE__ );

// Only load scripts in admin - is there a better way to do this?
if( is_admin() ) {
	require_once WPS_PLUGIN_DIR . 'includes/post-type.php';
	require_once WPS_PLUGIN_DIR . 'includes/edit-ticket.php';
	require_once WPS_PLUGIN_DIR . 'includes/widgets.php';
	require_once WPS_PLUGIN_DIR . 'includes/ajax.php';	
}
		
function wps_load_scripts($hook) {

	global $edit_ticket_page;

	if ($hook == 'index.php') {
		wp_enqueue_script('peity', 'https://raw.github.com/rendro/easy-pie-chart/master/jquery.easy-pie-chart.js', array('jquery'));
	}
	
	if ($hook == $edit_ticket_page) {
		wp_enqueue_script('wps-ajax', WPS_PLUGIN_URL . 'assets/js/wps.js', array('jquery'));
		wp_localize_script('wps-ajax', 'wps_vars', array(
			'wps_nonce' => wp_create_nonce('wps-nonce')
			)
		);
	}			

	// Load this everywhere for now as used in a few places
	wp_enqueue_style('wps-style', WPS_PLUGIN_URL . 'assets/css/wps.css');
		
}
add_action('admin_enqueue_scripts', 'wps_load_scripts');
