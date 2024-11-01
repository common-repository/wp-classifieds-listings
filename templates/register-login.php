<?php
if ( ! defined( 'ABSPATH' ) )
     exit;

xyz_cls_get_template_part('header');
global $wpdb;

$username = $email = $user = $password = $rpassword = $xyz_login_error = $msg1 = '';
$flag = 0;$f = 0;

if(isset($_GET['msg'])&&intval($_GET['msg'])==1){
	$cl="system_notice_area_style1";
	$msg1 = __('Check your e-mail for the confirmation link.','wp-classifieds-listings');
?>
<div class="<?php echo $cl;?>" id="system_notice_area" style="height:30px !important">
    <?php echo $msg1;?> &nbsp;&nbsp;&nbsp;
    <span id="system_notice_area_dismiss"><?php _e('Dismiss','wp-classifieds-listings');?></span>
</div>
<?php
}
               
if(isset($_POST['signup'])){

    if (!isset( $_REQUEST['_wpnonce'] ) || !wp_verify_nonce( $_REQUEST['_wpnonce'],'cls-reg_')){
        wp_nonce_ays('cls-reg_');
        exit;
    }

    $username  = sanitize_text_field($_POST['username']);
    $email     = $_POST['email'];
    $password  = sanitize_text_field($_POST['pwd']);
    $rpassword = sanitize_text_field($_POST['rpwd']);

    if($username == '' || $email == '' || $password = '' || $rpassword = ''){
        $f=1;
        $msg1 = __('All fields are mandatory.','wp-classifieds-listings');
    }

    else if(!filter_var($email,FILTER_VALIDATE_EMAIL)){  
        $f=1;
        $msg1 = __(' Invalid Email','wp-classifieds-listings');
    }

    else if($_POST['pwd']!=$_POST['rpwd']){  
        $f=1;
        $msg1 = __('Password does not match.','wp-classifieds-listings');
    }

    else if(username_exists($username)){
        $f=1; 
        $msg1 = __('This username is already registered, please choose another one.','wp-classifieds-listings');
    }  

    else if(email_exists($email)){
        $f=1;
        $msg1 = __('This email is already registered, please choose another one.','wp-classifieds-listings');
    }  

    if($f!=1){
        $query="INSERT INTO ".$wpdb->base_prefix."users (`user_login`, `user_pass`,`user_nicename`,`user_email`,`user_registered`,`display_name`) VALUES ( %s, %s, %s, %s, %s, %s)";
        $wpdb->query($wpdb->prepare($query, $username,wp_hash_password($_POST['pwd']),$username,$email,date('Y-m-d h:i:s',time()),$username));

        $uid=$wpdb->insert_id;
        add_user_meta($uid, 'xyz_wp_user_status', -1);
        $usr = new WP_User( $uid );
        
        // Add role
        if(get_role('classifieds_user') && get_option('xyz_cls_custom_role'))
            $usr->add_role('classifieds_user');
        else
            $usr->add_role(get_option('default_role')); 

        $header="Content-type:text/html;charset=UTF-8" . "rn";
            $link=add_query_arg(array('xyz_wp_cls'=>'verify','xyz_wp_login'=>$uid.'_'.md5($uid.$email)),site_url());
        $sql="select * from ".$wpdb->prefix."xyz_cls_email_templates where id=%d";
        $row1=$wpdb->get_row($wpdb->prepare($sql, 7));
        $row1->sub=str_ireplace("{SITE_NAME}",get_bloginfo('name'),$row1->sub);
        $row1->body=str_ireplace("{USER_NAME}",$username,$row1->body);
        $row1->body=str_ireplace("{SITE_NAME}",get_bloginfo('name'),$row1->body);
        $row1->body=str_ireplace("{LOGIN_LINK}",$link,$row1->body);
                    
        wp_mail($email,$row1->sub,$row1->body,$header);
        $msg1 = __('Your registration was successful. Please check your email to proceed','wp-classifieds-listings');
        $username = $email = $user = $password = $rpassword='';         
    }
}

if(isset($_POST['signup']) && $msg1!=""){
    if($f==0)
        $cl="system_notice_area_style1";
    else if($f==1)
        $cl="system_notice_area_style0";
?>

<div class="<?php echo $cl;?>" id="system_notice_area" style="height:30px !important">
    <?php echo $msg1;?> &nbsp;&nbsp;&nbsp;
    <span id="system_notice_area_dismiss"><?php _e('Dismiss','wp-classifieds-listings');?></span>
</div>
<?php 
}

