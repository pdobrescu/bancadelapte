<?php
/*
Plugin Name: WooCommerce Euplatesc Gateway
Description: Extends WooCommerce with an euplatesc gateway.
Version: 1.0
Author: Andreea Stoican
*/
 
add_action('plugins_loaded', 'woocommerce_gateway_euplatesc_init', 0);
 
function woocommerce_gateway_euplatesc_init() {
 
	if ( !class_exists( 'WC_Payment_Gateway' ) ) return;
 
	/**
 	 * Localisation
	 */
	load_plugin_textdomain('wc-gateway-euplatesc', false, dirname( plugin_basename( __FILE__ ) ) . '/languages');
    
	/**
 	 * Gateway class
 	 */
	class WC_Gateway_Euplatesc extends WC_Payment_Gateway {
	
		public function __construct() {
			global $woocommerce;

			$this->id = "euplatesc";

			$this->has_fields   = false;
			$this->method_title = __( 'Plata card online', 'woocommerce' );
       		//$this->icon         = apply_filters( 'woocommerce_euplatesc_icon', $woocommerce->plugin_url() . '/assets/images/icons/euplatesc.png' );


			// Load the settings.
			$this->init_form_fields();
			$this->init_settings();

			// Define user set variables
			$this->title = $this->get_option( 'title' );
			//$this->description = $this->get_option( 'description' );


			add_action( 'woocommerce_update_options_payment_gateways_' . $this->id, array( $this, 'process_admin_options' ) );

		}

		function init_form_fields() {
		 	$this->form_fields = array(
				'enabled' => array(
					'title' => __( 'Enable/Disable', 'woocommerce' ),
					'type' => 'checkbox',
					'label' => __( 'Enable EuPlatesc', 'woocommerce' ),
					'default' => 'yes'
				),
				'title' => array(
					'title' => __( 'Title', 'woocommerce' ),
					'type' => 'text',
					'description' => __( 'This controls the title which the user sees during checkout.', 'woocommerce' ),
					'default' => __( 'Plata card online', 'woocommerce' ),
					'desc_tip'      => true,
				),
				'description' => array(
					'title' => __( 'Customer Message', 'woocommerce' ),
					'type' => 'textarea',
					'default' => __('Pay via EuPlatesc.ro - you can pay online by credit card')
				)
			);
		}

		function process_payment( $order_id ) {
			global $woocommerce;
			$order = new WC_Order( $order_id );
			

			$mid="44840980063";
			$key="840B07B1F6D03A086E4DF86FF59D26CE878F2469";

			  $dataAll = array(
				'amount'      => $order->get_total(),                                                   //suma de plata
				'curr'        => 'RON',                                                   // moneda de plata
				'invoice_id'  => $order_id,  // numarul comenzii este generat aleator. inlocuiti cuu seria dumneavoastra
				'order_desc'  => 'Pay online by credit card',                                           //descrierea comenzii
	                     // va rog sa nu modificati urmatoarele 3 randuri
				'merch_id'    => $mid,                                                    // nu modificati
				'timestamp'   => gmdate("YmdHis"),                                        // nu modificati
	 			'nonce'       => md5(microtime() . mt_rand()),                            //nu modificati
			); 

			$dataAll['fp_hash'] = strtoupper($this->euplatesc_mac($dataAll,$key));

			//completati cu valorile dvs
			$dataBill = array(
						'fname'	   => $_POST['billing_first_name'],      // nume
						'lname'	   => $_POST['billing_last_name'],   // prenume
						'country'  => $_POST['billing_country'],      // tara
						'company'  => '',   // firma
						'city'	   => $_POST['billing_city'],      // oras
						'add'	   => $_POST['billing_address_1'],    // adresa
						'email'	   => $_POST['billing_email'],     // email
						'phone'	   => $_POST['billing_phone'],   // telefon
						'fax'	   => '',       // fax
			);
			$dataShip = array(
						'sfname'       => $_POST['billing_first_name'],     // nume
						'slname'       => $_POST['billing_last_name'],  // prenume
						'scountry'     => $_POST['billing_country'],     // tara
						'scompany'     => '',  // firma
						'scity'	       => $_POST['billing_city'],     // oras
						'sadd'         => $_POST['billing_address_1'],      // adresa
						'semail'       => $_POST['billing_email'],    // email
						'sphone'       => $_POST['billing_phone'],  // telefon
						'sfax'	       => '',      // fax
						'price'		   => $order->get_shipping_tax()
			);
	
			$ORDER_PNAME = array();
			$ORDER_PRICE = array();
			$ORDER_QTY   = array();
	    
			foreach ($order->get_items() as $item) {
				array_push($ORDER_PNAME, $item['name']); 
				array_push($ORDER_PRICE, ($item['line_total'] + $item['line_tax']) / $item['qty']);
				array_push($ORDER_QTY, $item['qty']);
			}
			
			$details = array("orders" => $ORDER_PNAME, "prices" => $ORDER_PRICE, "qty" => $ORDER_QTY);
			
			?>
		
			<div align="center">
			<form ACTION="https://secure.euplatesc.ro/tdsprocess/tranzactd.php" METHOD="POST" name="gateway" target="_self">

			<!-- begin billing details -->
			    <input name="fname" type="hidden" value="<?php echo $dataBill['fname'];?>" />
			    <input name="lname" type="hidden" value="<?php echo $dataBill['lname'];?>" />
			    <input name="country" type="hidden" value="<?php echo $dataBill['country'];?>" />
			    <input name="company" type="hidden" value="<?php echo $dataBill['company'];?>" />
			    <input name="city" type="hidden" value="<?php echo $dataBill['city'];?>" />
			    <input name="add" type="hidden" value="<?php echo $dataBill['add'];?>" />
			    <input name="email" type="hidden" value="<?php echo $dataBill['email'];?>" />
			    <input name="phone" type="hidden" value="<?php echo $dataBill['phone'];?>" />
			    <input name="fax" type="hidden" value="<?php echo $dataBill['fax'];?>" />
			<!-- snd billing details -->

			<!-- daca detaliile de shipping difera -->
			<!-- begin shipping details -->
			    <input name="sfname" type="hidden" value="<?php echo $dataShip['sfname'];?>" />
			    <input name="slname" type="hidden" value="<?php echo $dataShip['slname'];?>" />
			    <input name="scountry" type="hidden" value="<?php echo $dataShip['scountry'];?>" />
			    <input name="scompany" type="hidden" value="<?php echo $dataShip['scompany'];?>" />
			    <input name="scity" type="hidden" value="<?php echo $dataShip['scity'];?>" />
			    <input name="sadd" type="hidden" value="<?php echo $dataShip['sadd'];?>" />
			    <input name="semail" type="hidden" value="<?php echo $dataShip['semail'];?>" />
			    <input name="sphone" type="hidden" value="<?php echo $dataShip['sphone'];?>" />
			    <input name="sfax" type="hidden" value="<?php echo $dataShip['sfax'];?>" />

				<input name='ship_prices' type='hidden' value='<?php echo $dataShip['price']?>' />
			<!-- end shipping details -->

			<input type="hidden" NAME="amount" VALUE="<?php echo  $dataAll['amount'] ?>" SIZE="12" MAXLENGTH="12" />
			<input TYPE="hidden" NAME="curr" VALUE="<?php echo  $dataAll['curr'] ?>" SIZE="5" MAXLENGTH="3" />
			<input type="hidden" NAME="invoice_id" VALUE="<?php echo  $dataAll['invoice_id'] ?>" SIZE="32" MAXLENGTH="32" />
			<input type="hidden" NAME="order_desc" VALUE="<?php echo  $dataAll['order_desc'] ?>" SIZE="32" MAXLENGTH="50" />
			<input TYPE="hidden" NAME="merch_id" SIZE="15" VALUE="<?php echo  $dataAll['merch_id'] ?>" />
			<input TYPE="hidden" NAME="timestamp" SIZE="15" VALUE="<?php echo  $dataAll['timestamp'] ?>" />
			<input TYPE="hidden" NAME="nonce" SIZE="35" VALUE="<?php echo  $dataAll['nonce'] ?>" />
			<input TYPE="hidden" NAME="fp_hash" SIZE="40" VALUE="<?php echo  $dataAll['fp_hash'] ?>" />
			
			<?php foreach($details as $key => $values) : ?>
				<?php foreach($values as $i => $value) : ?>
			
					<input type='hidden' name='<?=$key?>[<?=$i?>]' value='<?=$value?>' />
			
				<?php endforeach; ?>
			<?php endforeach; ?>
			
				<p class="tx_red_mic">Transferring to EuPlatesc.ro gateway</p>
				<p><img src="https://www.euplatesc.ro/plati-online/tdsprocess/images/progress.gif" alt="" title="" onload="javascript:document.gateway.submit()"></p>
			<p><a href="javascript:gateway.submit();" class="txtCheckout">Go Now!</a></p>
			</form>                                                                 
			</div>

			<script type="text/javascript">
				gateway.submit();
			</script>

			<?php

			// Remove cart
			$woocommerce->cart->empty_cart();
			
			exit;
		}

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
		function euplatesc_mac($data, $key)
		{
		  $str = NULL;

		  foreach($data as $d)
		  {
		   	if($d === NULL || strlen($d) == 0)
		  	  $str .= '-'; // valorile nule sunt inlocuite cu -
		  	else
		  	  $str .= strlen($d) . $d;
		  }
		     
		  // ================================================================
		  $key = pack('H*', $key); // convertim codul secret intr-un string binar
		  // ================================================================

		// echo " $str " ;

		  return $this->hmacsha1($key, $str);
		}

		public function get_total() {
          return apply_filters( 'woocommerce_order_amount_total', number_format( (double) $this->order_total, 2, '.', '' ) );
        }

		public function admin_options() {

	    	?>
	    	<h3><?php _e( 'Euplatesc Payment', 'woocommerce' ); ?></h3>
	    	<p><?php _e( 'Allows cheque payments. Why would you take cheques in this day and age? Well you probably wouldn\'t but it does allow you to make test purchases for testing order emails and the \'success\' pages etc.', 'woocommerce' ); ?></p>
	    	<table class="form-table">
	    	<?php
	    		// Generate the HTML For the settings form.
	    		$this->generate_settings_html();
	    	?>
			</table><!--/.form-table-->
	    	<?php
	    }
	}
	

	/**
 	* Add the Gateway to WooCommerce
 	**/
	function woocommerce_add_gateway_euplatesc_gateway($methods) {
		$methods[] = 'WC_Gateway_Euplatesc';
		return $methods;
	}
	
	add_filter('woocommerce_payment_gateways', 'woocommerce_add_gateway_euplatesc_gateway' );
} 