<?php
if ( ! defined( 'ABSPATH' ) )
    exit;

//********************* create pages **************************
if(!function_exists('xyz_cls_create_page')){
    function xyz_cls_create_page( $slug, $option, $page_title = '', $page_content = '', $post_parent = 0){
        global $wpdb;
        $option_value = get_option( $option );

        if ($option_value > 0 && get_post( $option_value )){
        	$wpdb->delete( $wpdb->prefix."posts", array( 'ID' => $option_value ) );
        }

        $page_found = $wpdb->get_var( $wpdb->prepare( "SELECT ID FROM " . $wpdb->posts . " WHERE post_name = %s LIMIT 1;", $slug ) );

        if( $page_found ) {
            if ( ! $option_value )
                update_option( $option, $page_found );
            return;
        }

        $page_data = array(
            'post_status' 		=> 'publish',
            'post_type' 		=> 'page',
            'post_author' 		=> 1,
            'post_name' 		=> $slug,
            'post_title' 		=> $page_title,
            'post_content' 		=> $page_content,
            'post_parent' 		=> $post_parent,
            'comment_status' 	=> 'closed'
        );

        $page_id = wp_insert_post( $page_data );
        update_option( $option, $page_id );
    }
}

//*****************************category list for search************************************
if( !function_exists( 'xyz_cls_get_category_display' ) ){
    function xyz_cls_get_category_display($pid,$i,$catid){
        global $wpdb;
        $cat_value="";
        $res=$wpdb->get_results($wpdb->prepare("SELECT name, ".$wpdb->prefix."terms.term_id ,".$wpdb->prefix."term_taxonomy.parent FROM ".$wpdb->prefix."term_taxonomy  JOIN ".$wpdb->prefix."terms ON ".$wpdb->prefix."term_taxonomy.term_id = ".$wpdb->prefix."terms.term_id WHERE ".$wpdb->prefix."term_taxonomy.taxonomy =  'xyz_cls_category' AND ".$wpdb->prefix."term_taxonomy.parent =%d",$pid));

        foreach($res as $row){
            $tot=$wpdb->get_col("SELECT COUNT(term_id) FROM ".$wpdb->prefix."term_taxonomy WHERE parent='".$row->term_id."'");
            if($catid!=$row->term_id)
                $cat_value=$cat_value.'<option value="'.$row->term_id.'">';
            else
                $cat_value=$cat_value.'<option selected="selected" value="'.$row->term_id.'">';

            for($count=0;$count<$i;$count++){
                $cat_value=$cat_value.'&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&raquo;';
            }

            $cat_value=$cat_value.$row->name.'</option>';
            
            if($tot!=0)
                $cat_value=$cat_value.xyz_cls_get_category_display($row->term_id,$i+1,$catid);
        }
        return $cat_value;
    }
}

