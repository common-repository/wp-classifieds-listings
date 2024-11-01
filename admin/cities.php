<?php
if (! defined('ABSPATH'))
    exit; 

global $wpdb;
$_POST = stripslashes_deep($_POST);
$_GET = stripslashes_deep($_GET);
$city_error=""; 
$xyz_cls_cc="";
$xyz_cls_sn="";
$xyz_cls_countryname="";
$upload_dir = wp_upload_dir();

if(isset( $_POST['xyz_cls_countryname']))
    $xyz_cls_countryname= sanitize_text_field($_POST['xyz_cls_countryname']);

$sql=$wpdb->get_results( "SELECT  * FROM `".$wpdb->prefix."xyz_cls_countries`");
if(isset($_POST['xyz_cls_cityname']))
    $xyz_cls_cityname=sanitize_text_field($_POST['xyz_cls_cityname']);


if(isset($_POST['xyz_cls_countryname']))
    $xyz_cls_cc=sanitize_text_field($_POST['xyz_cls_countryname']);

if(isset($_POST['xyz_cls_statename'])){
    if(sanitize_text_field($_POST['xyz_cls_statename'])!=-1)
        $xyz_cls_sn=sanitize_text_field($_POST['xyz_cls_statename']);
}

$result1=$wpdb->get_results($wpdb->prepare("SELECT *  FROM `".$wpdb->prefix."xyz_cls_states` WHERE scode= %s AND ccode=%s",$xyz_cls_sn,$xyz_cls_countryname));

foreach ($result1 as $citycheck)
    $xyz_cls_editstate=$citycheck->sname;

if(isset($_POST) && isset($_POST['xyz_cls_submit_city'])){
    if (!isset($_REQUEST['_wpnonce'] )||!wp_verify_nonce( $_REQUEST['_wpnonce'], 'cls-city_' )){
        wp_nonce_ays( 'cls-city_' );
        exit;
    }
        $f=1;$msg1="";
        if($_POST['xyz_cls_countryname']==-1){
            $msg1="Please fill up the mandatory fields..!";
            $f=0;
        }

        if(!empty($_POST['xyz_cls_cityname'])){  
            if(!preg_match ("/^[a-zA-Z\s]+$/",$xyz_cls_cityname)){
                $f=0;
                $city_error = "City name must not contain any special characters";
            }
        }

        if($_POST['xyz_cls_cityname']==""){
            $msg1="Please fill up the mandatory fields..!";
            $f=0;
        }

        global $wpdb;
        $sql1=$wpdb->get_results($wpdb->prepare( "SELECT  * FROM `".$wpdb->prefix."xyz_cls_cities` WHERE city=%s AND scode=%s ",sanitize_text_field($_POST['xyz_cls_cityname']),sanitize_text_field($_POST['xyz_cls_statename'])));

        if(count($sql1)>0){
            $msg1="Specified city is already added for this country. ";
            $f=0;
        }

        if($f==1){
            $wpdb->query($wpdb->prepare("INSERT INTO `".$wpdb->prefix."xyz_cls_cities` (`id`, `ccode`, `city`, `scode`) VALUES (%d, %s, %s, %s)",NULL,sanitize_text_field($_POST['xyz_cls_countryname']),sanitize_text_field($_POST['xyz_cls_cityname']),sanitize_text_field($_POST['xyz_cls_statename'])));
            $msg1="City added successfully";
        }
    
}
?>

