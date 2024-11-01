<?php
/**
*Plugin Name: WP Classifieds Listings
*Plugin URI: http://xyzscripts.com/wordpress-plugins/
*Description: This plugin allows you to convert your worpdress installation into a fully fledged classifieds site. 
*Version: 1.0
*Author: xyzscripts.com
*Author URI: http://xyzscripts.com/
*
*Text Domain:wp-classifieds-listings
*/

if ( ! defined( 'ABSPATH' ) )
    exit;

if ( !function_exists( 'add_action' ) ) {
    echo "Hi there!  I'm just a plugin, not much I can do when called directly.";
    exit;
}

//ob_start();
// error_reporting(0);
// error_reporting(E_ALL );

define('XYZ_CLASSIFIEDS',__FILE__);
define('XYZ_CLASSIFIEDS_DIR',dirname( plugin_basename( __FILE__ ) ));
if(!defined('XYZ_CLS_COOKIE_CITY'))
    define('XYZ_CLS_COOKIE_CITY','xyz_cls_my_city');
if(!defined('XYZ_CLS_COOKIE_CNAME'))
    define('XYZ_CLS_COOKIE_CNAME','xyz_cls_city_name');
     
//define('COOKIEPATH', preg_replace('|https?://[^/]+|i', '', get_settings('home') . '/' ) );

global $wpdb;
//$wpdb->query('SET SQL_MODE=""');

require( dirname( __FILE__ ) . '/xyz-functions.php' );
require( dirname( __FILE__ ) . '/admin/install.php' );
require( dirname( __FILE__ ) . '/admin/menu.php' );
require( dirname( __FILE__ ) . '/admin/metabox.php' );
require( dirname( __FILE__ ) . '/ajax-handler.php' );
require( dirname( __FILE__ ) . '/admin/uninstall.php' );
require( dirname( __FILE__ ) . '/shortcode-handler.php' );
require( dirname( __FILE__ ) . '/direct-call.php' );
require( dirname( __FILE__ ) . '/template-handler.php' );
require( dirname( __FILE__ ) . '/admin/admin-notices.php' );

if(!function_exists('wp_classifieds_listngs')){
    function wp_classifieds_listngs(){
        load_plugin_textdomain( 'wp-classifieds-listings', false, dirname( plugin_basename( __FILE__ ) ) . '/languages/' );
    }
}
add_action('init', 'wp_classifieds_listngs');

if(get_option('xyz_credit_link')=="cls"){
    add_action('wp_footer', 'xyz_cls_credit');
}

if(!function_exists('xyz_cls_credit')){
    function xyz_cls_credit(){
        $content = '<div style="clear:both;width:100%;text-align:center; font-size:11px; "><a target="_blank" title="WP Classifieds Listings" href="#" >WP Classifieds Listings</a> Powered By : <a target="_blank" title="PHP Scripts & Wordpress Plugins" href="http://www.xyzscripts.com" >XYZScripts.com</a></div>';
        echo $content;
    }
}
