<?php
if ( ! defined( 'ABSPATH' ) )
     exit;
    
xyz_cls_get_template_part('header');

global $wpdb;
$path=get_permalink(get_option( 'xyz_wp_cls_city' ));
?> 

<div class="container">
    <div class="row">
    <div class="col-lg-12 col-sm-12 col-md-12 col-xs-12 xyz_premium" style="margin-top: 10px;    margin-bottom: 20px;">   
        <h3 class="xyz_cls_main_head">
            <i class="fa fa-star xyz_cls_main_head_icon"></i>
            Select Your State
            <i class="fa fa-star xyz_cls_main_head_icon"></i> 
        </h3>  
    </div>
    <div class="clear">
    </div>
    <div class="wrapper xyz_cls_loginpage">
        <div class="clear"></div>
        <?php
        
       foreach ($result_reg as $reg){ 
           $path= add_query_arg('rcode',$reg->scode,$path);
        ?>
        <div class="xyz_cls_cntryListing col-lg-3 col-sm-4 col-md-4 col-xs-12">
            <a class="xyz_cls_cntryList" href="<?php echo $path;?>" title="<?php echo $reg->sname;?>">
                <i class="fa fa-map-marker" aria-hidden="true">
                </i> <?php echo $reg->sname;?></a>
        </div>
        <?php
        }
        ?>
    </div>
</div>
</div>