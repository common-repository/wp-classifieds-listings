<?php

if ( ! defined( 'ABSPATH' ) )
     exit;
     
$img=get_option('xyz_cls_default_item_image');

$imgpath=content_url() . '/uploads/xyz-cls-uploads/'.$img;
?>

<div class="xyz_cls_rightBox" id="col5">

<div class="xyz_ttl"><?php _e('Recent Ads','wp-classifieds-listings');?></div>

<div class="clear"></div>



<?php 

foreach ($recent as $pi){
if($pi->featured==1 &&strtotime( date("Y-m-d"))>$pi->featured_expiry)
$pr="xyz_premiumNoti";
else $pr="";
?>
<div class="<?php echo $pr;?>"></div>
<a class="col-lg-12 col-sm-12 col-md-12 col-xs-12 xyz_cls_rcent_add" href="<?php echo get_permalink($pi->ID);?>">



<div class="col-lg-12 col-sm-12 col-md-12 col-xs-12" style="color: green;width: 100%;height: 100%;padding: 10px 0px;border: 1px solid #eee;background-color: #fff;margin-top: 10px;">
    
    <?php if ( has_post_thumbnail($pi->ID) ) { echo get_the_post_thumbnail( $pi->ID, array(50,50));}else echo   $thumbnail='<img src="'.$imgpath.'" height="50" width="50" style="margin-right:5px;"/>';?><span class=""><?php echo substr($pi->post_title,0,5);?></span>
<span style="font-size: 10px;float: right;padding-top:15px;">More <i class="fa fa-long-arrow-right" aria-hidden="true"></i></span></a>
</div>
<?php  } ?>
</div>
