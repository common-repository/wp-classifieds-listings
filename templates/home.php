<?php
if ( ! defined( 'ABSPATH' ) )
    exit;


xyz_cls_get_template_part('header');
?>

<div>
    <div class="col-lg-9 col-md-9 col-sm-8 col-xs-12 xyz_cls_leftBox">
        <div class="row">
        <div class="col-lg-12 col-sm-12 col-md-12 col-xs-12 premium" style="margin-top:10px;">
            <h3 class="xyz_cls_main_head">
               <i class="fa fa-star xyz_cls_main_head_icon"></i> <?php _e('Choose Category','wp-classifieds');?> <i class="fa fa-star xyz_cls_main_head_icon"></i>
            </h3>
            <div class="xyz_cls_cat_head"></div>
           
        </div>
        <div class="clear">
        </div>
        <div style="white-space: nowrap;height:auto ">
   <?php
        $mpid='';
        $col=0;
        $array=array();
        $catTerms=get_terms('xyz_cls_category',array('hide_empty'=> 0,'orderby'=>'DESC','parent'=>0));

        if(get_option('xyz_cls_category_view')==0){
            $catTerms = get_terms('xyz_cls_category', array('hide_empty' => 0, 'orderby' => 'DESC',  'parent' =>0));
            foreach($catTerms as $index=> $catTerm){
                $result_subcat=xyz_cls_get_subcat($catTerm->term_id);
                $subcatcount=count($result_subcat);
                if($subcatcount==0){
                    $array[]=$catTerm->term_id;
                }
            }
            $catTerms = get_terms('xyz_cls_category',array('hide_empty'=> 0,'orderby'=>'DESC','parent'=>0,'exclude_tree'=>$array));
        }

        $count=count($catTerms);
        $q=intval($count/3);
        $r=$count%3;
        $c=$b=1;
        $l=0;

        if($r==0)
            $l=$q;
        else if ($r==1||$r==2)
            $l=$q+1;

        foreach($catTerms as $index=> $catTerm){
            $items_path=get_permalink(get_option('xyz_wp_cls_items'));
            $items_path=add_query_arg(array('city'=>$city,'cid'=>$cid,'category'=>$catTerm->term_id),$items_path);
            $result_subcat=xyz_cls_get_subcat($catTerm->term_id);
            $subcatcount=count($result_subcat);
            if($col==0){
    ?>
                <div class="col col-lg-4 col-sm-6 col-md-4 col-xs-12" id="col<?php echo $c; ?>">
    <?php
                $c=$c+1;
                $b=1;
            }
                $b=$b+1;
                ?>
                <div class="xyz_cls_boxes" style="width:100%">
                    <div class="xyz_cls_hd">
                        <a href="<?php echo $items_path;?>" title="<?php echo $catTerm->name;?>"><?php echo $catTerm->name;?></a>
                    </div>
    <?php
    $subcats = xyz_cls_get_subcat($catTerm->term_id);
    $childcount=count($subcats);
    $ci=0;
    foreach ($subcats as $sc){
        $cat_item=xyz_cls_get_cat_items($sc->term_id,$cid);
        $subitems_path=add_query_arg(array('city'=>$city,'cid'=>$cid,'category'=>$sc->term_id),$items_path);
        if($ci ==0 && $childcount >4){
            if($mpid =='')
                $mpid.=$sc->term_id;
            else
                $mpid.='_'.$sc->term_id;
        }

        $count=count($cat_item);
        if($ci ==0){
    ?>
        <div>
   <?php }
        else if($ci ==4){
    ?>
                        <span id="morespan_<?php echo $catTerm->term_id ;?>" class="xyz_cls_more_icon" onclick="xyz_cls_loadMore(<?php echo $catTerm->term_id ;?>);" title="More"><i class="fa fa-angle-down" aria-hidden="true">
                            </i></span>
        </div>
        <div id="morediv_<?php echo $catTerm->term_id;?>" style="display: none;">
    <?php 
        }
    ?>
        <a class="xyz_cls_cat_items" href="<?php echo $subitems_path;?>" title="<?php echo $sc->name.' ('.count($cat_item).')';?>"><i class="fa fa-angle-right" aria-hidden="true"></i> 
        <?php echo $sc->name.' ('.count($cat_item).')';?></a>
        <?php
            $ci=$ci+1;
            if($childcount >4 && $childcount ==$ci){
                        ?>
                        <span id="hidespan_<?php echo $catTerm->term_id;?>" class="xyz_cls_hide_icon" onclick="xyz_cls_hideMore(<?php echo $catTerm->term_id;?>);" title="Hide"><i class="fa fa-angle-up" aria-hidden="true">
                            </i>
                        </span>
                        <?php
        }
    }
    if($childcount >0){
?>  
                    </div>
<?php 
    }
    ?>
                </div>
                <?php
    if($b>$l)
    {
        $col=0;?>
            </div>
            <?php
    }
    else
    {
        $col=$col+1;
    }
}   ?>
            <input type="hidden" name="mpid" id="mpid" value="<?php echo $mpid; ?>" />
        </div>
        <?php // if(get_option('xyz_cls_category_view')==0)
{
    //echo '</div>';
}?>
    </div>
    </div>
    <div class="col-lg-3 col-md-3 col-sm-4 col-xs-12 xyz_cls_rtSide" style="margin-top:115px;">
        <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12" style="background-color:#f9f9f9; padding-top:10px; padding-bottom:15px; border:1px solid #eeeeee;">
            <?php  xyz_cls_get_template_part('recent-ads');?>
            <div class="clear">
            </div>
            <?php  xyz_cls_get_template_part('cities');?></div>
    </div>
</div>

<script type="text/javascript">
    jQuery(document).ready(function() {
        jQuery(".xyz_cls_rightBox").each(function(){
            jQuery(this).children(".row:last").addClass("lst");
        }
                                );
        jQuery(".xyz_cls_leftBox .col .xyz_cls_boxes").each(function(){
            jQuery(this).children("a:last").addClass("lst");
        }
                                           );
        jQuery(".xyz_cls_rightBox > a.row:even").addClass("evn");
        jQuery("input:text,input:password").addClass("xyz_cls_textBox");
        jQuery("input:button,input:submit").addClass("commonButton");
    }
                          );
    if(typeof xyz_cls_loadMore == 'undefined')
    {
        function xyz_cls_loadMore(id)
        {
            mpid=jQuery('#mpid').val();
            if(mpid !='')
            {
                mpidarray=mpid.split('_');
                mpidcount=mpidarray.length;
                for(i=0;i<mpidcount;i++)
                {
                    jQuery('#morediv_'+mpidarray[i]).hide(200);
                    jQuery('#morespan_'+mpidarray[i]).show();
                    jQuery('#hidespan_'+mpidarray[i]).hide();
                }
            }
            jQuery('#morediv_'+id).show(200);
            jQuery('#morespan_'+id).hide();
            jQuery('#hidespan_'+id).show();
        }
    }
    if(typeof xyz_cls_hideMore == 'undefined')
    {
        function xyz_cls_hideMore(id)
        {
            jQuery('#morediv_'+id).hide(200);
            jQuery('#morespan_'+id).show();
            jQuery('#hidespan_'+id).hide();
        }
    }
</script>

<div class="clear-fix"></div>