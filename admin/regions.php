<?php
if ( ! defined( 'ABSPATH' ) )
    exit;

global $wpdb;
$xyz_cls_rc=$msg2 = $msg3 = "";
$sql=$wpdb->get_results( "SELECT  * FROM `".$wpdb->prefix."xyz_cls_countries` ");
$_POST = stripslashes_deep($_POST);
$_GET = stripslashes_deep($_GET);

if(isset($_POST['xyz_cls_region']))
    $xyz_cls_region =sanitize_text_field($_POST['xyz_cls_region']);

if(isset($_POST['xyz_cls_rcode']))
    $xyz_cls_rcode  = sanitize_text_field($_POST['xyz_cls_rcode']);

if(isset($_POST['xyz_cls_country'])){
    $xyz_cls_rc=sanitize_text_field($_POST['xyz_cls_country']);
}
if(isset($_POST) && isset($_POST['xyz_cls_submit_reg'])){
    if (!isset( $_REQUEST['_wpnonce']) || !wp_verify_nonce( $_REQUEST['_wpnonce'],'cls-region_')){
        wp_nonce_ays( 'cls-region_' );
        exit;
    }
    else{
        $f=1;$msg1="";
        if($_POST['xyz_cls_rcode']!=""){
            if(strlen($_POST['xyz_cls_rcode'])>2){
                $msg2="Region code limit exceeded..!";
                $f=0;
            }
        }
        if($_POST['xyz_cls_rcode']==""){
            $msg1="Please fill up the mandatory fields..!";
            $f=0;
        }
        if($_POST['xyz_cls_region']!=""){
            if(!preg_match('/^[a-zA-Z\s]+$/', $xyz_cls_region)){
                $f=0;
                $msg3="Region name must be in alphabets!";
            }
        }
        if($_POST['xyz_cls_region']==""){
            $msg1="Please fill up the mandatory fields..!";
            $f=0;
        }
        $sql1=$wpdb->get_results($wpdb->prepare( "SELECT  sname FROM `".$wpdb->prefix."xyz_cls_states` WHERE ccode=%s AND scode=%s",$xyz_cls_rc,$xyz_cls_rcode));
        if(count($sql1)!=0){
            $msg1="You have already entered the same region name/region code for this country. ";
            $f=0;
        }
        if($f==1){
            if(isset($_POST['xyz_cls_rcode'])&&isset($_POST['xyz_cls_region'])){
                $wpdb->query($wpdb->prepare("INSERT INTO `".$wpdb->prefix."xyz_cls_states` (`id`, `scode`, `sname`, `ccode`)VALUES (%d, %s, %s, %s)",NULL,sanitize_text_field($_POST['xyz_cls_rcode']),sanitize_text_field($_POST['xyz_cls_region']),sanitize_text_field($_POST['xyz_cls_country'])));
            }
            $msg1=" You have successfully added the new region.";
        }
    }
}
?>
<div style="clear: both;"></div>

<div class='wrap'>
    <div id="div_region">
        <form name="xyz_cls_add_region" method="post" >
            <?php wp_nonce_field('cls-region_');?>
            <h3>Add Region</h3>
            <h4>All fields marked <span style=" color:red">*</span> are mandatory</h4>
            <table  class="widefat xyz_cls_tab"  style="width:98%;">
                <tr>
                    <td></td>
                </tr>
                <tr>
                    <td style="width:50%;">Select Country</td>
                    <td style="width:50%;">
                        <select id="country_dropdown_reg" name='xyz_cls_country'>
                            <?php foreach ($sql as $key ){?>
                            <option value="<?php echo $key->ccode;?>"<?php if($xyz_cls_rc== $key->ccode)echo "selected";?>>
                                <?php echo esc_attr($key->cname);?></option><?php }?>
                        </select>
                    </td>
                    <td>
                        <div id="ldng" style="display: none;">
                            <img src="<?php echo plugins_url(XYZ_CLASSIFIEDS_DIR."/images/ldng.gif")?>"/>
                        </div>
                    </td>
                </tr>
                <tr>
                    <td style="width:50%;">Region Name</td>
                    <td style="width:50%;">
                        <input type="text" name="xyz_cls_region" id="xyz_cls_region" value="<?php ?>">
                        <span style=" color:red">*</span>
                    </td>
                    <td>
                        <p style="color:red;" id="error"><?php echo $msg3;?></p>
                    </td>
                </tr>
                <tr>
                    <td style="width:50%;">Region Code</td>
                    <td style="width:50%;">
                        <input type="text" name="xyz_cls_rcode" id="xyz_cls_rcode" value="<?php ?>">
                        <span style=" color:red">*</span>
                    </td>
                    <td>
                        <p style="color:red;" id="error"><?php echo $msg2;?></p>
                    </td>
                </tr>
                <tr>
                    <td style="width:50%;" align="center">
                        <input type="submit" value="Add Region" name="xyz_cls_submit_reg" id="xyz_cls_submit_reg" class="xyz_cls_submit_button" onclick="load_regions()" >
                    </td>
                </tr>
            </table>
        </form>

        <div style="clear: both;"></div>
        <br>
        <h3>Manage Regions</h3>
        <div id="d1"></div>
        <br>
        <table id="manage_reg" class="widefat"  style="width:98%;">
            <thead>
                <tr>
                    <th><b><?php _e('Region Name','wp-classifieds-listings');?></b></th>
                    <th><b><?php _e('Region code','wp-classifieds-listings');?></b></th>
                    <th></th>
                    <th><b><?php _e('Action','wp-classifieds-listings');?></b></th>
                    <th></th>
                </tr>
            </thead>
            <tfoot>
                <tr>
                    <th><b><?php _e('Region Name','wp-classifieds-listings');?></b></th>
                    <th><b><?php _e('Region code','wp-classifieds-listings');?></b></th>
                    <th></th>
                    <th><b><?php _e('Action','wp-classifieds-listings');?></b></th>
                    <th></th>
                </tr>
            </tfoot>
            <tbody>
                <tr>
                    <td><br>No region found.</td>
                </tr>
            </tbody>
        </table>
    </div>
