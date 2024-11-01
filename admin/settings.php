<?php
if(!defined('ABSPATH'))
	exit;

global $wpdb;

$_POST = stripslashes_deep($_POST);
$_GET = stripslashes_deep($_GET);
$f=1;$msg1=$xyz_cls_auth_token=$xyz_cls_premium_ad="";

if(!$_POST && (isset($_GET['cls_notice'])&& $_GET['cls_notice'] == 'hide')){
    if (! isset( $_REQUEST['_wpnonce'] ) || !wp_verify_nonce( $_REQUEST['_wpnonce'],'cls-shw')){
        wp_nonce_ays( 'cls-shw');
        exit;
    } 
    update_option('xyz_cls_admin_notice_shw', "hide");
?>
<style type='text/css'>
    #xyz_cls_notice_td{
        display:none !important;
    }
</style>
<?php
}

$user_roles= get_editable_roles();

$xyz_cls_auth_token = get_option('xyz_cls_auth_token');
$xyz_cls_roles=get_option('xyz_cls_roles');

$xyz_cls_sttng1=get_option( 'xyz_cls_item_expiry' );
$xyz_cls_sttng4=get_option( 'xyz_cls_premium_items_displayed_per_page' );
$xyz_cls_sttng5=get_option( 'xyz_cls_premium_listing_period');
$xyz_cls_sttng6=get_option( 'xyz_cls_premium_listing_price' );
//$xyz_cls_premium_ad = get_option('xyz_cls_hidepmAds');
$xyz_cls_sttng7=get_option( 'xyz_cls_currency_symbol' );
$xyz_cls_sttng8=get_option( 'xyz_cls_paypal_email' );
$xyz_cls_sttng9=get_option( 'xyz_cls_paypal_currency' );
$xyz_cls_sttng11=get_option( 'xyz_cls_default_country' );
$xyz_cls_sttng12=get_option( 'xyz_cls_default_item_image' );
$dupfile=$xyz_cls_sttng12;/*new */
$xyz_cls_sttng13=get_option('xyz_cls_item_count');
$xyz_cls_sttng14 = get_option('xyz_cls_default_sender_email');
$xyz_cls_sttng15 = get_option('xyz_cls_default_sender_name');
$xyz_cls_sttng16 = get_option('xyz_cls_general_notication_email');
$xyz_cls_gal=get_option('xyz_cls_gallery');

$pg1=get_option('xyz_wp_cls_home');
$pg2=get_option( 'xyz_wp_cls_items' );
$pg3=get_option( 'xyz_wp_cls_region' );
$pg4=get_option( 'xyz_wp_cls_city' );
$pg5=get_option('xyz_wp_cls_register');
$pg6=get_option('xyz_wp_cls_forgotpassword');
$xyz_cls_login=get_option('xyz_cls_login');
$disable=get_option('xyz_cls_disable_dflt_login');

//$xyz_cls_custom_fields=get_option('xyz_cls_custom_fields');new

$role=get_option('xyz_cls_custom_role');
$xyz_credit_link=get_option('xyz_credit_link');
$show_cat=get_option('xyz_cls_category_view');

