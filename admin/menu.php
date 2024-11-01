<?php

if ( ! defined( 'ABSPATH' ) )
	 exit;

add_action( 'wp', 'xyz_cls_get_pages' );
function xyz_cls_get_pages(){
	global $wp_query;
	if(!empty($wp_query->post)){
		$xyz_current_page = $wp_query->post->ID;
		
		if(isset($_GET['city'])&&isset($_GET['cid'])){
			ob_start();
		}
		 if(get_option('xyz_wp_cls_register')== $xyz_current_page){
		 	ob_start();
		 }
		  if(get_option('xyz_wp_cls_home')== $xyz_current_page){
		 	ob_start();
		 }
		  if(get_option('xyz_wp_cls_region')== $xyz_current_page){
		 	ob_start();
		 }
		  if(get_option('xyz_wp_cls_city')== $xyz_current_page){
		 	ob_start();
		 }
		 if(get_option('xyz_wp_cls_forgotpassword')== $xyz_current_page){
		 	ob_start();
		 }	
	}
}

//********************************Create Pages***********************************
if(!function_exists('xyz_cls_create_pages')){
    function xyz_cls_create_pages() {
        
        if(get_option('xyz_wp_cls_register')==""){
        	xyz_cls_create_page( 'register', 'xyz_wp_cls_register', __( 'Register', 'wp-classifieds-listings' ), '[xyz_wp_cls_register]' );
        }
        
        if(get_option('xyz_wp_cls_forgotpassword')==""){
        	xyz_cls_create_page(  'forgot-password', 'xyz_wp_cls_forgotpassword', __( 'Lost Password', 'wp-classifieds-listings' ), '[xyz_wp_cls_forgotpassword]' );
        }
       
        if(get_option('xyz_wp_cls_home')==""){
        	xyz_cls_create_page( 'home', 'xyz_wp_cls_home', __( 'Home', 'wp-classifieds-listings' ), '[xyz_wp_cls_home]' );
        }
        
        if(get_option('xyz_wp_cls_region')==""){
        	xyz_cls_create_page( 'region', 'xyz_wp_cls_region', __( 'Region', 'wp-classifieds-listings' ), '[xyz_wp_cls_region]');
        }
        
        if(get_option('xyz_wp_cls_city')==""){
        	xyz_cls_create_page(   'city' , 'xyz_wp_cls_city', __( 'City', 'wp-classifieds-listings' ), '[xyz_wp_cls_city]' );
        }
        
        if(get_option('xyz_wp_cls_categories')==""){
        	xyz_cls_create_page( 'useritems', 'xyz_wp_cls_items', __( 'Items', 'wp-classifieds-listings' ), '[xyz_wp_cls_items]');
        }
        
    }
}


if(!function_exists('xyz_cls_admin_init')){
    function xyz_cls_admin_init() {
        if ( ! empty( $_GET['install_classifieds_pages'] ) ) {
            xyz_cls_create_pages();
            update_option( 'xyz_cls_needs_pages',0);
            wp_safe_redirect( admin_url( 'index.php?page=xyz_cls_settings&wc-installed=true' ) );
            exit;
        }
        elseif ( ! empty( $_GET['skip_install_classifieds_pages'] ) ) {
            delete_option( 'xyz_cls_needs_pages');
            wp_safe_redirect( admin_url( 'index.php?page=xyz_cls_settings' ) );
            exit;
        } 
    }
}
add_action('admin_init', 'xyz_cls_admin_init');


if(!function_exists('xyz_cls_admin_install_notices')){
    function xyz_cls_admin_install_notices() {
        global $woocommerce;
        if ( get_option( 'xyz_cls_needs_pages' ) == 1 && current_user_can('administrator')) {
            include( 'notice-install.php' );
        }
    }
}

if ( get_option( 'xyz_cls_needs_pages' ) == 1 ) {
    add_action( 'admin_notices', 'xyz_cls_admin_install_notices' );
}

