<?php
if ( ! defined( 'ABSPATH' ) )
    exit;

$item_name = "";
$item_number = "";
$payment_status = "";
$payment_amount = '';
$payment_currency ='';
$txn_id = '';
$receiver_email = '';
$txn_type = '';
$pending_reason = '';
$payment_type = '';
$userid ='';
$fee='';
$payer_email='';

$pp_hostname = "www.paypal.com"; 
//$pp_hostname = "www.sandbox.paypal.com";

//read the post from PayPal system and add 'cmd'
$req = 'cmd=_notify-synch';
$tx_token = isset($_GET['tx']) ? sanitize_text_field($_GET['tx']) : '';
$keyarray = array();



$_POST = stripslashes_deep($_POST);
$_GET = stripslashes_deep($_GET);
if(isset($_GET['action'])&&$_GET['action']=='cancel'){
	echo '<span style="color:green;">'.__('You have successfully cancelled the payment.','wp-classifieds-listings').'</span>';
	
}

if($tx_token!=''){
	$auth_token=get_option('xyz_cls_auth_token');

	if($auth_token!=''){
		$req .= "&tx=$tx_token&at=$auth_token";
		$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL, "https://$pp_hostname/cgi-bin/webscr");
		curl_setopt($ch, CURLOPT_POST, 1);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER,1);
		curl_setopt($ch, CURLOPT_POSTFIELDS, $req);
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 1);
		//set cacert.pem verisign certificate path in curl using 'CURLOPT_CAINFO' field here,
		//if your server does not bundled with default verisign certificates.
		curl_setopt($ch, CURLOPT_CAINFO, plugin_dir_path(__DIR__).'paypal-cert/cacert.pem');
		curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 2);
		curl_setopt($ch, CURLOPT_HTTPHEADER, array("Host: $pp_hostname"));
		$res = curl_exec($ch);
		curl_close($ch);
		if($res){
				$lines = explode(PHP_EOL, trim($res));
			if(strcmp ($lines[0], "SUCCESS") == 0){
				for ($i = 1; $i < count($lines); $i++){
					$temp = explode("=", $lines[$i],2);
					$keyarray[urldecode($temp[0])] = urldecode($temp[1]);
				}
			}
			else if(strcmp ($lines[0], "FAIL") == 0){
				 _e('Invalid Payment','wp-classifieds-listings');
    			exit;
			}
		}
	}
}
else{
	wp_redirect(admin_url().'?page=xyz_cls_payment_summary');
	
}
if(count($keyarray)>0){
	$item_name = $keyarray['item_name'];
	$item_number = $keyarray['item_number'];
	$payment_status = $keyarray['payment_status'];
	$payment_amount = $keyarray['mc_gross'];
	$payment_currency =$keyarray['mc_currency'];
	$txn_id = $keyarray['txn_id'];
	$receiver_email = $keyarray['business'];
	$payer_email = $keyarray['payer_email'];
	$txn_type = $keyarray['txn_type'];
	
	if(isset($keyarray['pending_reason']))
		$pending_reason = $keyarray['pending_reason'];
	
	$payment_type = $keyarray['payment_type'];
	$userid =$keyarray['custom'];
	$fee=$keyarray['payment_fee'];
}
else if(isset($_GET['item_name']) && isset($_GET['item_number']) && isset($_GET['st']) && isset($_GET['amt']) && isset($_GET['cc']) && isset($_GET['cm']) && isset($_GET['tx'])){
	$item_name = sanitize_text_field($_GET['item_name']);
	$item_number = intval($_GET['item_number']);
	$payment_status = sanitize_text_field($_GET['st']);
	$payment_amount = intval($_GET['amt']);
	$payment_currency =sanitize_text_field($_GET['cc']);
	$userid =intval($_GET['cm']);
	$txn_id = sanitize_text_field($_GET['tx']);
}
?>

<center>
	<h3><?php _e('Paypal Success','wp-classifieds-listings');?></h3>
	<table class="widefat" style="width:550px;">
		<tbody>
			<tr>
				<td><?php _e('Your paypal transaction is completed','wp-classifieds-listings');?></td>
			</tr>
			<tr>
                <td >
                    <strong><?php _e('Payment Details','wp-classifieds-listings');?></strong>
                </td>
            </tr>
            <tr>
                <td><?php _e(' Payment Status','wp-classifieds-listings');?></td>
                <td> <?php _e('','wp-classifieds-listings');?>:</td>
                <td><?php echo esc_attr($payment_status);?></td>
            </tr>
            <tr>
<?php 			if($payment_type!=""){
?>
					<td><?php _e(' Payment Type','wp-classifieds-listings');?></td>
					<td >:</td>
					<td><?php echo esc_attr($payment_type);?></td>
<?php
            	}
?>		
			</tr>
			 <tr>
                <td><?php _e('Payment Amount','wp-classifieds-listings');?></td>
                <td>:</td>
                <td><?php echo xyz_cls_get_money_format($payment_amount);?></td>
            </tr>
            <tr>
                <td><?php _e('Payment Currency','wp-classifieds-listings');?></td>
                <td>:</td>
                <td><?php echo esc_attr($payment_currency);?></td>
            </tr>
            <tr>
                <td><?php _e('Transaction ID','wp-classifieds-listings');?></td>
                <td>:</td>
                <td><?php echo esc_attr($txn_id);?></td>
            </tr>
<?php 			if($payer_email!=""){
?>            
            <tr>

				<td><?php _e('Payer Email','wp-classifieds-listings');?></td>
				<td>:</td>
				<td><?php echo esc_attr($payer_email);?></td>
			</tr>
<?php
				}
				if($receiver_email!=""){    
?>
			<tr>
				<td><?php _e('Receiver Email','wp-classifieds-listings');?></td>
				<td>:</td>
				<td><?php echo esc_attr($receiver_email);?></td>
			</tr>
<?php
				}
?>
			<tr>
                <td colspan="3" height="10px"></td>
            </tr>
<?php
		if($payment_status!="Completed"){
?>
			<tr>
				<td><?php _e('Pending reason','wp-classifieds-listings');?></td>
				<td>:</td>
				<td><?php echo esc_attr($pending_reason);?></td>
			</tr>
<?php
		}
?>
		</tbody>
	</table>
</center>
