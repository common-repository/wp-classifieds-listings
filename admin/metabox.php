<?php

if ( ! defined( 'ABSPATH' ) )
	 exit;
	 
//*****************************Add Meta boxes********************************

$_POST= stripslashes_deep($_POST);
add_action( 'add_meta_boxes', 'xyz_cls_add_custom_box');

if (!function_exists( 'xyz_cls_add_custom_box' ) ){
    function xyz_cls_add_custom_box(){
        add_meta_box( "xyz_cls_add_location",__( 'Location / Contact Details','wp-classifieds-listings'), 'xyz_cls_location_details','classifieds_listing','normal') ;
        add_meta_box( "xyz_cls_add_desc", __( 'Add Description','wp-classifieds-listings'), 'xyz_cls_desc','classifieds_listing','normal','high');
        add_meta_box( 'xyz-cls-product-images', __( 'Listing Gallery','wp-classifieds-listings' ), 'xyz_cls_product_images_box', 'classifieds_listing', 'side' );
        add_meta_box('xyz_cls_cat_metabox',__('Categories','wp-classifieds-listings'),'xyz_cls_categorybox','classifieds_listing', 'side');
        if(current_user_can( 'administrator' ))
            add_meta_box( "xyz_cls_featured1",__( 'Premium Listing Options','wp-classifieds-listings'), 'xyz_cls_featured_enable','classifieds_listing','normal') ;
    }
}

//*****************************premium options**************************************

if( !function_exists( 'xyz_cls_featured_enable' )){
	function xyz_cls_featured_enable(){
		$xyz_cls_checked = $xyz_cls_rem_checked = $cur = $exp_date= "";
        global $wpdb;
        global $post;
        $postid = $post->ID;
        $list= $wpdb->get_row($wpdb->prepare("SELECT * FROM ".$wpdb->prefix."xyz_cls_listing_details WHERE pid =%d",$postid));

        if(count($list)==0)
            $numlist = 0;
        else
            $numlist = count($list);
?>
		<table class="xyz_item_status" style="width:100%;">
			<tr>
				<td style="width:50%;"><?php _e('Current Item Status','wp-classifieds-listings');?></td>
				<td><?php if($numlist!=0){
			        	if($list->featured==0)
			                echo "General";
			            else
			                echo "Premium";
        			     }	
        			     else{
            			     echo "General";
        			     }
        		?></td>
			</tr>
   	<?php 
        if($numlist!=0){
            if($list->featured==1){
    ?>
			<tr>
				<td><?php _e(' Premium Expiry: ','wp-classifieds-listings');?></td>
				<td>
	<?php
				if($list->featured_expiry!=0){
                    $exp_date=xyz_cls_local_date_time('d-m-Y',$list->featured_expiry);
                    printf('%s',$exp_date);
                }
                else{
                    _e(' -N/A- ','wp-classifieds-listings');
                }
	?>
				</td>
			</tr>
	<?php
			}
		}
	?>
                        <tr>
    <?php
        if($numlist!=0){
            if($list->featured_no_of_days>0){
    ?>
                <td style="width:50%"><?php _e(' Pending Premium Extension Period','wp-classifieds-listings');?></td>
                <td><?php printf('%d',$list->featured_no_of_days);?></td>
    <?php 
            }
            else{
    ?>          <td style="width:50%"><?php _e(' Pending Premium Extension Period','wp-classifieds-listings');?></td>
                <td><?php _e(' None! ','wp-classifieds-listings');?></td>
    <?php
            }
        }
    ?>
            </tr>
		</table>

		<h4>
		    <span class="xyz_cls_meta_style"><?php _e('Update Premium Expiry','wp-classifieds-listings');?></span>
		</h4>
		<table class="xyz_cls_feat_box xyz_cls_tab" style="width: 100%;">
    		<tr>
        		<td style="width: 50%;"><?php _e('Current Premium Expiry','wp-classifieds-listings');?> </td>
        		<td>
    <?php
		        if($numlist>0){	
		            if($list->featured_expiry!=0){
		                $exp_date=xyz_cls_local_date_time('d-m-Y',$list->featured_expiry);
		                printf('%s',$exp_date);
		            }
		            else{
		                _e('-N/A-','wp-classifieds-listings');
		            }
		        }
		        else{
		            _e('-N/A-','wp-classifieds-listings');
		        }
    ?>		
        		</td>
    		</tr>
    		<tr>
		        <td><?php _e('Change Premium Expiry','wp-classifieds-listings');?></td>
        		<td><input type="text" id="MyDate" name="xyz_cls_exdate" value="<?php if(isset($list->featured)!=0) {echo esc_attr($exp_date);}?>"></td>
    		</tr>	
    <?php 
        if($numlist>0){
            if($list->featured_expiry!=0){
    ?>
    		<tr>
        		<td><?php _e('Remove Premium:','wp-classifieds-listings');?></td>
		        <td><input type="checkbox" id="xyz_cls_rem_premium" name="xyz_cls_rem_premium" value="1"
		            <?php if($xyz_cls_rem_checked=="1")echo "checked";?>/></td>
			</tr>
	<?php
            }
        }
	?>
		</table>

	<?php
	}
}