//********************************add menu page******************************************************************************************
add_action("admin_menu","xyz_cls_menu");
if ( !function_exists( 'xyz_cls_menu' ) ) {
	function xyz_cls_menu()
	{
		$imgpath1=plugins_url(XYZ_CLASSIFIEDS_DIR.'/images/');
		add_menu_page( "XYZ Classifieds", "XYZ Classifieds", "manage_options", "xyz_cls_settings", "xyz_cls_settings" ,$imgpath1.'s.png',30);
		add_submenu_page( "xyz_cls_settings", "Settings", "Settings", "manage_options", "xyz_cls_settings", "xyz_cls_settings" );
		add_submenu_page( "xyz_cls_settings", "Email Templates", "Email Templates", "manage_options", "xyz_cls_email_template", "xyz_cls_email_template" );
		add_submenu_page( "xyz_cls_settings", "Cities", "Manage Cities", "manage_options", "manage_cities", "xyz_cls_manage_cities" );
		add_submenu_page( "xyz_cls_settings", "Regions", "Manage Regions", "manage_options", "manage_regions", "xyz_cls_manage_regions" );
		add_submenu_page( "xyz_cls_settings", "Payment Summary", "Payment History", "manage_options", "payment_summary", "xyz_cls_payment_summary_admin" );
		add_submenu_page('xyz_cls_settings', 'About', 'About','manage_options','xyz-cls-about','xyz_cls_about');

		add_menu_page( "null", "XYZ Classifieds Premium", 'edit_cls_listings', "xyz_cls_premium", "xyz_cls_premium" );
		add_menu_page( "null", "XYZ Classifieds Payment", 'edit_cls_listings', "xyz_cls_payment", "xyz_cls_payment" );
		//  add_menu_page( "null", "XYZ Classifieds Email Template", 'edit_cls_template', "xyz_cls_email_template", "xyz_cls_email_template" );
		if(current_user_can('edit_cls_listings')&& !current_user_can('administrator'))
			add_menu_page( "Payments History", "Payment History", 'edit_cls_listings', "xyz_cls_payment_summary", "xyz_cls_payment_summary" ,$imgpath1.'pl.png',40);
		remove_menu_page('xyz_cls_premium');
		remove_menu_page('xyz_cls_payment');
	}
}

if( !function_exists('xyz_cls_about')){
	function xyz_cls_about(){
		require( dirname( __FILE__ ) . '/header.php');
		require( dirname( __FILE__ ) . '/about.php');
		require( dirname( __FILE__ ) . '/footer.php');
	}
}

