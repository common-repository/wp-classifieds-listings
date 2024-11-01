<?php
if ( ! defined( 'ABSPATH' ) )
    exit; 

global $wpdb;

//$timenew=gmmktime(0,0,0,gmdate("m",time()),gmdate("d",time())-5,gmdate("Y",time()));
$error1=$error2="";

$error1=$wpdb->query($wpdb->prepare("UPDATE ".$wpdb->prefix."xyz_cls_listing_details SET featured=%d,featured_expiry=%d WHERE featured=%d AND featured_expiry <".time()." AND featured_expiry !=%d",0,0,1,0));

// $posts=	get_posts( array(
//     			'numberposts' => -1, // we want to retrieve all of the posts
//     			'post_type' => 'classifieds_listing'
// 				));
// $postids=0;
// foreach ($posts as $p){
//     $postids=$postids.','.$p->ID;
// }

// $wpdb->query("DELETE  FROM ".$wpdb->prefix."xyz_cls_listing_details  WHERE `pid` NOT IN (".$postids.")");

if($error1==""){
    update_option('xyz_cls_cron_running_time',time());
    echo "<strong>Cron running successfully</strong>";
}

die;
?>