if(isset($_POST) && isset($_POST['xyz_cls_submit'])){
	if (!isset( $_REQUEST['_wpnonce'] )||!wp_verify_nonce( $_REQUEST['_wpnonce'], 'cls-settings_' )){
		wp_nonce_ays( 'cls-settings_' );
		exit;
	}

	$xyz_cls_auth_token = sanitize_text_field($_POST['xyz_cls_auth_token']);

	$pg1=sanitize_text_field($_POST['xyz_wp_cls_home']);
	$pg2=sanitize_text_field($_POST['xyz_wp_cls_items']);
	$pg3=sanitize_text_field($_POST['xyz_wp_cls_region']);
	$pg4=sanitize_text_field($_POST['xyz_wp_cls_city']);
	$pg5=sanitize_text_field($_POST['xyz_wp_cls_register']);
	$pg6=sanitize_text_field($_POST['xyz_wp_cls_forgotpassword']);

	$xyz_cls_login=sanitize_text_field($_POST['xyz_cls_login']);

	if(isset($_POST['xyz_cls_disable_dflt_login']))
	    $disable=sanitize_text_field($_POST['xyz_cls_disable_dflt_login']);

	$role=intval($_POST['xyz_cls_custom_role']);
	$xyz_credit_link = sanitize_text_field($_POST['xyz_credit_link']);

	$xyz_cls_sttng1 = absint($_POST['xyz_cls_item_expiry']);
	$xyz_cls_sttng4 = intval($_POST['xyz_cls_premium_items_displayed_per_page']);
	$xyz_cls_sttng5 = intval($_POST['xyz_cls_premium_listing_period']);
	//$xyz_cls_custom_fields=$_POST['xyz_cls_custom_fields'];new
	$xyz_cls_sttng6 = intval($_POST['xyz_cls_premium_listing_price']);
	$xyz_cls_sttng7 = sanitize_text_field($_POST['xyz_cls_currency_symbol']);
	$xyz_cls_sttng8 = sanitize_email($_POST['xyz_cls_paypal_email']);
	$xyz_cls_sttng14 = sanitize_email($_POST['xyz_cls_default_sender_email']);
	$xyz_cls_sttng15 = sanitize_text_field($_POST['xyz_cls_default_sender_name']);
	$xyz_cls_sttng16 = sanitize_email($_POST['xyz_cls_general_notification_email']);
	$xyz_cls_sttng9=sanitize_text_field($_POST['xyz_cls_paypal_currency']);
	//	$xyz_cls_premium_ad = intval($_POST['xyz_cls_premium_ad']);
	$xyz_cls_sttng11=sanitize_text_field($_POST['xyz_cls_default_country']);

	if(isset($_POST['xyz_cls_default_item_image']))
	    $xyz_cls_sttng12=sanitize_text_field($_POST['xyz_cls_default_item_image']);
	else
	    $xyz_cls_sttng12=get_option('xyz_cls_default_item_image');

	if(isset($_POST['xyz_cls_category_view']))
	    $show_cat=intval($_POST['xyz_cls_category_view']);
	else
	    $show_cat=0;

	$xyz_cls_gal=intval($_POST['xyz_cls_gallery']);
	$xyz_cls_sttng13=absint($_POST['xyz_cls_item_count']);

	if($_POST['xyz_cls_item_display_order']==1)
	    $xyz_cls_item_display_order=1;
	else
	    $xyz_cls_item_display_order=2;

	if($_POST['xyz_cls_default_item_status']==1)
	    $xyz_cls_default_item_status=1;
	else
	    $xyz_cls_default_item_status=2;

	if($_POST['xyz_cls_currency_position']=='Suffix')
	    $xyz_cls_currency_position=1;
	else
	    $xyz_cls_currency_position=2;

	if($_POST['xyz_cls_premium_listing_enable']==1)
	    $xyz_cls_premium_listing_enable=1;
	else
	    $xyz_cls_premium_listing_enable=2;

	$upload_dir = wp_upload_dir();
	$xyz_plugin_dir_path=$upload_dir['basedir'] ;
	$file= move_uploaded_file($_FILES["xyz_cls_default_item_image"]["tmp_name"],
	                          $xyz_plugin_dir_path."/xyz-cls-uploads/".$_FILES['xyz_cls_default_item_image']['name']);

	if($xyz_cls_sttng1==""||$xyz_cls_sttng4==""||$xyz_cls_sttng5==""||$xyz_cls_sttng6==""||$xyz_cls_sttng7==""||$xyz_cls_sttng8==""||$xyz_cls_sttng9==""||$xyz_cls_sttng13==""||$xyz_cls_sttng14==""||$xyz_cls_sttng15=""||$xyz_cls_sttng16==""){
		 $msg1="Please fill up the mandatory fields..!";
			$f=0;
	}
	else if(!filter_var($_POST['xyz_cls_paypal_email'],FILTER_VALIDATE_EMAIL)){
		$msg1="Please enter valid email ID..!";
		$f=0;
	}
	else if(!filter_var($_POST['xyz_cls_default_sender_email'],FILTER_VALIDATE_EMAIL)){
		$msg1="Please enter valid email ID..!";
		$f=0;
	}
	else if(!filter_var($_POST['xyz_cls_general_notification_email'],FILTER_VALIDATE_EMAIL)){
		$msg1="Please enter valid email ID..!";
		$f=0;
	}
	else if(!is_numeric ($xyz_cls_sttng1)||!is_numeric ($xyz_cls_sttng4)||!is_numeric ($xyz_cls_sttng5)||
				!is_numeric($xyz_cls_sttng6)||!is_numeric ($xyz_cls_sttng13)){
		$msg1="Value should be  an  integer.";
		$f=0;
	}
	else
		$f=1;

	if($f==1){
		//update_option('xyz_cls_hidepmAds',$xyz_cls_premium_ad);
		update_option( 'xyz_cls_item_expiry',intval($_POST['xyz_cls_item_expiry']));
		update_option( 'xyz_cls_premium_items_displayed_per_page',intval($_POST['xyz_cls_premium_items_displayed_per_page']) );
		update_option( 'xyz_cls_premium_listing_period', intval($_POST['xyz_cls_premium_listing_period']));
		update_option( 'xyz_cls_premium_listing_price', intval($_POST['xyz_cls_premium_listing_price']));
		update_option( 'xyz_cls_item_display_order', $xyz_cls_item_display_order);
		update_option( 'xyz_cls_default_item_status',$xyz_cls_default_item_status);
		update_option( 'xyz_cls_item_count',intval($_POST['xyz_cls_item_count']));
		update_option( 'xyz_cls_gallery',intval($_POST['xyz_cls_gallery']));

			if(($_FILES['xyz_cls_default_item_image']['name'])!=$dupfile && ($_FILES['xyz_cls_default_item_image']['name'])!="") {
		unlink( $xyz_plugin_dir_path."/xyz-cls-uploads/".$xyz_cls_sttng12);
		update_option( 'xyz_cls_default_item_image',  $_FILES['xyz_cls_default_item_image']['name']);
				$xyz_cls_sttng12=get_option( 'xyz_cls_default_item_image' );
	}

		//update_option( 'xyz_cls_custom_fields',$_POST['xyz_cls_custom_fields']);new
		update_option('xyz_cls_currency_symbol',$xyz_cls_sttng7);
		update_option('xyz_cls_currency_position',intval($_POST['xyz_cls_currency_position']));
		update_option('xyz_cls_paypal_email', $xyz_cls_sttng8 );
		update_option('xyz_cls_paypal_currency',$xyz_cls_sttng9 );
		update_option('xyz_cls_default_sender_email',$xyz_cls_sttng14);
		update_option('xyz_cls_default_sender_name',sanitize_text_field($_POST['xyz_cls_default_sender_name']) );
		update_option( 'xyz_cls_general_notification_email',$xyz_cls_sttng16 );
		
		update_option( 'xyz_cls_premium_listing_enable', intval($_POST['xyz_cls_premium_listing_enable']));
		update_option( 'xyz_cls_default_country',$xyz_cls_sttng11);
		update_option('xyz_cls_auth_token',$xyz_cls_auth_token);
		
		update_option( 'xyz_wp_cls_home',$pg1);
		update_option( 'xyz_wp_cls_items', $pg2);
		update_option( 'xyz_wp_cls_region', $pg3);
		update_option( 'xyz_wp_cls_city',$pg4);
		update_option( 'xyz_wp_cls_register',$pg5);
		update_option( 'xyz_wp_cls_forgotpassword',$pg6);
		update_option( 'xyz_cls_login',$xyz_cls_login);
		update_option('xyz_credit_link', $xyz_credit_link);

		$wpdb->query($wpdb->prepare("UPDATE ".$wpdb->prefix."posts SET post_content=%s   WHERE  ID=%d ",'[xyz_wp_cls_home]',get_option('xyz_wp_cls_home') ));

		$wpdb->query($wpdb->prepare("UPDATE ".$wpdb->prefix."posts SET post_content=%s   WHERE  ID=%d ",'[xyz_wp_cls_items]',get_option('xyz_wp_cls_items') ));

		$wpdb->query($wpdb->prepare("UPDATE ".$wpdb->prefix."posts SET post_content=%s   WHERE  ID=%d ",'[xyz_wp_cls_region]',get_option('xyz_wp_cls_region') ));

		$wpdb->query($wpdb->prepare("UPDATE ".$wpdb->prefix."posts SET post_content=%s   WHERE  ID=%d ",'[xyz_wp_cls_city]',get_option('xyz_wp_cls_city') ));

		// $wpdb->query($wpdb->prepare("UPDATE ".$wpdb->prefix."posts SET post_content=%s   WHERE  ID=%d ",'[xyz_wp_cls_register]',get_option('xyz_wp_cls_register') ));

		// $wpdb->query($wpdb->prepare("UPDATE ".$wpdb->prefix."posts SET post_content=%s   WHERE  ID=%d ",'[xyz_wp_cls_forgotpassword]',get_option('xyz_wp_cls_forgotpassword') ));

		if($xyz_credit_link=='cls'){
		    update_option('xyz_cls_credit_dismiss',0);
		}

		update_option('xyz_cls_category_view', $show_cat);
		update_option('xyz_cls_custom_role', $role);
		
		if (isset($_POST['xyz_cls_disable_dflt_login']))
		    update_option('xyz_cls_disable_dflt_login', 'true');
		else
		    update_option('xyz_cls_disable_dflt_login', 'false');

		if(isset($_POST['xyz_cls_roles'])){
		    $xyz_cls_roles = array_map( 'sanitize_text_field', $_POST['xyz_cls_roles'] );
		    update_option('xyz_cls_roles', $xyz_cls_roles);
		}

		if(is_array($xyz_cls_roles))
			foreach ($xyz_cls_roles as $role1){
				$r=get_role($role1);
				$r->add_cap( 'edit_cls_listings' );
				$r->add_cap( 'publish_cls_listings' );
				$r->add_cap( 'delete_cls_listings' );
				$r->add_cap( 'delete_published_cls_listings' );
				$r->add_cap('edit_published_cls_listings' );
			}

			if(is_array($xyz_cls_roles))
				foreach ($user_roles as $role_name => $user){
					if($role_name==get_option('default_role') || $role_name=='administrator')
						continue;

					if(!in_array($role_name, $xyz_cls_roles) && $role_name!='administrator'  ){
						$r1=get_role($role_name);
						if($r1->has_cap('edit_cls_listings'))
							$r1->remove_cap( 'edit_cls_listings' );
						if($r1->has_cap('publish_cls_listings'))
							$r1->remove_cap( 'publish_cls_listings' );
						if($r1->has_cap('delete_cls_listings'))
							$r1->remove_cap( 'delete_cls_listings' );
						if($r1->has_cap('delete_published_cls_listings'))
							$r1->remove_cap( 'delete_published_cls_listings' );
						if($r1->has_cap('edit_published_cls_listings'))
							$r1->remove_cap('edit_published_cls_listings' );
					}
				}		

				update_option( 'xyz_cls_cron_runnig_time',$_POST['xyz_cls_cron_runnig_time'] );
				$msg1=" Settings updated successfully";		
	}

	if( $msg1!=""){
	    if($f==0)
	        $cl="system_notice_area_style0";
	    else if($f==1)
	        $cl="system_notice_area_style1";
?>
		<div class="<?php echo $cl;?>" id="system_notice_area">
		    <?php echo $msg1;?> &nbsp;&nbsp;&nbsp;
		    <span id="system_notice_area_dismiss">Dismiss</span>
		</div>
<?php  
	}
}
?>

