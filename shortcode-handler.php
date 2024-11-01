<?php
if ( ! defined( 'ABSPATH' ) )
    exit;

//**********************************Home Shortcode**********************************
if(!function_exists('xyz_cls_home_shortcode')){   
    function xyz_cls_home_shortcode(){

        $city="";
        $cid="";
        $t=time();
        if(isset($_GET['city'])&&isset($_GET['cid'])){
            $city=sanitize_text_field($_GET['city']);
            $cid=intval($_GET['cid']);
            setcookie(XYZ_CLS_COOKIE_CITY, $cid,0,COOKIEPATH);
            setcookie(XYZ_CLS_COOKIE_CNAME, $city,0,COOKIEPATH);

        }
        else{
            if(!isset($_COOKIE[XYZ_CLS_COOKIE_CITY])){
                $path= esc_url(get_permalink(get_option('xyz_wp_cls_region')));
                header("Location:".$path);
            }
			else{
			$city=$_COOKIE[XYZ_CLS_COOKIE_CNAME];
			$cid=$_COOKIE[XYZ_CLS_COOKIE_CITY];
			}

        }

        global $wpdb;
        if(isset($_GET['cid'])){
            $cid=intval($_GET['cid']);
        }
        else	
            $cid=$_COOKIE[XYZ_CLS_COOKIE_CITY];
        $cat_path=get_permalink();
        $r=$wpdb->get_row($wpdb->prepare("SELECT * FROM ".$wpdb->prefix."xyz_cls_cities WHERE id=%d",$cid));
        $city_result=$wpdb->get_results($wpdb->prepare("SELECT * FROM ".$wpdb->prefix."xyz_cls_cities WHERE scode=%s AND ccode=%s",$r->scode,get_option('xyz_cls_default_country')));
        
        if(is_numeric(ini_get('output_buffering'))){
            ob_start();
            if(wp_get_theme()=='XYZ Classifieds Basic')
                require  get_template_directory(). '/xyz-cls-home.php';
            else
                require( dirname( __FILE__ ) .'/templates/home.php' );
            $xyz_cls_home = xyz_remove_extra_newlines(ob_get_contents());
            ob_clean();
            ob_end_flush();
            return $xyz_cls_home;
        }
        else{
            if(wp_get_theme()=='XYZ Classifieds Basic')
                require  get_template_directory(). '/xyz-cls-home.php';
            else
                require( dirname( __FILE__ ) .'/templates/home.php' );
        }
    }
}

add_shortcode("xyz_wp_cls_home","xyz_cls_home_shortcode");

//*****************************Region Shortcode************************************
if(!function_exists('xyz_cls_region_shortcode'))
{
    function xyz_cls_region_shortcode()
    {
        global $wpdb;
        $result_reg=$wpdb->get_results($wpdb->prepare("SELECT * FROM ".$wpdb->prefix."xyz_cls_states WHERE ccode=%s ORDER BY  sname ASC" ,get_option('xyz_cls_default_country')));
        if(count($result_reg)==0){
            $path= get_permalink(get_option('xyz_wp_cls_city'));
            $url = add_query_arg( 'rcode','00', $path );
            header("Location:".$url);
            die;
        }
        // if(is_numeric(ini_get('output_buffering'))){
        //     // ob_start();
        //     if(wp_get_theme()=='XYZ Classifieds Basic'){
        //         require get_template_directory(). '/xyz-cls-region.php';
        //     }
        //     else
        //         require( dirname( __FILE__ ) .'/templates/location-region.php' );
        //     $xyz_cls_region = xyz_remove_extra_newlines(ob_get_contents());
        //     // ob_clean();
        //     // ob_end_flush();
        //     return $xyz_cls_region;
        // }
        // else{
            if(wp_get_theme()=='XYZ Classifieds Basic'){
                require get_template_directory(). '/xyz-cls-region.php';
            }
            else
                require( dirname( __FILE__ ) .'/templates/location-region.php' );
        // }
    }
}
add_shortcode("xyz_wp_cls_region","xyz_cls_region_shortcode");

//************************City Shortcode****************************************