<div class='wrap'>
    <form name="xyz_cls_add_region" method="post" >
        <?php wp_nonce_field('cls-city_');?>
        <h3><?php _e('Add Cities','wp-classifieds-listings');?></h3>
        <h4>All fields marked <span style="color:red">*</span>are mandatory</h4>
        <table class="widefat xyz_cls_tab"  style="width:100%;">
            <tr>
                <td>
                    <input type="hidden" value="<?php echo esc_attr($xyz_cls_sn);?>" name="hstate" id="hstate1">
                </td>
            </tr>
            <tr>
                <td style="width:50%;">
                    <?php _e('Select Country','wp-classifieds-listings');?> 
                </td>
                <td style="width:50%;">
                    <select id="country_dropdown" name='xyz_cls_countryname'>
                        <option value="-1"><?php _e('---Select---','wp-classifieds-listings');?> </option>
                        <?php foreach ($sql as $key ){?>
                        <option value="<?php echo esc_attr($key->ccode);?>"<?php if($xyz_cls_cc==$key->ccode)echo "selected";?>>
                            <?php echo esc_attr($key->cname);?></option>
                        <?php }?>
                    </select>
                    <span style=" color:red">*</span>
                </td>
                <td>
                    <div id="ldng" style="display: none;">
                        <img src="<?php echo plugins_url(XYZ_CLASSIFIEDS_DIR."/images/ldng.gif")?>"/>
                    </div>
                </td>
            </tr>
            <?php  
    if(($xyz_cls_sn!=-1)){
        $states=$wpdb->get_results($wpdb->prepare("SELECT *  FROM `".$wpdb->prefix."xyz_cls_states` WHERE ccode=%s",$xyz_cls_cc));
            ?>  
            <tr>
                <td style="width:50%;">
                    <?php _e('Select State','wp-classifieds-listings');?> 
                </td>
                <td style="width:50%;">
                    <select id="state_dropdown" name="xyz_cls_statename">
                        <option value="-1"><?php _e('---Select---','wp-classifieds-listings');?> </option>
                        <?php foreach ($states as $st)
            {?>
                        <option value="<?php echo esc_attr($st->scode);?>"<?php if($xyz_cls_sn==$st->scode) echo'selected';?>>
                            <?php  echo esc_attr($st->sname);?></option>
                        <?php }?>
                    </select>
                    <!-- <span style=" color:red">*</span> -->
                </td>
                <td>
                    <div id="ldng1" style="display: none;">
                        <img src="<?php echo plugins_url(XYZ_CLASSIFIEDS_DIR."/images/ldng.gif")?>"/>
                    </div>
                </td>
            </tr>
            <?php
    }
    else{
            ?><tr>
                <td style="width:50%;">
                    <?php _e('Select State','wp-classifieds-listings');?> 
                </td>
                <td style="width:50%;">
                    <select id="state_dropdown" name="xyz_cls_statename">
                        <option value="-1"><?php _e('---Select---','wp-classifieds-listings');?> </option>
                    </select>
                    <!-- <span style=" color:red">*</span> -->
                </td>
                <td>
                    <div id="ldng1" style="display: none;">
                        <img src="<?php echo plugins_url(XYZ_CLASSIFIEDS_DIR."/images/ldng.gif")?>"/>
                    </div>
                </td>
            </tr>
            <?php  
        }?>
            <tr>
                <td>
                    <?php _e('City Name','wp-classifieds-listings');?></td>
                <td>
                    <input type="text" name="xyz_cls_cityname" >
                    <span style=" color:red">*</span>
                    <p style="color:red;" id="error">
                        <?php echo esc_attr($city_error);?></p>
                </td>
               <!--  <td>
                    <p style="color:red;" id="error">
                        <?php echo esc_attr($city_error);?></p>
                </td> -->
            </tr>
            <tr>
                <td align="center">
                    <input type="submit" value="Add City" name="xyz_cls_submit_city" id="xyz_cls_submit_city" class="xyz_cls_submit_button">
                </td>
            </tr>
        </table>
    </form>
    
    <div style="clear: both;"></div>
    <br>
    <h3><?php _e('Manage Cities','wp-classifieds-listings');?></h3>
    <div id="d1">
    </div>
    <br>
    <table id="manage_div" class="widefat"  style="width:100%;">
        <thead>
            <tr>
                <th><b><?php _e('City Name','wp-classifieds-listings');?></b></th>
                <th><b><?php _e('Action','wp-classifieds-listings');?></b></th>
            </tr>
        </thead>
        <tfoot>
            <tr>
                <th><b><?php _e('City Name','wp-classifieds-listings');?></b></th>
                <th><b><?php _e('Action','wp-classifieds-listings');?></b></th>
            </tr>
        </tfoot>
        <tbody>
            <tr>
                <td>
                    <br><b><?php _e('No city found','wp-classifieds-listings');?></b>
                </td>
            </tr>
        </tbody>
    </table>
</div>
<?php

if(isset($_POST['xyz_cls_submit_city']) && $msg1!=""){
    if($f==0)
        $cl="system_notice_area_style0";
    else if($f==1)
        $cl="system_notice_area_style1";
?>

<div class="<?php echo $cl;?>" id="system_notice_area">
    <?php echo $msg1;?> &nbsp;&nbsp;&nbsp;
    <span id="system_notice_area_dismiss"><?php _e('Dismiss','wp-classifieds-listings');?></span>
</div>
<?php 
}
?>