<div class="wrap">
	<h3>Classifieds Settings</h3>
	<h4> All fields marked <span style=" color:red">*</span> are mandatory</h4>

	<form name="item_settings" method="post" id="item_settings" enctype="multipart/form-data" >
		<?php wp_nonce_field('cls-settings_');?>
		<table  id=settings_table  class="widefat xyz_cls_tab">
			<thead>
				<tr>
					<th><b>Item Settings</b></th>
					<th></th>
				</tr>
			</thead>

			<tr valign="top">
    			<td scope="row">Item expiry in days</td>
    			<td>
    				<input type="text" value="<?php echo esc_attr($xyz_cls_sttng1);?>" name='xyz_cls_item_expiry' onkeyup="this.value = this.value.replace(/[^0-9\.]/g,'')">
        			<span style=" color:red">*</span>
    			</td>
			</tr>

<?php
$a="";$b="";$c="";$d="";

	if(isset($_POST['xyz_cls_item_display_order']))
 		$a = intval($_POST['xyz_cls_item_display_order']);
 	else
 		$a =get_option('xyz_cls_item_display_order');

 	if(isset($_POST['xyz_cls_default_item_status']))
  		$b = intval($_POST[ 'xyz_cls_default_item_status']);
 	else
 		$b = get_option('xyz_cls_default_item_status');

 	if(isset($_POST['xyz_cls_premium_listing_enable']))
  		$c = intval($_POST['xyz_cls_premium_listing_enable']);
 	else
 		$c = get_option('xyz_cls_premium_listing_enable');

 	if(isset($_POST['xyz_cls_currency_position']))
  		$d = intval($_POST['xyz_cls_currency_position']);
 	else
 		$d = get_option('xyz_cls_currency_position');

