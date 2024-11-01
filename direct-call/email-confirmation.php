<?php
if ( ! defined( 'ABSPATH' ) )
     exit;
 
global $wpdb;
if(isset($_GET['xyz_wp_login']))
{
    $login=explode('_', $_GET['xyz_wp_login']);
    $res=get_userdata( $login[0]);
    if($login[1]==md5($login[0].$res->user_email))
    {
        update_usermeta($login[0], 'xyz_wp_user_status', 1);
        wp_redirect(admin_url());
    }
    else{
        update_usermeta($login[0], 'xyz_wp_user_status',-1);
        wp_redirect(site_url(),0);
    }
}
?>