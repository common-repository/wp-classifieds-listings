<?php
if ( ! defined( 'ABSPATH' ) )
    exit;
    if(!function_exists('log_trans')){
        function log_trans($ecode){
            global $wpdb;
            
            $pending_reason='';
            $item_name = sanitize_text_field($_POST['item_name']);
            $item_number = intval($_POST['item_number']);
            $payment_status = sanitize_text_field($_POST['payment_status']);
            $payment_amount = sanitize_text_field($_POST['mc_gross']);
            $payment_currency =sanitize_text_field($_POST['mc_currency']);
            $txn_id = sanitize_text_field($_POST['txn_id']);
            if (filter_var($_POST['business'], FILTER_VALIDATE_EMAIL)) {
                $receiver_email = $_POST['business'];
            }
            if (filter_var($_POST['payer_email'], FILTER_VALIDATE_EMAIL)) {
                $payer_email = $_POST['payer_email'];
            }
            $txn_type = sanitize_text_field($_POST['txn_type']);
            if(isset($_POST['pending_reason']))
            $pending_reason = sanitize_text_field($_POST['pending_reason']);
            $payment_type = sanitize_text_field($_POST['payment_type']);
            $userid =intval($_POST['custom']);
            $fee=sanitize_text_field($_POST['payment_fee']);
            $t=time();
            $result = ($ecode=="Success"?1:0);
            if($ecode=="Success"){
                $sql1="INSERT INTO ".$wpdb->prefix."xyz_cls_payment_summary (uid,amount,payment_type,date,payment_status) values (%d,%f,%d,%d,%d)";
                $res1=$wpdb->query($wpdb->prepare($sql1,$userid,$payment_amount,1,$t,1));
                $p_id=$wpdb->insert_id;
                $sql="INSERT INTO ".$wpdb->prefix."xyz_cls_paypal_ipn (payment_id,txnid,userid,result,resultdetails,amount,currency,payeremail,
receiveremail,paymenttype,status,pendingreason,receivedate,fee) values (%d,%s,%d,%s,%s,%f,%s,%s,%s,%s,%s,%s,%d,%f)";
                $reslt=$wpdb->query($wpdb->prepare($sql,$p_id,$txn_id,$userid,$result,$ecode,$payment_amount,$payment_currency,$payer_email,$receiver_email,$txn_type,$payment_status,$pending_reason,$t,$fee));
                return $wpdb->insert_id;
            }
            else // all paypal failure cases
            {
                if($userid!=""){
                    $uname=get_the_author_meta( 'user_nicename', $userid );
                    $admin_email=get_option('xyz_cls_paypal_email');
                    $query1="select sub,body from ".$wpdb->prefix."xyz_cls_email_templates where id=6";
                    $result1=$wpdb->get_row($query1);
                    $subject=$result1->sub;
                    $message=$result1->body;
                    $search="{AMT}";
                    $replace=$payment_amount.$payment_currency;
                    $search1="{USERNAME}";
                    $replace1=get_the_author_meta( 'user_nicename', $userid);
                    $search2="{ITEMNAME}";
                    $replace2=$item_name;
                    $search3="{TXNID}";
                    $replace3=$txn_id;
                    $message=str_ireplace($search, $replace,$message);
                    $message=str_ireplace($search1, $replace1,$message);
                    $message=str_ireplace($search2, $replace2,$message);
                    $message=str_ireplace($search3, $replace3,$message);
                    
                    $message=str_ireplace('{ERROR}', $ecode, $message);
                    $message=str_ireplace("{SITE_NAME}",get_bloginfo('name'),$message);
                    $subject=str_ireplace("{SITE_NAME}",get_bloginfo('name'),$subject);
                    $message=str_ireplace($search, $replace,$message);
                    $headers= "Content-type: text/html  ";
                    $to=get_the_author_meta( 'user_email', $userid );
                    wp_mail( $gen_notification, $subject, $message,$headers);
                    $query1="select sub,body from ".$wpdb->prefix."xyz_cls_email_templates where id=8";
                    $result1=$wpdb->get_row($query1);
                    $subject=$result1->sub;
                    $message=$result1->body;
                    $message=str_ireplace($search, $replace,$message);
                    $message=str_ireplace($search1, $replace1,$message);
                    $message=str_ireplace($search2, $replace2,$message);
                    $message=str_ireplace($search3, $replace3,$message);
                    $subject=str_ireplace("{SITE_NAME}",get_bloginfo('name'),$subject);
                    $message=str_ireplace("{SITE_NAME}",get_bloginfo('name'),$message);
                    wp_mail($to, $subject, $message,$headers);
                }
            }
        }
    }
    global $wpdb;
    
         $verify_url = "https://ipnpb.paypal.com/cgi-bin/webscr";
    //$verify_url = 'https://ipnpb.sandbox.paypal.com/cgi-bin/webscr';
    
    $raw_post_data = file_get_contents('php://input');
    
    $raw_post_array = explode('&', $raw_post_data);
    $myPost = array();
    foreach ($raw_post_array as $keyval) {
        $keyval = explode('=', $keyval);
        if (count($keyval) == 2) {
            // Since we do not want the plus in the datetime string to be encoded to a space, we manually encode it.
            if ($keyval[0] === 'payment_date') {
                if (substr_count($keyval[1], '+') === 1) {
                    $keyval[1] = str_replace('+', '%2B', $keyval[1]);
                }
            }
            $myPost[$keyval[0]] = urldecode($keyval[1]);
        }
    }
    
    // Build the body of the verification post request, adding the _notify-validate command.
    $req = 'cmd=_notify-validate';
    $get_magic_quotes_exists = false;
    if (function_exists('get_magic_quotes_gpc')) {
        $get_magic_quotes_exists = true;
    }
    foreach ($myPost as $key => $value) {
        if ($get_magic_quotes_exists == true && get_magic_quotes_gpc() == 1) {
            $value = urlencode(stripslashes($value));
        }
        else {
            $value = urlencode($value);
        }
        $req .= "&$key=$value";
    }
    // Post the data back to PayPal, using curl. Throw exceptions if errors occur.
    
    $ch = curl_init($verify_url);
    curl_setopt($ch, CURLOPT_HTTP_VERSION, CURL_HTTP_VERSION_1_1);
    
    curl_setopt($ch, CURLOPT_POST, 1);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($ch, CURLOPT_POSTFIELDS, $req);
    curl_setopt($ch, CURLOPT_SSLVERSION, 6);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 1);
    curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 2);
    // This is often required if the server is missing a global cert bundle, or is using an outdated one.
    curl_setopt($ch, CURLOPT_CAINFO, plugin_dir_path(__DIR__).'paypal-cert/cacert.pem');
    curl_setopt($ch, CURLOPT_FORBID_REUSE, 1);
    curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 30);
    curl_setopt($ch, CURLOPT_HTTPHEADER, array('Connection: Close'));
    $res = curl_exec($ch);
    if (!($res)) {
        $errno = curl_errno($ch);
        $errstr = curl_error($ch);
        curl_close($ch);
        
        log_trans("cURL error: [$errno] $errstr");
        die;
    }
    
    $info = curl_getinfo($ch);
    
    $http_code = $info['http_code'];
    if ($http_code != 200) {
        log_trans("PayPal responded with http code $http_code");
        die;
    }
    curl_close($ch);
    
    $pending_reason='';
    $item_name = sanitize_text_field($_POST['item_name']);
    $item_number = intval($_POST['item_number']);
    $payment_status = sanitize_text_field($_POST['payment_status']);
    $payment_amount = sanitize_text_field($_POST['mc_gross']);
    $payment_currency =sanitize_text_field($_POST['mc_currency']);
    $txn_id = sanitize_text_field($_POST['txn_id']);
    $txn_type = sanitize_text_field($_POST['txn_type']);
    if(isset($_POST['pending_reason']))
    $pending_reason = sanitize_text_field($_POST['pending_reason']);
    $payment_type = sanitize_text_field($_POST['payment_type']);
    $userid =intval($_POST['custom']);
    $fee=sanitize_text_field($_POST['payment_fee']);
    $uname=get_the_author_meta( 'user_nicename', $userid );
    $admin_email=get_option('xyz_cls_paypal_email');
    if(filter_var($_POST['business'], FILTER_VALIDATE_EMAIL)) {
        $receiver_email = $_POST['business'];
    }
    if (filter_var($_POST['payer_email'], FILTER_VALIDATE_EMAIL)) {
        $payer_email = $_POST['payer_email'];
    }
    $author_email=get_the_author_meta('email',$userid);
    $send_to_admin = get_option('xyz_cls_default_sender_email');
    $gen_notification = get_option('xyz_cls_general_notification_email');
    
    if(!$info){
        // HTTP error
        log_trans("HTTP Error, can't connect to Paypal");
        die;
    }
    else{
        $ret = "";
        
        //	if(strcmp ($ret, "VERIFIED") == 0){
        // check that receiver_email is your Primary PayPal email
        $paypal_email=get_option('xyz_cls_paypal_email');
        if($receiver_email!=$paypal_email){
            
            log_trans("Wrong Receiver Email - $item_number");
            die;
        }
        
        // check that txn_id has not been previously processed
        $sql = "SELECT txnid FROM ".$wpdb->prefix."xyz_cls_paypal_ipn WHERE txnid =%s AND result = '1'";
        $val1=$wpdb->get_results($wpdb->prepare($sql,$txn_id));
        foreach ($val1 as $val)
            if($val->txnid!="")
            {
                // Entry present
                
                log_trans("Invalid/Duplicate Transaction - $txn_id");
                die;
            }
        
        $sql="select i.* from ".$wpdb->prefix."xyz_cls_listing_details i where i.pid=%d";
        $valu=$wpdb->get_row($wpdb->prepare($sql,$item_number));
        $org_status=$valu->status;
        $expiry=$valu->featured_expiry;
        $normal_expiry=$valu->expiry;
        $num_days=$valu->featured_no_of_days;
        $f=$valu->featured;
        $listing_period=get_option('xyz_cls_premium_listing_period');
        $min_user_transaction_amount=get_option('xyz_cls_premium_listing_price');
        $min_user_transaction_amount=round($min_user_transaction_amount,2);
        
        if($payment_amount != $min_user_transaction_amount){
            
            log_trans("Amount Less than Order Amount - Received: $payment_amount $payment_currency; Order Amount: $min_user_transaction_amount $paypal_currency");
            die;
        }
        $paypal_currency=get_option('xyz_cls_paypal_currency');
        if ($payment_currency != $paypal_currency){
            
            log_trans("Wrong Currency - Received: $payment_currency; Expected: $paypal_currency");
            die;
        }
        // check the payment_status is Completed
        if ($payment_status != "Completed"){
            
            log_trans("Incomplete Payment - Payment Status: $payment_status");
            die;
        }
        
        $wpdb->query("begin");
        $error_flag=0;
        
        $payment_id=log_trans("Success");
        
        if($payment_id>0){
            $time_arr1=getdate($expiry);
            $time_arr=getdate(time());
            
            if($expiry>0){
                $t2=xyz_cls_local_date_time_create(gmmktime($time_arr1['hours'],$time_arr1['minutes'],$time_arr1['seconds'],$time_arr1['mon'],$time_arr1['mday']+$listing_period,$time_arr1['year']));
            }
            else{
                $t2=xyz_cls_local_date_time_create(gmmktime($time_arr['hours'],$time_arr['minutes'],$time_arr['seconds'],$time_arr['mon'],$time_arr['mday']+$listing_period,$time_arr['year']));
            }
            
            $days=$num_days+$listing_period;
            
            if($org_status=='pending'){
                $query_num="UPDATE ".$wpdb->prefix."xyz_cls_listing_details SET featured_no_of_days=%d where pid=%d";
                $result_num=$wpdb->query($wpdb->prepare($query_num,$days,$item_number));
                
                if($result_num==0){
                    $error_flag=1;
                }
            }
            
            if($f!=1){
                $query="UPDATE ".$wpdb->prefix."xyz_cls_listing_details set featured=%d where pid=%d";
                $result=$wpdb->query($wpdb->prepare($query,1,$item_number));
                
                if($result==0){
                    $error_flag=1;
                }
            }
            
            if($org_status=='publish'){
                if($normal_expiry < $t2 || get_option('xyz_cls_premium_listing_enable')==2){
                    $normal_expiry=$t2;
                }
                
                $query="UPDATE ".$wpdb->prefix."xyz_cls_listing_details SET expiry=%d,featured_expiry=%d,featured_no_of_days=%d WHERE pid=%d" ;
                $resultq=$wpdb->query($wpdb->prepare($query,$normal_expiry,$t2,0,$item_number));
                
                if($resultq==0){
                    $error_flag=1;
                }
            }
        }
        else{
            $error_flag=1;
        }
        
        $wpdbstatusmsg_admin='';
        if($error_flag==1){
            $wpdb->query("rollback");
            $wpdbstatusmsg_admin='The database entries related to the payment could not be added. Please do it manually';
        }
        else{
            $wpdb->query("commit");
            $wpdbstatusmsg_admin='The database entries related to the payment were successfully captured.';
        }
        
        $query1="select sub,body from ".$wpdb->prefix."xyz_cls_email_templates where id=4";
        $result1=$wpdb->get_row($query1);
        $subject=$result1->sub;
        $message=$result1->body;
        
        $search="{AMT}";
        $replace=$payment_amount.$payment_currency;
        
        $search1="{USERNAME}";
        $replace1=get_the_author_meta( 'user_nicename', $userid );
        
        $search2="{ITEMNAME}";
        $replace2=$item_name;
        
        $search3="{TXNID}";
        $replace3=$txn_id;
        
        $message=str_ireplace("{SITE_NAME}",get_bloginfo('name'),$message);
        $subject=str_ireplace("{SITE_NAME}",get_bloginfo('name'),$subject);
        $message=str_ireplace($search, $replace,$message);
        $message=str_ireplace($search1, $replace1,$message);
        $message=str_ireplace($search2, $replace2,$message);
        $message=str_ireplace($search3, $replace3,$message);
        $message=str_ireplace('{DBENTRY}', $wpdbstatusmsg_admin,$message);
        
        $headers= "Content-type: text/html  ";
        $to=get_the_author_meta( 'user_email', $userid );
        
        wp_mail( $gen_notification, $subject, $message,$headers);
        
        
        $query1="select sub,body from ".$wpdb->prefix."xyz_cls_email_templates where id=5";
        
        $result1=$wpdb->get_row($query1);
        $subject=$result1->sub;
        $message=$result1->body;
        $message=str_ireplace($search, $replace,$message);
        $message=str_ireplace($search1, $replace1,$message);
        $message=str_ireplace($search2, $replace2,$message);
        $message=str_ireplace($search3, $replace3,$message);
        $message=str_ireplace("{SITE_NAME}",get_bloginfo('name'),$message);
        $subject=str_ireplace("{SITE_NAME}",get_bloginfo('name'),$subject);
        
        wp_mail($to, $subject, $message,$headers);
        
        die;
        // }
        // else{
        //		log_trans("Invalid Transaction - $ret");
        //		die;
        // }
    }
    ?>