//*****************************fun for cat drpdwn*******************************************************************************************************
if ( !function_exists( 'xyz_cls_get_category_dropdown' ) ) {
    function xyz_cls_get_category_dropdown($pid=0,$selected,$level=0)
    {
        global $drpstr;
        global $wpdb;
        $result=$wpdb->get_results($wpdb->prepare("SELECT name, ".$wpdb->prefix."terms.term_id ,".$wpdb->prefix."term_taxonomy.parent FROM ".$wpdb->prefix."term_taxonomy  JOIN ".$wpdb->prefix."terms ON ".$wpdb->prefix."term_taxonomy.term_id = ".$wpdb->prefix."terms.term_id WHERE ".$wpdb->prefix."term_taxonomy.taxonomy =  'xyz_cls_category' AND ".$wpdb->prefix."term_taxonomy.parent =%d",$pid));
        foreach ($result as $leveldata){
            $stepstr="";
            for($i=0;$i< $level;$i++){
                $stepstr.="&nbsp;&nbsp;&nbsp;&nbsp;";
                if($i ==($level-1))
                    $stepstr.="&nbsp;&nbsp;&raquo;&nbsp;";
            }

            $idcount=$wpdb->get_col("SELECT COUNT(term_id) FROM ".$wpdb->prefix."term_taxonomy WHERE parent='".$leveldata->term_id."'");

            if($idcount[0] ==0)
                $lastlevelflag=1;
            else
                $lastlevelflag=0;

            if($lastlevelflag==1){
                xyz_cls_get_category_dropdown($leveldata->term_id,$selected,$level+1);
                if($selected ==$leveldata->term_id)
                    $selectstr="selected";
                else
                    $selectstr="";
                if($leveldata->parent==0)
                    $drpstr.="<option value='".$leveldata->term_id."' ".$selectstr.">".$stepstr.$leveldata->name."</option>";
                else
                    $drpstr.="<option value='".$leveldata->term_id."' ".$selectstr.">&nbsp;&nbsp;&nbsp;&nbsp;".$stepstr.$leveldata->name."</option>";
            }
            else{
                if($leveldata->parent==0)
                    $drpstr.="<optgroup label='".$stepstr.$leveldata->name."'></optgroup>";
                else
                    $drpstr.="<optgroup label='"."&nbsp;&nbsp;".$stepstr.$leveldata->name."'></optgroup>";
                xyz_cls_get_category_dropdown($leveldata->term_id,$selected,$level+1);
            }
        }
        return $drpstr;
    }
}
/* Local time Insert */
if(!function_exists('xyz_cls_local_date_time_create')){
    function xyz_cls_local_date_time_create($timestamp){
        return $timestamp - ( get_option( 'gmt_offset' ) * HOUR_IN_SECONDS );
    }
}
/* Local time formating */
if(!function_exists('xyz_cls_local_date_time')){
    function xyz_cls_local_date_time($format,$timestamp){
        return gmdate($format, $timestamp + ( get_option( 'gmt_offset' ) * HOUR_IN_SECONDS ));
    }
}

//******************************* get category path***********************************************
if(!function_exists('xyz_cls_get_taxonomy_parents')){
    function xyz_cls_get_taxonomy_parents( $id, $taxonomy = 'category', $link = false, $separator = '/', $nicename = false, $visited = array() ){
        $link=add_query_arg(array('category'=>$id) ,get_permalink(get_option('xyz_wp_cls_items')));
        $chain = '';
        $parent = get_term( $id, $taxonomy );

        if ( is_wp_error( $parent ) )
            return $parent;

        if ( $nicename )
            $name = $parent->slug;
        else
            $name = $parent->name;

        if ( $parent->parent && ( $parent->parent != $parent->term_id ) && !in_array( $parent->parent, $visited ) ){
            $visited[] = $parent->parent;
            $chain .= xyz_cls_get_taxonomy_parents( $parent->parent, $taxonomy, $link, $separator, $nicename, $visited );
        }

        if ( $link )
            $chain .= '<a href="' . $link . '" title="' . esc_attr( sprintf( __( "View all items in %s" ), $parent->name ) ) . '">'.$name.'</a>' . $separator;
        else
            $chain .= $name.$separator;

        return $chain;
    }
}

if(!function_exists('xyz_cls_get_childs')){
    function xyz_cls_get_childs($category_id){
        global $wpdb;
        $ret_new="";
        $cat_result=$wpdb->get_results($wpdb->prepare("SELECT name, ".$wpdb->prefix."terms.term_id ,".$wpdb->prefix."term_taxonomy.parent FROM ".$wpdb->prefix."term_taxonomy  JOIN ".$wpdb->prefix."terms ON ".$wpdb->prefix."term_taxonomy.term_id = ".$wpdb->prefix."terms.term_id WHERE ".$wpdb->prefix."term_taxonomy.taxonomy =  'xyz_cls_category' AND ".$wpdb->prefix."term_taxonomy.parent =%d", $category_id));
        foreach ($cat_result as $cat){
            $ret_new.=$cat->term_id.','.xyz_cls_get_childs($cat->term_id);
        }
        return $ret_new;
    }}

