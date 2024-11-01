<?php
if ( ! defined( 'ABSPATH' ) )
    exit;

global $wpdb;
$pagenum = isset( $_GET['pagenum'] ) ? absint( $_GET['pagenum'] ) : 1;
$limit = 20;
$offset = ( $pagenum - 1 ) * $limit;
$result=$wpdb->get_results("SELECT * FROM ".$wpdb->prefix."xyz_cls_payment_summary ORDER BY id DESC LIMIT $offset,$limit");
$total = $wpdb->get_var( "SELECT COUNT(`id`) FROM  ".$wpdb->prefix."xyz_cls_payment_summary" );
$num_of_pages = ceil( $total / $limit );
$page_links = paginate_links( array(
    'base' => add_query_arg( 'pagenum','%#%'),
    'format' => '',
    'prev_text' =>  '&laquo;',
    'next_text' =>  '&raquo;',
    'total' => $num_of_pages,
    'current' => $pagenum
) );
$i=0;
?>
<center>
    <div class='wrap'>
        <h3><?php _e( 'Payment History','wp-classifieds-listings');?></h3>
        <table   class="widefat" style="width:99%;">
            <thead>
                <tr>
                    <th>
                        <b>
                            <?php _e( 'Date','wp-classifieds-listings');?></b>
                    </th>
                    <th>
                        <b>
                            <?php _e( 'User','wp-classifieds-listings');?></b>
                    </th>
                    <th>
                        <b>
                            <?php _e( 'Amount','wp-classifieds-listings');?></b>
                    </th>
                    <th>
                        <b>
                            <?php _e( 'Payment Type','wp-classifieds-listings');?></b>
                    </th>
                    <th>
                        <b>
                            <?php _e( 'Payment Status','wp-classifieds-listings');?></b>
                    </th>
                </tr>
            </thead>
            <tfoot>
                <tr>
                    <th>
                        <b>
                            <?php _e( 'Date','wp-classifieds-listings');?></b>
                    </th>
                    <th>
                        <b>
                            <?php _e( 'User','wp-classifieds-listings');?></b>
                    </th>
                    <th>
                        <b>
                            <?php _e( 'Amount','wp-classifieds-listings');?></b>
                    </th>
                    <th>
                        <b>
                            <?php _e( 'Payment Type','wp-classifieds-listings');?></b>
                    </th>
                    <th>
                        <b>
                            <?php _e( 'Payment Status','wp-classifieds-listings');?></b>
                    </th>
                </tr>
            </tfoot>
            <tbody>
                <?php
					if(count($result)==0){?>
                <tr>
                    <td>
                        <?php _e( 'No history available','wp-classifieds-listings');?></td>
                </tr>
                <?php }
else{
    foreach ($result as $res){
        $c=($i%2==0) ? "": "alternate";
        $user_info = get_userdata($res->uid);?>
                <tr class="<?php echo $c;?>">
                    <td>
                        <?php echo date('d M Y' ,$res->date);?></td>
                    <td>
                        <?php echo !empty($user_info)?$user_info->display_name:"NA"?></td>
                    <td>
                        <?php echo xyz_cls_get_money_format($res->amount);?></td>
                    <td>
                        <?php if( $res->payment_type==1) echo 'Instant';?></td>
                    <td>
                        <?php if($res->payment_status==1) echo 'Completed';?></td>
                </tr>
                <?php $i++;
    }
}
if ( $page_links ) {
    echo '<div class="tablenav" style="width:99%;"><div class="tablenav-pages" style="margin: 1em 0;">' . $page_links . '</div></div>';
}
?>
            </tbody>
        </table>
    </div>
</center>
