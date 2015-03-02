<?php

	require("wp-config.php");
		
		 get_header('shop');
do_action('woocommerce_before_main_content');
		
	function hmacsha1($key,$data) {
	   $blocksize = 64;
	   $hashfunc  = 'md5';
	   
	   if(strlen($key) > $blocksize)
	     $key = pack('H*', $hashfunc($key));
	   
	   $key  = str_pad($key, $blocksize, chr(0x00));
	   $ipad = str_repeat(chr(0x36), $blocksize);
	   $opad = str_repeat(chr(0x5c), $blocksize);
	   
	   $hmac = pack('H*', $hashfunc(($key ^ $opad) . pack('H*', $hashfunc(($key ^ $ipad) . $data))));
	   return bin2hex($hmac);
	}
	// ===========================================================================================
	function euplatesc_mac($data, $key = NULL)
	{
	  $str = NULL;

	  foreach($data as $d)
	  {
	   	if($d === NULL || strlen($d) == 0)
	  	  $str .= '-'; // valorile nule sunt inlocuite cu -
	  	else
	  	  $str .= strlen($d) . $d;
	  }
	     
	  $key = pack('H*', $key); 

	  return hmacsha1($key, $str);
	}

	$key="56FBF72170A93E130DBF6E302A3BD77DD81C6857";
		$zcrsp =  array (
		'amount'     => addslashes(trim(@$_POST['amount'])),  //original amount
		'curr'       => addslashes(trim(@$_POST['curr'])),    //original currency
		'invoice_id' => addslashes(trim(@$_POST['invoice_id'])),//original invoice id
		'ep_id'      => addslashes(trim(@$_POST['ep_id'])), //Euplatesc.ro unique id
		'merch_id'   => addslashes(trim(@$_POST['merch_id'])), //your merchant id
		'action'     => addslashes(trim(@$_POST['action'])), // if action ==0 transaction ok
		'message'    => addslashes(trim(@$_POST['message'])),// transaction responce message
		'approval'   => addslashes(trim(@$_POST['approval'])),// if action!=0 empty
		'timestamp'  => addslashes(trim(@$_POST['timestamp'])),// meesage timestamp
		'nonce'      => addslashes(trim(@$_POST['nonce'])),
		);
		 
		$zcrsp['fp_hash'] = strtoupper(euplatesc_mac($zcrsp, $key));

		$fp_hash=addslashes(trim(@$_POST['fp_hash']));
	if($zcrsp['fp_hash']!=$fp_hash)	{
		?><p><?php _e( 'Something is wrong.', 'woocommerce' ); ?></p><?php
	
	} else {
	
	
	$order = new WC_Order( $_POST['invoice_id'] );

	if($_POST['action'] == 2) {

		// Mark as on-hold (we're awaiting the cheque)
		$order->update_status('failed', __( 'Awaiting euplatesc payment', 'woocommerce' ));

		?><p><?php _e( 'Tranzacția a eșuat! Vă rugăm să reluați plata.', 'woocommerce' ); ?></p><?php

	} elseif($_POST['action'] == 0) {

			// Mark as on-hold (we're awaiting the cheque)
			$order->update_status('completed', __( 'Awaiting euplatesc payment', 'woocommerce' ));

			// Reduce stock levels
			$order->reduce_order_stock();

		?><p><?php _e( 'Thank you. Your order has been received.', 'woocommerce' ); ?></p>
	<?php
	}
	
	}

?>

<?php do_action('woocommerce_after_main_content'); ?>
<?php do_action('woocommerce_sidebar'); ?>
<?php get_footer('shop'); ?>