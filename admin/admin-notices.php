<?php
function xyz_cls_admin_notice(){
	if(!current_user_can('manage_options'))
		return;

	add_thickbox();
	$sharelink_text_array_cls = array
						(
						"I use WP Classifieds Listings wordpress plugin from @xyzscripts and you should too.",
						"WP Classifieds Listings wordpress plugin from @xyzscripts is awesome",
						"Thanks @xyzscripts for developing such a wonderful wordpress plugin for listing classifieds",
						"I was looking for a Classifieds listing plugin and I found this. Thanks @xyzscripts",
						"Its very easy to use WP Classifieds Listings wordpress plugin from @xyzscripts",
						"I installed WP Classifieds Listings from @xyzscripts,it works flawlessly",
						"WP Classifieds Listings wordpress plugin that i use works terrific",
						"I am using WP Classifieds Listings wordpress plugin from @xyzscripts and I like it",
						"The WP Classifieds Listings plugin from @xyzscripts is simple and works fine",
						"I've been using this Classifieds plugin for a while now and it is really good",
						"WP Classifieds Listings wordpress plugin is a fantastic plugin",
						"WP Classifieds Listings wordpress plugin is easy to use and works great. Thank you!",
						"Good and flexible  WP Classifieds Listings plugin especially for beginners",
						"The best WP Classifieds Listings wordpress plugin I have used ! THANKS @xyzscripts",
						);
$sharelink_text_cls = array_rand($sharelink_text_array_cls, 1);
$sharelink_text_cls = $sharelink_text_array_cls[$sharelink_text_cls];
$xyz_cls_link = admin_url('admin.php?page=xyz_cls_settings&cls_blink=en');
$xyz_cls_link = wp_nonce_url($xyz_cls_link,'cls-blk');
$xyz_cls_notice = admin_url('admin.php?page=xyz_cls_settings&cls_notice=hide');
$xyz_cls_notice = wp_nonce_url($xyz_cls_notice,'cls-shw');

	echo '<style>
	#TB_window { width:50% !important;  height: 100px !important;
	margin-left: 25% !important; 
	left: 0% !important; 
	}
	</style>
	<script type="text/javascript">
	function xyz_ihs_share_snippet(){
	tb_show("Share on","#TB_inline?width=500&amp;height=75&amp;inlineId=show_share_icons_ihs&class=thickbox");
	}
	</script>
	<div id="xyz_cls_notice_td" class="error" style="color: #666666;margin-left: 2px; padding: 5px;line-height:16px;">
	<p>Thank you for using  <a href="https://wordpress.org/plugins/wp-classifieds-listings/" target="_blank">WP Classifieds Listings</a> plugin from <a href="https://xyzscripts.com/" target="_blank">xyzscripts.com</a>. Would you consider supporting us with the continued development of the plugin using any of the below methods?</p>
	<p>
	<a href="https://wordpress.org/support/plugin/wp-classifieds-listings/reviews/" class="button xyz_rate_btn" target="_blank">Rate it 5★\'s on wordpress</a>';
	
	if(get_option('xyz_credit_link')=="0")
		echo '<a href="'.$xyz_cls_link.'" class="button xyz_backlink_btn xyz_blink">Enable Backlink</a>';
	
	echo '<a class="button xyz_share_btn" onclick=xyz_cls_share_snippet();>Share on</a>
	
		<a href="https://xyzscripts.com/donate/5" class="button xyz_donate_btn" target="_blank">Donate</a>
		
		
		
	<a href="'.$xyz_cls_notice.'" class="button xyz_show_btn">Don\'t Show This Again</a>
	</p>
	
	<div id="show_share_icons_cls" style="display: none;">
	<a class="button" style="background-color:#3b5998;color:white;margin-right:4px;margin-left:100px;margin-top: 25px;" href="https://www.facebook.com/sharer/sharer.php?u=https://wordpress.org/plugins/wp-classifieds-listings/&text='.$sharelink_text_cls.'" target="_blank">Facebook</a>
	<a class="button" style="background-color:#00aced;color:white;margin-right:4px;margin-left:20px;margin-top: 25px;" href="https://twitter.com/share?url=https://wordpress.org/plugins/wp-classifieds-listings/&text='.$sharelink_text_cls.'" target="_blank">Twitter</a>
	<a class="button" style="background-color:#007bb6;color:white;margin-right:4px;margin-left:20px;margin-top: 25px;" href="https://www.linkedin.com/shareArticle?mini=true&url=https://wordpress.org/plugins/wp-classifieds-listings/" target="_blank">LinkedIn</a>
	<a class="button" style="background-color:#dd4b39;color:white;margin-right:4px;margin-left:20px;margin-top: 25px;" href="https://plus.google.com/share?&hl=en&url=https://wordpress.org/plugins/wp-classifieds-listings/" target="_blank">google+</a>
	</div>
	</div>';	
	
}
$xyz_cls_installed_date = get_option('xyz_cls_installed_date');
if ($xyz_cls_installed_date=="") {
	$xyz_cls_installed_date = time();
}

if($xyz_cls_installed_date < ( time() - (30*24*60*60) ))
{
	if (get_option('xyz_cls_admin_notice_shw') != "hide")
	{
			
		add_action('admin_notices', 'xyz_cls_admin_notice');
	}
}