if ( !function_exists( 'xyz_cls_payment_summary_admin' ) ) {
	function xyz_cls_payment_summary_admin(){
		require( dirname( __FILE__ ) . '/header.php' );
		require( dirname( __FILE__ ) . '/payment-summary-admin.php' );
		require( dirname( __FILE__ ) . '/footer.php' );
	}
}
if ( !function_exists( 'xyz_cls_payment_summary' ) ) {
	function xyz_cls_payment_summary(){
		require( dirname( __FILE__ ) . '/header.php' );
		require( dirname( __FILE__ ) . '/payment-summary-user.php' );
		require( dirname( __FILE__ ) . '/footer.php' );
	}
}
if ( !function_exists( 'xyz_cls_payment' ) ) {
	function xyz_cls_payment()
	{
		require( dirname( __FILE__ ) . '/header.php' );
		require( dirname( __FILE__ ) . '/paypal-payment.php' );
		require( dirname( __FILE__ ) . '/footer.php' );
	}
}
if(!function_exists('xyz_cls_premium')){
    function xyz_cls_premium()
    {   require( dirname( __FILE__ ) . '/header.php' );
     $_POST = stripslashes_deep($_POST);
     $_GET = stripslashes_deep($_GET);
     require( dirname( __FILE__ ) . '/add-premium.php' );
     require( dirname( __FILE__ ) . '/footer.php' );
    }
}
if ( !function_exists( 'xyz_cls_email_template' ) ) {
	function xyz_cls_email_template(){
		require( dirname( __FILE__ ) . '/header.php' );
		require( dirname( __FILE__ ) . '/email-template.php' );
		require( dirname( __FILE__ ) . '/footer.php' );
	}
}
if(!function_exists('xyz_cls_manage_regions')){
    function xyz_cls_manage_regions()
    {
        $_POST = stripslashes_deep($_POST);
        $_GET = stripslashes_deep($_GET);
        require( dirname( __FILE__ ) . '/header.php' );
        require( dirname( __FILE__ ) . '/regions.php' );
        require( dirname( __FILE__ ) . '/footer.php' );
    }
}
if ( !function_exists( 'xyz_cls_manage_cities' ) ) {
	function xyz_cls_manage_cities()
	{   
		require( dirname( __FILE__ ) . '/header.php' );
		require( dirname( __FILE__ ) . '/cities.php' );
		require( dirname( __FILE__ ) . '/footer.php' );
	}
}
if ( !function_exists( 'xyz_cls_settings' ) ) {
	function xyz_cls_settings()
	{
		require( dirname( __FILE__ ) . '/header.php' );
		require( dirname( __FILE__ ) . '/settings.php' );
		require( dirname( __FILE__ ) . '/footer.php' );
	}
}
//********************disable wp-login*******************************************
add_action('init', 'xyz_cls_prevent_wp_login');
if ( !function_exists( 'xyz_cls_prevent_wp_login' ) ) {
	function xyz_cls_prevent_wp_login() {
		global $pagenow;
		if(get_option('xyz_cls_disable_dflt_login')=='true'&&get_option('xyz_cls_login')==2)
		{
			$action = (isset($_GET['action'])) ? $_GET['action'] : '';
			if( $pagenow == 'wp-login.php' && ! in_array($action, array('logout', 'lostpassword', 'rp')))
			{
				$page =get_permalink(get_option('xyz_wp_cls_register'));
				wp_redirect($page);
				exit();
			}
			if( $pagenow == 'wp-login.php' &&  in_array($action, array( 'lostpassword')))
			{
				$page = get_permalink(get_option('xyz_wp_cls_forgotpassword'));
				wp_redirect($page);
				exit();
			}
		}
	}
}
//*******************************add status column to users table ***************************************
add_filter('manage_users_columns', 'xyz_cls_add_user_status_column');
if ( !function_exists( 'xyz_cls_add_user_status_column' ) ) {
	function xyz_cls_add_user_status_column($columns)
	{
		$columns['status'] = 'Status';
		return $columns;
	}
}
add_action('manage_users_custom_column',  'xyz_cls_show_user_status_column_content', 10, 3);


//********************Show User Status***************
if ( !function_exists( 'xyz_cls_show_user_status_column_content' ) ) {
	function xyz_cls_show_user_status_column_content($value, $column_name, $user_id)
	{
		$status='';
		if(metadata_exists('user', $user_id, 'xyz_wp_user_status')==true)
		{
			$user = get_user_meta($user_id,'xyz_wp_user_status',true);
			if ( 'status' == $column_name )
			{
				if($user==1)
					$status='<span style="color:green;">Active</span>';
				else if($user==0)
					$status='<span style="color:red;">Disabled</span>';
			 	else 
			 		$status='<span style="color:red;" >Email not confirmed</span>';
			 	
			 	return $status;
			}
		}
		else $status='<span style="color:green;">Active</span>';
		return $status;
	}
}
//***************************enqueue scripts****************************************************
if(!function_exists( 'xyz_cls_scripts')){
	function xyz_cls_scripts(){   
		wp_enqueue_script('jquery');
		wp_register_style('xyz_cls_style-admin', plugins_url(XYZ_CLASSIFIEDS_DIR.'/css/admin-style.css'));
		wp_enqueue_style('xyz_cls_style-admin');

	 wp_enqueue_script('jquery-ui-datepicker');
	 
	 wp_register_style('xyz_cls_jquery-ui', plugins_url(XYZ_CLASSIFIEDS_DIR.'/css/jquery-ui.css'));
	 wp_enqueue_style('xyz_cls_jquery-ui');
	}
}
add_action("admin_enqueue_scripts","xyz_cls_scripts");