//**********************************location details*******************************

if(!function_exists( 'xyz_cls_location_details' )){
	function xyz_cls_location_details(){
		$xyz_cls_url="";
		$xyz_cls_phone="";
		$xyz_cls_locality="";
		$xyz_cls_state="";
		$xyz_cls_cityid="";
		$xyz_cls_cityn="";
		$xyz_cls_checked="";

		global $wpdb;
		global $post;

		$xyz_cls_countryname=get_option('xyz_cls_default_country');
		$resStates=$wpdb->get_results("SELECT * FROM `".$wpdb->prefix."xyz_cls_states` WHERE ccode='$xyz_cls_countryname'");
		$ccode=$xyz_cls_countryname;
		$res= $wpdb->get_results($wpdb->prepare("SELECT * FROM ".$wpdb->prefix."xyz_cls_listing_details WHERE pid =%d",$post->ID));	

		foreach ($res as $v){
		    if(isset($_POST['xyz_cls_url']))
		        $xyz_cls_url=esc_url($_POST['xyz_cls_url']);
		    else
		        $xyz_cls_url=$v->url;
		    if(isset($_POST['xyz_cls_phone']))
		        $xyz_cls_phone=sanitize_text_field($_POST['xyz_cls_phone']);
		    else {
		        if($v->phone==0)
		            $xyz_cls_phone="";
		        else
		            $xyz_cls_phone=$v->phone;
		    }
		    if(isset($_POST['xyz_cls_locality']))
		        $xyz_cls_locality=sanitize_text_field($_POST['xyz_cls_locality']);
		    else
		        $xyz_cls_locality=$v->locality;
		    if(isset($_POST['xyz_cls_state']))
		        $xyz_cls_state=sanitize_text_field($_POST['xyz_cls_state']);
		    else	
		        $xyz_cls_state=$v->state_id;
		    $xyz_cls_cityid=$v->city_id;
		}

		if(count($resStates)==0){
			$xyz_cls_state = '00';
			$sql=$wpdb->get_results($wpdb->prepare( "SELECT  * FROM ".$wpdb->prefix."xyz_cls_cities  WHERE scode=%s AND ccode=%s ORDER BY city ASC",$xyz_cls_state,$ccode));
		}
		else{
			$sql=$wpdb->get_results($wpdb->prepare( "SELECT  * FROM ".$wpdb->prefix."xyz_cls_cities  WHERE scode=%s AND ccode=%s ORDER BY city ASC",$xyz_cls_state,$ccode));
		}
?>
	<table class="xyz_cls_loc_info_box xyz_cls_tab" style="width:100%;">
		<tr>
			<td style="width:50%;"><input type="hidden" name="xyz_cls_country" value="<?php echo esc_attr($ccode);?>" id="xyz_cls_country"></td>
		</tr>
		<tr>
            <td><input type="hidden" name="xyz_cls_postid" value="<?php echo esc_attr($post->ID) ;?>" id="xyz_cls_postid">
            </td>
    	</tr>
    	<tr>
            <td><input type="hidden" name="xyz_cls_hcity" value="<?php echo esc_attr($xyz_cls_cityid) ;?>" id="xyz_cls_hcity"></td>
    	</tr>
<?php 
    if(count($resStates)>0){
?>
        <tr>
    		<td><?php _e('State','wp-classifieds-listings');?></td>	
    		<td>
                <select id="state_dropdown" name="xyz_cls_state">
                    <option value="-1"><?php _e('---Select---','wp-classifieds-listings');?></option>
        <?php
            foreach ($resStates as $row){
                if($xyz_cls_state==$row->scode){
                    $selected= 'selected';
                }
                else
                    $selected="";
        ?>
                    <option value="<?php echo $row->scode;?>"<?php echo $selected; ?>><?php echo $row->sname;?></option>
        <?php
            }
        ?>
                </select>
                <span class="xyz_mandatory">*</span>
            </td>
            <td>
                <div id="ldng" style="display: none;">
                    <img src="<?php echo plugins_url(XYZ_CLASSIFIEDS_DIR."/images/ldng.gif")?>"/>
                </div>
            </td>
    	</tr>
<?php
        }
    	if( $xyz_cls_cityid!=-1){
?>
	    <tr>
	        <td><?php _e('City','wp-classifieds-listings');?></td>
	        <td>
	            <select id="city_dropdown" name="xyz_cls_city">
	                <option value="-1"><?php _e('---Select---','wp-classifieds-listings');?></option>
	                <?php foreach ($sql as $city){?>
	                <option value="<?php echo $city->id;?>"<?php if($city->id==$xyz_cls_cityid){ echo 'selected';}?>><?php echo $city->city;?></option>
	                <?php }?>
	            </select>
	            <span class="xyz_mandatory">*</span>
	        </td>
	    </tr>
<?php
        }
        else{
?>
		<tr>
	        <td><?php _e('City','wp-classifieds-listings');?></td>
	        <td>
	            <select id="city_dropdown" name="xyz_cls_city">
	                <option value="-1"><?php _e('---Select---','wp-classifieds-listings');?></option></select>
	            <span class="xyz_mandatory">*</span>
	        </td>
	    </tr>	
<?php
        }
?>	
    	<tr>
	        <td><?php _e('Locality','wp-classifieds-listings');?></td>
	        <td>
	            <input type="text" name="xyz_cls_locality" id="xyz_cls_locality" value="<?php echo esc_attr($xyz_cls_locality);?>">
	            <span class="xyz_mandatory">*</span>
	        </td>
	    </tr>
	    <tr>
	        <td><?php _e('Phone','wp-classifieds-listings');?></td>
	        <td>
	            <input type="text" maxlength="12" name="xyz_cls_phone" value="<?php echo esc_attr($xyz_cls_phone);?>" onkeyup="this.value = this.value.replace(/[^0-9.]/g,'');">
	        </td>
	    </tr>
	    <tr>
	        <td><?php _e('URL','wp-classifieds-listings');?></td>
	        <td>
	            <input type="text" name="xyz_cls_url" value="<?php echo esc_attr($xyz_cls_url);?>">
	        </td>
	    </tr>	    
	</table>

<script type="text/javascript">
	jQuery(document).ready(function(){
		jQuery("#post").submit(function(){
			if(jQuery("#title").val()==''||jQuery("#des").val()==''||jQuery("#state_dropdown").val()=='-1'||jQuery("#city_dropdown").val()=='-1'||jQuery("#xyz_cls_category").val()=='0' ||jQuery("#xyz_cls_locality").val()==''){
				alert("<?php  _e('Please fill up mandatory fields','wp-classifieds-listings');?>");
                jQuery('#ajax-loading').hide();
                jQuery('.spinner').hide();
                jQuery('#publish').removeClass('button-primary-disabled');
                jQuery('#save-post').removeClass('button-disabled');
                return false;
			}
		});

		jQuery('#MyDate').datepicker({
            dateFormat : 'dd-mm-yy'
        });

        jQuery("#state_dropdown").change(function(){
        	document.getElementById("ldng").style.display="";
        	<?php $ajax_cls_nonce = wp_create_nonce('xyz-cls-load-cty');?>
            var dataString = {
                action: 'xyz_cls_load_city',
                security:'<?php echo $ajax_cls_nonce;?>',
                region:  jQuery("#state_dropdown").val(),
                ccode:	jQuery("#xyz_cls_country").val(),
                hcity:jQuery("#xyz_cls_hcity").val()
            };
            jQuery.post(ajaxurl, dataString, function(response){
                document.getElementById("ldng").style.display="none";
                if(response!=0)
                    jQuery("#city_dropdown").html(response);
                else
                {
                    alert("<?php  _e('No City Found','wp-classifieds-listings');?>");
                    jQuery("#city_dropdown").html('<option value="-1">---Select---</option>');
                }
            });
        });
	});
</script>
<?php
	}
}

