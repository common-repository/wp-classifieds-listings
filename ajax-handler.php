<?php

if ( ! defined( 'ABSPATH' ) )
     exit;
//*****************Ajax backlink enable***************************************************

if(!function_exists('xyz_cls_ajax_backlink')){
    function xyz_cls_ajax_backlink()
    {     
          if(current_user_can('administrator')){ 
           
            check_ajax_referer( 'xyz-cls-blink','security' );
            global $wpdb;
            if(isset($_POST)){ 
                if($_POST['enable']==1){
                    update_option('xyz_credit_link','cls');
                    echo 1;
                }
                if($_POST['enable']==-1){
                    update_option('xyz_cls_credit_dismiss','dis');
                    update_option('xyz_credit_link',0);
                    echo -1;
                }
            }
         }
        die;
    }
}
add_action('wp_ajax_xyz_cls_ajax_backlink', 'xyz_cls_ajax_backlink');
//*********************display  cities  ****from metabox************************
if(!function_exists('xyz_cls_load_cities')){
    function xyz_cls_load_cities(){	
        global $wpdb;
        check_ajax_referer('xyz-cls-load-cty','security');
        $loadId1=sanitize_text_field($_POST['region']);
        $ccode=sanitize_text_field($_POST['ccode']);
        $hcity=sanitize_text_field($_POST['hcity']);
        $sql=$wpdb->get_results($wpdb->prepare( "SELECT  * FROM ".$wpdb->prefix."xyz_cls_cities  WHERE scode=%s AND ccode=%s ORDER BY city ASC",$loadId1,$ccode));
        $HTML='<option value="-1">---Select---</option>';
        foreach($sql as $row){
            if($hcity==$row->id)
                $a="selected";
            else
                $a="";
            
        $HTML.="<option value=".$row->id." .echo $a;.>".$row->city."</option>";
     }

    if($HTML=='<option value="-1">---Select---</option>')
         echo 0;
    else
         echo $HTML;

    die;
    }
}
add_action('wp_ajax_xyz_cls_load_city','xyz_cls_load_cities');
//**************load region dropdown******from cities.php*************************
if(!function_exists('xyz_cls_load_states'))
{
    function xyz_cls_load_states()
    {
        global $wpdb;
        check_ajax_referer('xyz-cls-state','security');
        $loadId2=sanitize_text_field($_POST['ccode']);
        $hstate='';

        if(isset($_POST['hstate']))
            $hstate=sanitize_text_field($_POST['hstate']);

        $sql=$wpdb->get_results($wpdb->prepare( "SELECT  * FROM ".$wpdb->prefix."xyz_cls_states  WHERE ccode=%s  ORDER BY sname ASC",$loadId2));

        $HTML='<option value="-1">---Select---</option>';

        foreach($sql as $row){
            if($hstate==$row->scode)
                $a="selected";
            else 
                $a="";

            $HTML.="<option value=".$row->scode." ".$a.">".$row->sname."</option>";
        }
        
        if( $HTML=='<option value="-1">---Select---</option>'){

            $HTML ='<option value="00">States Not Available</option>';
            echo $HTML;
            // echo 0;
        }
        else 	
            echo $HTML;

        die;
    }
}
add_action('wp_ajax_xyz_cls_load_state','xyz_cls_load_states');
//********************display cities ****** from cities.php************
if(!function_exists('xyz_cls_city_display'))
{
    function xyz_cls_city_display()
    {
        global $wpdb;
        check_ajax_referer('xyz-cls-city','security');
        $ccode=sanitize_text_field($_POST['ccode1']);
        $scode=sanitize_text_field($_POST['scode1']);
        if( $scode ==-1)
            $cityresult=$wpdb->get_results($wpdb->prepare( "SELECT  * FROM ".$wpdb->prefix."xyz_cls_cities  WHERE  ccode=%s ORDER BY city ASC",$ccode));
        else
            $cityresult=$wpdb->get_results($wpdb->prepare( "SELECT  * FROM ".$wpdb->prefix."xyz_cls_cities  WHERE scode=%s AND ccode=%s ORDER BY city ASC",$scode,$ccode));
        if(count($cityresult)==0){
?><table>
    <thead>
        <tr>
            <th >
                <b>
                    <?php _e('City Name','wp-classifieds-listings');?></b>
            </th>
            <th>
            </th>
            <th>
                <b>
                    <?php _e('Action','wp-classifieds-listings');?> 
                </b>
            </th>
            <th>
            </th>
        </tr>
    </thead>
    <tfoot>
        <tr>
            <th >
                <b>
                    <?php _e('City Name','wp-classifieds-listings');?></b>
            </th>
            <th>
            </th>
            <th>
                <b>
                    <?php _e('Action','wp-classifieds-listings');?> 
                </b>
            </th>
            <th>
            </th>
        </tr>
    </tfoot>
    <tbody>
        <tr >
            <td>
                <br>
                No city found.
            </td>
        </tr>
    </tbody>
</table>
<?php }
        else{ ?>
<table class="widefat"  style="width:99%;">
    <thead>
        <tr>
            <th >
                <b>
                    <?php _e('City Name','wp-classifieds-listings');?></b>
            </th>
            <th>
            </th>
            <th>
                <b>
                    <?php _e('Action','wp-classifieds-listings');?> 
                </b>
            </th>
            <th>
            </th>
        </tr>
    </thead>
    <tfoot>
        <tr>
            <th >
                <b>
                    <?php _e('City Name','wp-classifieds-listings');?></b>
            </th>
            <th>
            </th>
            <th>
                <b>
                    <?php _e('Action','wp-classifieds-listings');?> 
                </b>
            </th>
            <th>
            </th>
        </tr>
    </tfoot>
    <tbody>
        <?php $i=0;
             foreach($cityresult as $citylist){
                 $c=($i%2==0) ? "": "alternate";
        ?><tr  id="tr_id_<?php echo $citylist->id;?>" class="<?php echo $c;?>">
            <td style=" width: 250px;" >
                <input style="border: 0px; " type="text" name="city<?php echo $citylist->id;?>" id="city<?php echo $citylist->id;?>" value="<?php echo $citylist->city;?>" readonly>
            </td>
            <td>
                <input type="hidden" name="citytemp<?php echo $citylist->id;?>" id="citytemp<?php echo $citylist->id;?>"  value="<?php echo $citylist->city;?>" >
            </td>
            <td style=" width: 250px;">
                <input type="button" value="Edit" id="button_edit<?php echo $citylist->id;?>" name="button_edit<?php echo $citylist->id;?>" onclick="xyz_cls_edit_city(<?php echo $citylist->id;?>)" style="" class="button-secondary">
                <input style="display: none;" type="button" name="button_update<?php echo $citylist->id;?>" id="button_update<?php echo $citylist->id;?>" value="Update" onclick="xyz_cls_update_city(<?php echo $citylist->id;?>)" class="button-secondary">
                <input style="display: none;" type="button" name="button_cancel<?php echo $citylist->id;?>" id="button_cancel<?php echo $citylist->id;?>" value="Cancel" onclick="xyz_cls_cancel_city(<?php echo $citylist->id;?>)" class="button-secondary">
                <input type="button" value="Delete" name="button_delete<?php echo $citylist->id;?>" id="button_delete<?php echo  $citylist->id?>" onclick="xyz_cls_delete_city(<?php echo  $citylist->id;?>)" class="button-secondary">
            </td>
            <td>
                <div id="load<?php echo $citylist->id;?>" style="display: none;">
                    <img src="<?php echo plugins_url(XYZ_CLASSIFIEDS_DIR."/images/load.gif")?>"/>
                </div>
            </td>
        </tr>
        <?php $i++;}?>
    </tbody>
</table>
<?php
            }
        die;
    }
}
add_action('wp_ajax_xyz_cls_disp_city','xyz_cls_city_display');
//********display region list**********from regions.php*********************************
if(!function_exists('xyz_cls_region_display'))
{
    function xyz_cls_region_display(){
        global $wpdb;
        check_ajax_referer('xyz-cls-reg-load','security');
        $ccode=sanitize_text_field($_POST['ccode1']);
        $regresult=$wpdb->get_results($wpdb->prepare( "SELECT  * FROM ".$wpdb->prefix."xyz_cls_states  WHERE  ccode=%s ORDER BY sname ASC",$ccode));
        if(count($regresult)==0){
?><table>
<thead>
    <tr>
        <th >
            <b>
                <?php _e('Region Name','wp-classifieds-listings');?></b>
        </th>
        <th>
        </th>
        <th >
            <b>
                <?php _e('Region code','wp-classifieds-listings');?></b>
        </th>
        <th>
        </th>
        <th>
            <b>
                <?php _e('Action','wp-classifieds-listings');?> 
            </b>
        </th>
        <th>
        </th>
    </tr>
</thead>
<tfoot>
    <tr>
        <th >
            <b>
                <?php _e('Region Name','wp-classifieds-listings');?></b>
        </th>
        <th>
        </th>
        <th >
            <b>
                <?php _e('Region code','wp-classifieds-listings');?></b>
        </th>
        <th>
        </th>
        <th>
            <b>
                <?php _e('Action','wp-classifieds-listings');?> 
            </b>
        </th>
        <th>
        </th>
    </tr>
</tfoot>
<tbody>
    <tr >
        <td>
            <br>
            No region found.
        </td>
    </tr>
</tbody>
</table>
<?php
                                }
        else{?>
<table class="widefat"  style="width:99%;">
    <thead>
        <tr>
            <th >
                <b>
                    <?php _e('Region Name','wp-classifieds-listings');?></b>
            </th>
            <th>
            </th>
            <th >
                <b>
                    <?php _e('Region code','wp-classifieds-listings');?></b>
            </th>
            <th>
            </th>
            <th>
                <b>
                    <?php _e('Action','wp-classifieds-listings');?> 
                </b>
            </th>
            <th>
            </th>
        </tr>
    </thead>
    <tfoot>
        <tr>
            <th >
                <b>
                    <?php _e('Region Name','wp-classifieds-listings');?></b>
            </th>
            <th>
            </th>
            <th >
                <b>
                    <?php _e('Region code','wp-classifieds-listings');?></b>
            </th>
            <th>
            </th>
            <th>
                <b>
                    <?php _e('Action','wp-classifieds-listings');?> 
                </b>
            </th>
            <th>
            </th>
        </tr>
    </tfoot>
    <tbody>
        <?php $i=0;
             foreach($regresult as $rlist){
                 $c=($i%2==0) ? "": "alternate";
        ?>
        <tr id="tr_id_<?php echo $rlist->id;?>" class="<?php echo $c;?>">
            <td>
                <input style="border: 0px" type="text" name="state<?php echo $rlist->id;?>" id="state<?php echo  $rlist->id;?>" value="<?php echo  $rlist->sname;?>" readonly/>
            </td>
            <td>
                <input type="hidden" name="statetemp<?php echo  $rlist->id;?> " id="statetemp<?php echo  $rlist->id;?>" value="<?php echo  $rlist->sname;?>" >
            </td>
            <td>
                <input style="border: 0px" type="text" name="scode<?php echo $rlist->id;?>" id="scode<?php echo $rlist->id;?>" value="<?php echo  $rlist->scode;?>" readonly/>
            </td>
            <td>
                <input type="hidden" name="scodetemp<?php echo  $rlist->id;?> "id="scodetemp<?php echo  $rlist->id;?>" value="<?php echo  $rlist->scode;?>" >
            </td>
            <td style=" width: 250px;">
                <input type="button" value="Edit" id="button_edit<?php echo  $rlist->id;?>"name="button_edit<?php echo $rlist->id;?>" onclick="xyz_cls_edit_region('<?php echo  $rlist->id;?>')" style="" class="button-secondary">
                <input style="display: none;" type="button" name="button_update<?php echo  $rlist->id;?>" id="button_update<?php echo  $rlist->id;?>" value="Update" onclick="xyz_cls_update_region('<?php echo  $rlist->id;?>')" class="button-secondary">
                <input style="display: none;" type="button" name="button_cancel<?php echo  $rlist->id;?>" id="button_cancel<?php echo  $rlist->id;?>" value="Cancel" onclick="xyz_cls_cancel_region('<?php echo  $rlist->id;?>')" class="button-secondary">
                <input type="button" value="Delete" name="button_delete<?php echo $rlist->id;?>" id="button_delete<?php echo  $rlist->id?>" onclick="xyz_cls_delete_region('<?php echo  $rlist->id;?>')" class="button-secondary">
            </td>
            <td>
                <div id="load<?php echo $rlist->id;?>" style="display: none;">
                    <img src="<?php echo plugins_url(XYZ_CLASSIFIEDS_DIR."/images/load.gif")?>" style="height:10"/>
                </div>
            </td>
        </tr>
        <?php $i++;}?>
    </tbody>
</table>
<?php
            }
        die;
    }
}
add_action('wp_ajax_xyz_cls_load_region','xyz_cls_region_display');
//**********************update region***************************************
if(!function_exists('xyz_cls_reg_update'))
{
    function xyz_cls_reg_update()
    {    

     $msg=0;
     global $wpdb;
     check_ajax_referer('xyz-cls-reg-upt','security');
     $id=intval($_POST['enable']);
     $scode=sanitize_text_field($_POST['scode']);
     $state=sanitize_text_field($_POST['state']);
     $ccode=sanitize_text_field($_POST['ccode']);
     $sql1=$wpdb->get_results($wpdb->prepare( "SELECT  sname FROM ".$wpdb->prefix."xyz_cls_states WHERE ccode=%s AND scode=%s AND id!=%d",$ccode,$scode,$id));
     if(count($sql1)==0){
         $wpdb->update($wpdb->prefix."xyz_cls_states", array(  'sname' => $state,'scode'=>$scode),array('id'=>$id));
         echo 1;
     }
     else echo 'e1';
     die;
    }
}
add_action("wp_ajax_update_reg","xyz_cls_reg_update");
//********************************update city******************************************************
if(!function_exists('xyz_cls_city_update'))
{
    function xyz_cls_city_update()
    {
        global $wpdb;
        check_ajax_referer('xyz-cls-cty-upt','security');
        $id=intval($_POST['enable']);
        $scode=sanitize_text_field($_POST['scode']);
        $city=sanitize_text_field($_POST['city']);
        $sql1=$wpdb->get_results($wpdb->prepare( "SELECT  city FROM ".$wpdb->prefix."xyz_cls_cities  WHERE city=%s AND scode=%s AND id!=%d ",$city,$scode,$id));
        if(count($sql1)==0){
            $wpdb->update($wpdb->prefix."xyz_cls_cities", array(  'city' => $city),array('id'=>$id));
            echo '1';}
        else 
            echo 'e1';
        die;
    }
}
add_action("wp_ajax_xyz_cls_update_city","xyz_cls_city_update");
//******************************delete region ***********************************************
if(!function_exists('xyz_cls_city_delete'))
{
    function xyz_cls_city_delete()
    {
        global $wpdb;
        check_ajax_referer('xyz-cls-cty-del','security');
        $id=intval($_POST['enabledel']);
        $wpdb->delete($wpdb->prefix."xyz_cls_cities",array('id'=>$id) );
        die;
    }
}
add_action("wp_ajax_del_city","xyz_cls_city_delete");
//******************************delete city ***********************************************
if(!function_exists('xyz_cls_reg_delete'))
{
    function xyz_cls_reg_delete()
    {
        global $wpdb;
        check_ajax_referer('xyz-cls-reg-del','security');
        $id=intval($_POST['enable']);
        $wpdb->delete($wpdb->prefix."xyz_cls_states",array('id'=>$id) );
        die;
    }
}
add_action("wp_ajax_del_reg","xyz_cls_reg_delete");
//******************************contact form ***************************************************************
if(!function_exists('xyz_cls_contact'))
{
    function xyz_cls_contact()
    {
        global $wpdb;
        $msg=sanitize_text_field($_POST['msg']);
        if (filter_var($_POST['email'], FILTER_VALIDATE_EMAIL)) {
            $email=sanitize_email($_POST['email']);
        }
 
        $id=intval($_POST['id']);
        $author=get_post_field('post_author', $id);
        $to=get_the_author_meta('user_email',$author);

        $sql="select * from ".$wpdb->prefix."xyz_cls_email_templates where id=%d";
        $row1=$wpdb->get_row($wpdb->prepare($sql,3));
        $row1->sub=str_ireplace("{SITE_NAME}",get_bloginfo('name'),$row1->sub);
        $row1->sub=str_ireplace("{LISTING_TITLE}",get_the_title($id),$row1->sub);
        $row1->body=str_ireplace("{SITE_NAME}",get_bloginfo('name'),$row1->body);
        $row1->body=str_ireplace("{LISTING_TITLE}",get_the_title($id),$row1->body);
        $row1->body=str_ireplace("{RESPONSE}",$msg,$row1->body);
        $headers = 'From:'.$email.'' . "\r\n";
        $headers.= "Content-type: text/html  ";
        $headers.='Reply-To:'.$email ;
        $a=wp_mail($to,$row1->sub,$row1->body,$headers);
        echo $a;
        die;
    }
}
add_action("wp_ajax_quick_contact","xyz_cls_contact");
add_action( 'wp_ajax_nopriv_quick_contact', 'xyz_cls_contact' );
//************************** remove premium***************************************************************************
if(!function_exists('xyz_cls_remove_premium'))
{
    function xyz_cls_remove_premium()
    {
        global $wpdb;
        check_ajax_referer('xyz-cls-premv','security');
        $id=intval($_POST['id']);
        $ex=get_post_field('post_date', $id);
        $ftime_arr=getdate(strtotime($ex));
        $expiry=xyz_cls_local_date_time_create(gmmktime($ftime_arr['hours'],$ftime_arr['minutes'],$ftime_arr['seconds'],$ftime_arr['mon'],$ftime_arr['mday']+get_option('xyz_cls_item_expiry'),$ftime_arr['year']));
        $wpdb->update($wpdb->prefix."xyz_cls_listing_details", array('expiry'=>$expiry,'featured' => 0,'featured_expiry'=>0,'featured_no_of_days'=>0),array('pid'=>$id));
        die;
    }
}
add_action('wp_ajax_xyz_cls_remove_premium', 'xyz_cls_remove_premium');
//********************************configure default custom fields***************************************
if(!function_exists('xyz_cls_configure_custom'))
{
    function xyz_cls_configure_custom()
    {
        global $wpdb;
        check_ajax_referer('xyz-cls-cfg-cust','security');
        $cat0=array('Cars and Bikes', 'Real Estate','Home and Lifestyle','Electronics and Technology','Jobs','Services');
        $tab_name='xyz_cls_category';
        foreach ($cat0 as $c)
        {
            $term=get_term_by('name', $c, 'xyz_cls_category')->term_id;
            $wpdb->query($wpdb->prepare("INSERT INTO ".$wpdb->prefix."xyz_cfl_group (`xyz_cfl_group_name`, `xyz_cfl_group_post_type`, `xyz_cfl_group_taxonomy`, `xyz_cfl_group_taxonomy_term_id`, `xyz_cfl_group_order`, `xyz_cfl_group_status`)
VALUES ('%s','%s','%s',%d,%d,%d)",$c.' - Additional Details', 'classifieds_listing', 'xyz_cls_category', $term, 1, 1));
            $id=$wpdb->insert_id;
            $queryMapping1 ="CREATE TABLE IF NOT EXISTS ".$wpdb->prefix."xyz_cfl_field_values_".$tab_name."_".$term." (
`id` int(11) NOT NULL AUTO_INCREMENT,
`post_id` int(11) NOT NULL,
PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=1 ;";
            $wpdb->query($queryMapping1);
        }
        $data=$wpdb->get_row($wpdb->prepare("SELECT `id` FROM `".$wpdb->prefix."xyz_cfl_group` WHERE `xyz_cfl_group_name`=%s",'Cars and Bikes - Additional Details'));
        $id=$data->id;
        $term=get_term_by('name', 'Cars and Bikes', 'xyz_cls_category')->term_id;
        $wpdb->query($wpdb->prepare("INSERT INTO ".$wpdb->prefix."xyz_cfl_fields (`xyz_cfl_group_id`, `xyz_cfl_field_name`,`xyz_cfl_field_type`, `xyz_cfl_field_order`, `xyz_cfl_field_status`, `xyz_cfl_field_mandatory`,`xyz_cfl_field_placeholder`, `xyz_cfl_field_default`)
VALUES (%d,%s,%s,%d,%d,%d,%s,%s)",$id, 'Condition', 'Dropdown',0,1,1, '','New,Used' ));
        $tab_fld_id=$wpdb->insert_id;
        $queryMapping2 ="ALTER TABLE `".$wpdb->prefix."xyz_cfl_field_values_".$tab_name."_".$term."` ADD `field_".$tab_fld_id."` TEXT CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL";
        $wpdb->query($queryMapping2);
        $wpdb->query($wpdb->prepare("INSERT INTO ".$wpdb->prefix."xyz_cfl_fields (`xyz_cfl_group_id`, `xyz_cfl_field_name`,`xyz_cfl_field_type`, `xyz_cfl_field_order`, `xyz_cfl_field_status`, `xyz_cfl_field_mandatory`,`xyz_cfl_field_placeholder`, `xyz_cfl_field_default`)
VALUES (%d,%s,%s,%d,%d,%d,%s,%s)",$id, 'Color', 'Dropdown',0,1,1, '','Black,Red,White,Blue,Other' ));
        $tab_fld_id=$wpdb->insert_id;
        $queryMapping2 ="ALTER TABLE `".$wpdb->prefix."xyz_cfl_field_values_".$tab_name."_".$term."` ADD `field_".$tab_fld_id."` TEXT CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL";
        $wpdb->query($queryMapping2);
        $wpdb->query($wpdb->prepare("INSERT INTO ".$wpdb->prefix."xyz_cfl_fields (`xyz_cfl_group_id`, `xyz_cfl_field_name`,`xyz_cfl_field_type`, `xyz_cfl_field_order`, `xyz_cfl_field_status`, `xyz_cfl_field_mandatory`,`xyz_cfl_field_placeholder`, `xyz_cfl_field_default`)
VALUES (%d,%s,%s,%d,%d,%d,%s,%s)",$id, 'Brand', 'Dropdown',0,1,1, '','Honda,Bajaj,Yamaha' ));
        $tab_fld_id=$wpdb->insert_id;
        $queryMapping2 ="ALTER TABLE `".$wpdb->prefix."xyz_cfl_field_values_".$tab_name."_".$term."` ADD `field_".$tab_fld_id."` TEXT CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL";
        $wpdb->query($queryMapping2);
    }
}
add_action('wp_ajax_xyz_cls_configure_custom', 'xyz_cls_configure_custom');
if(!function_exists('xyz_cls_activate_cfl'))
{
    function xyz_cls_activate_cfl()
    {
        check_ajax_referer('xyz-cls-cfl-act','security');
        activate_plugin('custom-field-manager/custom-field-manager.php',admin_url('plugins.php'));
    }
}
add_action('wp_ajax_xyz_cls_activate_cfl', 'xyz_cls_activate_cfl');
