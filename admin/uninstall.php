<?php
if ( ! defined( 'ABSPATH' ) )
    exit;

if ( !function_exists( 'xyz_cls_network_destroy' ) ) {
    function xyz_cls_network_destroy($networkwide) {
        global $wpdb;
        if (function_exists('is_multisite') && is_multisite()) {
            // check if it is a network activation - if so, run the activation function for each blog id
            if ($networkwide) {
                $old_blog = $wpdb->blogid;
                // Get all blog ids
                $blogids = $wpdb->get_col("SELECT blog_id FROM $wpdb->blogs");
                foreach ($blogids as $blog_id) {
                    switch_to_blog($blog_id);
                    xyz_cls_uninstall();
                }
                switch_to_blog($old_blog);
                return;
            }
        }
        xyz_cls_uninstall();
    }
}

if ( !function_exists( 'xyz_cls_uninstall' ) ) {
    function xyz_cls_uninstall(){
        global $wpdb;
        $pluginName = 'xyz-wp-classifieds/xyz-wp-classifieds.php';
        if (!is_plugin_active($pluginName)){
            if(get_option('xyz_credit_link')=="cls"){
                update_option("xyz_credit_link", '0');
            }
            delete_option( 'xyz_cls_item_expiry');
            delete_option( 'xyz_cls_premium_items_displayed_per_page' );
            delete_option( 'xyz_cls_premium_listing_period' );
            delete_option( 'xyz_cls_premium_listing_price' );
            delete_option( 'xyz_cls_item_display_order' );
            delete_option( 'xyz_cls_default_item_image');
            delete_option( 'xyz_cls_default_item_status' );
            delete_option( 'xyz_cls_default_item_image');
            delete_option('xyz_cls_auth_token');
            delete_option( 'xyz_cls_gallery' );
            delete_option( 'xyz_cls_currency_symbol');
            delete_option( 'xyz_cls_currency_position');
            delete_option( 'xyz_cls_paypal_email' );
            delete_option( 'xyz_cls_paypal_currency');
            delete_option( 'xyz_cls_default_sender_email' );
            delete_option( 'xyz_cls_default_sender_name' );
            delete_option( 'xyz_cls_general_notification_email' );
            delete_option( 'xyz_cls_default_itm');
            delete_option( 'xyz_cls_premium_listing_enable');
            delete_option( 'xyz_cls_default_country');
            delete_option( 'xyz_cls_cron_runnig_time');
            delete_option('xyz_cls_item_type');
            delete_option( 'xyz_wp_cls_home');
            delete_option( 'xyz_wp_cls_register' );
            delete_option( 'xyz_wp_cls_items');
            delete_option( 'xyz_wp_cls_region');
            delete_option( 'xyz_wp_cls_city');
            delete_option( 'xyz_cls_item_count');
            delete_option( 'xyz_wp_cls_forgotpassword');
            delete_option( 'xyz_cls_login');
             delete_option( 'xyz_cls_needs_pages');
            delete_option( 'xyz_cls_disable_dflt_login');
            delete_option('xyz_cls_custom_role');
            $roles=get_option('xyz_cls_roles');
            foreach ($roles as $r)
            {
                $r=get_role($r);
                if($r->has_cap('edit_cls_listings'))
                    $r->remove_cap( 'edit_cls_listings' );
                if($r->has_cap('publish_cls_listings'))
                    $r->remove_cap( 'publish_cls_listings' );
                if($r->has_cap('delete_cls_listings'))
                    $r->remove_cap( 'delete_cls_listings' );
                if($r->has_cap('delete_published_cls_listings'))
                    $r->remove_cap( 'delete_published_cls_listings' );
                if($r->has_cap('edit_published_cls_listings'))
                    $r->remove_cap('edit_published_cls_listings' );
            }
            delete_option('xyz_cls_roles');
            if(get_role('classifieds_user'))
                remove_role('classifieds_user');
            $wpdb->query("DELETE FROM ".$wpdb->prefix."postmeta meta LEFT JOIN ".$wpdb->prefix."posts posts ON posts.ID = meta.post_id WHERE posts.post_type='classifieds_listing'");
            $wpdb->query("DELETE FROM ".$wpdb->prefix."posts WHERE post_type='classifieds_listing'");
            /* taxonomy delete*/
            $wpdb->query("DELETE FROM ".$wpdb->prefix."term_relationships tr LEFT JOIN ".$wpdb->prefix."term_taxonomy tt ON tt.term_taxonomy_id = tr.term_taxonomy_id WHERE tt.taxonomy='xyz_cls_category'");
            $terms = get_terms( 'xyz_cls_category', array( 'fields' => 'ids', 'hide_empty' => false ) );
            foreach ( $terms as $value ) {
                wp_delete_term( $value, 'xyz_cls_category' );
            }
        }
    }
}
register_uninstall_hook( XYZ_CLASSIFIEDS, 'xyz_cls_network_destroy' );
