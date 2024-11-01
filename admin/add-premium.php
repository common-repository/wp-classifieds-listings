<?php
if ( ! defined( 'ABSPATH' ) )
    exit;

$_POST = stripslashes_deep($_POST);
$_GET = stripslashes_deep($_GET);

$post_id=isset($_GET['pid']) ? intval($_GET['pid']) : 0;

if(! isset( $_REQUEST['_wpnonce'] ) || ! wp_verify_nonce( $_REQUEST['_wpnonce'], 'cls-mpre_'.$post_id )) {
    wp_nonce_ays( 'cls-mpre_'.$post_id );
    exit;
} 

global $wpdb;
$result=$wpdb->get_row($wpdb->prepare("SELECT * FROM ".$wpdb->prefix."posts JOIN ".$wpdb->prefix."xyz_cls_listing_details ON ".$wpdb->prefix."posts.ID =pid WHERE pid=%d",$post_id));
if($result->featured==0){?>
<h3>
    <?php _e('Make Premium','wp-classifieds-listings');?></h3>
<table  class="widefat" style="width:99%;height:auto;">
    <tbody>
        <tr>
            <tr>
                <td>
                    <input type="hidden" value="<?php echo esc_attr($result->pid);?>" id="id">
                </td>
        </tr>
        <tr>
            <td>
                <br>
                <?php _e('Item Title','wp-classifieds-listings');?></td>
            <td>
                <br>
                :
            </td>
            <td>
                <br>
                <a href="<?php echo get_permalink($result->pid);?>" style="color:green"><?php echo $result->post_title;?></a>
            </td>
        </tr>
        <tr>
            <td >
                <br>
                <?php _e('Premium Price','wp-classifieds-listings');?></td>
            <td >
                <br>
                :
            </td>
            <td>
                <br>
                <?php echo xyz_cls_get_money_format(get_option('xyz_cls_premium_listing_price'));?></td>
        </tr>
        <tr>
            <td>
                <br>
                <?php _e('Premium listing period in days','wp-classifieds-listings');?></td>
            <td>
                <br>
                :
            </td>
            <td>
                <br>
                <?php echo get_option('xyz_cls_premium_listing_period')?></td>
        </tr>
        <tr>
            <td>
                <form action="https://www.paypal.com/cgi-bin/webscr" method="post" name="classifieds_payments-<?php echo $result->ID;?>" id="xyz_classifieds_payments-<?php echo $result->ID;?>">
                    <input type="hidden" name="cmd" value="_xclick">
                    <input type="hidden" name="item_name" value="Premium Listing-<?php echo esc_attr($result->post_title);?>">
                    <input type="hidden" name="item_number" value="<?php echo esc_attr($result->ID);?>">
                    <input type="hidden" name="amount" id="amount" value="<?php echo esc_attr(get_option('xyz_cls_premium_listing_price'));?>">
                    <input type="hidden" name="currency_code" value="<?php echo esc_attr(get_option('xyz_cls_paypal_currency'));?>">
                    <input type="hidden" name="notify_url" value="<?php echo esc_attr(get_site_url().'/index.php?xyz_wp_cls=paypalnotify');?>">
                    <input type="hidden" name="cancel_return" value="<?php echo esc_attr(admin_url().'?page=xyz_cls_payment&action=cancel');?>">
                    <input type="hidden" name="business" value="<?php echo esc_attr(get_option('xyz_cls_paypal_email'));?>">
                    <input type="hidden" name="no_shipping" value="1">
                    <input type="hidden" name="no_note" value="0">
                    <input type="hidden" name="custom" id="custom" value="<?php echo esc_attr( $result->post_author); ?>">
                    <input type="hidden" name="rm" value="2">
                    <input type="hidden" name="return" value="<?php echo esc_attr(admin_url().'?page=xyz_cls_payment&action=success');?>">
                    <div align="center">
                        <input type="submit" name="submit" class="xyz_cls_submit_button" value="<?php _e('Pay with Paypal','wp-classifieds-listings');?>">
                    </div>
                </form>
            </td>
        </tr>
    </tbody>
</table>
<?php }else {?>
<h3>
    <?php _e('Extend Premium','wp-classifieds-listings');?></h3>
<table  class="widefat" style="width:99%;height:auto;" >
    <tbody>
        <tr>
            <td>
                <br>
                <?php _e('Item Title','wp-classifieds-listings');?></td>
            <td>
                <br>
                :
            </td>
            <td>
                <br>
                <a href="<?php echo get_permalink($result->pid);?>" style="color:green"><?php echo $result->post_title;?></a>
            </td>
        </tr>
        <tr>
            <td >
                <br>
                <?php _e('Current Premium Expiry','wp-classifieds-listings');?></td>
            <td>
                <br>
                :
            </td>
            <td>
                <br>
                <?php 
                    if($result->featured_expiry!=0)
                        echo xyz_cls_local_date_time('d M Y  H:m:s A ', $result->featured_expiry);
                    else
                        echo "-N/A-";

                ?>
            </td>
        </tr>
        <tr>
            <td >
                <br>
                <?php _e('Premium Price','wp-classifieds-listings');?></td>
            <td >
                <br>
                :
            </td>
            <td>
                <br>
                <?php echo xyz_cls_get_money_format(get_option('xyz_cls_premium_listing_price'));?></td>
        </tr>
        <tr>
            <td>
                <br>
                <?php _e('Premium listing period in days','wp-classifieds-listings');?></td>
            <td>
                <br>
                :
            </td>
            <td>
                <br>
                <?php echo get_option('xyz_cls_premium_listing_period')?></td>
        </tr>
        <tr>
            <td>
                <form action="https://www.paypal.com/cgi-bin/webscr" method="post" name="classifieds_payments-<?php echo $result->ID;?>" id="xyz_classifieds_payments-<?php echo $result->ID;?>">
                    <input type="hidden" name="cmd" value="_xclick">
                    <input type="hidden" name="item_name" value="Premium Listing-<?php echo esc_attr($result->post_title);?>">
                    <input type="hidden" name="item_number" value="<?php echo esc_attr($result->ID);?>">
                    <input type="hidden" name="amount" id="amount" value="<?php echo esc_attr(get_option('xyz_cls_premium_listing_price'));?>">
                    <input type="hidden" name="currency_code" value="<?php echo esc_attr(get_option('xyz_cls_paypal_currency'));?>">
                    <input type="hidden" name="notify_url" value="<?php echo esc_attr(get_site_url().'/index.php?xyz_wp_cls=paypalnotify');?>">
                    <input type="hidden" name="cancel_return" value="<?php echo esc_attr(admin_url().'?page=xyz_cls_payment&action=cancel');?>">
                    <input type="hidden" name="business" value="<?php echo esc_attr(get_option('xyz_cls_paypal_email'));?>">
                    <input type="hidden" name="no_shipping" value="1">
                    <input type="hidden" name="no_note" value="0">
                    <input type="hidden" name="custom" id="custom" value="<?php echo  $result->post_author; ?>">
                    <input type="hidden" name="rm" value="2">
                    <input type="hidden" name="return" value="<?php echo esc_attr(admin_url().'?page=xyz_cls_payment&action=success');?>">
                    <div align="center">
                        <input type="submit" name="submit" class="xyz_cls_submit_button" value="<?php _e('Pay with Paypal','wp-classifieds-listings');?>">
                    </div>
                </form>
            </td>
        </tr>
    </tbody>
</table>				 	
<?php 
}
?>