<?php
/**
 * Plugin Name: WooCommerce SDM Bank Gateway
 * Plugin URI:
 * Description: WooCommerce gateway to make payments via SDM bank.
 * Author: KAGG Design
 * Version: 1.5.1
 * Author URI: https://kagg.eu/en/
 * Requires at least: 4.4
 * Tested up to: 4.8
 * WC requires at least: 3.0.0
 * WC tested up to: 3.2.2
 *
 * Text Domain: woocommerce-gateway-sdm
 * Domain Path: /languages/
 *
 * @package WooCommerce SDM Bank Gateway
 * @author KAGG Design
 */

/**
 * This plugin directory url.
 */
define( 'SDM_GATEWAY_URL', plugin_dir_url( __FILE__ ) );

/**
 * Init SDM Gateway class on plugin load.
 */
add_action( 'plugins_loaded', 'init_sdm_gateway_class' );

/**
 * Init SDM Gateway Class.
 */
function init_sdm_gateway_class() {
	if ( ! class_exists( 'WooCommerce' ) ) {
		return;
	}

	/**
	 * SDM Gateway Class.
	 *
	 * @class WC_Gateway_SDM_Gateway
	 * @version 1.4
	 */
	class WC_Gateway_SDM_Gateway extends WC_Payment_Gateway {

		/**
		 * Plugin id.
		 *
		 * @var string
		 */
		public $id;

		/**
		 * Plugin icon.
		 *
		 * @var string
		 */
		public $icon;

		/**
		 * Plugin method title - required by WooCommerce.
		 *
		 * @var string
		 */
		public $method_title;

		/**
		 * Plugin method description - required by WooCommerce.
		 *
		 * @var string
		 */
		public $method_description;

		/**
		 * Plugin has fields ( = false).
		 *
		 * @var string
		 */
		public $has_fields;

		/**
		 * Plugin supports ( = products ).
		 *
		 * @var string
		 */
		public $supports;

		/**
		 * Plugin enabled option value.
		 *
		 * @var string
		 */
		public $enabled;

		/**
		 * Title.
		 *
		 * @var string
		 */
		public $title;

		/**
		 * Description.
		 *
		 * @var string
		 */
		public $description;

		/**
		 * Merchant name.
		 *
		 * @var string
		 */
		protected $merchant_name;

		/**
		 * Merchant url.
		 *
		 * @var string
		 */
		protected $merchant_url;

		/**
		 * Backward reference
		 *
		 * @var string
		 */
		protected $back_reference;

		/**
		 * Merchant ID set by SDM bank.
		 *
		 * @var string
		 */
		protected $merchant;

		/**
		 * Terminal ID set by SDM bank.
		 *
		 * @var string
		 */
		protected $terminal;

		/**
		 * Merchant key set by SDM bank.
		 *
		 * @var string
		 */
		protected $key;

		/**
		 * Merchant email
		 *
		 * @var string
		 */
		protected $email;

		/**
		 * Test mode (yes or no)
		 *
		 * @var string
		 */
		protected $test_mode;

		/**
		 * Merchant ID set by SDM bank in test mode.
		 *
		 * @var string
		 */
		protected $test_merchant;

		/**
		 * Terminal ID set by SDM bank in test mode.
		 *
		 * @var string
		 */
		protected $test_terminal;

		/**
		 * Merchant key set by SDM bank in test mode.
		 *
		 * @var string
		 */
		protected $test_key;

		/**
		 * Merchant email in test mode.
		 *
		 * @var string
		 */
		protected $test_email;

		/**
		 * WC_Gateway_SDM_Gateway constructor.
		 */
		public function __construct() {
			$this->id                 = 'sdm_gateway';
			$this->icon               = SDM_GATEWAY_URL . 'sdm-logo.png';
			$this->method_title       = __( 'SDM Bank', 'woocommerce-gateway-sdm' );
			$this->method_description = __( 'WooCommerce gateway to make payments via SDM bank', 'woocommerce-gateway-sdm' );
			$this->has_fields         = false;
			$this->supports           = array( 'products' );

			// Load settings.
			$this->enabled        = $this->get_option( 'enabled' );
			$this->title          = $this->get_option( 'title' );
			$this->description    = $this->get_option( 'description' );
			$this->merchant_name  = $this->get_option( 'merch_name' );
			$this->merchant_url   = $this->get_option( 'merch_url' );
			$this->back_reference = $this->get_option( 'backref' );
			$this->merchant       = $this->get_option( 'merchant' );
			$this->terminal       = $this->get_option( 'terminal' );
			$this->key            = $this->get_option( 'key' );
			$this->email          = $this->get_option( 'email' );
			$this->test_mode      = $this->get_option( 'test_mode' );
			$this->test_merchant  = $this->get_option( 'test_merchant' );
			$this->test_terminal  = $this->get_option( 'test_terminal' );
			$this->test_key       = $this->get_option( 'test_key' );
			$this->test_email     = $this->get_option( 'test_email' );

			$this->init_form_fields();
			$this->init_settings();

			add_action( 'check_sdm_gateway', array( $this, 'check_response' ) );

			// Save settings.
			if ( is_admin() ) {
				add_action( 'woocommerce_update_options_payment_gateways_' . $this->id,
					array( $this, 'process_admin_options' )
				);
			}

			add_action( 'plugins_loaded', array( $this, 'sdm_load_textdomain' ) );
		}

		/**
		 * Initialize form fields on admin page.
		 */
		public function init_form_fields() {
			$this->form_fields = array(
				'enabled'     => array(
					'title'   => __( 'Enable', 'woocommerce-gateway-sdm' ),
					'type'    => 'checkbox',
					'label'   => __( 'Enable SDM Bank Gateway', 'woocommerce-gateway-sdm' ),
					'default' => 'yes',
				),
				'title'       => array(
					'title'       => __( 'Title', 'woocommerce-gateway-sdm' ),
					'type'        => 'text',
					'description' => __( 'Payment method title that the customer will see on checkout.', 'woocommerce-gateway-sdm' ),
					'default'     => __( 'SDM Bank Gateway', 'woocommerce-gateway-sdm' ),
					'desc_tip'    => true,
				),
				'description' => array(
					'title'       => __( 'Description', 'woocommerce-gateway-sdm' ),
					'type'        => 'textarea',
					'description' => __( 'Payment method description that the customer will see on checkout.', 'woocommerce-gateway-sdm' ),
					'default'     => __( 'Pay via SDM Bank Gateway', 'woocommerce-gateway-sdm' ),
					'desc_tip'    => true,
				),
				'merch_name'  => array(
					'title'       => __( 'Merchant name', 'woocommerce-gateway-sdm' ),
					'type'        => 'text',
					'description' => __( 'Merchant company name.', 'woocommerce-gateway-sdm' ),
					'default'     => '',
					'desc_tip'    => true,
				),
				'merch_url'   => array(
					'title'       => __( 'Merchant URL', 'woocommerce-gateway-sdm' ),
					'type'        => 'text',
					'description' => __( 'Merchant URL string.', 'woocommerce-gateway-sdm' ),
					'default'     => '',
					'desc_tip'    => true,
				),
				'backref'     => array(
					'title'       => __( 'Merchant back ref', 'woocommerce-gateway-sdm' ),
					'type'        => 'text',
					'description' => __( 'Merchant back reference.', 'woocommerce-gateway-sdm' ),
					'default'     => '',
					'desc_tip'    => true,
				),
				'test_mode'   => array(
					'title'       => __( 'Test mode', 'woocommerce-gateway-sdm' ),
					'type'        => 'checkbox',
					'description' => __( 'Fields below have different values in test mode.', 'woocommerce-gateway-sdm' ),
					'label'       => __( 'Enable test mode', 'woocommerce-gateway-sdm' ),
					'default'     => 'no',
				),
			);

			if ( 'yes' === $this->test_mode ) {
				$var_form_fields = array(
					'test_merchant' => array(
						'title'       => __( 'Test Merchant ID', 'woocommerce-gateway-sdm' ),
						'type'        => 'text',
						'description' => __( 'Test Merchant ID in SDM Bank.', 'woocommerce-gateway-sdm' ),
						'default'     => '',
						'desc_tip'    => true,
					),
					'test_terminal' => array(
						'title'       => __( 'Test Terminal', 'woocommerce-gateway-sdm' ),
						'type'        => 'text',
						'description' => __( 'Test Merchant terminal ID.', 'woocommerce-gateway-sdm' ),
						'default'     => '',
						'desc_tip'    => true,
					),
					'test_key'      => array(
						'title'       => __( 'Test Key', 'woocommerce-gateway-sdm' ),
						'type'        => 'text',
						'description' => __( 'Test Merchant key.', 'woocommerce-gateway-sdm' ),
						'default'     => '',
						'desc_tip'    => true,
					),
					'test_email'    => array(
						'title'       => __( 'Test Email', 'woocommerce-gateway-sdm' ),
						'type'        => 'text',
						'description' => __( 'Test Merchant Email.', 'woocommerce-gateway-sdm' ),
						'default'     => '',
						'desc_tip'    => true,
						'placeholder' => 'you@email.com',
					),
				);
			} else {
				$var_form_fields = array(
					'merchant' => array(
						'title'       => __( 'Merchant ID', 'woocommerce-gateway-sdm' ),
						'type'        => 'text',
						'description' => __( 'Merchant ID in SDM Bank.', 'woocommerce-gateway-sdm' ),
						'default'     => '',
						'desc_tip'    => true,
					),
					'terminal' => array(
						'title'       => __( 'Terminal', 'woocommerce-gateway-sdm' ),
						'type'        => 'text',
						'description' => __( 'Merchant terminal ID.', 'woocommerce-gateway-sdm' ),
						'default'     => '',
						'desc_tip'    => true,
					),
					'key'      => array(
						'title'       => __( 'Key', 'woocommerce-gateway-sdm' ),
						'type'        => 'text',
						'description' => __( 'Merchant key.', 'woocommerce-gateway-sdm' ),
						'default'     => '',
						'desc_tip'    => true,
					),
					'email'    => array(
						'title'       => __( 'Email', 'woocommerce-gateway-sdm' ),
						'type'        => 'text',
						'description' => __( 'Merchant Email.', 'woocommerce-gateway-sdm' ),
						'default'     => '',
						'desc_tip'    => true,
						'placeholder' => 'you@email.com',
					),
				);
			} // End if().

			$this->form_fields = array_merge( $this->form_fields, $var_form_fields );
		}

		/**
		 * Function process payment via SDM bank interface
		 *
		 * @param int $order_id order id in WooCommerce.
		 *
		 * @return array
		 */
		function process_payment( $order_id ) {
			$order = new WC_Order( $order_id );

			$all_items = array();
			$subtotal  = 0;

			// Get all products.
			foreach ( $order->get_items( array( 'line_item', 'fee', 'coupon' ) ) as $item ) {
				$cur_item = array();
				if ( 'fee' === $item['type'] ) {
					$cur_item['name']  = __( 'Fee', 'woocommerce-gateway-sdm' );
					$cur_item['qty']   = 1;
					$cur_item['price'] = $item['line_total'];
					$subtotal          += $item['line_total'];
				} elseif ( 'coupon' === $item['type'] ) {
					$cur_item['name']  = 'coupon';
					$cur_item['price'] = (string) ( $item['discount'] * -1 );
					$subtotal          -= $item['discount'];
				} else {
					$product           = $item->get_product();
					$sku               = $product ? $product->get_sku() : '';
					$cur_item['name']  = $item['name'];
					$cur_item['qty']   = $item['qty'];
					$cur_item['price'] = $order->get_item_subtotal( $item, false );
					$subtotal          += $order->get_item_subtotal( $item, false ) * $item['qty'];
					$cur_item['sku']   = $sku;
				}
				$all_items[] = $cur_item;
			}

			$shipping_total = wc()->cart->get_shipping_total();

			/* Payment values */

			// SDM bank requires AMOUNT field length = 12.
			// But leading spaces cause an error.
			// And field with leading zeroes outputs as is: 000000000xxx.
			// Testing shows that dynamic length is OK.
			$amount = $subtotal + $shipping_total;

			$currency = $this->get_currency_code( get_woocommerce_currency() );

			// SDM bank requires CURRENCY field length = 3 and value = 643 (Russian roubles).
			if ( 643 !== $currency ) {
				$message = __( 'SDM Bank supports payment only in Rubles', 'woocommerce-gateway-sdm' );
				wc_add_notice( __( 'Payment error: ', 'woocommerce-gateway-sdm' ) . $message, 'error' );

				return array(
					'result'   => 'failure',
					'redirect' => '',
				);
			}

			// SDM bank requires ORDER field length = 6 - 32.
			// ORDER must be unique during a 24 hours.
			// We take last 6 digits from $order_id and add 6 random digits.
			$order_id = sprintf( '%06d%06d', (int) $order_id % 1000000, rand( 0, 999999 ) );

			$desc       = '';
			$first_item = true;
			foreach ( $all_items as $cur_item ) {
				if ( ! $first_item ) {
					$desc .= '; ';
				}
				if ( 'coupon' === $cur_item['name'] ) {
					$desc .= __( 'Coupon', 'woocommerce-gateway-sdm' );
					$desc .= ' = ' . $cur_item['price'] ;
					$first_item = false;
					continue;
				}
				if ( '' !== $cur_item['sku'] ) {
					$desc .= '(' . $cur_item['sku'] . ') ';
				}
				// SDM bank does not accept currency symbol and space (!) at the end of $desc field.
				$desc       .= $cur_item['name'] . ' * ' . $cur_item['qty'] . ' = ' . $cur_item['price'];
				$first_item = false;
			}

			// SDM bank does not accept currency symbol and space(!) at the end of $desc field.
			$desc = trim( $desc );

			// SDM bank requires DESC field length 1-50.
			$desc = $this->ellipsis( $desc, 50 );
			if ( '' === $desc ) {
				$message = __( 'Description must be in 1-50 characters', 'woocommerce-gateway-sdm' );
				wc_add_notice( __( 'Payment error: ', 'woocommerce-gateway-sdm' ) . $message, 'error' );

				return array(
					'result'   => 'failure',
					'redirect' => '',
				);
			}

			// MERCHANT, TERMINAL, KEY, EMAIL fields are validated on input.
			if ( 'yes' === $this->test_mode ) {
				$merchant = $this->test_merchant;
				$terminal = $this->test_terminal;
				$key      = $this->test_key;
				$email    = $this->test_email;
			} else {
				$merchant = $this->merchant;
				$terminal = $this->terminal;
				$key      = $this->key;
				$email    = $this->email;
			}

			$merchant_name = $this->merchant_name;
			$merchant_url  = $this->merchant_url;

			// SDM bank requires TIMESTAMP field length = 2.
			$trtype = '01';

			// SDM bank requires TIMESTAMP field length = 14.
			$timestamp = gmdate( 'YmdHis' );

			// SDM bank requires TIMESTAMP field length 1-5.
			$merch_gmt = get_option( 'gmt_offset' );

			// Create nonce. Length is always is 10 chars.
			$nonce = wp_create_nonce( 'sdm_gateway' );

			// SDM bank requires NONCE field length 16-64.
			$sdm_nonce = $nonce . $nonce;

			// SDM bank requires NONCE field length 1-250.
			// Validated on input.
			$back_reference = $this->back_reference;
			/* End of payment values */

			$body   = array(
				'AMOUNT'     => $amount,
				'CURRENCY'   => $currency,
				'ORDER'      => $order_id,
				'DESC'       => $desc,
				'MERCH_NAME' => $merchant_name,
				'MERCH_URL'  => $merchant_url,
				'MERCHANT'   => $merchant,
				'TERMINAL'   => $terminal,
				'EMAIL'      => $email,
				'TRTYPE'     => $trtype,
				'TIMESTAMP'  => $timestamp,
				'NONCE'      => $sdm_nonce,
				'BACKREF'    => $back_reference,
				'KEY'        => $key,
			);
			$result = wp_remote_post( 'https://kagg.eu/?auth=sdm', array(
				'method'      => 'POST',
				'redirection' => 1,
				'httpversion' => '1.0',
				'blocking'    => true,
				'headers'     => array(),
				'body'        => $body,
				'cookies'     => array(),
			) );
			if ( is_wp_error( $result ) ) {
				return array(
					'result'   => 'failure',
					'redirect' => '',
				);
			}

			$mac     = $result['body'];
			$mac     = json_decode( $mac );
			$success = $mac->success;
			if ( ! $success ) {
				return array(
					'result'   => 'failure',
					'redirect' => '',
				);
			}
			$mac = $mac->data;

			if ( isset( $_SERVER['HTTPS'] ) ) { // Input var okay.
				$scheme = sanitize_text_field( wp_unslash( $_SERVER['HTTPS'] ) ); // Input var okay.
			} else {
				$scheme = '';
			}
			if ( ( $scheme ) && ( 'off' !== $scheme ) ) {
				$scheme = 'https';
			} else {
				$scheme = 'http';
			}
			$link = site_url( '', $scheme );
			$link = $link . '?sdm_gateway=post';
			if ( 'yes' === $this->test_mode ) {
				$link = $link . '&sdm_mode=test';
			}
			$query = "&AMOUNT={$amount}&CURRENCY={$currency}&ORDER={$order_id}";
			$query .= "&DESC={$desc}&TERMINAL={$terminal}&TRTYPE={$trtype}&MERCH_NAME={$merchant_name}";
			$query .= "&MERCH_URL={$merchant_url}&MERCHANT={$merchant}&EMAIL={$email}&TIMESTAMP={$timestamp}";
			$query .= "&MERCH_GMT={$merch_gmt}&NONCE={$sdm_nonce}&BACKREF={$back_reference}&P_SIGN={$mac}";
			$query .= "&_wpnonce={$nonce}";

			// wp_sanitize_redirect() removes spaces from query strings
			parse_str( $query, $parsed_query );
			$parsed_query = rawurlencode_deep( $parsed_query );
			$link = add_query_arg( $parsed_query, $link );

			return array(
				'result'   => 'success',
				'redirect' => $link,
			);
		}

		/**
		 * Function to check response from SDM bank interface
		 *
		 * @return bool
		 */
		public function check_response() {
			kagg_write_log( '***Response from Bank***' );
			if ( isset( $_POST['RRN'] ) ) { // Input var okay.
				kagg_dump( $_POST );
				isset( $_POST['Order'] ) ? $order_id = sanitize_text_field( $_POST['Order'] ) : $order_id = 0; // Input var okay.
				if ( 0 === $order_id ) {
					return false;
				}

				$order_id = substr( $order_id, 0, 6 ); // Drop random part.
				$order = new WC_Order( $order_id );
				if ( $order->has_status( 'completed' ) || $order->has_status( 'processing' ) ) {
					return false;
				}

				isset( $_POST['Result'] ) ? $result = intval( $_POST['Result'] ) : $result = 0; // Input var okay.
				if ( 0 === $result ) {
					// Add order note.
					$order->add_order_note( __( 'SDM bank payment approved', 'woocommerce-gateway-sdm' ) );

					$order->payment_complete();
					$order->update_status( 'completed' );

					// Remove cart.
					WC()->cart->empty_cart();

					return true;
				} else {
					// Add order note.
					$note = __( 'SDM bank payment error: ', 'woocommerce-gateway-sdm' );
					$note .= 'Result=' . $_POST['Result'] . '; RC=' . $_POST['RC'];
					$note .= '; AuthCode=' . $_POST['AuthCode'];
					$order->add_order_note( $note );
				}
			}

			return false;
		}

		/**
		 * Get currency code
		 *
		 * @param string $currency 3 character currency name in WooCommerce.
		 *
		 * @return int
		 */
		public function get_currency_code( $currency ) {
			$currency_codes = array(
				'AED' => 784,
				'AFN' => 971,
				'ALL' => 8,
				'AMD' => 051,
				'ANG' => 532,
				'AOA' => 973,
				'ARS' => 032,
				'AUD' => 036,
				'AWG' => 533,
				'AZN' => 944,
				'BAM' => 977,
				'BBD' => 052,
				'BDT' => 050,
				'BGN' => 975,
				'BHD' => 48,
				'BIF' => 108,
				'BMD' => 060,
				'BND' => 96,
				'BOB' => 68,
				'BOV' => 984,
				'BRL' => 986,
				'BSD' => 044,
				'BTN' => 064,
				'BWP' => 072,
				'BYN' => 933,
				'BZD' => 84,
				'CAD' => 124,
				'CDF' => 976,
				'CHE' => 947,
				'CHF' => 756,
				'CHW' => 948,
				'CLF' => 990,
				'CLP' => 152,
				'CNY' => 156,
				'COP' => 170,
				'COU' => 970,
				'CRC' => 188,
				'CUC' => 931,
				'CUP' => 192,
				'CVE' => 132,
				'CZK' => 203,
				'DJF' => 262,
				'DKK' => 208,
				'DOP' => 214,
				'DZD' => 012,
				'EGP' => 818,
				'ERN' => 232,
				'ETB' => 230,
				'EUR' => 978,
				'FJD' => 242,
				'FKP' => 238,
				'GBP' => 826,
				'GEL' => 981,
				'GHS' => 936,
				'GIP' => 292,
				'GMD' => 270,
				'GNF' => 324,
				'GTQ' => 320,
				'GYD' => 328,
				'HKD' => 344,
				'HNL' => 340,
				'HRK' => 191,
				'HTG' => 332,
				'HUF' => 348,
				'IDR' => 360,
				'ILS' => 376,
				'INR' => 356,
				'IQD' => 368,
				'IRR' => 364,
				'ISK' => 352,
				'JMD' => 388,
				'JOD' => 400,
				'JPY' => 392,
				'KES' => 404,
				'KGS' => 417,
				'KHR' => 116,
				'KMF' => 174,
				'KPW' => 408,
				'KRW' => 410,
				'KWD' => 414,
				'KYD' => 136,
				'KZT' => 398,
				'LAK' => 418,
				'LBP' => 422,
				'LKR' => 144,
				'LRD' => 430,
				'LSL' => 426,
				'LYD' => 434,
				'MAD' => 504,
				'MDL' => 498,
				'MGA' => 969,
				'MKD' => 807,
				'MMK' => 104,
				'MNT' => 496,
				'MOP' => 446,
				'MRO' => 478,
				'MUR' => 480,
				'MVR' => 462,
				'MWK' => 454,
				'MXN' => 484,
				'MXV' => 979,
				'MYR' => 458,
				'MZN' => 943,
				'NAD' => 516,
				'NGN' => 566,
				'NIO' => 558,
				'NOK' => 578,
				'NPR' => 524,
				'NZD' => 554,
				'OMR' => 512,
				'PAB' => 590,
				'PEN' => 604,
				'PGK' => 598,
				'PHP' => 608,
				'PKR' => 586,
				'PLN' => 985,
				'PYG' => 600,
				'QAR' => 634,
				'RON' => 946,
				'RSD' => 941,
				'RUB' => 643,
				'RWF' => 646,
				'SAR' => 682,
				'SBD' => 90,
				'SCR' => 690,
				'SDG' => 938,
				'SEK' => 752,
				'SGD' => 702,
				'SHP' => 654,
				'SLL' => 694,
				'SOS' => 706,
				'SRD' => 968,
				'SSP' => 728,
				'STD' => 678,
				'SVC' => 222,
				'SYP' => 760,
				'SZL' => 748,
				'THB' => 764,
				'TJS' => 972,
				'TMT' => 934,
				'TND' => 788,
				'TOP' => 776,
				'TRY' => 949,
				'TTD' => 780,
				'TWD' => 901,
				'TZS' => 834,
				'UAH' => 980,
				'UGX' => 800,
				'USD' => 840,
				'USN' => 997,
				'UYI' => 940,
				'UYU' => 858,
				'UZS' => 860,
				'VEF' => 937,
				'VND' => 704,
				'VUV' => 548,
				'WST' => 882,
				'XAF' => 950,
				'XAG' => 961,
				'XAU' => 959,
				'XBA' => 955,
				'XBB' => 956,
				'XBC' => 957,
				'XBD' => 958,
				'XCD' => 951,
				'XDR' => 960,
				'XOF' => 952,
				'XPD' => 964,
				'XPF' => 953,
				'XPT' => 962,
				'XSU' => 994,
				'XTS' => 963,
				'XUA' => 965,
				'XXX' => 999,
				'YER' => 886,
				'ZAR' => 710,
				'ZMW' => 967,
				'ZWL' => 932,
			);

			return $currency_codes[ $currency ];
		}

		/**
		 * Validate Merchant name field.
		 *
		 * @param string $key Name of the field.
		 * @param string $value Field value.
		 *
		 * @return null | string
		 */
		public function validate_merch_name_field( $key, $value ) {
			return $this->validate_field( $key, $value, 1, 50 );
		}

		/**
		 * Validate Merchant URL field.
		 *
		 * @param string $key Name of the field.
		 * @param string $value Field value.
		 *
		 * @return null | string
		 */
		public function validate_merch_url_field( $key, $value ) {
			return $this->validate_field( $key, $value, 1, 250 );
		}

		/**
		 * Validate Merchant back ref field.
		 *
		 * @param string $key Name of the field.
		 * @param string $value Field value.
		 *
		 * @return null | string
		 */
		public function validate_backref_field( $key, $value ) {
			return $this->validate_field( $key, $value, 1, 250 );
		}

		/**
		 * Validate Merchant field.
		 *
		 * @param string $key Name of the field.
		 * @param string $value Field value.
		 *
		 * @return null | string
		 */
		public function validate_merchant_field( $key, $value ) {
			return $this->validate_field( $key, $value, 1, 15 );
		}

		/**
		 * Validate Terminal field.
		 *
		 * @param string $key Name of the field.
		 * @param string $value Field value.
		 *
		 * @return null | string
		 */
		public function validate_terminal_field( $key, $value ) {
			return $this->validate_field( $key, $value, 8, 8 );
		}

		/**
		 * Validate Key field.
		 *
		 * @param string $key Name of the field.
		 * @param string $value Field value.
		 *
		 * @return null | string
		 */
		public function validate_key_field( $key, $value ) {
			return $this->validate_field( $key, $value, 32, 32 );
		}

		/**
		 * Validate Email field.
		 *
		 * @param string $key Name of the field.
		 * @param string $value Field value.
		 *
		 * @return null | string
		 */
		public function validate_email_field( $key, $value ) {
			return $this->validate_field( $key, $value, 5, 80 );
		}

		/**
		 * Validate Merchant field.
		 *
		 * @param string $key Name of the field.
		 * @param string $value Field value.
		 *
		 * @return null | string
		 */
		public function validate_test_merchant_field( $key, $value ) {
			return $this->validate_field( $key, $value, 1, 15 );
		}

		/**
		 * Validate Terminal field.
		 *
		 * @param string $key Name of the field.
		 * @param string $value Field value.
		 *
		 * @return null | string
		 */
		public function validate_test_terminal_field( $key, $value ) {
			return $this->validate_field( $key, $value, 8, 8 );
		}

		/**
		 * Validate Key field.
		 *
		 * @param string $key Name of the field.
		 * @param string $value Field value.
		 *
		 * @return null | string
		 */
		public function validate_test_key_field( $key, $value ) {
			return $this->validate_field( $key, $value, 32, 32 );
		}

		/**
		 * Validate Email field.
		 *
		 * @param string $key Name of the field.
		 * @param string $value Field value.
		 *
		 * @return null | string
		 */
		public function validate_test_email_field( $key, $value ) {
			return $this->validate_field( $key, $value, 5, 80 );
		}

		/**
		 * Cut string at given length with ellipsis.
		 *
		 * @param string $string A string to cut.
		 * @param int $length Desired length.
		 *
		 * @return string
		 */
		private function ellipsis( $string, $length ) {
			if ( $length < 3 ) {
				$length = 3;
			}
			if ( mb_strlen( $string ) > $length ) {
				$string = mb_substr( $string, 0, $length - 3 ) . '...';
			}

			return $string;
		}

		/**
		 * Validate length of the field.
		 *
		 * @param string $key Key of the field.
		 * @param string $value Value of the field.
		 * @param int $min Minimum length.
		 * @param int $max Maximum length.
		 *
		 * @return null | string
		 */
		private function validate_field( $key, $value, $min, $max ) {
			$length = mb_strlen( $value );
			if ( ( $length < $min ) || ( $length > $max ) ) {
				$message = __( 'Length of the field "', 'woocommerce-gateway-sdm' );
				$message .= $this->form_fields[ $key ]['title'] . '" ';
				$message .= __( 'must be in ', 'woocommerce-gateway-sdm' );
				$message .= $min . '-' . $max . ' ' . __( 'characters.', 'woocommerce-gateway-sdm' );

				$this->add_error( $message );
				$this->display_errors();

				// WooCommerce does not clean $this->errors and does not process it by itself.
				$this->errors = array();

				return null;
			}

			return $value;
		}

		/**
		 * Load plugin text domain.
		 */
		public function sdm_load_textdomain() {
			load_plugin_textdomain( 'woocommerce-gateway-sdm', false,
				dirname( plugin_basename( __FILE__ ) ) . '/languages/'
			);
		}
	}
}

