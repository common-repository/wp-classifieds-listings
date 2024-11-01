<?php
if ( ! defined( 'ABSPATH' ) )
    exit;
?>


<?php 
if(!$_POST && (isset($_GET['cls_blink'])&&isset($_GET['cls_blink'])=='en')){
    if (! isset( $_REQUEST['_wpnonce'] ) || ! wp_verify_nonce( $_REQUEST['_wpnonce'],'cls-blk')){
        wp_nonce_ays( 'cls-blk');
        exit;
    } 
    update_option('xyz_credit_link',"cls");
?>
<style type="text/css">
    .xyz_blink{
        display:none !important;
    }
</style>
<?php
}

if($_POST && isset($_POST['xyz_credit_link'])){
    $xyz_credit_link=sanitize_text_field($_POST['xyz_credit_link']);
    update_option('xyz_credit_link', $xyz_credit_link);
    update_option('xyz_gal_credit_dismiss', '0');
}

if((get_option('xyz_credit_link')=="0")&&(get_option('xyz_cls_credit_dismiss')=="0")){
?>
<div style="float:left;background-color: #FFECB3;border-radius:5px;padding: 0px 5px;margin-top: 10px;border: 1px solid #E0AB1B" id="xyz_backlink_div">
    Please do a favour by enabling backlink to our site.
    <a id="xyz_cls_backlink" style="cursor: pointer;" >Okay, Enable</a>.
    <a id="xyz_cls_backlink1" style="cursor: pointer;" >Dismiss</a>.
    <script type="text/javascript">
        var stat = 0;
        jQuery(document).ready(function() {
            jQuery('#xyz_cls_backlink').click(function() {
                xyz_cls_blink(1)

            });

            jQuery('#xyz_cls_backlink1').click(function() { 
                xyz_cls_blink(-1)

            });

            function xyz_cls_blink(stat){
                <?php $ajax_cls_nonce = wp_create_nonce( "xyz-cls-blink" );?>
                var dataString = {
                    action: 'xyz_cls_ajax_backlink',
                    security:'<?php echo $ajax_cls_nonce; ?>',
                    enable: stat
                };
                jQuery.post(ajaxurl, dataString, function(response) {
                	if(response==1){
                        jQuery("#xyz_backlink_div").html('Thank you for enabling backlink !');
                        jQuery("#xyz_backlink_div").css('background-color', '#D8E8DA');
                        jQuery("#xyz_backlink_div").css('border', '1px solid #0F801C');
               		}
               		if(response==-1){
						jQuery("#xyz_backlink_div").remove();
					}
                });
            }
        });
    </script>
</div>
<br/>
<?php
 }
?>
<div style="margin-top: 10px" >
    <table style="float:right; margin-right: 5px;">
        <tr>
            <td style="float:right;">
                <a class="xyz_cls_link"  target="_blank" href="http://help.xyzscripts.com/docs/wp-classifieds-listings/faq" style="margin-right:12px;"><b>FAQ</b></a>
            </td>
            <td style="float:right;">
                <a class="xyz_cls_link"  target="_blank" href="http://help.xyzscripts.com/docs/wp-classifieds-listings/"><b>Readme</b></a> |
            </td>
            <td style="float:right;">
                <a class="xyz_cls_link"  target="_blank" href="http://xyzscripts.com/wordpress-plugins/wp-classifieds-listings/details"><b>About</b></a> |
            </td>
            <td style="float:right;">
                <a class="xyz_cls_link"  target="_blank" href="http://xyzscripts.com"><b>XYZScripts</b></a> |
            </td>
        </tr>
    </table>
</div>

<div style="clear: both"></div>