///*********************************get_subcategories**********************************************
if(!function_exists('xyz_cls_get_subcat')){
    function xyz_cls_get_subcat($parent){
        global $wpdb;
        $subcategory=$wpdb->get_results($wpdb->prepare("SELECT name, ".$wpdb->prefix."terms.term_id ,".$wpdb->prefix."term_taxonomy.parent FROM ".$wpdb->prefix."term_taxonomy  JOIN ".$wpdb->prefix."terms ON ".$wpdb->prefix."term_taxonomy.term_id = ".$wpdb->prefix."terms.term_id WHERE ".$wpdb->prefix."term_taxonomy.taxonomy =  'xyz_cls_category' AND ".$wpdb->prefix."term_taxonomy.parent =%d ORDER BY name ASC",$parent));
        return $subcategory;
    }
}

///*********************************get_subcategories comma seperated**********************************************
if(!function_exists('xyz_cls_get_cat_items')){
    function xyz_cls_get_cat_items($cat,$cid){
        global $wpdb;
        $cat=xyz_cls_get_childs($cat).$cat;
        $t=time();
        $cat_item=$wpdb->get_results($wpdb->prepare("SELECT * FROM ".$wpdb->prefix."xyz_cls_listing_details  JOIN  ".$wpdb->prefix."posts ON ".$wpdb->prefix."xyz_cls_listing_details.pid=".$wpdb->prefix."posts.ID  WHERE category  IN (". $cat.")  AND city_id=%d AND expiry>%d AND status=%s",$cid,$t,'publish'));
        //echo $wpdb->prepare("SELECT * FROM ".$wpdb->prefix."xyz_cls_listing_details  JOIN  ".$wpdb->prefix."posts ON ".$wpdb->prefix."xyz_cls_listing_details.pid=".$wpdb->prefix."posts.ID  WHERE category  IN (". $cat.")  AND city_id=%d AND expiry>%d AND  ".$wpdb->prefix."xyz_cls_listing_details.status='publish'",$cid,$t);die;
        return $cat_item;
    }
}

if(!function_exists('xyz_cls_get_premium_items')){
    function xyz_cls_get_premium_items($cid,$cat,$type,$order,$catid,$search){
        global $wpdb;
        if($catid)
            $cat=$catid;
        if($search)
            $str2="  AND ".$wpdb->prefix."posts.post_title='".$search."' " ;
        else $str2="";
        $t=time();
        if($type)
            $str=" AND item_type=".$type." " ;
        else $str="";
        $t=time();
        if($cat){$c=xyz_cls_get_childs($cat).$cat;
                 $cat="AND category IN (". $c.")";}
        else $cat="";
        //new $premium_items=$wpdb->get_results($wpdb->prepare("SELECT * FROM ".$wpdb->prefix."posts  JOIN ".$wpdb->prefix."xyz_cls_listing_details ON ".$wpdb->prefix."posts.ID = ".$wpdb->prefix."xyz_cls_listing_details.pid
        //new		WHERE ".$wpdb->prefix."posts.post_type = %s AND city_id=%d  AND expiry>%d    AND featured_expiry >=%d ".$cat." AND featured=%d ".$str."  ".$str2."  AND post_status=%s AND status=%s ORDER BY  last_display_time, $order ASC LIMIT 0,%d",'classifieds_listing',$cid,$t,strtotime(date('Y m d')),1,'publish','publish',get_option('xyz_cls_premium_items_displayed_per_page')));
        $premium_items=$wpdb->get_results($wpdb->prepare("SELECT * FROM ".$wpdb->prefix."posts  JOIN ".$wpdb->prefix."xyz_cls_listing_details ON ".$wpdb->prefix."posts.ID = ".$wpdb->prefix."xyz_cls_listing_details.pid WHERE ".$wpdb->prefix."posts.post_type = %s AND city_id=%d  AND expiry>=%d AND featured_expiry >=%d ".$cat." AND featured=%d ".$str."  ".$str2."  AND post_status=%s AND status=%s ORDER BY last_display_time, $order ASC LIMIT 0,%d",'classifieds_listing',$cid,$t,strtotime(date('Y-m-d')),1,'publish','publish',get_option('xyz_cls_premium_items_displayed_per_page')));
        return $premium_items;
    }
}

