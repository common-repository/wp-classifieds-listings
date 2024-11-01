    <?php
if ( ! defined( 'ABSPATH' ) )
    exit;

xyz_cls_get_template_part('header');

?>
<script type="text/javascript">
    jQuery(document).ready(function(){
        jQuery(".xyz_cls_rightBox").each(function(){
            jQuery(this).children(".row:last").addClass("lst");
        });

        jQuery(".xyz_cls_leftBox .col .boxes").each(function(){
            jQuery(this).children("a:last").addClass("lst");
        });

        jQuery(".xyz_cls_rightBox > a.row:even").addClass("evn");
        jQuery("input:text,input:password").addClass("xyz_cls_textBox");
        jQuery("input:button,input:submit").addClass("commonButton");

        jQuery(".xyz_social_tag").css("left","0px");
    });
</script>
<div class="clear-fix"></div>
<div class="container">
    <div class="row" style="margin-top:10px;">
    <div class="xyz_cls_leftBox col-lg-9 col-sm-8 col-md-8 col-xs-12">
        <div class="listingTabs1 col-lg-12 col-sm-12 col-md-12 col-xs-12" style="border-bottom: 1px solid #bfe9ed;"/>
            <div class="row">
                <div class="xyz_cls_left xyz_cls_cat_link col-lg-12 col-sm-12 col-md-6 col-xs-12 " style="padding-top:10px;">
                    <span><a href="<?php echo  $all_cat;?>" ><?php _e('All categories','wp-classifieds-listings');?></a>
                    </span>
                    <i style="padding-top:5px;" class="fa fa-angle-double-right" aria-hidden="true"></i>
                    <span> 
                        <?php  
                            if($category_id) 
                                echo xyz_cls_get_taxonomy_parents($category_id, 'xyz_cls_category',$link, ' &raquo;');
                        ?>           
                    </span>
                </div>
                <div class="xyz_cls_listingTabs col-lg-6 col-sm-12 col-md-6 col-xs-12">
                    <a href="<?php echo $offered;?>" class="<?php echo $o;?>"><?php _e('Offered','wp-classifieds');?></a>
                    <a href="<?php echo $wanted;?>" class= "<?php echo $w;?>"><?php _e('Wanted','wp-classifieds');?></a>
                    <a href="<?php echo $all_adds;?>" class="<?php echo $a;?>"><?php _e('All Ads','wp-classifieds');?></a>
                </div>
            </div>
        </div>
        <div class="clear"></div>