?>	
			<tr valign="top">
				<td scope="row">Item display order</td>
				<td>
					<select name='xyz_cls_item_display_order'>
						<option value="1"<?php if($a=='1') echo 'selected';?>>Publish Time</option>
						<option  value="2"<?php if($a =='2') echo'selected';?>>Last Modified Time</option>
					</select>
				</td>
			</tr>

			<tr valign="top">
				<td scope="row">
					Default item status
				</td>
				<td>
					<select name='xyz_cls_default_item_status'>
						<option value="1"<?php if($b=='1') echo 'selected';?>>Pending Review</option>
						<option value="2"<?php if($b=='2') echo 'selected';?>>Publish</option>
					</select>
				</td>
			</tr>

			<tr valign="top">
				<td scope="row">
					Default item image
				</td>
				<td>
					<input type="file" name="xyz_cls_default_item_image" value="<?php echo $xyz_cls_sttng12;?>"><?php $imgpath=content_url() . '/uploads/xyz-cls-uploads/';?>
					<div id="thumbnail" class="xyz_cls_thumbnail"><img src="<?php echo esc_attr($imgpath.$xyz_cls_sttng12); ?>"></div>
				</td>
			</tr>

			<tr valign="top">
				<td scope="row">
					Number of items to be displayed per page
				</td>
				<td>
					<input type="text" value="<?php echo esc_attr($xyz_cls_sttng13);?>" name='xyz_cls_item_count' onkeyup="this.value = this.value.replace(/[^0-9\.]/g,'')"><span style=" color:red">*</span>
				</td>
			</tr>	
			
			<tr valign="top">
				<td scope="row">
					Choose gallery shortcode handler
				</td>
				<td>
					<select name="xyz_cls_gallery">
						<option value="1" <?php if($xyz_cls_gal==1) echo 'selected';?>>Wordpress Default Gallery</option>
				<?php 
					if(is_plugin_active('wp-gallery-manager/wp-gallery-manager.php')){
				?>
						<option value="2" <?php if($xyz_cls_gal==2) echo 'selected';?>>WP Gallery Manager</option>
				<?php 
					}
				?>
					</select>
				</td>
			</tr>		
		</table>
		<br>
		<table  class="widefat xyz_cls_tab">
			<thead>
				<tr>
					<th><b>Payment Settings</b></th>
					<th></th>
				</tr>
			</thead>

			<tr valign="top">
				<td scope="row">Currency symbol</td>
				<td><input type="text" value="<?php echo $xyz_cls_sttng7;?>" name='xyz_cls_currency_symbol'> <span style=" color:red">*</span></td>
			</tr>

			<tr valign="top">
				<td scope="row">Currency position</td><td><select name='xyz_cls_currency_position'>
					<option value="1"<?php if($d=='1') echo 'selected';?>>Suffix</option>
					<option value="2"<?php if($d=='2') echo 'selected';?>>Prefix</option></select></td>
			</tr>

			<tr valign="top">
				<td scope="row">Paypal email</td>
				<td><input type="text" value="<?php echo esc_attr($xyz_cls_sttng8);?>" name='xyz_cls_paypal_email'> <span style=" color:red">*</span></td>
			</tr>

			<tr valign="top">
				<td scope="row">Paypal currency</td>
				<td><input type="text" value="<?php echo esc_attr($xyz_cls_sttng9);?>" name='xyz_cls_paypal_currency'> <span style=" color:red">*</span></td>
			</tr>

			<tr valign="top">
				<td scope="row">Identity Token</td>
				<td><input type="text" name="xyz_cls_auth_token" value="<?php echo esc_attr($xyz_cls_auth_token);?>"></td>
			</tr>
		</table>
		<br>

		<table class="widefat xyz_cls_tab">
			<thead>
				<tr>
					<th><b>Email Settings</b></th>
					<th></th>
				</tr>
			</thead>
			
			<tr valign="top">
				<td scope="row">
					<label for="xyz_cls_default_sender_email">Default sender email</label>
				</td>
				<td>
					<input type="text" name="xyz_cls_default_sender_email" id="xyz_cls_default_sender_email"
				    value="<?php if(isset($_POST['xyz_cls_default_sender_email'])){echo sanitize_email($_POST['xyz_cls_default_sender_email']);}else{echo get_option('xyz_cls_default_sender_email');}?>" >
				     <span style=" color:red">*</span>
				</td>
			</tr>
			<tr valign="top">
				<td scope="row">
					<label for="xyz_cls_default_sender_name">Default sender name</label>
				</td>
				<td>
					<input type="text" name="xyz_cls_default_sender_name" id="xyz_cls_default_sender_name"
				    value="<?php if(isset($_POST['xyz_cls_default_sender_name'])){echo sanitize_text_field($_POST['xyz_cls_default_sender_name']);}else{echo get_option('xyz_cls_default_sender_name');}?>" >
				     <span style=" color:red">*</span>
				</td>
			</tr>

			<tr valign="top">
				<td scope="row"><label for="xyz_cls_general_notification_email">General notification email</label>
				</td>
				<td><input type="text" name="xyz_cls_general_notification_email" id="xyz_cls_general_notification_email"
				    value="<?php if(isset($_POST['xyz_cls_general_notification_email'])){echo sanitize_email($_POST['xyz_cls_general_notification_email']);}else{echo get_option('xyz_cls_general_notification_email');}?>" >
				     <span style=" color:red">*</span>
				</td>
			</tr>
		</table>
		<br>