</div>

<?php
if(isset($_POST['xyz_cls_submit_reg']) && $msg1!=""){
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
?>

<script type="text/javascript">
    jQuery(document).ready(function(){
        jQuery("#country_dropdown_reg").change(function(){
            document.getElementById("ldng").style.display="";
            <?php $ajax_cls_nonce = wp_create_nonce('xyz-cls-reg-load');?>
            var dataString = {
                action: 'xyz_cls_load_region',
                security:'<?php echo $ajax_cls_nonce;?>',
                ccode1: jQuery("#country_dropdown_reg").val()
            };
            jQuery.post(ajaxurl, dataString, function(response) {
                document.getElementById("ldng").style.display="none";
                if(response!=0)
                    jQuery("#manage_reg").html(response);
                else alert("No State Found");
            });
        });
        <?php $ajax_cls_nonce = wp_create_nonce('xyz-cls-reg-load');?>
        var dataString = {
            action: 'xyz_cls_load_region',
            security:'<?php echo $ajax_cls_nonce;?>',
            ccode1:  jQuery("#country_dropdown_reg").val()
        };
        jQuery.post(ajaxurl, dataString, function(response) {
            if(response!=0)
                jQuery("#manage_reg").html(response);
            else alert("No State Found");
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

    if(typeof xyz_cls_edit_region == 'undefined'){
        function xyz_cls_edit_region(id){
            document.getElementById("button_edit"+id).style.display="none";
            document.getElementById("button_update"+id).style.display="";
            document.getElementById("button_cancel"+id).style.display="";
            document.getElementById("state"+id).readOnly=false;
            document.getElementById("scode"+id).readOnly=false;
            document.getElementById("state"+id).style.border="1px solid black";
            document.getElementById("scode"+id).style.border="1px solid black";
        }
    }

    if(typeof xyz_cls_cancel_region == 'undefined'){
        function xyz_cls_cancel_region(id){
            document.getElementById("button_edit"+id).style.display="";
            document.getElementById("button_update"+id).style.display="none";
            document.getElementById("button_cancel"+id).style.display="none";
            document.getElementById("load"+id).style.display="none";
            document.getElementById("state"+id).readOnly=true;
            document.getElementById("state"+id).style.border="0px";
            document.getElementById("state"+id).value=document.getElementById("statetemp"+id).value;
            document.getElementById("scode"+id).readOnly=true;
            document.getElementById("scode"+id).style.border="0px";
            document.getElementById("scode"+id).value=document.getElementById("scodetemp"+id).value;
        }
    }

    if(typeof xyz_cls_delete_region == 'undefined'){
        function xyz_cls_delete_region(id){
            if(confirm('Do you really want to delete this region ?')){
                jQuery(document).ready(function(){
                    <?php $ajax_cls_nonce = wp_create_nonce('xyz-cls-reg-del');?>
                    var dataString = {
                        action:'del_reg',
                        security:'<?php echo $ajax_cls_nonce;?>',
                        enable:id
                    };
                    jQuery.post(ajaxurl, dataString, function(response) {
                        jQuery("#d1").html('<b style="color:green">Successfully deleted </b>');
                        jQuery("#tr_id_"+id).fadeOut();
                    });
                });
            }
        }
    }

    if(typeof xyz_cls_update_region == 'undefined'){
        function xyz_cls_update_region(id){
            state=jQuery("#state"+id).val();
            scode=jQuery("#scode"+id).val();
            document.getElementById("load"+id).style.display="";
            if(state!="" && scode!="" &&  scode!="00" && scode!="0"){
                jQuery(document).ready(function(){
                    <?php $ajax_cls_nonce = wp_create_nonce('xyz-cls-reg-upt');?>   
                    var dataString = {
                        action: 'update_reg',
                        enable: id,
                        security:'<?php echo $ajax_cls_nonce;?>',
                        state:jQuery("#state"+id).val(),
                        scode:jQuery("#scode"+id).val(),
                        ccode:jQuery("#country_dropdown_reg").val()
                    };
                    jQuery.post(ajaxurl, dataString, function(response) {
                        if(response==1){
                            jQuery("#d1").html('<b style="color:green">Successfully updated </b>');
                            document.getElementById("load"+id).style.display="none";
                            document.getElementById("button_edit"+id).style.display="";
                            document.getElementById("button_update"+id).style.display="none";
                            document.getElementById("button_cancel"+id).style.display="none";
                            document.getElementById("state"+id).readOnly=true;
                            document.getElementById("state"+id).style.border="0px solid black";
                            document.getElementById("statetemp"+id).value=state;
                            document.getElementById("scode"+id).readOnly=true;
                            document.getElementById("scode"+id).style.border="0px solid black";
                            document.getElementById("scodetemp"+id).value=scode;
                        }
                        else
                            jQuery("#d1").html('<b style="color:red">You have already entered the same region name/region code for this country. </b>');
                    });
                });
            }
            else{
                alert("Please fill up the mandatory fields..!");
                jQuery("#state"+id).focus();
            }
        }
    }
</script>
    
<script type="text/javascript">
    jQuery(document).ready(function(){
        jQuery("#xyz_cls_rcode").keydown(function(){
            jQuery("#xyz_cls_rcode").attr('maxlength',2)
        });
    });
</script>
