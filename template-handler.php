<?php
if ( ! defined( 'ABSPATH' ) )
    exit;

add_filter( 'the_content', 'xyz_cls_filter_content');
if(!function_exists('xyz_cls_filter_content')){
    function xyz_cls_filter_content($content) {
        global $post;
        if(wp_get_theme()!='XYZ Classifieds Basic'){

                if ($post->post_type == 'classifieds_listing') {
                    if(is_numeric(ini_get('output_buffering'))){
                        ob_start();
                        include( dirname( __FILE__ ) . '/templates/single.php' );
                        $xyz_cls_fcont = xyz_remove_extra_newlines(ob_get_contents());
                        ob_clean();
                        ob_end_flush();
                        return $xyz_cls_fcont;
                    }
                    else{
                        include( dirname( __FILE__ ) . '/templates/single.php');
                    }
                }
                else
                    return $content;
            }
            else
                return $content;
    }
}

add_filter( 'the_title', 'xyz_cls_filter_title',10,2);
if(!function_exists('xyz_cls_filter_title')){
function xyz_cls_filter_title($title,$id) {
	if ($id!=NULL && get_post_type($id)=="classifieds_listing" && is_single($id)) {
    	return "";
  	}
  // otherwise returns the database title
  else
  	return $title;
}
}

if(!function_exists('xyz_cls_remove_link')){
    function xyz_cls_remove_link( $format, $link ) {
        return false;
    }
}

add_filter( 'previous_post_link', 'xyz_cls_remove_link',10,2 );
add_filter( 'next_post_link', 'xyz_cls_remove_link',10,2 );

// define the post_thumbnail_html callback 
if(!function_exists('xyz_cls_filter_post_thumbnail_html')){
    function xyz_cls_filter_post_thumbnail_html( $html, $post_id, $post_thumbnail_id, $size, $attr ){ 
        if(wp_get_theme()!='XYZ Classifieds Basic'){
                if ($post_id!=NULL && get_post_type($post_id)=="classifieds_listing" && is_single($post_id)){
                   return "";
                }
                    // otherwise returns the database title
                else
                      return $html;
              }
                  else
                    return $html;
    }
}
// add the filter 
add_filter( 'post_thumbnail_html', 'xyz_cls_filter_post_thumbnail_html', 10, 5 ); 
if(!function_exists('xyz_cls_get_template_part')){
    function xyz_cls_get_template_part( $name) {
        global $wpdb;
        $city="";
        $cid="";
        $t=time();
        if(isset($_GET['cid'])){
            $cid=intval($_GET['cid']);
            $city=sanitize_text_field($_GET['city']);
        }
        else{
            if(isset($_COOKIE[XYZ_CLS_COOKIE_CNAME]))
                $city=$_COOKIE[XYZ_CLS_COOKIE_CNAME];
            if(isset($_COOKIE[XYZ_CLS_COOKIE_CITY]))
                $cid=$_COOKIE[XYZ_CLS_COOKIE_CITY];
        }
        switch ($name){
            case 'header':	$page=get_permalink(get_option('xyz_wp_cls_items'));
            
            $catid="";
            $search="";
            if(isset($_GET['catid'])){
                $catid=intval($_GET['catid']);
                $search=sanitize_text_field($_GET['search']);
            }
            require( dirname( __FILE__ ) .'/templates/part-header.php' );
            break;
            
            case 'cities': $cat_path=get_permalink(get_option('xyz_wp_cls_home'));
            $result1=$wpdb->get_row($wpdb->prepare("SELECT * FROM ".$wpdb->prefix."xyz_cls_cities WHERE id=%d",$cid));
            $city_result=$wpdb->get_results($wpdb->prepare("SELECT * FROM ".$wpdb->prefix."xyz_cls_cities WHERE scode=%s AND ccode=%s ORDER BY city ASC",$result1->scode,get_option('xyz_cls_default_country')));
            require( dirname( __FILE__ ) .'/templates/part-cities.php' );
            break;
            
            case 'categories':  $subitems_path=get_permalink(get_option('xyz_wp_cls_items'));
            $category_id="";
            if(isset($_GET['category'])){
                $category_id=intval($_GET['category']);
            }
            if(isset($_GET['catid'])){
                $catid=intval($_GET['catid']);
                $category_id=$catid;
            }
            else 
            	$catid="";
            $cat_result=$wpdb->get_results($wpdb->prepare("SELECT name, ".$wpdb->prefix."terms.term_id ,".$wpdb->prefix."term_taxonomy.parent FROM ".$wpdb->prefix."term_taxonomy  JOIN ".$wpdb->prefix."terms ON ".$wpdb->prefix."term_taxonomy.term_id = ".$wpdb->prefix."terms.term_id
WHERE ".$wpdb->prefix."term_taxonomy.taxonomy =  'xyz_cls_category' AND ".$wpdb->prefix."term_taxonomy.parent =%d ORDER BY name ASC", $category_id));
            $sub=$wpdb->get_row($wpdb->prepare("SELECT name FROM ".$wpdb->prefix."terms WHERE term_id=%d ", $category_id));
            require( dirname( __FILE__ ) .'/templates/part-categories.php' );
            break;
            
            case 'recent-ads':	$recent=$wpdb->get_results($wpdb->prepare("SELECT * FROM ".$wpdb->prefix."posts JOIN ".$wpdb->prefix."xyz_cls_listing_details ON ".$wpdb->prefix."posts.ID = pid
WHERE city_id =%d AND expiry>%d	 AND post_status=%s AND status=%s ORDER BY ".$wpdb->prefix."posts.ID DESC LIMIT 0 ,3 ",$cid,$t,'publish','publish'));
            require( dirname( __FILE__ ) .'/templates/part-recent-ads.php' );
            break;
            
            default: require( dirname( __FILE__ ) .'/templates/part-'.$name.'.php' );
            break;
        }
    }
}