<?php 
    remove_filter( 'post_thumbnail_html', 'xyz_cls_filter_post_thumbnail_html', 10, 5 ); 
    if(count($result_items)==0 && count($premium_result)==0){
?>
        <div class="xyz_cls_listingsRow col-lg-12 col-sm-12 col-md-12 col-xs-12">
            <div class="xyz_cls_leftside col-lg-12 col-sm-12 col-md-12 col-xs-12">
                <?php  _e('No Items Found.','wp-classifieds');?>
            </div>
        </div>
<?php
    }
    if(count($premium_result)>0){
        foreach($premium_result as $item){
            if(has_post_thumbnail($item->ID)){
                $thumbnail=get_the_post_thumbnail( $item->ID, array(100,100));
            }
            else{
                $img=get_option('xyz_cls_default_item_image');
                $imgpath=content_url() . '/uploads/xyz-cls-uploads/'.$img;
                $thumbnail='<img src="'.$imgpath.'" height="100" width="100"/>';
            }
            $postid=$item->ID;
            $item_path=get_permalink($postid);
            $old_date_timestamp =strtotime( $item->post_date);
            $new_date =xyz_cls_time_ago(xyz_cls_local_date_time('d M Y h:i:s A ', $old_date_timestamp));
?>
        <div class="xyz_cls_listingsRow col-lg-12 col-sm-12 col-md-12 col-xs-12 premium">
            <div class="xyz_cls_prem">PREMIUM</div>
            <div class="xyz_cls_leftside col-lg-3 col-sm-4 col-md-3 col-xs-12">
                <div class="xyz_cls_img_outer">
                    <?php echo $thumbnail;?>
                </div>
            </div>
            <div class="xyz_cls_desc col-lg-6 col-sm-4 col-md-6 col-xs-6">
                <?php  echo $item->locality;?> : <a href="<?php echo $item_path;?>"><?php echo $item->post_title;?></a>
                <p>
<?php 
                $desc=substr($item->post_content,0,10);
                echo  $desc;
?>
                <a href="<?php echo $item_path;?>"><?php _e('Read More','wp-classifieds');?></a>...</p>
                <div class="xyz_cls_date_deatail">
                    <i class="fa fa-calendar" aria-hidden="true"></i> <?php  echo $new_date;?>
                </div>
            </div>
            <div class="xyz_cls_otherDetails col-lg-3 col-sm-4 col-md-3 col-xs-6">
<?php 
    if($item->item_type==1){
        $type="Offered";
        $status='wantedStatus';
        $tab='date wantedTab';
    }
    else{
        $type="Wanted";
        $status='status';
        $tab='date';
    }   
?>  
                <div class="<?php echo $tab;?>">
                    <div class="<?php echo $status;?>"><?php echo $type;?></div>
                    <a href="<?php echo $item_path;?>" class="xyz_button_color_b"><?php _e('Reply Now','wp-classifieds');?></a>
                </div>         
            </div>
        </div>
<?php
        }
    }

    if(count($result_items)>0){
        foreach($result_items as $item){
            if(has_post_thumbnail($item->ID)){
                $thumbnail=get_the_post_thumbnail( $item->ID, array(100,100));
            }
            else{
                $img=get_option('xyz_cls_default_item_image');
                $imgpath=content_url() . '/uploads/xyz-cls-uploads/'.$img;
                $thumbnail='<img src="'.$imgpath.'" height="100" width="100"/>';
            }
            $postid=$item->ID;
            $item_path=get_permalink($postid);
            $old_date_timestamp =strtotime( $item->post_date);
            $new_date = xyz_cls_local_date_time('d M Y h:i:s A ', $old_date_timestamp);
?>
        <div class="xyz_cls_listingsRow col-lg-12 col-sm-12 col-md-12 col-xs-12">
            <div class="xyz_cls_leftside col-lg-3 col-sm-4 col-md-3 col-xs-12">
                <div class="xyz_cls_img_outer">
                    <?php echo $thumbnail;?>
                </div>
            </div>
            <div class="xyz_cls_desc col-lg-6 col-sm-4 col-md-6 col-xs-6">
                <?php  echo $item->locality; ?> : <a href="<?php echo $item_path;?>"><?php echo $item->post_title;?></a>
                <p><?php $desc=substr($item->post_content,0,150); echo $desc.'&nbsp;&nbsp';?><a href="<?php echo $item_path;?>"><?php _e( ' Read More','wp-classifieds');?></a>...</p>
                <div class="xyz_cls_date_deatail">
                    <i class="fa fa-calendar" aria-hidden="true"></i> <?php  echo $new_date;?>
                </div>
            </div>
            <div class="xyz_cls_otherDetails col-lg-3 col-sm-4 col-md-3 col-xs-6">
<?php 
    if( $item->item_type==1 ){
        $type="Offered";
        $status='wantedStatus';
        $tab='date wantedTab';
    }
    else{
        $type="Wanted";
        $status='status';
        $tab='date';    
    }
?>         
                <div class="<?php echo $tab;?>">
                    <div class="<?php echo $status;?>"><?php  echo $type;?></div>
                    <a href="<?php echo $item_path;?>" class="xyz_button_color_b"><?php _e( 'Reply Now','wp-classifieds');?></a>
                </div>       
            </div>
        </div>
<?php
        }
        echo $result->links();
    }
?>
    </div>
    <div class="xyz_cls_rtSide col-lg-3 col-sm-4 col-md-4 col-xs-12">
        <div class="col-lg-12 col-sm-12 col-sm-12 col-xs-12" style="background-color:#f9f9f9; padding-bottom:20px; margin-top:45px;">
<?php
    xyz_cls_get_template_part('categories');
?>      
            <div class="clear"></div>
<?php
    xyz_cls_get_template_part('cities');
?>  

        </div>
    </div> 
</div>
</div>