//************************* custom product gallery************************************

if(!function_exists( 'xyz_cls_product_images_box' )){
	function xyz_cls_product_images_box(){
		global $post;
        $product_image_gallery='';
    ?>
    <div id="product_images_container">
    	<ul class="product_images">
    <?php
        if(metadata_exists( 'post', $post->ID, 'xyz_cls_listing_image_gallery' )){
            $product_image_gallery = get_post_meta( $post->ID, 'xyz_cls_listing_image_gallery', true );
        }
        $attachments = array_filter( explode( ',', $product_image_gallery ));
        if( $attachments )
            foreach ( $attachments as $attachment_id ){
            	echo '<li class="image" data-attachment_id="'.$attachment_id.'">'.wp_get_attachment_image( $attachment_id, 'thumbnail' ).'<ul class="actions"><li><a href="#" class="delete" title="' . __( 'Delete image','wp-classifieds-listings').'"></a></li></ul></li>';
        }
    ?>
    	</ul>
    		<input type="hidden" id="product_image_gallery" name="product_image_gallery" value="<?php echo esc_attr( $product_image_gallery ); ?>"/>
    </div>
	    <p class="add_product_images hide-if-no-js">
	    	<a href="#"><?php _e( 'Add listing gallery images', 'wp-classifieds-listings' ); ?></a>
		</p>
<script type="text/javascript">
	jQuery(document).ready(function($){
		// Uploading files
        var product_gallery_frame;
        var $image_gallery_ids = $('#product_image_gallery');
        var $product_images = $('#product_images_container ul.product_images');
        jQuery('.add_product_images').on( 'click', 'a', function( event ){
 			var $el = $(this);
            var attachment_ids = $image_gallery_ids.val();
            event.preventDefault();
            // If the media frame already exists, reopen it.
            if( product_gallery_frame ){
                product_gallery_frame.open();
                return;
            }

            // Create the media frame.
            product_gallery_frame = wp.media.frames.downloadable_file = wp.media({
                // Set the title of the modal.
                title: '<?php _e( 'Add listing gallery images', 'wp-classifieds-listings' );?>',
                button: {text: '<?php _e( 'Add to gallery', 'wp-classifieds-listings' ); ?>',},multiple: true
            });  

            // When an image is selected, run a callback.
            product_gallery_frame.on( 'select', function(){
            	var selection = product_gallery_frame.state().get('selection');
                selection.map( function( attachment ){
                	attachment = attachment.toJSON();
                    console.log(attachment);
                    if( attachment.id ){
                        attachment_ids = attachment_ids ? attachment_ids + "," + attachment.id : attachment.id;
                        $product_images.append('<li class="image" data-attachment_id="' + attachment.id + '"><img src="' + attachment.url + '" /><ul class="actions"><li><a href="#" class="delete" title="<?php _e( 'Delete image', 'wp-classifieds-listings' ); ?>"></a></li></ul></li>');
                    }
                });
                	$image_gallery_ids.val( attachment_ids );
            });  
            	 product_gallery_frame.open();    	
        });
                // Image ordering
                $product_images.sortable({
                    items: 'li.image',
                    cursor: 'move',
                    scrollSensitivity:40,
                    forcePlaceholderSize: true,
                    forceHelperSize: false,
                    helper: 'clone',
                    opacity: 0.65,
                    placeholder: 'wc-metabox-sortable-placeholder',
                    start:function(event,ui){
                        ui.item.css('background-color','#f6f6f6');
                    },
                    stop:function(event,ui){
                        ui.item.removeAttr('style');
                    },
                    update: function(event, ui){
                        var attachment_ids = '';
                        $('#product_images_container ul li.image').css('cursor','default').each(function() {
                            var attachment_id = jQuery(this).attr( 'data-attachment_id' );
                            attachment_ids = attachment_ids + attachment_id + ',';
                        });
                        $image_gallery_ids.val( attachment_ids );
                    }
                });

                // Remove images
                $('#product_images_container').on( 'click', 'a.delete', function(){
                    $(this).closest('li.image').remove();
                    var attachment_ids = '';
                    $('#product_images_container ul li.image').css('cursor','default').each(function() {
                        var attachment_id = jQuery(this).attr( 'data-attachment_id' );
                        attachment_ids = attachment_ids + attachment_id + ',';
                    });
                    $image_gallery_ids.val( attachment_ids );
                    return false;
                });
	});
</script>
    <?php
	}
}