if(!function_exists('xyz_cls_city_shortcode')){
    function xyz_cls_city_shortcode(){
        $rcode="";
        if(!isset($_GET['rcode'])){
            $path= get_permalink(get_option('xyz_wp_cls_region'));
            header("Location:".$path);
        }
        else{
            $rcode=sanitize_text_field($_GET['rcode']);
        }
        global $wpdb;
        $result_city=$wpdb->get_results($wpdb->prepare("SELECT * FROM ".$wpdb->prefix."xyz_cls_cities WHERE scode=%s AND ccode=%s ORDER BY city ASC",$rcode,get_option('xyz_cls_default_country')));
        if(is_numeric(ini_get('output_buffering'))){
            ob_start();
            if(wp_get_theme()=='XYZ Classifieds Basic'){
                require get_template_directory(). '/xyz-cls-city.php';
            }
            else
                require( dirname( __FILE__ ) .'/templates/location-city.php' );
            $xyz_cls_loc = xyz_remove_extra_newlines(ob_get_contents());
            ob_clean();
            ob_end_flush();
            return $xyz_cls_loc;
        }
        else{
            if(wp_get_theme()=='XYZ Classifieds Basic'){
                require get_template_directory(). '/xyz-cls-city.php';
            }
            else
                require( dirname( __FILE__ ) .'/templates/location-city.php' );
        }
    }
}
add_shortcode("xyz_wp_cls_city","xyz_cls_city_shortcode");

//***********************Items Shortcode**********************************************

