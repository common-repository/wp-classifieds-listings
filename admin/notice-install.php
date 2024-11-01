<?php 
if ( ! defined( 'ABSPATH' ) )
    exit;
?>

<br>
<div style="margin-top: 20px; clear: left; width: 98%;">
    <div style="background: #d7f5ff;margin: 5px 0 20px 0;border: solid 1px #bad5df;border-radius: 4px;">
    <h4 style="color:#000;font-family:fantasy;font-size:large;padding-left: 10px;">
        <?php _e( '<strong>Welcome to WP Classifieds</strong> ', 'wp-classifieds' ); ?></h4>
    <p  style="padding-left: 20px;">
        <a href="<?php echo add_query_arg('install_classifieds_pages', 'true', admin_url('admin.php?page=xyz_cls_settings') ); ?>" class="button-secondary" style="color: #0074a2;"><?php _e( 'Install Classifieds Pages', 'wp-classifieds' ); ?></a>
        <a class="button-secondary" href="<?php echo add_query_arg('skip_install_classifieds_pages', 'true', admin_url('admin.php?page=xyz_cls_settings') ); ?>" style="color: #0074a2;"><?php _e( 'Skip setup', 'wp-classifieds' ); ?></a>
    </p>
    </div>
</div>