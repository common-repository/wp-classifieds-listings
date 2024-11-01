<?php

if ( ! defined( 'ABSPATH' ) )
    exit;

$cid='';
$city='';

$page=get_permalink(get_option('xyz_wp_cls_items'));
if(isset($_GET['cid']))
{
    $cid=intval($_GET['cid']);
    $city=sanitize_text_field($_GET['city']);
}
else if(isset($_COOKIE[XYZ_CLS_COOKIE_CNAME]))
{
    $city=$_COOKIE[XYZ_CLS_COOKIE_CNAME];
    $cid=$_COOKIE[XYZ_CLS_COOKIE_CITY];
}
else {
    $cid='';
    $city='';
}

$catid="";
$search="";
if(isset($_GET['catid']))
{
    $catid=intval($_GET['catid']);
    $search=sanitize_text_field($_GET['search']);
}
?>
<?php
        if(wp_get_theme()=='XYZ Classifieds Basic'){?>


<style>
    .search_head h2{
        color:#fff !important;
        margin-top: 12%;
    }
</style>

        
<div class="col-lg-12" style="height:500px;">
    <div class="row">
       
        <div style="width:100%; height:100%; background-color:rgba(0,0,0,0.5); position:absolute; z-index:1;">
        <?php
        }
        ?>
            <div class="col-xs-12">
                <div class="xyz_cls_search_head">
                    <h2>
                        <?php
                        if($city=='')
                            _e(' What are you looking for ?','wp-classifieds');
                        else
                            printf(__(' What are you looking for  in %s ?'),$city);?>
                    </h2>
                </div>
                <div class="xyz_cls_menuBar">
                    <form class="form-inline" name="itemsearch" id="itemsearch" method="post" action="" >
                        <div class="form-group">
                            <select name="catid" id="catid" class="form-control xyz_cls_search_field"><option value='0'><?php _e('---Select---','wp-classifieds');?></option><?php echo xyz_cls_get_category_display(0,0,$catid);?></select>
                        </div>
                        <div class="form-group">
                            <input type="text"  name="search" id="search" class="form-control xyz_cls_search_field" value="<?php echo $search;?>">
                        </div>
                        <div class="form-group">
                            <button class="xyz_cls_post_btn" type="submit" name="go" id="go" title="<?php _e('Search','wp-classifieds-listings');?>">
                                <i class="fa fa-search"></i>
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        <?php
            if(wp_get_theme()=='XYZ Classifieds Basic'){
        ?>
        </div>
        
        <script async defer src="https://maps.googleapis.com/maps/api/js?key=AIzaSyD8vRlwZ_DSzp6_H8_XeE9ejPvg3ycHLjo&callback=initMap"></script>
        <div style='overflow:hidden;height:500px;width:px;'>
            <div id='gmap_canvas' style='height:500px;width:px;'>
            </div>
            <style>
                #gmap_canvas img{
                    max-width:none!important;
                    background:none!important
                }
            </style>
        </div>       
    </div> 
</div>
    <?php
            }
        ?>
<script>
    var map, infoWindow;
    function initMap() {
        map = new google.maps.Map(document.getElementById('gmap_canvas'), {
            center: {lat: 51.5073509, lng: -0.12775829999998223},
            zoom: 12
        }
                                 );
        infoWindow = new google.maps.InfoWindow;
        // Try HTML5 geolocation.
        if (navigator.geolocation) {
            navigator.geolocation.getCurrentPosition(function(position) {
                var pos = {
                    lat: position.coords.latitude,
                    lng: position.coords.longitude
                };
                var marker = new google.maps.Marker({
                    position: pos,
                    map: map
                });
               
                infoWindow.open(map);
                map.setCenter(pos);
            },  function() {
                    handleLocationError(true, infoWindow, map.getCenter());
                });
        }
        else {
            // Browser doesn't support Geolocation
            handleLocationError(false, infoWindow, map.getCenter());
        }
    }
    function handleLocationError(browserHasGeolocation, infoWindow, pos) {
        infoWindow.setPosition(pos);
        infoWindow.setContent(browserHasGeolocation ?
                              'Error: The Geolocation service failed.' :
                              'Error: Your browser doesn\'t support geolocation.');
        infoWindow.open(map);
    }
</script>

<script type="text/javascript">
    jQuery(document).ready(function(){
        jQuery("#itemsearch").submit(function(){
            var search =jQuery("#search").val();
            var catid=jQuery("#catid").val();
            if(search==""){
                alert("<?php _e('Enter keyword for search.','wp-classifieds-listings');?>");
                jQuery("#search").focus();
                return false;
            }
            else if(catid==0){
                alert("<?php _e('Please select category.','wp-classifieds-listings');?>");
                jQuery("#catid").focus();
                return false;
            }
            else{
                var page='<?php echo $page;?>';
                window.event.returnValue = false;
                document.location.href = page+'?catid='+catid+'&search='+search;
                return false;
            }
        });
    });
</script>
<div class="wrap">