add_action( 'init', 'check_for_sdm', 1000 );
/**
 * Function to check if request to WordPress is related to this WooCommerce SDM Bank Gateway plugin.
 * If POST contains RRN - it is response from SDM bank, process it.
 * If GET contains sdm_gateway - it is self hook from this plugin,
 * we have to create form and make POST request to the bank.
 */
function check_for_sdm() {
	if ( isset( $_POST ) ) { // Input var okay.
		if ( isset( $_POST['RRN'] ) ) { // Input var okay.
			// Start the gateways.
			WC()->payment_gateways();
			do_action( 'check_sdm_gateway' );
		}
	}
	if ( isset( $_GET['sdm_gateway'] ) ) { // Input var okay.
		if ( isset( $_GET['_wpnonce'] ) && wp_verify_nonce( sanitize_key( $_GET['_wpnonce'] ), 'sdm_gateway' ) ) { // Input var okay.
			$action = sanitize_text_field( wp_unslash( $_GET['sdm_gateway'] ) ); // Input var okay.
			if ( 'post' === $action ) {
				if ( isset( $_GET['sdm_mode'] ) ) { // Input var okay.
					$sdm_mode = sanitize_text_field( wp_unslash( $_GET['sdm_mode'] ) ); // Input var okay.
				} else {
					$sdm_mode = '';
				}
				if ( 'test' === $sdm_mode ) {
					$gateway_link = 'https://3dst.sdm.ru/cgi-bin/cgi_link';
				} else {
					$gateway_link = 'https://3ds.sdm.ru/cgi-bin/cgi_link';
				}
				?>
                <form id="sdm_form" action="<?php echo esc_url( $gateway_link ); ?>" method="post">
					<?php
					$request = array();
					foreach ( $_GET as $a => $b ) { // Input var okay.
						if ( ( 'sdm_gateway' !== $a ) && ( 'sdm_mode' !== $a ) && ( '_wpnonce' !== $a ) ) {
							$request[ $a ] = $b;
							?>
                            <input type="hidden" name="<?php echo esc_attr( $a ); ?>"
                                   value="<?php echo esc_attr( $b ); ?>">
							<?php
						}
					}
					kagg_write_log( '***Request***' );
					kagg_dump( $request );
					?>
                </form>
                <script type="text/javascript">
                    document.getElementById('sdm_form').submit();
                </script>
				<?php
				die();
			}
		} else {
			wp_redirect( home_url() );
			die();
		} // End if().
	} // End if().

	return false;
}

/**
 * Add SDM gateway class in WooCommerce payment methods.
 *
 * @param array $methods - array of WooCommerce payment methods.
 *
 * @return array
 */
function add_sdm_gateway_class( $methods ) {
	$methods[] = 'WC_Gateway_SDM_Gateway';

	return $methods;
}

add_filter( 'woocommerce_payment_gateways', 'add_sdm_gateway_class' );