if(isset($_POST['signin'])){
    if (!isset( $_REQUEST['_wpnonce'] ) || !wp_verify_nonce( $_REQUEST['_wpnonce'],'cls-sign_')){
        wp_nonce_ays('cls-sign_');
        exit;
    }

    $user     = sanitize_text_field($_POST['user_login']);
    $password1= sanitize_text_field($_POST['password1']);

    if($user==''||$password1==''){
       echo  "<div style='color:black;background-color: #ffebe8;border: 1px solid #dd3c10;'>".__('All fields are mandatory','wp-classifieds-listings')." </div>";
       $flag=1;
    }
    if($flag==0){
        $creds = array();
        $creds['user_login'] = $user;
        $creds['user_password'] = $password1;
        $userdata=get_user_by('login',$user);
        
        if($userdata!=''){ 
            if(metadata_exists('user', $userdata->data->ID, 'xyz_wp_user_status')==true){   
                if(get_user_meta($userdata->data->ID,'xyz_wp_user_status',true)==1){    
                    if(! wp_check_password($password1, $userdata->data->user_pass, $userdata->ID)){
                        $msg1 = __('Invalid username or password','wp-classifieds-listings');
                        $flag=1;
                    }
                    else{
                        $user_info = wp_signon( $creds, false );
                        wp_redirect(admin_url());
                    }
                }
                else 
                    $msg1 = __('Your account has not been activated','wp-classifieds-listings');
            }
            else{
                if(! wp_check_password( $password1, $userdata->data->user_pass, $userdata->ID)){
                    $msg1 = __('Invalid username or password','wp-classifieds-listings');
                }
                else{
                    $user_info = wp_signon( $creds, false );
                    wp_redirect(admin_url());
                }
            }
        }
        else 
            $msg1 = __('Invalid user','wp-classifieds-listings');
    }
}

if(isset($_POST['signin']) && $msg1!=""){
    if($f==1)
        $cl="system_notice_area_style1";
    else if($f==0)
        $cl="system_notice_area_style0";
?>

<div class="<?php echo $cl;?>" id="system_notice_area" style="height:30px !important">
    <?php echo $msg1;?> &nbsp;&nbsp;&nbsp;
    <span id="system_notice_area_dismiss"><?php _e('Dismiss','wp-classifieds-listings');?></span>
</div>
<?php 
}
?>

<script type="text/javascript">