if ( !function_exists( 'xyz_cls_user_scripts' ) ) {
	function xyz_cls_user_scripts(){
		wp_enqueue_script('jquery');
		wp_localize_script('jquery','xyz_cls_ajax_object',array( 'ajax_url' => admin_url('admin-ajax.php' )) );

		wp_register_style('bootstrap', plugins_url(XYZ_CLASSIFIEDS_DIR.'/css/bootstrap.css'));
	 	wp_enqueue_style('bootstrap');
	 	
		wp_register_style('xyz_cls_style',plugins_url(XYZ_CLASSIFIEDS_DIR.'/css/style.css'));
		wp_enqueue_style('xyz_cls_style');

		wp_register_style('xyz_cls_style_pagination', plugins_url(XYZ_CLASSIFIEDS_DIR.'/css/pagination.css'));
		wp_enqueue_style('xyz_cls_style_pagination');


if(wp_get_theme()!='XYZ Classifieds Basic'){


	 	 wp_enqueue_script( 'xyz_cls_basic-script', plugins_url(XYZ_CLASSIFIEDS_DIR.'/js/functions.js'), array( 'jquery' ), '2013-07-18', true );

	 	wp_register_style('font-awesome', plugins_url(XYZ_CLASSIFIEDS_DIR.'/css/font-awesome.css'));
	 	wp_enqueue_style('font-awesome');
		
		wp_register_style('fonts-genericons', plugins_url(XYZ_CLASSIFIEDS_DIR.'/css/fonts/genericons.css'));
		wp_enqueue_style('fonts-genericons');
		
		wp_enqueue_script( 'xyz_cls_slim-script', plugins_url(XYZ_CLASSIFIEDS_DIR.'/js/jquery-3.2.1.slim.min.js'), array( 'jquery' ), '2013-07-18', true );


		wp_register_script('bootstrap_script', plugins_url(XYZ_CLASSIFIEDS_DIR.'/js/bootstrap.js'), array('jquery'), '1.1', true);
	 	wp_enqueue_script('bootstrap_script');

}

	}
}
if(!(is_admin()))
{
	add_action("init","xyz_cls_user_scripts");
}
//**************************hide dashbord widgets from user *****************************
if ( !function_exists( 'xyz_cls_remove_dashboard_widgets' ) ) {
	function xyz_cls_remove_dashboard_widgets(){
		if(current_user_can('classifieds_user'))
		{
			remove_meta_box('dashboard_right_now', 'dashboard', 'normal');   // Right Now
			remove_meta_box('dashboard_recent_comments', 'dashboard', 'normal'); // Recent Comments
			remove_meta_box('dashboard_incoming_links', 'dashboard', 'normal');  // Incoming Links
			remove_meta_box('dashboard_plugins', 'dashboard', 'normal');   // Plugins
			remove_meta_box('dashboard_quick_press', 'dashboard', 'side');  // Quick Press
			remove_meta_box('dashboard_recent_drafts', 'dashboard', 'side');  // Recent Drafts
			remove_meta_box('dashboard_primary', 'dashboard', 'side');   // WordPress blog
			remove_meta_box('dashboard_secondary', 'dashboard', 'side');   // Other WordPress News
			remove_meta_box( 'dashboard_activity', 'dashboard', 'normal');
			// use 'dashboard-network' as the second parameter to remove widgets from a network dashboard.
		}
	}
}
add_action('wp_dashboard_setup', 'xyz_cls_remove_dashboard_widgets');
//**************************remove menu***************************************************************
if(!function_exists('xyz_cls_remove_menus')){
    function xyz_cls_remove_menus () {
        if( !current_user_can( 'administrator' ) )
        {
            global $menu;
            $restricted = array('New','Posts','Media', 'Links', 'Pages', 'Appearance','Tools', 'Users', 'Settings', 'Comments', 'Plugins');
            end ($menu);
            while (prev($menu)){
                $value = explode(' ',$menu[key($menu)][0]);
                if(in_array($value[0] != NULL?$value[0]:"" , $restricted)){
                    unset($menu[key($menu)]);
                }
            }
        }
    }
}
add_action('admin_menu', 'xyz_cls_remove_menus');
//*************************add custom admin columns**************************************************************************
if ( !function_exists( 'xyz_cls_add_columns' ) ) {
	function xyz_cls_add_columns($columns)
	{
		$imgpath=plugins_url().'/'.XYZ_CLASSIFIEDS_DIR.'/images/tfimg.png';
		if(current_user_can('administrator')){
			return array_merge($columns,
						   array('image'=>__('Image','wp-classifieds-listings'),'category'=>__('Category','wp-classifieds-listings'),'type'=>__('Item Type','wp-classifieds-listings'),'city'=>__('City','wp-classifieds-listings'),'expiry' => __('Expiry','wp-classifieds-listings'),
								 'featured_expiry' =>__( 'Premium Expiry','wp-classifieds-listings'),'author'=>__('Author','wp-classifieds-listings'),'premium'=>'<img src="'.$imgpath.'" height="30"width="30" title="Featured">'
								));
		}
		else{
			return array_merge($columns,
					array('image'=>__('Image','wp-classifieds-listings'),'category'=>__('Category','wp-classifieds-listings'),'type'=>__('Item Type','wp-classifieds-listings'),'city'=>__('City','wp-classifieds-listings'),'expiry' => __('Expiry','wp-classifieds-listings'),
								 'featured_expiry' =>__( 'Premium Expiry','wp-classifieds-listings'),'premium'=>'<img src="'.$imgpath.'" height="30"width="30" title="Featured">'
					));
		}
	}
}
add_filter('manage_classifieds_listing_posts_columns' , 'xyz_cls_add_columns');
add_action( 'manage_classifieds_listing_posts_custom_column', 'xyz_cls_manage_columns', 10, 2 );
if ( !function_exists('xyz_cls_manage_columns') ) {


function xyz_cls_manage_columns( $column, $post_id )
{
	$imgpath1=plugins_url('/'.XYZ_CLASSIFIEDS_DIR.'/images/');
	global $post;
	global $wpdb;
	if(!isset($GLOBALS['xyz_cls_query_result'][$post_id])){
	$result=$wpdb->get_row($wpdb->prepare("SELECT * FROM ".$wpdb->prefix."posts JOIN ".$wpdb->prefix."xyz_cls_listing_details ON ".$wpdb->prefix."posts.ID =pid WHERE pid=%d",$post_id));
	
	$GLOBALS['xyz_cls_query_result'][$post_id]=$result;}
	
    $result=$GLOBALS['xyz_cls_query_result'][$post_id];

	switch( $column ) {
		
			case 'city' :if (empty($result->city_id)||$result->city_id==-1)
				echo '-NA-' ;
			else{
				$city=$wpdb->get_row($wpdb->prepare("SELECT * FROM ".$wpdb->prefix."xyz_cls_cities WHERE id=%d",$result->city_id));
				if(!empty($city))
					echo $city->city;
				else '-NA-';
			}
			break;
			case  'type':if(empty($result))echo '-NA-';
			else if($result->item_type==1)	echo 'Offered';
				else echo 'Wanted';
				break;
			case 'category':if ( empty(  $result->category )||$result->category==-1 )
				echo '-NA-' ;
			else
				echo  substr(xyz_cls_get_taxonomy_parents($result->category, 'xyz_cls_category',admin_url('edit.php?post_type=classifieds_listing&xyz_cls_category='.get_term_by('term_id',$result->category,'xyz_cls_category')->slug), ' &raquo; '),0,-8);
			break;
			case 'expiry' : if ( empty($result->expiry ) )
				echo '-NA-' ;
			else if(!empty($result->expiry )&& time()>$result->expiry){
				echo 'Expired';
			}
			else
				echo xyz_cls_local_date_time('d M Y  H:m:s A ',$result->expiry);
			break;
			case 'premium':     
			global $wpdb;
			if(empty($result))
				echo '-NA-';
			else	if ( empty($result->featured ))
			{
				echo '<img src="'.$imgpath1.'timg.png" height="30"width="30" title="" class="img-swap" >';
				if(!current_user_can('administrator'))
				{
					if(($result->post_status=='publish'||$result->post_status=='pending')&&get_option('xyz_cls_premium_listing_enable')==1)
					{


						$preurl = admin_url('admin.php?page=xyz_cls_premium&post_type=classifieds_listing&pid='.$result->ID);
?>

		<a href="<?php echo wp_nonce_url($preurl,'cls-mpre_'.$result->ID);?>"><img src='<?php echo $imgpath1.'pre.png';?>' height="32px" width="32px" title="Make Premium"></a>
<?php
					
					}
				}
			}
			else if($result->featured==1 && strtotime( date("Y-m-d"))<=$result->featured_expiry && current_user_can('administrator')){
				echo '<input type="hidden" value="'.$result->id.'" id="id"><img src="'.$imgpath1.'tfimg.png" height="32"width="32" title=" Premium" class="img-swap" >';
				echo '<a href="" onclick="xyz_cls_remove_premium('.$result->ID.')" id="remove_premium'.$result->ID.'"><img src="'.$imgpath1.'close.png" height="25"width="25" title="Remove Premium"></a>';
?>
<script type="text/javascript">
	function xyz_cls_remove_premium(id){
		jQuery(document).ready(function(){
			if (confirm("Do you really want to remove premium listing of this item?")){
				<?php $ajax_cls_nonce = wp_create_nonce('xyz-cls-premv');?>
				var dataString = {
					id:id,
					security:'<?php echo $ajax_cls_nonce;?>',
					action: 'xyz_cls_remove_premium'
				};
				jQuery.post(ajaxurl, dataString, function(response) {
					location.reload();
				});
			}
		});
	}
</script>
<?php
			}
			else	if ( $result->featured ==1 && current_user_can('administrator')&& strtotime( date("Y-m-d"))>$result->featured_expiry){
				echo '<img src="'.$imgpath1.'timg.png" height="30"width="30" title="" class="img-swap" >';
			}
			else if(!empty($result->expiry )&& strtotime( date("Y-m-d"))>$result->expiry && !current_user_can('administrator'))
			{
				echo '<img src="'.$imgpath1.'timg.png" height="32"width="32" title=" Premium" class="img-swap" >';
				if(($result->status=='publish'||$result->status=='pending')&&get_option('xyz_cls_premium_listing_enable')==1)
					{
						$preurl = admin_url('admin.php?page=xyz_cls_premium&post_type=classifieds_listing&pid='.$result->ID);
?>

		<a href="<?php echo wp_nonce_url($preurl,'cls-mpre_'.$result->ID);?>"><img src='<?php echo $imgpath1.'pre.png';?>' height="32px" width="32px" title="Make Premium"></a>
<?php
					}
			}
			else{
				echo '<img src="'.$imgpath1.'tfimg.png" height="32"width="32" title=" Premium" class="img-swap" >';
				if(($result->status=='publish'||$result->status=='pending')&&get_option('xyz_cls_premium_listing_enable')==1)
					{
						$preurl = admin_url('admin.php?page=xyz_cls_premium&post_type=classifieds_listing&pid='.$result->ID);
?>

		<a href="<?php echo wp_nonce_url($preurl,'cls-mpre_'.$result->ID);?>"><img src='<?php echo $imgpath1.'pre.png';?>' height="32px" width="32px" title="Make Premium"></a>
<?php
					}
			}
			break;
			case 'featured_expiry' :
			if(!empty($result->featured_expiry )&& strtotime( date("Y-m-d"))>$result->featured_expiry)
			{echo 'Expired';
			}else if ( !empty($result->featured_expiry )&& strtotime( date("Y-m-d"))<$result->featured_expiry ) {
				echo xyz_cls_local_date_time('d M Y  H:m:s A ', $result->featured_expiry);
			}
			else {
				echo "-NA-";
			}
			break;
			case 'image': 	if (!(get_the_post_thumbnail($post_id ) ))
				echo '-NA-';
			else
				echo	$thumbnail=get_the_post_thumbnail($post_id, array(50,50));
			break;
			default :			break;
		}
	}
}
//************************************************hide quick edit*****************************************************
if ( !function_exists('xyz_cls_remove_quick_edit') ) {
	function xyz_cls_remove_quick_edit( $actions )
	{
		global $post;
		if( $post->post_type == 'classifieds_listing' )
			unset($actions['inline hide-if-no-js']);
		return $actions;
	}
}
add_filter('post_row_actions','xyz_cls_remove_quick_edit',10,1);
//****************************************** Fix post counts****************************************************************
add_action('pre_get_posts', 'xyz_cls_query_set_only_author' );
if ( !function_exists('xyz_cls_query_set_only_author') ) {
	function xyz_cls_query_set_only_author( $wp_query )
	{
		global $current_user;
		if( is_admin() && !current_user_can('edit_others_posts') ) {
		$wp_query->set( 'author', $current_user->ID );

	}
	}
}

