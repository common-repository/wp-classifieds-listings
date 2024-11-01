<?php

if ( ! defined( 'ABSPATH' ) )
     exit;

include_once( ABSPATH . 'wp-admin/includes/plugin.php' );
$postid=$post->ID;
$city=$state=$type=$msg="";
$author_id = $post->post_author;
$posttitle = $post->post_title;
$postcon   = do_shortcode($post->post_content);
$msg="";

global $wpdb;
$result=$wpdb->get_row($wpdb->prepare("SELECT * FROM ".$wpdb->prefix."xyz_cls_listing_details WHERE pid=%d",$postid));

$thumbnails=get_post_meta($postid,'xyz_cls_listing_image_gallery',true);
?>

<div id="primary" class="content-area">
    <div id="content">
        <div id="contentHolder" class="wrapper entry-content">
            <div  style="">
                <?php xyz_cls_get_template_part('header');?>
            </div>
            <div class="clear">
            </div>
            <div class="container xyz_cls_detailedView">
                <div class="col-lg-12" style="margin-bottom: 20px;border: 1px solid #eee;margin-top: 40px;">
                    <div class="xyz_cls_listingTabs">
                        <div class="xyz_cls_leftSide1 col-lg-8 col-sm-6 col-md-8 col-xs-12">
                        <?php
                            $c= get_option('xyz_cls_default_country');
                            $country=$wpdb->get_row($wpdb->prepare("SELECT * FROM ".$wpdb->prefix."xyz_cls_countries WHERE ccode=%s",$c));

                            if($result){
                                $state=$wpdb->get_row($wpdb->prepare("SELECT * FROM ".$wpdb->prefix."xyz_cls_states WHERE scode=%s AND ccode=%s",$result->state_id,$c));
                                $city=$wpdb->get_row($wpdb->prepare("SELECT * FROM ".$wpdb->prefix."xyz_cls_cities WHERE id=%d",$result->city_id));
                                if($result &&  $result->item_type==1 ) 
                                    $type="Offered";else $type="Wanted";
                            }

                            $old_date_timestamp = strtotime( $post->post_date);
                            $new_date =xyz_cls_local_date_time('d M y h:i:s A ', $old_date_timestamp);
                            
                            if($result && $result->featured==1)
                                $premium='premiumBig';
                            else 
                                $premium="";
                        ?>           
                            <h1><?php echo $posttitle;?></h1>
                            <span>
                        <?php  
                            if($result && $country) 
                                echo $country->cname.',';
 
                            if($result && $state->sname) 
                                echo $state->sname.','; 
 
                            if($result && $city)  
                                echo $city->city.',';

                            if($result)  
                                echo $result->locality;
                        ?>        
                            </span>
                        </div>
                    </div>
                    <?php 
                        if($city) 
                            if($city!='') 
                                $all_cat=add_query_arg(array('city'=>$city->city,'cid'=>$city->id),get_permalink(get_option('xyz_wp_cls_items')));
                    ?>
                    <div class="xyz_cls_left">
                        <b>
                            <a href="<?php echo  $all_cat;?>" ><?php _e('All categories','wp-classifieds'); ?> </a> » <?php   if($result->category) echo  substr(xyz_cls_get_taxonomy_parents($result->category, 'xyz_cls_category','', ' &raquo; '),0,-8);
                    ?>
                        </b>
                    </div>
                    <div class="clear"></div>
                    <div class="xyz_cls_otherDetails">
                    
                       <span class="xyz_cls_date_deatail"><i class="fa fa-calendar" aria-hidden="true">
                            </i>  <?php  echo $new_date;?></span>
                        <span style="float:right;"><?php _e('Item Type','wp-classifieds');?>    :<?php  echo  $type;?>
                        </span>
                    </div>
                </div>
                <div class="clear"></div>
                <div class="row">
                    <div class="xyz_cls_rtSide col-lg-4 col-md-5 col-sm-6 col-xs-12">
                        <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12" style="border:1px solid #eee; padding-top:15px;">
                            <div class="xyz_cls_rightBox">
                                <h2><?php _e('Item Image','wp-classifieds');?></h2>
                                <div class="xyz_cls_image_preview">
                                    <div class="xyz_cls_image_preview_inner" >
                                        <div class="<?php echo $premium;?>">
                                        <?php
                                            if($premium!="")
                                                echo "PREMIUM";
                                        ?>                                                
                                        </div>
                    <?php 
                        remove_filter( 'post_thumbnail_html', 'xyz_cls_filter_post_thumbnail_html', 10, 5 ); 
                        if(has_post_thumbnail($postid)){
                           echo get_the_post_thumbnail( $postid, array( 300, 300) ); 
                        }
                        else{
                            $img=get_option('xyz_cls_default_item_image');
                            $imgpath=content_url() . '/uploads/xyz-cls-uploads/'.$img;
                            echo $thumbnail='<img src="'.$imgpath.'" height="300" width="300"/>';
                        }
                    ?>                                        
                                    </div>
                                </div>
                            </div>
                    <?php
                        $contactmsg="";
                        $email="";
                        if(isset($_POST['contact'])){
                            if(isset($_POST['contact_message'])){   
                                $contactmsg=$_POST['contact_message'];
                            }
                            else 
                                $contactmsg="";
                            if(isset($_POST['email'])){
                                $email=$_POST['email'];
                            }
                            else 
                                $email="";
                            if($contactmsg==""||$email=="")
                                $msg="Please fill up mandatory fields";
                            else if(!filter_var($_POST['email'],FILTER_VALIDATE_EMAIL))
                                $msg="Invalid email";
                            else {
                                $msg="";
                            }
                        }
                    ?>
                            <div class="xyz_cls_rightBox xyz_cls_contactBox" id="contContainer">
                                <h2>
                                    <?php _e('Quick Contact','wp-classifieds');?></h2>
                                <?php echo  '<span style="color:red"><b> '.$msg.'</b></span>';
                                    if($result->phone==0)
                                        $phone="-NA-";
                                    else 
                                        $phone=$result->phone;
                                ?>
                                <div id="contact_msg" style="font-size: 14px;font-weight: bold;color: red;">
                                </div>
                                <div class="form-group">
                                    <?php _e('URL','wp-classifieds');echo '&nbsp:&nbsp';?><a target="_blank" href="<?php echo $result->url;?>" style="text-decoration: underline;">Visit Url</a>
                                </div>
                                <div class="form-group">
                                    <?php _e('Contact Number','wp-classifieds');echo '&nbsp:&nbsp';?><?php echo $phone;?>
                                </div>
                                <div class="form-group">
                                    <label><?php _e('Leave a message','wp-classifieds');?><span class="mandatory">*</span></label>
                                    <textarea class="xyz_cls_text_box form-control" rows="2" cols="33" id="contact_message" name="contact_message"><?php echo $contactmsg;?></textarea>
                                </div>
                                <div class="form-group">
                                    <label><?php _e('Your Email','wp-classifieds');?><span class="mandatory">*</span></label>
                                    <input class="xyz_cls_text_box xyz_cls_textBox form-control" type="text" name="email" id="email" value="<?php echo $email;?>" size="22">
                                </div>
                                <div class="form-group">
                                    <input type="button"  id="submit_button" class="button commonButton" value="Contact Now"  name="contact" style="border-radius: 2px; padding:10px 15px;">
                                </div>
                                <div id="d">
                                </div>
                            </div>
                        </div>  
                    </div>
                    <div class="xyz_cls_leftBox col-lg-8 col-md-7 col-sm-6 col-xs-12">
                        <div class="xyz_cls_listingsRow" style="padding: 15px 10px;">
                            <h1 style="margin: 0px;">
                                <?php _e('Description','wp-classifieds');?> 
                                <b>
                                </b>
                            </h1>
                            <span style="display: block;"><?php echo $postcon."&nbsp";?>;</span>
                        </div>
                    <?php 
                        if(is_plugin_active('custom-field-manager/custom-field-manager.php')){
                            //&& get_option('xyz_cfl_shortcode_field')==1)
                            //Custom fields are added for classifieds listings irrespective of the value of 'xyz_cfl_shortcode_field'
                    ?>
                        <div class="xyz_cls_listingsRow">
                            <h1 style="margin: 0px;">
                                <?php _e('Additional Details','wp-classifieds');?><b>
                                </b>
                            </h1>
                            <span style="display: block;"><?php echo do_shortcode('[xyz_cfl_shortcode id='.$postid.']');?>
                            </span>
                        </div>
                    <?php
                        }
                        if($thumbnails){
                    ?>
                            <div class="xyz_cls_listingsRow" style="padding: 15px 10px;">
                                <h1 style="margin: 0px;"><?php _e('Gallery','wp-classifieds');?><b></b></h1>
                                <span style="display: block;">
                                <?php
                                    if(get_option('xyz_cls_gallery')==2){
                                        if( get_option('xyz_gal_wp_gallery_override')==1 ){
                                           echo do_shortcode('[gallery ids="'.$thumbnails.'"  ]');
                                        }
                                        do_shortcode('[xyz_wp_gallery ids="'.$thumbnails.'"  xyz_cls_listing_id="'.$postid.'"]');
                                    }
                                    else
                                        echo do_shortcode('[gallery ids="'.$thumbnails.'"  ]');
                                ?>    
                                </span>
                            </div>
                    <?php        
                        }
                    ?>
                        