if(!function_exists('xyz_cls_items_shortcode'))
{
    function xyz_cls_items_shortcode()
    {
        require_once( dirname( __FILE__ ) .'/classes/pagination.php' );
        $city=$o=$w=$a="";
        $cid="";
        $catid="";
        if(isset($_GET['cid']))
        {
            $cid=intval($_GET['cid']);
            $city=sanitize_text_field($_GET['city']);
        }
        elseif (!isset($_COOKIE[XYZ_CLS_COOKIE_CITY])){
            $path= get_permalink(get_option('xyz_wp_cls_region'));
            header("Location:".$path);
        }
        else  {
            $city=$_COOKIE[XYZ_CLS_COOKIE_CNAME];
            $cid=$_COOKIE[XYZ_CLS_COOKIE_CITY];
        }
        global $wpdb;
        $stepstr="&nbsp;&nbsp;&raquo;&nbsp;";
        $category_id="";
        if(isset($_GET['category']))
        {
            $category_id=intval($_GET['category']);
        }
        if(isset($_GET['catid'])){
            $catid=intval($_GET['catid']);
            $category_id=$catid;
        }
        // else $catid="";
        $t=time();
        if(isset($_GET['search'])){
            $search=sanitize_text_field($_GET['search']);
            //$str2="  AND ".$wpdb->prefix."posts.post_title='".$search."' " ;
            $str2="  AND (".$wpdb->prefix."posts.post_title like %s  OR ".$wpdb->prefix."posts.post_content like %s)" ;
            $arr= 	array('classifieds_listing',$cid,0,1,$t,$t,"%".$search."%","%".$search."%",'publish','publish');
            //echo $str2;
        }
        else 
        {
            $search="";
            $str2="";
            $arr= 	array('classifieds_listing',$cid,0,1,$t,$t,'publish','publish');
        }

        $type="";
        if(isset($_GET['type']))
        {
            $type=intval($_GET['type']);
        }
        if($type==1)
            $o='active';
        elseif($type==2)
            $w='active';
        else 
            $a='active';
        if(get_option('xyz_cls_item_display_order')==1)
            $order='post_date';
        else 
            $order='post_modified';
        $cat_path=get_permalink(get_option('xyz_wp_cls_home'));
        $subitems_path=get_permalink(get_option('xyz_wp_cls_items'));
        if($type)
            $str=' AND item_type='.$type.' ';
        else 
            $str="";
        $t=time();
        $premium_result=xyz_cls_get_premium_items($cid,$category_id,$type,$order,$catid,$search);
        foreach ($premium_result as $r)
            $wpdb->update($wpdb->prefix."xyz_cls_listing_details", array('last_display_time' => $t),array('id'=>$r->id));
        if( $category_id==""){
            $result = new XYZ_WP_Pagination ("SELECT * FROM ".$wpdb->prefix."posts  JOIN ".$wpdb->prefix."xyz_cls_listing_details ON ".$wpdb->prefix."posts.ID = ".$wpdb->prefix."xyz_cls_listing_details.pid
WHERE ".$wpdb->prefix."posts.post_type = %s AND city_id=%d  AND (featured=%d or featured= %d) AND featured_expiry<=%d and expiry>%d ".$str. "  ".$str2." AND post_status=%s AND status=%s ORDER BY $order,".$wpdb->prefix."xyz_cls_listing_details.id  DESC",$arr,get_option('xyz_cls_item_count'));
            $result_items=$result->xyz_cls_get_result();
        }
        else{
            $cat=xyz_cls_get_childs($category_id).$category_id;
            $result = new XYZ_WP_Pagination ("SELECT * FROM ".$wpdb->prefix."posts  JOIN ".$wpdb->prefix."xyz_cls_listing_details ON ".$wpdb->prefix."posts.ID = ".$wpdb->prefix."xyz_cls_listing_details.pid
WHERE ".$wpdb->prefix."posts.post_type = %s AND city_id=%d  AND (featured=%d or featured= %d) AND featured_expiry<=%d  AND category IN (". $cat.")  AND expiry>%d  ".$str." ".$str2." AND post_status=%s AND status=%s ORDER BY $order ,".$wpdb->prefix."xyz_cls_listing_details.id  DESC",$arr,get_option('xyz_cls_item_count'));
          
            $result_items=$result->xyz_cls_get_result();
        }
        foreach ($result_items as $r)
            $wpdb->update($wpdb->prefix."xyz_cls_listing_details", array('last_display_time' => $t),array('id'=>$r->id));
        $all_cat=add_query_arg(array('city'=>$city,'cid'=>$cid) ,get_permalink(get_option('xyz_wp_cls_items')));
        $all_adds=add_query_arg(array('city'=>$city,'cid'=>$cid,'category'=>$category_id) ,get_permalink(get_option('xyz_wp_cls_items')));
        $offered=add_query_arg(array('city'=>$city,'cid'=>$cid,'category'=>$category_id,'type'=>'1') ,get_permalink(get_option('xyz_wp_cls_items')));
        $wanted=add_query_arg(array('city'=>$city,'cid'=>$cid,'category'=>$category_id,'type'=>'2') ,get_permalink(get_option('xyz_wp_cls_items')));
        $link=get_permalink(get_option('xyz_wp_cls_items'));
        if(is_numeric(ini_get('output_buffering'))){
            ob_start();
            if(wp_get_theme()=='XYZ Classifieds Basic')
                require get_template_directory(). '/xyz-cls-category-item-listing.php';
            else
                require( dirname( __FILE__ ) .'/templates/category-item-listing.php' );
            $xyz_cls_item = xyz_remove_extra_newlines(ob_get_contents());
            ob_clean();
            ob_end_flush();
            return $xyz_cls_item;
        }
        else{
            if(wp_get_theme()=='XYZ Classifieds Basic')
                require get_template_directory(). '/xyz-cls-category-item-listing.php';
            else
                require( dirname( __FILE__ ) .'/templates/category-item-listing.php' );
        }
    }
}
add_shortcode("xyz_wp_cls_items","xyz_cls_items_shortcode");

//*****************************Register Shortcode******************************

if(!function_exists('xyz_cls_register_shortcode')){
    function xyz_cls_register_shortcode(){
        if(is_user_logged_in()){
            wp_redirect(admin_url());
        }
        else{
            if( get_option('xyz_cls_login')==1 ){
                $return_url = esc_url( home_url('/wp-login.php') );
                // echo $return_url;
                wp_redirect( $return_url );
                exit;
            }
            if(is_numeric(ini_get('output_buffering'))){
                ob_start();
                if(wp_get_theme()=='XYZ Classifieds Basic')
                    require  get_template_directory(). '/xyz-cls-register-login.php';
                else
                    require( dirname( __FILE__ ) .'/templates/register-login.php');
                $xyz_cls_login = xyz_remove_extra_newlines(ob_get_contents());
                ob_clean();
                ob_end_flush();
                return $xyz_cls_login;
            }
            else{
                if(wp_get_theme()=='XYZ Classifieds Basic')
                    require  get_template_directory(). '/xyz-cls-register-login.php';
                else
                    require( dirname( __FILE__ ) .'/templates/register-login.php');
            }
        }
    }
}
add_shortcode("xyz_wp_cls_register","xyz_cls_register_shortcode");

//***********************Forgot Password***********************************

if(!function_exists('xyz_cls_forgotpass_shortcode'))
{
    function xyz_cls_forgotpass_shortcode()
    {
        if(is_user_logged_in())
        {
            wp_redirect(admin_url());
        }
        else{
            if(is_numeric(ini_get('output_buffering'))){
                ob_start();
                if(wp_get_theme()=='XYZ Classifieds Basic')
                    require  get_template_directory(). '/xyz-cls-forgot-password.php';
                else
                    require( dirname( __FILE__ ) .'/templates/forgot-password.php' );
                $xyz_cls_fpass = xyz_remove_extra_newlines(ob_get_contents());
                ob_clean();
                ob_end_flush();
                return $xyz_cls_fpass;
            }
            else{
                if(wp_get_theme()=='XYZ Classifieds Basic')
                    require  get_template_directory(). '/xyz-cls-forgot-password.php';
                else
                    require( dirname( __FILE__ ) .'/templates/forgot-password.php' );
            }
        }
    }
}
add_shortcode("xyz_wp_cls_forgotpassword","xyz_cls_forgotpass_shortcode");
