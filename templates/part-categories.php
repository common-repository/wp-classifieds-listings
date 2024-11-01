<?php 
if ( ! defined( 'ABSPATH' ) )
    exit;
?>    

<div class="xyz_cls_rightBox" id="col6">
    <div class="xyz_ttl">
        <?php _e( 'Categories','wp-classifieds-listings');?>
    </div>
    <a class="xyz_item_list" href="<?php echo $subitems_path ;?>" onclick="this.style.color='green';"><i class="fa fa-angle-right" aria-hidden="true">
        </i> <?php _e( 'All Categories','wp-classifieds-listings');?></a>
<?php
if(count($cat_result)==0){
?>
  <a class="xyz_item_list" href="" onclick="this.style.color='green';"><i class="fa fa-angle-right" aria-hidden="true"></i> <?php  echo $sub->name;?></a>
<?php 
}
foreach($cat_result as $cat){
  $subitems_path= add_query_arg(array('city'=>$city,'cid'=>$cid,'category' => $cat->term_id),$subitems_path);
    ?>
    <a class="xyz_item_list" href="<?php echo $subitems_path ;?>" ><i class="fa fa-angle-right" aria-hidden="true"></i> <?php  echo $cat->name; ?></a>
    <?php 
}?>
</div>