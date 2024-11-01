<?php

if ( ! defined( 'ABSPATH' ) )
	 exit;
	 

if(!function_exists('xyz_cls_query_vars')){	 
function xyz_cls_query_vars($vars) {
	$vars[] = 'xyz_wp_cls';
	$vars[]='xyz_wp_login';
	$vars[]='xyz_wp_resetpassword';
	$vars[]='xyz_wp_activate_cfl';
	return $vars;
}
}
add_filter('query_vars', 'xyz_cls_query_vars');

if(!function_exists('xyz_cls_parse_request')){	 
function xyz_cls_parse_request($wp) {
	/*paypal notify*/
	if (array_key_exists('xyz_wp_cls', $wp->query_vars) && $wp->query_vars['xyz_wp_cls'] == 'paypalnotify') {
		require( dirname( __FILE__ ) . '/direct-call/paypal-ipn.php' );
		die;
	}
	/*cron*/
	if (array_key_exists('xyz_wp_cls', $wp->query_vars) && $wp->query_vars['xyz_wp_cls'] == 'cron') {
		require( dirname( __FILE__ ) . '/direct-call/cron.php' );
		die;
	}
	/*email-confirmation*/
	if (array_key_exists('xyz_wp_cls', $wp->query_vars) && $wp->query_vars['xyz_wp_cls'] == 'verify' && array_key_exists('xyz_wp_login', $wp->query_vars) ) {
		require( dirname( __FILE__ ) . '/direct-call/email-confirmation.php' );
		die;
	}
	/*reset password*/
	if (array_key_exists('xyz_wp_cls', $wp->query_vars) && $wp->query_vars['xyz_wp_cls'] == 'reset'&& array_key_exists('xyz_wp_resetpassword', $wp->query_vars)) {
		require( dirname( __FILE__ ) . '/direct-call/reset-password.php' );
		die;
	}
}
}
add_action('parse_request', 'xyz_cls_parse_request');