<script type="text/javascript">
    jQuery(document).ready(function(){
        jQuery("#country_dropdown").change(function(){
            document.getElementById("ldng").style.display="";
            <?php $ajax_cls_nonce = wp_create_nonce('xyz-cls-state');?>
            var dataString = {
                action: 'xyz_cls_load_state',
                ccode:  jQuery("#country_dropdown").val(),
                security: '<?php echo $ajax_cls_nonce;?>',
                hstate: jQuery("#hstate").val()
            };

            jQuery.post(ajaxurl, dataString, function(response){

                document.getElementById("ldng").style.display="none";
                jQuery("#state_dropdown").html(response);
                var xyzres = jQuery("#state_dropdown").val();

                if(xyzres=='00'){
                    <?php $ajax_cls_nonce = wp_create_nonce('xyz-cls-city');?>
                    var dataString = {
                        action: 'xyz_cls_disp_city',
                        security: '<?php echo $ajax_cls_nonce;?>',
                        scode1:  '00',
                        ccode1: jQuery("#country_dropdown").val()
                    };
                    jQuery.post(ajaxurl, dataString, function(response){
                        document.getElementById("ldng1").style.display="none";
                        if(response!=0)
                            jQuery("#manage_div").html(response);
                    });
                }
                else{
                    var defmsg = '<td><?php _e('No city found','wp-classifieds-listings');?></td>';
                    jQuery("#manage_div tbody").html(defmsg);
                }
            });
        });

        jQuery("#state_dropdown").change(function(){
            document.getElementById("ldng1").style.display="";
            <?php $ajax_cls_nonce = wp_create_nonce('xyz-cls-city');?>
            var dataString = {
                action: 'xyz_cls_disp_city',
                security: '<?php echo $ajax_cls_nonce;?>',
                scode1:  jQuery("#state_dropdown").val(),
                ccode1: jQuery("#country_dropdown").val()
            };

            jQuery.post(ajaxurl, dataString, function(response) {
                document.getElementById("ldng1").style.display="none";
                if(response!=0)
                    jQuery("#manage_div").html(response);
            });
        });

        <?php $ajax_cls_nonce = wp_create_nonce('xyz-cls-city');?>       
        var dataString = {
            action: 'xyz_cls_disp_city',
            security: '<?php echo $ajax_cls_nonce;?>',
            scode1:  jQuery("#state_dropdown").val(),
            ccode1: jQuery("#country_dropdown").val()
        };

        jQuery.post(ajaxurl, dataString, function(response) {
            if(response!=0)
                jQuery("#manage_div").html(response);
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

    if(typeof xyz_cls_edit_city == 'undefined'){
        function xyz_cls_edit_city(id){
            document.getElementById("button_edit"+id).style.display="none";
            document.getElementById("button_edit"+id).style.display="none";
            document.getElementById("button_update"+id).style.display="";
            document.getElementById("button_cancel"+id).style.display="";
            document.getElementById("city"+id).readOnly=false;
            document.getElementById("city"+id).style.border="1px solid black";
        }
    }

    if(typeof xyz_cls_cancel_city == 'undefined'){
        function xyz_cls_cancel_city(id){
            document.getElementById("button_edit"+id).style.display="none";
            document.getElementById("button_edit"+id).style.display="";
            document.getElementById("button_update"+id).style.display="none";
            document.getElementById("button_cancel"+id).style.display="none";
            document.getElementById("city"+id).readOnly=true;
            document.getElementById("city"+id).style.border="0px";
            document.getElementById("city"+id).value=document.getElementById("citytemp"+id).value;
        }
    }

    if(typeof xyz_cls_delete_city == 'undefined'){
        function xyz_cls_delete_city(id){
            if(confirm('<?php _e('Do you really want to delete this city ?','wp-classifieds-listings')?>')){
                jQuery(document).ready(function(){
                    <?php $ajax_cls_nonce = wp_create_nonce('xyz-cls-cty-del');?>
                    var dataString = {
                        action:'del_city',
                        security: '<?php echo $ajax_cls_nonce;?>',
                        enabledel:id
                    };
                    jQuery.post(ajaxurl, dataString, function(response){
                        jQuery("#d1").html('<b style="color:green"><?php _e('Successfully deleted','wp-classifieds-listings');?> </b>');
                                           jQuery("#tr_id_"+id).fadeOut();
                    });
                });
            }
        }
    }
            
    if(typeof xyz_cls_update_city == 'undefined'){

        function xyz_cls_update_city(id){
            state=jQuery("#state"+id).val();
            scode=jQuery("#state_dropdown").val();
            city=jQuery("#city"+id).val();
            document.getElementById("load"+id).style.display="";
            if(city!="" && scode!=""){
                jQuery(document).ready(function(){
                    <?php $ajax_cls_nonce = wp_create_nonce('xyz-cls-cty-upt');?>

                    var dataString = {
                        action: 'xyz_cls_update_city',
                        enable: id,
                        security:'<?php echo $ajax_cls_nonce;?>',
                        state:jQuery("#state"+id).val(),
                        scode:jQuery("#state_dropdown").val(),
                        city:jQuery("#city"+id).val()
                    };

                    jQuery.post(ajaxurl, dataString, function(response){
                        if(response==1){
                            jQuery("#d1").html('<b style="color:green">Successfully updated </b>');
                            document.getElementById("load"+id).style.display="none";
                            document.getElementById("button_edit"+id).style.display="";
                            document.getElementById("button_update"+id).style.display="none";
                            document.getElementById("button_cancel"+id).style.display="none";
                            document.getElementById("city"+id).value=city;
                            document.getElementById("citytemp"+id).value=city;
                            document.getElementById("city"+id).readOnly=true;
                            document.getElementById("city"+id).style.border="0px solid black";
                        }
                        else
                            jQuery("#d1").html('<b style="color:red">You have already entered the same city for this state. </b>');
                    });
                });
            }
            else{
                alert("<?php _e('Please fill up the mandatory fields..!','wp-classifieds-listings')?>");
                document.getElementById("city"+id).focus();
            }
        }
    }
</script>