//******************************remove dflt category metabox*******************************

add_action('admin_init','xyz_cls_reset_classifieds');
if( !function_exists( 'xyz_cls_reset_classifieds' ) ){
    function xyz_cls_reset_classifieds(){
        remove_meta_box( 'xyz_cls_categorydiv','classifieds_listing','side' );
    }
}

//******************************add custom category metabox********************************

if( !function_exists( 'xyz_cls_categorybox' )){
    function xyz_cls_categorybox(){
        $xyz_cls_item_type="";
        $xyz_cls_category='';
        global $wpdb;
        global $post;
        $postid = $post->ID;
        $category= $wpdb->get_results($wpdb->prepare("SELECT item_type,category FROM ".$wpdb->prefix."xyz_cls_listing_details WHERE pid =%d",$postid));
        foreach ($category as $value){
            if(isset($_POST['xyz_cls_item_type']))
                $xyz_cls_item_type=intval($_POST['xyz_cls_item_type']);
            else	
                $xyz_cls_item_type=$value->item_type;
            if(isset($_POST['xyz_cls_category']))
                $xyz_cls_category=intval($_POST['xyz_cls_category']);
            else 
                $xyz_cls_category=$value->category;
        }
?>
<table class="xyz_cls_cat_box">
    <tr>
        <td>
            <?php _e('Category','wp-classifieds-listings');?> 
        </td>
        <td>
            <select style="width: 180px;" name="xyz_cls_category" id="xyz_cls_category" ><?php echo xyz_cls_get_category_dropdown($pid=0,$xyz_cls_category,$level=0);?></select>
            <span class="xyz_mandatory">*</span>
        </td>
    </tr>
    <tr>
        <td>
            <?php _e('Item Type','wp-classifieds-listings');?></td>
        <td>
            <select  style="width: 180px;"name="xyz_cls_item_type"><option value="1" <?php if ($xyz_cls_item_type == "1" ) echo "selected"; ?>><?php _e('Offered','wp-classifieds-listings');?></option>
                <option value="2" <?php if ($xyz_cls_item_type =="2" ) echo "selected"; ?>><?php _e('Wanted','wp-classifieds-listings');?></option  >
            </select>
        </td>
    </tr>
</table>
<?php
    }
}