<?php 
	if($c=='1')
		$dl='';
	else 
		$dl='none';
?>		
		<table class="widefat xyz_cls_tab">
			<thead>
				<tr>
					<th><b>Premium Settings</b></th>
					<th></th>
				</tr>
			</thead>

			<tr valign="top">
				<td scope="row">Enable premium listing</td>
				<td><select name='xyz_cls_premium_listing_enable' id="premium_listing"onchange="xyz_cls_display_listing()">
						<option value="1"<?php if($c=='1') echo 'selected';?>>Yes</option>
						<option value="2"<?php if($c=='2') echo 'selected';?>>No</option>
					</select>
				</td>
			</tr>

			<tr valign="top" id="premium_items_tr" style="display:<?php echo $dl;?>">
				<td scope="row">Premium items to be displayed per page</td>
				<td><input type="text"  value="<?php echo esc_attr($xyz_cls_sttng4);?>" name='xyz_cls_premium_items_displayed_per_page' onkeyup="this.value = this.value.replace(/[^0-9\.]/g,'')"> <span style=" color:red">*</span></td>
			</tr>

			<tr valign="top" id="premium_period_tr" style="display:<?php echo $dl;?>">
				<td scope="row">Default premium listing period [days]</td>
				<td><input type="text"   value="<?php echo esc_attr($xyz_cls_sttng5);?>" name='xyz_cls_premium_listing_period' onkeyup="this.value = this.value.replace(/[^0-9\.]/g,'')"> <span style=" color:red">*</span></td>
			</tr>

			<tr id="premium_price_tr" style="display:<?php echo $dl;?>">
				<td scope="row">Default premium price</td>
				<td><input type="text"  value="<?php echo esc_attr($xyz_cls_sttng6);?>"  name='xyz_cls_premium_listing_price' onkeyup="this.value = this.value.replace(/[^0-9\.]/g,'')"> <span style=" color:red">*</span></td>
			</tr>