add_filter('views_edit-classifieds_listing', 'xyz_cls_fix_post_counts');
if ( !function_exists('xyz_cls_fix_post_counts') ) {
	function xyz_cls_fix_post_counts($views) {
		global $current_user, $wp_query;
		unset($views['mine']);
		$types = array(
			array( 'status' =>  NULL ),
			array( 'status' => 'publish' ),
			array( 'status' => 'draft' ),
			array( 'status' => 'pending' ),
			array( 'status' => 'trash' )
		);
		foreach( $types as $type ) {
			$query = array(
				// 'author'      => $current_user->ID,
				'post_type'   => 'classifieds_listing',
				'post_status' => $type['status']
			);
			$result = new WP_Query($query);
			if( $type['status'] == NULL ):
			$class = (!isset($wp_query->query_vars['post_status'] )) ? ' class="current"' : '';
			$views['all'] = sprintf(__('<a href="%s"'. $class .'>All <span class="count">(%d)</span></a>', 'all'),
									admin_url('edit.php?post_type=classifieds_listing'),
									$result->found_posts);
			elseif( $type['status'] == 'publish' ):
			$class = (isset($wp_query->query_vars['post_status'] )&&$wp_query->query_vars['post_status'] == 'publish') ? ' class="current"' : '';
			$views['publish'] = sprintf(__('<a href="%s"'. $class .'>Published <span class="count">(%d)</span></a>', 'publish'),
										admin_url('edit.php?post_status=publish&post_type=classifieds_listing'),
										$result->found_posts);
			elseif( $type['status'] == 'draft' ):
			$class = (isset($wp_query->query_vars['post_status'] )&&$wp_query->query_vars['post_status'] == 'draft') ? ' class="current"' : '';
			$views['draft'] = sprintf(__('<a href="%s"'. $class .'>Draft'. ((sizeof($result->posts) > 1) ? "s" : "") .' <span class="count">(%d)</span></a>', 'draft'),
									  admin_url('edit.php?post_status=draft&post_type=classifieds_listing'),
									  $result->found_posts);
			elseif( $type['status'] == 'pending' ):
			$class = (isset($wp_query->query_vars['post_status'] )&&$wp_query->query_vars['post_status'] == 'pending') ? ' class="current"' : '';
			$views['pending'] = sprintf(__('<a href="%s"'. $class .'>Pending <span class="count">(%d)</span></a>', 'pending'),
										admin_url('edit.php?post_status=pending&post_type=classifieds_listing'),
										$result->found_posts);
			elseif( $type['status'] == 'trash' ):
			$class = (isset($wp_query->query_vars['post_status'] )&&$wp_query->query_vars['post_status'] == 'trash') ? ' class="current"' : '';
			$views['trash'] = sprintf(__('<a href="%s"'. $class .'>Trash <span class="count">(%d)</span></a>', 'trash'),
									  admin_url('edit.php?post_status=trash&post_type=classifieds_listing'),
									  $result->found_posts);
			endif;
		}
		return $views;
	}
}
//**********************************hide publishing actions*********************************************************************************
if ( !function_exists( 'xyz_cls_publishing_actions' ) ) {
	function xyz_cls_publishing_actions(){
		global $post_type;
		if( $post_type == 'classifieds_listing' ){
			echo '<style type="text/css">
#visibility{
display:none;
}
.curtime {
display:none;
}
}
</style>';
		}
	}
}
add_action('admin_head', 'xyz_cls_publishing_actions');


