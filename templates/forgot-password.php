<?php

if ( ! defined( 'ABSPATH' ) )
    exit;

xyz_cls_get_template_part('header');
global $wpdb;
$mail_error = $msg1 = "";

if(isset($_POST['xyz_cls_new_pass'])){
    if(!isset( $_REQUEST['_wpnonce'])|| !wp_verify_nonce( $_REQUEST['_wpnonce'], 'cls-mailver_' )){
        wp_nonce_ays( 'cls-mailver_' );
        exit;
    }
    if(isset($_POST['email'])){
    	$email = sanitize_email($_POST['email']);
        if(filter_var($email, FILTER_VALIDATE_EMAIL)){
            $res=$wpdb->get_row($wpdb->prepare("SELECT * FROM ".$wpdb->prefix."users WHERE  `user_email`=%s", $email));
            if(count($res)==0){
      			$msg1 = __('There is no user registered with that email address.','wp-classifieds-listings');
			}
            else{
                $uid=$res->ID;
$link=add_query_arg(array('xyz_wp_cls'=>'reset','xyz_wp_resetpassword'=>$uid.'_'.md5($uid.$email)),site_url());
                $username=$res->user_login;
                $header="Content-type:text/html;charset=UTF-8" . "rn";
                $sql="select * from ".$wpdb->prefix."xyz_cls_email_templates where id=%d";
                $row1=$wpdb->get_row($wpdb->prepare($sql, 9));
                $row1->sub=str_ireplace("{SITE_NAME}",get_bloginfo('name'),$row1->sub);
                $row1->body=str_ireplace("{USER_NAME}",$username,$row1->body);
                $row1->body=str_ireplace("{SITE_NAME}",get_bloginfo('name'),$row1->body);
                $row1->body=str_ireplace("{RESETPASS_LINK}",$link,$row1->body);
                wp_mail($email,$row1->sub,$row1->body,$header);
				$wp_get_login_page = get_option('xyz_wp_cls_register');
                wp_redirect(add_query_arg(array('msg'=>1),get_permalink($wp_get_login_page)));
                die;	
            }
        }
        else{
            $msg1 = __('Please provide a valid email address','wp-classifieds-listings');
        }
    }
}
if(isset($_POST['reset'])){
    if(!isset( $_REQUEST['_wpnonce'] ) || !wp_verify_nonce( $_REQUEST['_wpnonce'], 'cls-mailres_' )) {
        wp_nonce_ays( 'cls-mailres_' );
        exit;
    }
	if($_POST['pass1']=='' || $_POST['pass2']== ''){
		$msg1 = __('All fields are required','wp-classifieds-listings');
	}
	else if(strlen($_POST['pass1'])<7){
		$msg1 = __('Password should be at least seven characters long','wp-classifieds-listings');
	}	
	else if($_POST['pass1'] != $_POST['pass2']){
		$msg1 = __('Password Mismatch','wp-classifieds-listings');
	}
	else{
		$wpdb->update($wpdb->prefix."users", array( 'user_pass' => wp_hash_password($_POST['pass1'])),array('ID'=>$_POST['hid']));
    wp_redirect(esc_url(add_query_arg(array('msg'=>1),admin_url())));
    die;
	}
}
?>
<script>
    if(typeof isValidEmail == 'undefined'){
        function isValidEmail(emailText){
            var pattern = new RegExp(/^((([a-z]|d|[!#$%&'*+-/=?^_`{|}~]|[u00A0-uD7FFuF900-uFDCFuFDF0-uFFEF])+(.([a-z]|d|[!#$%&'*+-/=?^_`{|}~]|[u00A0-uD7FFuF900-uFDCFuFDF0-uFFEF])+)*)|((x22)((((x20|x09)*(x0dx0a))?(x20|x09)+)?(([x01-x08x0bx0cx0e-x1fx7f]|x21|[x23-x5b]|[x5d-x7e]|[u00A0-uD7FFuF900-uFDCFuFDF0-uFFEF])|(([x01-x09x0bx0cx0d-x7f]|[u00A0-uD7FFuF900-uFDCFuFDF0-uFFEF]))))*(((x20|x09)*(x0dx0a))?(x20|x09)+)?(x22)))@((([a-z]|d|[u00A0-uD7FFuF900-uFDCFuFDF0-uFFEF])|(([a-z]|d|[u00A0-uD7FFuF900-uFDCFuFDF0-uFFEF])([a-z]|d|-|.|_|~|[u00A0-uD7FFuF900-uFDCFuFDF0-uFFEF])*([a-z]|d|[u00A0-uD7FFuF900-uFDCFuFDF0-uFFEF]))).)+(([a-z]|[u00A0-uD7FFuF900-uFDCFuFDF0-uFFEF])|(([a-z]|[u00A0-uD7FFuF900-uFDCFuFDF0-uFFEF])([a-z]|d|-|.|_|~|[u00A0-uD7FFuF900-uFDCFuFDF0-uFFEF])*([a-z]|[u00A0-uD7FFuF900-uFDCFuFDF0-uFFEF]))).?$/i);
            return pattern.test(emailText);
        }
    }
    jQuery(document).ready(function(){
        jQuery("#fpass").submit(function(){
            if(jQuery('#email').val()=='')
            {
                alert("<?php _e('Please fill up mandatory fields','wp-classifieds-listings');?>");
                return false;
            }
            else if(!isValidEmail(jQuery('#email').val()))
            {
                alert("<?php _e('Invalid Email','wp-classifieds-listings');?>");
                return false;
            }
            else return true;
        });

        jQuery("#resetpass").submit(function(){
            if(jQuery('#pass1').val()==''||jQuery('#pass2').val()==''){
                alert("<?php _e('All fields are required','wp-classifieds');?>");
                return false;
            }
            else if(jQuery('#pass1').val().length < 7){
                alert("<?php _e('Password should be at least seven characters long','wp-classifieds-listings');?>");
                return false;
            }
            else if(jQuery('#pass1').val()!=jQuery('#pass2').val()){
                alert("<?php _e('Password Mismatch','wp-classifieds-listings');?>");
                return false;
            }
            else 
                return true;
        });

        jQuery("#login").submit(function(){
            if(jQuery('#user_login').val()=='' || jQuery('#user_pass').val()==''){
                alert("<?php _e('All fields are required','wp-classifieds-listings');?>");
                return false;
            }
            else 
                return true;
        });
		
		jQuery('#system_notice_area').animate({
            opacity : 'show',
            height : 'show'
        }, 500);
		
        jQuery('#system_notice_area_dismiss').click(function() {
            jQuery('#system_notice_area').animate({
                opacity : 'hide',
                height : 'hide'
            }, 500);
			
        });
    });
</script>
<?php
if(isset($_GET['resetpassword'])){
    $login=explode('_', $_GET['resetpassword']);
    $query="SELECT * FROM ".$wpdb->base_prefix."users WHERE  `ID`=%d";
    $res=$wpdb->get_row($wpdb->prepare($query, $login[0]));
    if($login[1]==md5($login[0].$res->user_email))
    {
?>
<div class="clear-fix"></div>
<div class="xyz_cls_registerForm" style="width:100%;">
    <form method="post" name="resetpass" id="resetpass" >
        <?php wp_nonce_field('cls-mailres_'); ?>
        <table  style="width: 100%;height: 200px;">
            <tr>
                <td colspan=3 style="width:auto;color:blue;">
                    <?php _e('Enter your new password below..','wp-classifieds-listings');?></td>
            </tr>
            <tr>
                <td>
                    <br>
                    <?php _e('New password','wp-classifieds-listings');?></td>
                <td>
                    <input name="pass1" type="password" id="pass1" value="">
                </td>
            </tr>
            <tr>
                <td>
                    <br>
                    <?php _e('Confirm new password','wp-classifieds-listings');?></td>
                <td>
                    <input name="pass2" type="password" id="pass2" value="">
                </td>
            </tr>
            <tr>
                <td>
                    <br>
                    <input name="hid" type="hidden" id="hid" value="<?php echo $login[0];?>">
                </td>
            </tr>
            <tr>
                <td>
                </td>
                <td>
                    <input class="button-primary" type="submit" name="reset" value="<?php _e('Reset Password','wp-classifieds-listings');?>">
                </td>
            </tr>
        </table>
    </form>
</div>
<?php 
    }
    else
        wp_redirect(site_url(),0);
}
else{?>
<div class="clear-fix">
</div>
<div class="xyz_cls_registerForm">
    <form class="form-inline" style="display: inline-block;text-align: center;" method="post" name="fpass" id="fpass" >
        <?php wp_nonce_field('cls-mailver_');?>
        <div class="col-lg-12 col-sm-12 col-md-12 col-xs-12" style="color:blue;">
            <?php _e('Please enter your email address. You will receive a link to create a new password via email.','wp-classifieds');?>
        </div>
        <div class="form-group">
            <?php _e('Email','wp-classifieds');?>
        </div>
        <div class="form-group">
            <input name="email" class="form-control" style="margin-top: 2px;" type="text" id="email" value="<?php if (isset($_POST['email']))echo esc_attr($_POST['email']);?>">
        </div>
        <div class="form-group">
            <button class="btn xyz_cls_btn_submit" type="submit" name="xyz_cls_new_pass"><?php _e('Get New Password','wp-classifieds-listings');?></button>
        </div>
        <div class="col-lg-12 col-sm-12 col-md-12 col-xs-12" style="color:red;">
            <?php echo $mail_error;?>
        </div>
    </form>
</div>
<?php 
    }

if((isset($_POST['reset']) && $msg1!="")||(isset($_POST['xyz_cls_new_pass'])&& $msg1!="")){
        $cl="system_notice_area_style0";
?>

<div class="<?php echo $cl;?>" id="system_notice_area" style="height:30px !important">
    <?php echo $msg1;?> &nbsp;&nbsp;&nbsp;
    <span id="system_notice_area_dismiss"><?php _e('Dismiss','wp-classifieds-listings');?></span>
</div>
<?php 
}
?>