<!-- 			<tr id="premium_ad">
				<td>
					Enable Premium Version Ad
				</td>
				<td>
					Yes <input type="radio" name="xyz_cls_premium_ad" value="1" <?php if($xyz_cls_premium_ad==1)echo 'checked="checked"'; ?>>
					No <input type="radio" name="xyz_cls_premium_ad" value="0"  <?php if($xyz_cls_premium_ad==0)echo 'checked="checked"'; ?>>
				</td>
			</tr> -->

		</table>
		<br>

		<table class="widefat xyz_cls_tab">
			<thead>
				<tr>
					<th><b>Roles and Capabilities Management</b></th>
					<th></th>
				</tr>
			</thead>
			<tr valign="top">
				<td scope="row">Use custom role [classifieds_user] for classifieds listing management</td>
				<td>Yes<input type="radio" name="xyz_cls_custom_role" value="1" <?php if($role==1)echo 'checked="checked"'; ?> id="ryes">No<input type="radio" name="xyz_cls_custom_role"  id="rno" value="0" <?php if($role==0)echo 'checked="checked"'; ?>></td>
			</tr>

			<tr valign="top">
				<td scope="row">Select user roles which require classifieds  management capabilities (Publish cls listing,Edit cls listing, Delete cls listing)</td>
				<td style="width:620px">
<?php
if(isset($_POST['xyz_cls_roles'])){
	$roles = array_map( 'sanitize_text_field', $_POST['xyz_cls_roles'] );
}
else
	$roles=get_option('xyz_cls_roles');

foreach ($user_roles as $role_name => $user){
//if($role_name=='classifieds_user' )continue;
?>				<div id="<?php echo $role_name;?>"><input type="checkbox" name="xyz_cls_roles[]"  value="<?php echo esc_attr($role_name);?>" <?php if(is_array($roles)){if(in_array($role_name,$roles)||$role_name=='administrator'||$role_name==get_option('default_role')) echo 'checked';}?> <?php //if($role_name=='administrator'||$role_name==get_option('default_role'))echo 'disabled';?> size="20"><?php echo ucwords(str_replace('_', " ", $role_name));
?></div>
<?php 
}
?>
			</tr>
		</table>
<?php

 $result=$wpdb->get_results($wpdb->prepare("SELECT DISTINCT post_title,ID FROM ".$wpdb->prefix."posts WHERE post_type=%s AND post_title!='' AND post_status=%s  AND  post_title!='Auto Draft'",'page','publish'));