//************ Add the field to user profiles*********************
if(!function_exists('xyz_profile_field')){
	function xyz_profile_field( $user ){
	// Only show this option to users who can delete other users
		if (!current_user_can('edit_users'))
			return;

		if(in_array('classifieds_user',(array) $user->roles)){
	    	$ustat = get_the_author_meta('xyz_wp_user_status', $user->ID);
	?>
			<table class="form-table">
				<tbody>
					<tr>
						<th>
							<label for="xyz_cls_user">
							<?php _e('User Account Activation', 'wp-classifieds-listings' );?>
							</label>
								
						</th>
						<td>
							<input type="radio" name="xyz_act_user" value="1" <?php if($ustat==1) echo 'checked="checked"';?>>Enable
							<input type="radio" name="xyz_act_user" value="0" <?php if($ustat==0) echo 'checked="checked"'?>>Disable	
						</td>
					</tr>
				<tbody>
			</table>
	<?php
		}
	}
}
add_action( 'show_user_profile','xyz_profile_field');
add_action( 'edit_user_profile','xyz_profile_field');

//**********Saves the custom field to user meta*****************
if(!function_exists('xyz_user_profile_save')){
function xyz_user_profile_save( $user_id ) {
		// Only worry about saving this field if the user has access
		$savestat = "";
		if ( !current_user_can( 'edit_users' ) )
			return;

		if ( isset( $_POST['xyz_act_user'] ) ) {
			$savestat = intval($_POST['xyz_act_user']);
		} 

	 
		update_user_meta( $user_id, 'xyz_wp_user_status', $savestat );
}
}
add_action( 'personal_options_update','xyz_user_profile_save');
add_action( 'edit_user_profile_update','xyz_user_profile_save');