//*****************************user welcome***********************************************************
if(!function_exists('xyz_cls_welcome_user')){
    function xyz_cls_welcome_user(){
        if ( is_user_logged_in() ){
            $current_user = wp_get_current_user();
            return  sprintf(__("Welcome %s ,"),ucfirst( $current_user->user_login ));
        }
        else{
            return __( 'Welcome guest,');
        }
    }
}

//******************************get money format********************
if(!function_exists('xyz_cls_get_money_format')){
    function xyz_cls_get_money_format($money){
        if(get_option('xyz_cls_currency_position')==1){
            $money=number_format($money,2).' '.get_option('xyz_cls_currency_symbol');
        }
        else{
            $money=get_option('xyz_cls_currency_symbol')." ".number_format($money,2);
        }
        return $money;
    }
}

///******************************style**********************************************************************
if(!function_exists('xyz_cls_links')){
    function xyz_cls_links($links, $file) {
        $base = plugin_basename(XYZ_CLASSIFIEDS);
        if ($file == $base) {
            $links[] = '<a href="#"  title="FAQ">FAQ</a>';
            $links[] = '<a href="#"  title="Read Me">README</a>';
            $links[] = '<a href="http://xyzscripts.com/support/" class="xyz_support" title="Support"></a>';
            $links[] = '<a href="http://twitter.com/xyzscripts" class="xyz_twitt" title="Follow us on Twitter"></a>';
            $links[] = '<a href="https://www.facebook.com/xyzscripts" class="xyz_fbook" title="Like us on Facebook"></a>';
            $links[] = '<a href="https://plus.google.com/+Xyzscripts" class="xyz_gplus" title="+1 us on Google+"></a>';
            $links[] = '<a href="http://www.linkedin.com/company/xyzscripts" class="xyz_linkedin" title="Follow us on LinkedIn"></a>';
        }
        return $links;
    }
}
add_filter( 'plugin_row_meta','xyz_cls_links',10,2);

if(!function_exists('xyz_cls_plugin_get_version')){
    function xyz_cls_plugin_get_version(){
        if ( ! function_exists( 'get_plugins' ) )
            require_once( ABSPATH . 'wp-admin/includes/plugin.php' );
        $plugin_folder = get_plugins( '/' . plugin_basename( dirname( XYZ_CLASSIFIEDS ) ) );
        return $plugin_folder['wp-classifieds-listings.php']['Version'];
    }
}

//*************************get cfl group******************************************
if(!function_exists('xyz_remove_extra_newlines')){
    function xyz_remove_extra_newlines($str){
        $str=str_replace(array("\r\n","\r","\t"),"\n",$str);
        $lbcarr=explode("\n",$str);
        if(is_array($lbcarr)){
            $lbcarr_new=array();	
            foreach ($lbcarr as  $lbcarrvalue) {
                if(strlen(trim($lbcarrvalue))>0){
                    $lbcarr_new[]=$lbcarrvalue;
                }
            }
            $str=implode("\n",$lbcarr_new);
        }
        return  $str;
    }
}

if(!function_exists('xyz_cls_time_ago')){
    function xyz_cls_time_ago($date){ 
        if($date)
            $time = strtotime($date);
        $time = time() - $time; // to get the time since that moment
        if($time>(86400*7)) 
        	return $date;
        else{
            $tokens = array (
                //   31536000 => 'year',
                //      2592000 => 'month',
                //      604800 => 'week',
                86400 => 'day',
                3600 => 'hour',
                60 => 'minute',
                1 => 'second'
            );
            foreach ($tokens as $unit => $text) {
                if ($time < $unit) continue;
                $numberOfUnits = floor($time / $unit);
                $new_time = $numberOfUnits.' '.$text.(($numberOfUnits>1)?'s':'');
                return ''.$new_time.' ago';
            }            
            
        }
    }	
}