?>
<br>
		<table class="widefat xyz_cls_tab">
			<thead>
				<tr>
					<th><b>Login Management</b></th>
					<th></th>
				</tr>
			</thead>
				<tr>
					<td scope="row">Choose login type</td>
					<td><select name="xyz_cls_login" id="xyz_cls_login" style="padding:4px;">
						<option value="1" <?php if($xyz_cls_login==1) echo 'selected';?>>Default WordPress Pages</option>
						<option value="2" <?php if($xyz_cls_login==2) echo 'selected';?>>Pages from  WP Classifieds Listings</option>
						</select>
					</td>
				</tr>

				<tr id="notice1">
					<td colspan="2"><i>Setup login pages here.Shortcode to place on  each page is given in the right side of corresponding page select box</i></td>
				</tr>

				<tr id="loginpage">
					<td scope="row">Login-Register page</td> 
					<td><select name='xyz_wp_cls_register'>
						<?php foreach ($result as $page){?>
							<option value="<?php echo esc_attr($page->ID);?>"<?php if($pg5==$page->ID) echo 'selected';?>><?php echo esc_attr($page->post_title);?></option>
							<?php }?>
						</select><i><code>[xyz_wp_cls_register]</code></i>
					</td>
				</tr>

				<tr id="forgotpassword">
					<td scope="row">Forgot password page</td> 
					<td><select name='xyz_wp_cls_forgotpassword'>
						<?php foreach ($result as $page){?>
							<option value="<?php echo esc_attr($page->ID);?>"<?php if($pg6==$page->ID) echo 'selected';?>><?php echo esc_attr($page->post_title);?></option>
						<?php }?>
						</select><i><code>[xyz_wp_cls_forgotpassword]</code></i>
					</td>
				</tr>

				<tr id="disablewppages">
					<td scope="row">Disable default login/forgot password pages</td> 
					<td><input type="checkbox" name="xyz_cls_disable_dflt_login" value="true" <?php if($disable=="true") echo 'checked';?>></td>
				</tr>

		</table>
			<br>
		<table class="widefat xyz_cls_tab">
			<thead>
				<tr>
					<th><b>Page Settings</b></th>
					<th></th>
				</tr>
			</thead>
				<tr>
					<td colspan="2" ><i>Setup Classifieds pages here.Shortcode to place on  each page is given in the right side of corresponding page select box</i></td>
				</tr>	

				<tr>
					<td scope="row">Region browsing page</td>
					<td><select name='xyz_wp_cls_region'>
						<?php foreach ($result as $page){?>
							<option value="<?php echo esc_attr($page->ID);?>"<?php if($pg3==$page->ID) echo 'selected';?>><?php echo esc_attr($page->post_title);?></option>
						<?php }?></select><i><code>[xyz_wp_cls_region]</code></i>
					</td>
				</tr>	

				<tr>
					<td scope="row">City browsing page</td>
 					<td><select name='xyz_wp_cls_city'><?php foreach ($result as $page){?>
						<option value="<?php echo esc_attr($page->ID);?>"<?php if($pg4==$page->ID) echo 'selected';?>><?php echo esc_attr($page->post_title);?></option><?php }?></select><i><code>[xyz_wp_cls_city]</code></i>
					</td>
				</tr>

				<tr>
					<td scope="row">Classifieds home page</td>
 					<td><select name='xyz_wp_cls_home'><?php foreach ($result as $page){?>
						<option value="<?php echo esc_attr($page->ID);?>"<?php if($pg1==$page->ID) echo 'selected';?>><?php echo esc_attr($page->post_title);?></option><?php }?></select><i><code>[xyz_wp_cls_home]</code></i>
					</td>
				</tr>

				<tr>
					<td scope="row">Category based items browsing page</td>
 					<td><select name='xyz_wp_cls_items'><?php foreach ($result as $page){?>
						<option value="<?php echo esc_attr($page->ID);?>"<?php if($pg2==$page->ID) echo 'selected';?>><?php echo esc_attr($page->post_title);?></option><?php }?></select><i><code>[xyz_wp_cls_items]</code></i>
					</td>
				</tr>

				<tr>
					<td scope="row">Show categories without subcategory</td>
					<td><input type="checkbox" name="xyz_cls_category_view" value="1" <?php if($show_cat=="1") echo 'checked';?>></td>
				</tr>
		</table>
		<br>

		<table class="widefat xyz_cls_tab">
			<thead>
				<tr>
					<th><b>Manage Custom Fields</b></th>
					<th></th>
				</tr>
			</thead>
	<?php
			if(array_key_exists('custom-field-manager/custom-field-manager.php', get_plugins())){
				if(is_plugin_active('custom-field-manager/custom-field-manager.php')){
					$custom_groups = $wpdb->get_results($wpdb->prepare("SELECT id FROM ".$wpdb->prefix."xyz_cfl_group WHERE xyz_cfl_group_taxonomy=%s ",'xyz_cls_category'));
        			$count = count($custom_groups);

        			if($count==0){
     ?>
        				<tr>
        					<td>Click the button below to setup default custom fields for your listing categories.<br><input type="button" value="Install default custom fields for Classifieds" class="button-secondary" id="custom_button"></td>
        				</tr>
    <?php    				
        			}
        			else{
    ?>
        				<tr>
    						<td>Click the below link to manage your custom fields.<br><a href="<?php echo admin_url('?page=custom-field-manager-customfields');?>" class ="button-secondary">Manage Custom Fields</a></td>
						</tr>
	<?php
        			}
				}
				else{
	?>
						<tr>
						    <td>Click the below link to activate Custom Field Manager.<br><input type="button"  class ="button-secondary" value="Activate Custom Field Manager" id="activate_cfl">
						    </td>
						</tr>	
	<?php
				}
			}
			else{
				$action = 'install-plugin';
    			$slug = 'custom-field-manager';		
    ?>
    				<tr>
    					<td>You can use any plugin to manage your custom fields.We recommend Custom Field Manager Plugin.<a href="<?php echo  wp_nonce_url(add_query_arg(array('action' => $action,'plugin' => $slug),admin_url( 'update.php' )),$action.'_'.$slug);?>">Click Here </a>to install Custom Field Manager by
    					<a href="http://xyzscripts.com">XYZScripts</a>.</td>
					</tr>
    <?php		
			}
	?>
			<tr>
				<td></td>
			</tr>
		</table>
		
		<br>
		<table class="widefat xyz_cls_tab">
			<thead>
				<tr>
					<th><b>Cron Settings</b></th>
					<th></th>
				</tr>
			</thead>
				<tr>
					<td scope="row">Cron Command[once every hour]</td>
					<td><span style="color: #21759B"><?php echo 'wget -O /dev/null --quiet '.get_site_url().'/index.php?xyz_wp_cls=cron';?></span>
					</td>
				</tr>
		</table>

		<br>