//************check to see if user account is disabled************
if(!function_exists('xyz_user_login')){
function xyz_user_login( $user_login, $user = null ) {
		if ( !$user ) {
			$user = get_user_by('login', $user_login);
		}
		if ( !$user ) {
			// not logged in - definitely not disabled
			return;
		}
		// Get user meta
		$disabled = get_user_meta( $user->ID, 'xyz_wp_user_status', true );
		
		// Is the use logging in disabled?
		if($disabled == '0') {
			// Clear cookies, a.k.a log user out
			wp_clear_auth_cookie();
			// Build login URL and then redirect
			$login_url = site_url( 'wp-login.php', 'login' );
			$login_url = add_query_arg( 'disabled', '0', $login_url );
			wp_redirect( $login_url );
			exit;
		}
}
}
add_action( 'wp_login','xyz_user_login', 10, 2 );

//***************Show notice to disabled users*******************
if(!function_exists('xyz_user_login_message')){
function xyz_user_login_message( $message ) {
		// Show the error message if it seems to be a disabled user
		if ( isset( $_GET['disabled'] ) && $_GET['disabled'] == 0 ) 
			$message =  '<div id="login_error">' . apply_filters( 'xyz_disable_users_notice', __( 'Your account has been disabled', 'wp-classifieds-listings' ) ) . '</div>';

		return $message;
}
}
add_filter( 'login_message','xyz_user_login_message');

//***************************************************************
if(!function_exists('xyz_register_user')){

function xyz_register_user($user_id){
	
	add_user_meta($user_id, 'xyz_wp_user_status', 1);
}
}
add_action('user_register','xyz_register_user',10);
