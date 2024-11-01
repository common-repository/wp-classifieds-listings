<?php
if ( ! defined( 'ABSPATH' ) )
    exit;

global $wpdb;
$_POST = stripslashes_deep($_POST);
$f=1;$msg1="";

if(isset($_POST) && isset($_POST['submit'])){
    if (! isset( $_REQUEST['_wpnonce'] ) || ! wp_verify_nonce( $_REQUEST['_wpnonce'], 'cls-email_' )){
        wp_nonce_ays( 'cls-email_' );
        exit;
    }
    else{
        for($x=0;$x<9;$x++){
            if($_POST['sub'.$x]==""||$_POST['body'.$x]==""){
                $msg1="Please fill up the mandatory fields.";
                $f=0;
            }			
        }
        if($f!=0){
            $res = $wpdb->get_results( "SELECT * FROM `".$wpdb->prefix."xyz_cls_email_templates` ");
            if(count($res)==0){	
                for($z=0;$z<9;$z++){
                    $tid = $z+1;
                    $wpdb->insert( $wpdb->prefix.'xyz_cls_email_templates', 
                                  array(
                                      'id'  => $tid,
                                      'sub' => $_POST['sub'.$z],
                                      'body'=> $_POST['body'.$z]
                                  ),
                                  array('%d','%s', '%s' )
                                 );
                }
            }
            else{
                for($x=0;$x<9;$x++){
                    $tid=$x+1;
                    $wpdb->update($wpdb->prefix.'xyz_cls_email_templates',
                                  array(
                                      'sub'=> $_POST['sub'.$x],
                                      'body'=>$_POST['body'.$x]
                                  ),
                                  array(  'id'=>$tid ),
                                  array( '%s','%s'), 
                                  array(  '%d' ) 
                                 );
                }	
            }
            $msg1=" Settings updated successfully";
        }	
        if( $msg1!=""){
            if($f==0)
                $cl="system_notice_area_style0";
            else if($f==1)
                $cl="system_notice_area_style1";
?>
<div class="<?php  echo $cl; ?>" id="system_notice_area">
    <?php echo $msg1;?> &nbsp;&nbsp;&nbsp; <span id="system_notice_area_dismiss">Dismiss</span>
</div>
<?php  
        }
    }
}

$settings = array( 'media_buttons' => false,'textarea_rows' => 8 );
$content="";
?>
<div class='wrap'>
    <h3>Email Template Settings</h3>
    <h4>All fields marked <span style=" color:#ff0000">*</span> are mandatory</h4>
    <form name="item_settings" method="post" id="item_settings" enctype="multipart/form-data" >
        <?php wp_nonce_field('cls-email_');?>
        <table  id="settings_table"  class="widefat xyz_cls_tab"  style="width:100%;">
<?php 
$sql = $wpdb->get_results( "SELECT * FROM `".$wpdb->prefix."xyz_cls_email_templates` ");
$lbl_sub = array("Listing Approved (Subject)","Listing Rejected (Subject)","New Response (Subject)","IPN Success:- Admin(Subject)","Payment Success:- User (Subject)","IPN Failed:- Admin(Subject)","Registration (Subject)","Payment Failed:- User(Subject)","Reset Password (Subject)"); 
$lbl_bdy = array("Listing Approved (Body)","Listing Rejected (Body)","New Response (Body)","IPN Success:- Admin(Body)","Payment Success:- User(Body)","IPN Failed:- Admin(Body)","Registration (Body)","Payment Failed:- User(Body)","Reset Password (Body)");
?>
            <thead>
                <tr>
                    <th><b>Email Settings</b></th>
                    <th></th>
                </tr>
            </thead>
<?php 
$i = 0;
$content="";
foreach ($sql as $val){
?>
            <tr valign="top">
                <td scope="row">
                    <label for="<?php echo"sub".$i;?>"><?php echo $lbl_sub[$i];?></label>
                    <span style=" color:#ff0000"> * </span>
                </td>
                <td scope="row">
                    <input size="30" style="width:100%" type="text" name="<?php echo "sub".$i;?>" id="<?php echo "sub".$i;?>"
                    value="<?php if(isset($_POST['sub'.$i])&& $_POST['sub'.$i]!=""){echo esc_attr($_POST['sub'.$i]);}else{echo esc_attr($val->sub);}?>" placeholder="Message">
                </td>
            </tr>
            <tr>
                <td scope="row"><?php echo $lbl_bdy[$i];?><span style=" color:#ff0000"> * </span>
                </td>

                <td scope="row">
<?php 
    if(isset($_POST['body'.$i])&&$_POST['body'.$i]!=""){
        $content = $_POST['body'.$i];
    }
    else{
        if($val->body!="")
            $content = $val->body;
    }
    $editor_id = 'body'.$i;
    wp_editor( $content, $editor_id , $settings);
?>
                </td>
            </tr>
<?php
    $i++;
}
?>
        </table>
        <br>
        <div style="left:400px;position:absolute;">
            <input type="submit" value="Update Settings"  class='xyz_cls_submit_button'  name="submit" >
        </div>
        <br>
        <br>
    </form>
</div>