<?php
	$sql=$wpdb->get_results( "SELECT * FROM `".$wpdb->prefix."xyz_cls_countries` ");
?>
		<table class="widefat xyz_cls_tab">
			<thead>
				<tr>
					<th><b>General Settings</b></th>
					<th></th>
				</tr>
			</thead>

			<tr>
				<td scope="row">Default country </td>
				<td><select  name='xyz_cls_default_country'>
					<?php foreach ($sql as $key ){?><option value="<?php echo esc_attr($key->ccode);?>"<?php  if($xyz_cls_sttng11== $key->ccode) echo "selected";?>><?php echo esc_attr($key->cname);?></option><?php }?></select>
				</td>
			</tr>

			<tr valign="top">
				<td scope="row" colspan="1">Enable credit link to author</td>
				<td>
					<select name="xyz_credit_link" id="xyz_credit_link" >
						<option value ="cls" <?php if($xyz_credit_link=='cls') echo 'selected'; ?> >Yes </option>
						<option value ="<?php echo $xyz_credit_link!='cls'?$xyz_credit_link:0;?>" <?php if($xyz_credit_link!='cls') echo 'selected'; ?> >No </option>
					</select>
				</td>
			</tr>
		</table>

		<br>
		<div style="left:400px;position:absolute;">
			<input type="submit" value="Update Settings"  class='xyz_cls_submit_button'  name="xyz_cls_submit">
		</div>
		<br>
	</form>
</div>

<script type="text/javascript">
   jQuery(document).ready(function(){
        var x=<?php echo get_option('xyz_cls_custom_role');?>;
        if(x==1)
            jQuery("#classifieds_user").show();
        else
            jQuery("#classifieds_user").hide();
        var y=<?php echo get_option('xyz_cls_login');?>;
        if(y==1){
            jQuery("#forgotpassword").hide();
            jQuery("#disablewppages").hide();
            jQuery("#loginpage").hide();
            jQuery("#notice1").hide();
        }
        else{
            jQuery("#forgotpassword").show();
            jQuery("#disablewppages").show();
            jQuery("#loginpage").show();
            jQuery("#notice1").show();
        }
        jQuery("#ryes").click(function(){
            jQuery("#classifieds_user").show();
        }
                             );
        jQuery("#rno").click(function(){
            jQuery("#classifieds_user").hide();
        }
                            );
        jQuery("#submit_button").click(function(){
            window.location.reload();
        }
                                      );
        jQuery("#xyz_cls_login").change(function(){
            if(jQuery("#xyz_cls_login").val()==1 ){
                jQuery("#forgotpassword").hide();
                jQuery("#disablewppages").hide();
                jQuery("#loginpage").hide();
                jQuery("#notice1").hide();
            }
            else{
                jQuery("#forgotpassword").show();
                jQuery("#disablewppages").show();
                jQuery("#loginpage").show();
                jQuery("#notice1").show();
            }
        }
                                       );
        jQuery('#system_notice_area').animate({
            opacity : 'show',
            height : 'show'
        }, 500);
        jQuery('#system_notice_area_dismiss').click(function() {
            jQuery('#system_notice_area').animate({
                opacity : 'hide',
                height : 'hide'
            } ,500);
        });
        
        jQuery("#custom_button").click(function(){
        	jQuery("#custom_button").attr("disabled", true);
        	jQuery('#custom_button').after(jQuery('<img>',{id:'xyz_ldng',src:'<?php echo plugins_url(XYZ_CLASSIFIEDS_DIR."/images/load.gif")?>'}))
            <?php $ajax_cls_nonce = wp_create_nonce('xyz-cls-cfg-cust');?>
            var dataString = {
                action: 'xyz_cls_configure_custom',
                security:'<?php echo $ajax_cls_nonce; ?>'
            };
            jQuery.post(ajaxurl, dataString, function(response) {
                if(response)
                    window.location.reload();
            });
        });
        
        jQuery("#activate_cfl").click(function(){
            <?php $ajax_cls_nonce = wp_create_nonce('xyz-cls-cfl-act');?>
            var dataString = {
                action: 'xyz_cls_activate_cfl',
                security:'<?php echo $ajax_cls_nonce; ?>'
            };
            jQuery.post(ajaxurl, dataString, function(response) {
                if(response)
                    window.location.reload();
            });
        });
    });

   	if(typeof xyz_cls_display_listing == 'undefined'){
		function xyz_cls_display_listing(){
			if(document.getElementById("premium_listing").value==1){
				document.getElementById("premium_items_tr").style.display="";
				document.getElementById("premium_period_tr").style.display="";
				document.getElementById("premium_price_tr").style.display="";
			}
			else{
				document.getElementById("premium_items_tr").style.display="none";
				document.getElementById("premium_period_tr").style.display="none";
				document.getElementById("premium_price_tr").style.display="none";
			}
		}
	}

</script>