<?php 
if ( ! defined( 'ABSPATH' ) )
    exit;
?>
<div class="xyz_cls_rightBox" id="col6">
    <div class="xyz_ttl" >
        Select City
    </div>
    <div class="clear">
    </div>
    <?php foreach ($city_result as $city){
        $cat_path= add_query_arg(array('city' => $city->city,'cid'=>$city->id),$cat_path);
        if(isset($_GET['cid']))
            $c=intval($_GET['cid']);
        else 
            $c=$_COOKIE[XYZ_CLS_COOKIE_CITY];
        if($c==$city->id){?>
    		<a class="xyz_cls_city_list_active" href="<?php echo $cat_path ;?>"><i class="fa fa-angle-right" aria-hidden="true"></i><?php echo $city->city; ?></a>
    <?php 
		}
		else{
	?>
    		<a class="xyz_cls_city_list"  href="<?php echo $cat_path ;?>"><i class="fa fa-angle-right" aria-hidden="true"></i><?php echo $city->city; ?></a>
    <?php 
		}
	}
	?>
</div>