//**********************************custom add desc*************************************

if(!function_exists( 'xyz_cls_desc' )){
    function xyz_cls_desc(){	
        global $wpdb;
        global $post;
        if(isset($_POST['xyz_cls_metades'])){ 	
            $xyz_cls_metades=$_POST['xyz_cls_metades'];
         }
	     else{
	         $xyz_cls_metades= get_post_field('post_content', $post->ID);
	     }
?>
		<tr valign="top">
    		<td></td>
		</tr>
		<tr>
    		<td><textarea  id="des" name="xyz_cls_metades"  style="height:100px;width:100%;" ><?php echo  esc_attr($xyz_cls_metades);?></textarea><span class="xyz_mandatory">*</span>
    		</td>
		</tr>
<?php
    }
}

//**********************************save meta data************************************

add_action('save_post', 'xyz_cls_save_meta',100);

if( !function_exists( 'xyz_cls_save_meta' ) ) {

function xyz_cls_save_meta($post_ID){
    global $wpdb;
    
	if( get_post_type($post_ID)!='classifieds_listing')
		return;

	if(isset($_GET['action']) && $_GET['action']=='trash'){
		return;
	}

	if(isset($_GET['action']) && $_GET['action']=='untrash'){
		return;
	}

	if(! current_user_can( 'edit_cls_listings' ))
		return;
		
	if( get_the_title($post_ID)=='Auto Draft')
		return;

	$POST_CPY=$_POST;
	$_POST= stripslashes_deep($_POST);
	$status=$cat=$city=$state=$data='';$country='';$locality=$phone=$type=$url='';
	
	if(isset($_POST['xyz_cls_metades']))
		$data =$_POST['xyz_cls_metades'];
	
	if(isset($_POST['xyz_cls_city']))
		$city=sanitize_text_field($_POST['xyz_cls_city']);
	
	if(isset($_POST['xyz_cls_state']))
		$state=sanitize_text_field($_POST['xyz_cls_state']);
	
	if(isset($_POST['xyz_cls_country']))
		$country=sanitize_text_field($_POST['xyz_cls_country']);
	
	if(isset($_POST['xyz_cls_phone']))
		$phone=sanitize_text_field($_POST['xyz_cls_phone']);
	
	if(isset($_POST['xyz_cls_locality']))
		$locality=sanitize_text_field($_POST['xyz_cls_locality']);
	
	if(isset($_POST['xyz_cls_category']))
		$cat=intval($_POST['xyz_cls_category']);
	if(isset($_POST['xyz_cls_item_type']))
	$type=intval($_POST['xyz_cls_item_type']);
	
	if(isset($_POST['xyz_cls_url']))
		$url=esc_url($_POST['xyz_cls_url']);
	
	
	$c_time=time();

    $time_arr=getdate($c_time);
	$xyz_cls_expiry=get_option( 'xyz_cls_item_expiry' );
	$xyz_cls_exdate=xyz_cls_local_date_time_create(gmmktime($time_arr['hours'],$time_arr['minutes'],$time_arr['seconds'],$time_arr['mon'],$time_arr['mday']+$xyz_cls_expiry,$time_arr['year']));

	$xyz_cls_fexpiry=0;
    $status=get_post_status($post_ID);
		
	if($city==-1||$cat==0||$locality==""||$data=="" ){
		$wpdb->update($wpdb->prefix.'posts',array('post_status'=>'draft'),array('id'=>$post_ID));
		$status='draft';
	}
	else if(get_option('xyz_cls_default_item_status')==1 && !current_user_can('administrator' )){
        if(get_post_status( $post_ID )!='draft'){
            $wpdb->update($wpdb->prefix.'posts',array('post_status'=>'pending'),array('id'=>$post_ID));
            $status='pending';
        }
	}
			
	$wpdb->update($wpdb->prefix.'posts',array('post_content'=>$data),array('id'=>$post_ID));

	if(isset($_POST['product_image_gallery'])){
		update_post_meta($post_ID,'xyz_cls_listing_image_gallery',sanitize_text_field($_POST['product_image_gallery']));
	}

	$res= $wpdb->get_results($wpdb->prepare("SELECT * FROM ".$wpdb->prefix."xyz_cls_listing_details WHERE pid=%d",$post_ID ));
		
	if(count($res)==0){// first time publishing by user or admin
		$xyz_cls_exdate=0;
		$xyz_cls_premium=0;
		$xyz_cls_featured_days=0;

		if($status=='publish'){
			$xyz_cls_exdate=xyz_cls_local_date_time_create(gmmktime($time_arr['hours'],$time_arr['minutes'],$time_arr['seconds'],$time_arr['mon'],$time_arr['mday']+$xyz_cls_expiry,$time_arr['year']));
		}

		if(current_user_can('administrator')){
		    if(isset($_POST['xyz_cls_exdate']) && $_POST['xyz_cls_exdate']!=''){
				if($status=='publish'){
					$xyz_cls_premium = 1;
					$xyz_cls_fexpiry=xyz_cls_local_date_time_create(gmmktime($time_arr['hours'],$time_arr['minutes'],$time_arr['seconds'],$time_arr['mon'],$time_arr['mday']+$xyz_cls_featured_days,$time_arr['year']));
				}
				else{
					$xyz_cls_fexpiry=0;
				}

				if($xyz_cls_fexpiry>$xyz_cls_exdate){
						$xyz_cls_exdate=$xyz_cls_fexpiry;
				}
			}						
			else{
				$xyz_cls_featured_days=0;
				$xyz_cls_premium=0;
				$xyz_cls_fexpiry=0;			
			}
		}

		$wpdb->query($wpdb->prepare("INSERT INTO  `".$wpdb->prefix."xyz_cls_listing_details` (`pid` ,`category` ,`expiry` ,`item_type` ,`country_code` ,`city_id` ,`state_id`  ,`locality` ,`url` ,`phone` ,`featured` ,`featured_expiry` ,`featured_no_of_days`,`status`)
				VALUES (%d,%d,%d,%d,%s,%d,%s,%s,%s,%s,%d,%s,%d,%s)",$post_ID,$cat,$xyz_cls_exdate,$type,$country,$city,$state,$locality,$url,$phone,$xyz_cls_premium,$xyz_cls_fexpiry,$xyz_cls_featured_days,$status));
		}
		else{  //cases other than first time publishing
			$flag=0;
			$xyz_cls_premium=0;
			$xyz_cls_featured_days=0;
			$res= $wpdb->get_row($wpdb->prepare("SELECT * FROM ".$wpdb->prefix."xyz_cls_listing_details WHERE pid=%d",$post_ID ));
			$str_exp='';
			$xyz_cls_exdate=$res->expiry;

			if($status=='publish'){
				if($res->expiry==0 ){
					$xyz_cls_exdate=xyz_cls_local_date_time_create(gmmktime($time_arr['hours'],$time_arr['minutes'],$time_arr['seconds'],$time_arr['mon'],$time_arr['mday']+$xyz_cls_expiry,$time_arr['year']));
					$str_exp=' ,`expiry`='.$xyz_cls_exdate.' ';
				}

            if(current_user_can('administrator')){
				if(isset($_POST['xyz_cls_exdate']) && $_POST['xyz_cls_exdate']!=''){	
					$xyz_cls_premium=1;
					$date = $_POST['xyz_cls_exdate'];
					$ftime_arr=getdate(strtotime($date));
					$xyz_cls_fexpiry=xyz_cls_local_date_time_create(strtotime($date));
					if($xyz_cls_fexpiry>$xyz_cls_exdate)
						$xyz_cls_exdate=$xyz_cls_fexpiry;

					$str_exp=' ,`expiry`='.$xyz_cls_exdate.',`featured`=1,`featured_expiry`='.$xyz_cls_fexpiry.',`featured_no_of_days`=0 ';
				}

                if(isset($_POST['xyz_cls_rem_premium'])==1){
                    $str_exp=' ,`expiry`='.$xyz_cls_exdate.',`featured`=0,`featured_expiry`=0,`featured_no_of_days`=0';
                }
            }    

				 if(($res->featured==1)&&($res->status=='pending')){
                    // &&($res->featured_expiry==0)	 
					$xyz_cls_featured_days = $res->featured_no_of_days;	

                    if($res->featured_expiry<time()){
                        $xyz_cls_fexpiry=xyz_cls_local_date_time_create(gmmktime($time_arr['hours'],$time_arr['minutes'],$time_arr['seconds'],$time_arr['mon'],$time_arr['mday']+$xyz_cls_featured_days,$time_arr['year']));
                    }	                   
					else{
                        $xyz_cls_fexpiry = strtotime('+'.$xyz_cls_featured_days.'days', $res->featured_expiry);
                    }

					if($xyz_cls_fexpiry>$xyz_cls_exdate)
						$xyz_cls_exdate=$xyz_cls_fexpiry;
          
					$str_exp=' ,`expiry`='.$xyz_cls_exdate.',`featured`=1,`featured_expiry`='.$xyz_cls_fexpiry.',`featured_no_of_days`=0 ';
				}

				
			}

			$wpdb->query($wpdb->prepare("UPDATE `".$wpdb->prefix."xyz_cls_listing_details` SET  `category` =  %d,`item_type` =  %d,`country_code` =  %s,`city_id`=%d,`state_id` = %s,`locality` = %s,`url` =%s,`phone` =%s,`status`=%s ".$str_exp." WHERE  `".$wpdb->prefix."xyz_cls_listing_details`.`pid` =%d",$cat,$type,$country,$city,$state,$locality,$url,$phone,$status,$post_ID));
		}
		
        wp_set_object_terms($post_ID, intval($cat), 'xyz_cls_category' );
		$_POST=$POST_CPY;
    }
}

//****************************************************************************************
if(!function_exists( 'xyz_cls_actions' )){
    function xyz_cls_actions(){
        global $post,$wpdb;
    
        if(isset($_GET['action']) && isset($_GET['post'])&&$_GET['action']=='delete'){
            $postid=intval($_GET['post']);
            
            if(get_post_type($postid) == 'classifieds_listing'){
                $wpdb->delete($wpdb->prefix."xyz_cls_listing_details", array('pid'=>$postid));
            }
        }
        if(isset($_GET['action']) && $_GET['action']=='trash'){
            $postid=intval($_GET['post']);
            if(get_post_type($postid) == 'classifieds_listing'){
                $wpdb->query($wpdb->prepare("UPDATE `".$wpdb->prefix."xyz_cls_listing_details` SET  `status` = 'trash' WHERE  `".$wpdb->prefix."xyz_cls_listing_details`.`pid` =%d",$postid));
                if(current_user_can('administrator')&&get_post_status($postid)=='publish'){
                    $headers= "Content-type: text/html  ";
                    $author=get_post_field('post_author', $postid);
                    $to=get_the_author_meta('user_email',$author);
                    $sql="select * from ".$wpdb->prefix."xyz_cls_email_templates where id=%d";
                    $row1=$wpdb->get_row($wpdb->prepare($sql, 2));
                    $row1->sub=str_ireplace("{SITE_NAME}",get_bloginfo('name'),$row1->sub);
                    $row1->sub=str_ireplace("{LISTING_TITLE}",get_the_title($postid),$row1->sub);
                    $row1->body=str_ireplace("{SITE_NAME}",get_bloginfo('name'),$row1->body);
                    $row1->body=str_ireplace("{LISTING_TITLE}",get_the_title($postid),$row1->body);
                    wp_mail($to,$row1->sub, $row1->body,$headers);
                }
            }
        }
    }
}
add_action('init', 'xyz_cls_actions');

//******************************************************************************

if( !function_exists( 'xyz_cls_untrash' )){
    function xyz_cls_untrash($postid){
        global $wpdb;
        if(get_post_type($postid)=='classifieds_listing'){
            if(current_user_can('administrator')){
                $status = get_post_status($postid);
                $wpdb->query($wpdb->prepare("UPDATE `".$wpdb->prefix."xyz_cls_listing_details` SET  `status` =%s WHERE  `".$wpdb->prefix."xyz_cls_listing_details`.`pid` =%d",$status,$postid));
            }
            if(current_user_can('edit_cls_listings')&& !current_user_can('administrator')){
                $status = 'draft';
                $wpdb->query($wpdb->prepare("UPDATE `".$wpdb->prefix."xyz_cls_listing_details` SET  `status` =%s WHERE  `".$wpdb->prefix."xyz_cls_listing_details`.`pid` =%d",$status,$postid));
                wp_update_post(
                    array(
                        'ID' => $postid,
                        'post_status' => $status
                    )
                );
            }

            if($status=='publish'){
                $r= $wpdb->get_row($wpdb->prepare("SELECT * FROM ".$wpdb->prefix."xyz_cls_listing_details WHERE pid=%d",$postid ));
                
                if($r->featured==1)
                    $msg='YES';
                else 
                    $msg='NO';
                
                $sql="select * from ".$wpdb->prefix."xyz_cls_email_templates where id=%d";
                $row1=$wpdb->get_row($wpdb->prepare($sql, 1));
                $row1->sub=str_ireplace("{SITE_NAME}",get_bloginfo('name'),$row1->sub);
                $row1->sub=str_ireplace("{LISTING_TITLE}",get_the_title($postid),$row1->sub);
                $row1->body=str_ireplace("{SITE_NAME}",get_bloginfo('name'),$row1->body);
                $row1->body=str_ireplace("{LISTING_TITLE}",get_the_title($postid),$row1->body);
                $row1->body=str_ireplace("{PREMIUM}",$msg,$row1->body);
                $headers= "Content-type: text/html  ";
                $author=get_post_field('post_author', $postid);
                $to=get_the_author_meta('user_email',$author);
                wp_mail($to,$row1->sub, $row1->body,$headers);
            }
        }
    }
}
add_action('untrashed_post', 'xyz_cls_untrash');

//***********************************************************************************

if ( !function_exists( 'xyz_cls_on_publish_post' )){
    function xyz_cls_on_publish_post( $post ) {
        global $wpdb;

        $p=get_post_type( $post);
        if($p != "nav_menu_item"){
            $r= $wpdb->get_row($wpdb->prepare("SELECT * FROM ".$wpdb->prefix."xyz_cls_listing_details WHERE pid=%d",$post->ID ));
            
            if($r->featured==1)
                $msg='YES';
            else 
                $msg='NO';
            
            $sql="select * from ".$wpdb->prefix."xyz_cls_email_templates where id=%d";
            $row1=$wpdb->get_row($wpdb->prepare($sql, 1));
            $row1->sub =str_ireplace("{SITE_NAME}",get_bloginfo('name'),$row1->sub);
            $row1->sub =str_ireplace("{LISTING_TITLE}",get_the_title($post->ID),$row1->sub);
            $row1->body=str_ireplace("{SITE_NAME}",get_bloginfo('name'),$row1->body);
            $row1->body=str_ireplace("{LISTING_TITLE}",get_the_title($post->ID),$row1->body);
            $row1->body=str_ireplace("{PREMIUM}",$msg,$row1->body);

            $headers= "Content-type: text/html";
            $author=get_post_field('post_author',$post);
            $to=get_the_author_meta('user_email',$author);
            wp_mail($to,$row1->sub, $row1->body,$headers);
        }
    }
}
add_action(  'pending_to_publish',  'xyz_cls_on_publish_post');
//add_action(  'draft_to_publish',  'xyz_cls_on_publish_post');

function xyz_cls_disp_publish_btn() {  
    if(get_post_type()=='classifieds_listing'){
        // Hide the publish button for following post status
        $xyz_hide_publish_for_status = array(
            'pending'  
        );
        if(in_array( get_post_status(),$xyz_hide_publish_for_status)){
			if(!current_user_can('administrator')){
            	if(get_option('xyz_cls_default_item_status')==1){
            	?>
                	<style>
                    	#publishing-action { display: none; }
                	</style>
                <?php
            	}
			}
        }
    }
}
add_action( 'admin_head', 'xyz_cls_disp_publish_btn' );