<script type="text/javascript">
    if(typeof isValidEmail == 'undefined')
            {
                function isValidEmail(emailText)
                {
                    var pattern = new RegExp(/^((([a-z]|d|[!#$%&'*+-/=?^_`{|}~]|[u00A0-uD7FFuF900-uFDCFuFDF0-uFFEF])+(.([a-z]|d|[!#$%&'*+-/=?^_`{|}~]|[u00A0-uD7FFuF900-uFDCFuFDF0-uFFEF])+)*)|((x22)((((x20|x09)*(x0dx0a))?(x20|x09)+)?(([x01-x08x0bx0cx0e-x1fx7f]|x21|[x23-x5b]|[x5d-x7e]|[u00A0-uD7FFuF900-uFDCFuFDF0-uFFEF])|(\([x01-x09x0bx0cx0d-x7f]|[u00A0-uD7FFuF900-uFDCFuFDF0-uFFEF]))))*(((x20|x09)*(x0dx0a))?(x20|x09)+)?(x22)))@((([a-z]|d|[u00A0-uD7FFuF900-uFDCFuFDF0-uFFEF])|(([a-z]|d|[u00A0-uD7FFuF900-uFDCFuFDF0-uFFEF])([a-z]|d|-|.|_|~|[u00A0-uD7FFuF900-uFDCFuFDF0-uFFEF])*([a-z]|d|[u00A0-uD7FFuF900-uFDCFuFDF0-uFFEF]))).)+(([a-z]|[u00A0-uD7FFuF900-uFDCFuFDF0-uFFEF])|(([a-z]|[u00A0-uD7FFuF900-uFDCFuFDF0-uFFEF])([a-z]|d|-|.|_|~|[u00A0-uD7FFuF900-uFDCFuFDF0-uFFEF])*([a-z]|[u00A0-uD7FFuF900-uFDCFuFDF0-uFFEF]))).?$/i);
                    return pattern.test(emailText);
                }
            }

jQuery(document).ready(function(){

    jQuery("#submit_button").click(function(){
        if(jQuery("#contact_message").val()==""){
            alert("<?php _e('Please fill up mandatory fields','wp-classifieds');?>");
            jQuery("#contact_message").focus();
            return false;
        }
        else if(!isValidEmail(jQuery("#email").val())){
            alert("<?php _e('Invalid Email','wp-classifieds');?>");
            jQuery("#email").focus();
            return false;
        }
        else{
            <?php $ajax_cls_nonce = wp_create_nonce( "xyz-cls-contact" );?>
                var dataString = {
                        msg: jQuery("#contact_message").val(),
                        email:jQuery("#email").val(),
                        security:'<?php echo $ajax_cls_nonce;?>',
                        id:<?php echo $result->pid;?>,
                        action: 'quick_contact'
                    };
                jQuery.post(xyz_cls_ajax_object.ajax_url, dataString, function(response){
                    if(response!=0)
                        jQuery("#d").html("<span style='color:green'>Your message has been sent successfully.</span>");
                    else jQuery("#d").html("<span style='color:red'>Error while sending message. Please try again....!</span>");
                });
        }
    });
});

</script>

<script type="text/javascript">
    jQuery(document).ready(function(){
        jQuery(".xyz_cls_detailedView .xyz_cls_listingsRow  h1").click(function(){
            jQuery(this).toggleClass("hided");
            jQuery(this).siblings("span").slideToggle(600);
        });
    });
</script>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>