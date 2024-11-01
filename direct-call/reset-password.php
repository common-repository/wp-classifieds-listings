<?php
if ( ! defined( 'ABSPATH' ) )
	 exit;
	 
global $wpdb;
if(isset($_GET['xyz_wp_resetpassword'])){

	$login=explode('_', $_GET['xyz_wp_resetpassword']);
	$query="SELECT * FROM ".$wpdb->base_prefix."users WHERE  `ID`=%d";
	$res=$wpdb->get_row($wpdb->prepare($query, $login[0]));


	if($login[1]==md5($login[0].$res->user_email))
	{
		
			wp_redirect(add_query_arg(array('resetpassword'=>$_GET['xyz_wp_resetpassword']),get_permalink(get_option('xyz_wp_cls_forgotpassword'))));
		
	}
	else
		wp_redirect(site_url(),0);
	}
?>