function isValidEmail(emailText){
    var pattern = new RegExp(/^((([a-z]|d|[!#$%&'*+-/=?^_`{|}~]|[u00A0-uD7FFuF900-uFDCFuFDF0-uFFEF])+(.([a-z]|d|[!#$%&'*+-/=?^_`{|}~]|[u00A0-uD7FFuF900-uFDCFuFDF0-uFFEF])+)*)|((x22)((((x20|x09)*(x0dx0a))?(x20|x09)+)?(([x01-x08x0bx0cx0e-x1fx7f]|x21|[x23-x5b]|[x5d-x7e]|[u00A0-uD7FFuF900-uFDCFuFDF0-uFFEF])|(([x01-x09x0bx0cx0d-x7f]|[u00A0-uD7FFuF900-uFDCFuFDF0-uFFEF]))))*(((x20|x09)*(x0dx0a))?(x20|x09)+)?(x22)))@((([a-z]|d|[u00A0-uD7FFuF900-uFDCFuFDF0-uFFEF])|(([a-z]|d|[u00A0-uD7FFuF900-uFDCFuFDF0-uFFEF])([a-z]|d|-|.|_|~|[u00A0-uD7FFuF900-uFDCFuFDF0-uFFEF])*([a-z]|d|[u00A0-uD7FFuF900-uFDCFuFDF0-uFFEF]))).)+(([a-z]|[u00A0-uD7FFuF900-uFDCFuFDF0-uFFEF])|(([a-z]|[u00A0-uD7FFuF900-uFDCFuFDF0-uFFEF])([a-z]|d|-|.|_|~|[u00A0-uD7FFuF900-uFDCFuFDF0-uFFEF])*([a-z]|[u00A0-uD7FFuF900-uFDCFuFDF0-uFFEF]))).?$/i);
    return pattern.test(emailText);
}

jQuery(document).ready(function(){
    jQuery("#register").submit(function(){
        if(jQuery('#username').val()==''||jQuery('#email').val()==''||jQuery('#pwd').val()==''||jQuery('#rpwd').val()==''){
            alert("<?php _e('All fields are required','wp-classifieds-listings');?>");
            return false;
        }
        else if(!isValidEmail(jQuery('#email').val())){ 
            alert("<?php _e('Invalid Email','wp-classifieds-listings');?>");
            return false;
        }
        else if(jQuery('#pwd').val().length < 7){
            alert("<?php _e('Password should be at least seven characters long','wp-classifieds');?>");
            return false;
        }
        else if(jQuery('#pwd').val()!=jQuery('#rpwd').val()){
            alert("<?php _e('Password Mismatch','wp-classifieds-listings');?>");
            return false;
        }
        else 
            return true;
    });

    jQuery("#login").submit(function(){
        if(jQuery('#user_login').val()=='' || jQuery('#user_pass').val()=='')
        {
              alert("<?php _e('All fields are required','wp-classifieds-listings');?>");
            return false;
         }

        else return true;
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

<div class="clear"></div>

<div class="container xyz_cls_loginpage" style="margin-top:20px;">
    <div class="row">
        <div class="col-lg-4 col-sm-6 col-md-6 col-xs-12">
            <div class="xyz_cls_loginForm col-lg-12 col-md-12 col-sm-12 col-xs-12">
                <form method="post" name="login" id="login" >
                    <?php wp_nonce_field('cls-sign_');?>
                    <h1><?php _e('Sign In','wp-classifieds-listings');?></h1>
                    <div class="form-group">
                        <label><?php _e('Username','wp-classifieds-listings');?><span class="xyz_mandatory">*</span></label>
                        <input class="form-control" type="text" name="user_login" id="user_login" value="<?php echo $user;?>" onkeyup="this.value = this.value.replace(/[^a-zA-Z0-9]/g, '')">
                    </div>
                    <div class="form-group">
                        <label><?php _e('Password','wp-classifieds-listings');?><span class="xyz_mandatory">*</span></label>
                        <input class="form-control" type="password" name="password1" id="user_pass" value="">
                    </div>
                    <div class="form-group" style="margin-bottom: 5px !important;">
                        <a href="<?php echo get_permalink(get_option('xyz_wp_cls_forgotpassword'));?>"><?php _e('Lost your password ?','wp-classifieds-listings');?></a>
                    </div>
                    <div class="xyz_login_error">
                        <?php echo esc_attr($xyz_login_error);?>
                    </div>
                    <div class="form-group">
                        <input class="xyz_cls_login_btn" type="submit" name="signin" value="<?php _e('Sign In','wp-classifieds-listings');?>">
                    </div>
                </form>
            </div>
        </div>

        <div class="col-lg-8 col-sm-6 col-md-6 col-xs-12">
            <div class="xyz_cls_registerForm col-lg-12 col-sm-12 col-md-12 col-xs-12">
                <div class="col-lg-12">
                    <h1>
                        <?php _e('Sign Up Now','wp-classifieds-listings');?> 
                    </h1>
                    <?php _e('All fields are mandatory');?>
                </div>
                <form  method="post" name="register" id="register">
                    <?php wp_nonce_field('cls-reg_');?>
                    <div class="col-lg-6">
                        <div class="form-group">
                            <label><?php _e('Username','wp-classifieds-listings');?></label>
                            <input class="form-control" name="username" type="text" id="username" value="<?php echo $username;?>" onkeyup="this.value = this.value.replace(/[^a-zA-Z0-9]/g,'')">
                        </div>
                        <div class="form-group">
                            <label><?php _e('Email','wp-classifieds-listings');?></label>
                            <input class="form-control" name="email" type="text" id="email" value="<?php echo $email;?>" />
                        </div>
                    </div>
                    <div class="col-lg-6">
                        <div class="form-group">
                            <label><?php _e('Password','wp-classifieds-listings');?></label>
                            <input class="form-control" name="pwd" type="password" id="pwd" value="">
                        </div>
                        <div class="form-group">
                            <label><?php _e('Confirm Password','wp-classifieds-listings');?></label>
                            <input class="form-control" name="rpwd" type="password" id="rpwd" value="">
                        </div>
                    </div>
                    <div class="col-lg-12">
                        <div class="form-group">
                            <input class="xyz_cls_register_btn" type="submit" name="signup" value="<?php _e('Sign Up','wp-classifieds-listings');?>" id="signup">
                        </div>
                    </div>
                </form>
            </div>
        </div>

    </div>
</div>
