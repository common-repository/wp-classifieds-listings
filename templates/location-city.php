<?php
if ( ! defined( 'ABSPATH' ) )
	 exit;
	 
xyz_cls_get_template_part('header');


global $wpdb;
$cat_path=get_permalink(get_option('xyz_wp_cls_home'));
?>

<div class="container">
    <div class="row">
    <div class="col-lg-12 col-sm-12 col-md-12 col-xs-12 xyz_premium" style="margin-top: 10px;    margin-bottom: 20px;">
        <h3 class="xyz_cls_main_head">
            <i class="fa fa-star xyz_cls_main_head_icon"></i>
            Select Your City
            <i class="fa fa-star xyz_cls_main_head_icon"></i>
        </h3>
    </div>
    <div class="clear">
    </div>
    <div class="wrapper xyz_cls_loginpage">
<?php
    if(count($result_city)==0) 
    	_e('No City  found..','wp-classifieds');

    foreach($result_city as $reg){
    	$cat_path= add_query_arg(array('city' => $reg->city,'cid'=>$reg->id),$cat_path);
?>
        <div class="xyz_cls_cntryListing col-lg-3 col-sm-4 col-md-4 col-xs-12">
            <a class="xyz_cls_cntryList" href="<?php echo $cat_path; ?>" title="<?php echo  $reg->city;?>">
                <i class="fa fa-map-marker" aria-hidden="true">
                </i> <?php echo  $reg->city;?></a>
        </div>
<?php
	}
?>
    </div>
</div>
</div>