<?php
/**
 * WC_Gateway_SDM class file.
 *
 * @package wc-gateway-sdm
 */

/**
 * Class WC_Gateway_SDM
 */
class WC_Gateway_SDM extends WC_Payment_Gateway {

	/**
	 * @var string Plugin version.
	 */
	public $version;

	/**
	 * @var string Absolute plugin path.
	 */
	public $plugin_path;

	/**
	 * @var string Absolute plugin URL.
	 */
	public $plugin_url;

	/**
	 * @var string Absolute path to plugin includes dir.
	 */
	public $includes_path;

	/**
	 * @var string Plugin id.
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
	 * @var bool Whether or not logging is enabled.
	 */
	public static $log_enabled = false;

	/**
	 * @var bool WC_Logger Logger instance.
	 */
	public static $log = false;

	/**
	 * WC_Gateway_SDM constructor.
	 */
	public function __construct() {
		$this->plugin_path   = trailingslashit( plugin_dir_path( __DIR__ ) );
		$this->plugin_url    = trailingslashit( plugin_dir_url( __DIR__ ) );
		$this->includes_path = $this->plugin_path . trailingslashit( 'includes' );

		// Do it directly as we are in plugins_loaded action now.
		$this->sdm_load_textdomain();

		$this->id                 = 'sdm_gateway';
		$this->icon               = SDM_GATEWAY_URL . 'sdm-logo.png';
		$this->method_title       = __( 'SDM Bank', 'woocommerce-gateway-sdm' );
		$this->method_description = __( 'WooCommerce gateway to make payments via SDM bank', 'woocommerce-gateway-sdm' );
		$this->has_fields         = false;
		$this->supports           = [ 'products' ];

		$this->init_form_fields();
		$this->init_settings();

		// Load settings.
		$this->enabled        = $this->get_option( 'enabled' );
		$this->title          = $this->get_option( 'title' );
		$this->description    = $this->get_option( 'description' );
		self::$log_enabled    = $this->get_option( 'log_enabled' );
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

		add_action( 'check_sdm_gateway', [ $this, 'check_response' ] );
		add_filter( 'woocommerce_payment_gateways', [ $this, 'add_sdm_gateway_class' ] );

		// Save settings.
		if ( is_admin() ) {
			add_action(
				'woocommerce_update_options_payment_gateways_' . $this->id,
				[ $this, 'process_admin_options' ]
			);
		}

		add_action( 'plugins_loaded', [ $this, 'sdm_load_textdomain' ] );
	}

	/**
	 * Initialize form fields on admin page.
	 */
	public function init_form_fields() {
		$this->form_fields = [
			'enabled'     => [
				'title'   => __( 'Enable', 'woocommerce-gateway-sdm' ),
				'type'    => 'checkbox',
				'label'   => __( 'Enable SDM Bank Gateway', 'woocommerce-gateway-sdm' ),
				'default' => 'yes',
			],
			'title'       => [
				'title'       => __( 'Title', 'woocommerce-gateway-sdm' ),
				'type'        => 'text',
				'description' => __( 'Payment method title that the customer will see on checkout.', 'woocommerce-gateway-sdm' ),
				'default'     => __( 'SDM Bank Gateway', 'woocommerce-gateway-sdm' ),
				'desc_tip'    => true,
			],
			'description' => [
				'title'       => __( 'Description', 'woocommerce-gateway-sdm' ),
				'type'        => 'textarea',
				'description' => __( 'Payment method description that the customer will see on checkout.', 'woocommerce-gateway-sdm' ),
				'default'     => __( 'Pay via SDM Bank Gateway', 'woocommerce-gateway-sdm' ),
				'desc_tip'    => true,
			],
			'log_enabled' => [
				'title'       => __( 'Log', 'woocommerce-gateway-sdm' ),
				'type'        => 'checkbox',
				'label'       => __( 'Enable logging', 'woocommerce-gateway-sdm' ),
				'default'     => 'no',
				'description' => __( 'Log events, such as SDM bank responses, inside', 'woocommerce-gateway-sdm' ) . ' <code>' . WC_Log_Handler_File::get_log_file_path( 'sdm' ) . '</code>',
			],
			'merch_name'  => [
				'title'       => __( 'Merchant name', 'woocommerce-gateway-sdm' ),
				'type'        => 'text',
				'description' => __( 'Merchant company name.', 'woocommerce-gateway-sdm' ),
				'default'     => '',
				'desc_tip'    => true,
			],
			'merch_url'   => [
				'title'       => __( 'Merchant URL', 'woocommerce-gateway-sdm' ),
				'type'        => 'text',
				'description' => __( 'Merchant URL string.', 'woocommerce-gateway-sdm' ),
				'default'     => '',
				'desc_tip'    => true,
			],
			'backref'     => [
				'title'       => __( 'Merchant back ref', 'woocommerce-gateway-sdm' ),
				'type'        => 'text',
				'description' => __( 'Merchant back reference.', 'woocommerce-gateway-sdm' ),
				'default'     => '',
				'desc_tip'    => true,
			],
			'test_mode'   => [
				'title'       => __( 'Test mode', 'woocommerce-gateway-sdm' ),
				'type'        => 'checkbox',
				'description' => __( 'Fields below have different values in test mode.', 'woocommerce-gateway-sdm' ),
				'label'       => __( 'Enable test mode', 'woocommerce-gateway-sdm' ),
				'default'     => 'no',
			],
		];

		if ( 'yes' === $this->test_mode ) {
			$var_form_fields = [
				'test_merchant' => [
					'title'       => __( 'Test Merchant ID', 'woocommerce-gateway-sdm' ),
					'type'        => 'text',
					'description' => __( 'Test Merchant ID in SDM Bank.', 'woocommerce-gateway-sdm' ),
					'default'     => '',
					'desc_tip'    => true,
				],
				'test_terminal' => [
					'title'       => __( 'Test Terminal', 'woocommerce-gateway-sdm' ),
					'type'        => 'text',
					'description' => __( 'Test Merchant terminal ID.', 'woocommerce-gateway-sdm' ),
					'default'     => '',
					'desc_tip'    => true,
				],
				'test_key'      => [
					'title'       => __( 'Test Key', 'woocommerce-gateway-sdm' ),
					'type'        => 'text',
					'description' => __( 'Test Merchant key.', 'woocommerce-gateway-sdm' ),
					'default'     => '',
					'desc_tip'    => true,
				],
				'test_email'    => [
					'title'       => __( 'Test Email', 'woocommerce-gateway-sdm' ),
					'type'        => 'text',
					'description' => __( 'Test Merchant Email.', 'woocommerce-gateway-sdm' ),
					'default'     => '',
					'desc_tip'    => true,
					'placeholder' => 'you@email.com',
				],
			];
		} else {
			$var_form_fields = [
				'merchant' => [
					'title'       => __( 'Merchant ID', 'woocommerce-gateway-sdm' ),
					'type'        => 'text',
					'description' => __( 'Merchant ID in SDM Bank.', 'woocommerce-gateway-sdm' ),
					'default'     => '',
					'desc_tip'    => true,
				],
				'terminal' => [
					'title'       => __( 'Terminal', 'woocommerce-gateway-sdm' ),
					'type'        => 'text',
					'description' => __( 'Merchant terminal ID.', 'woocommerce-gateway-sdm' ),
					'default'     => '',
					'desc_tip'    => true,
				],
				'key'      => [
					'title'       => __( 'Key', 'woocommerce-gateway-sdm' ),
					'type'        => 'text',
					'description' => __( 'Merchant key.', 'woocommerce-gateway-sdm' ),
					'default'     => '',
					'desc_tip'    => true,
				],
				'email'    => [
					'title'       => __( 'Email', 'woocommerce-gateway-sdm' ),
					'type'        => 'text',
					'description' => __( 'Merchant Email.', 'woocommerce-gateway-sdm' ),
					'default'     => '',
					'desc_tip'    => true,
					'placeholder' => 'you@email.com',
				],
			];
		}

		$this->form_fields = array_merge( $this->form_fields, $var_form_fields );
	}

	/**
	 * Function process payment via SDM bank interface
	 *
	 * @param int $order_id order id in WooCommerce.
	 *
	 * @return array
	 */
	public function process_payment( $order_id ) {
		$order = wc_get_order( $order_id );

		$all_items = [];
		$subtotal  = 0;

		// Get all products.
		/** @var WC_Order_Item $item */
		foreach ( $order->get_items( [ 'line_item', 'fee', 'coupon' ] ) as $item ) {
			$cur_item = [];
			if ( 'fee' === $item['type'] ) {
				$cur_item['name']  = __( 'Fee', 'woocommerce-gateway-sdm' );
				$cur_item['qty']   = 1;
				$cur_item['price'] = $item['line_total'];

				$subtotal += $item['line_total'];
			} elseif ( 'coupon' === $item['type'] ) {
				$cur_item['name']  = 'coupon';
				$cur_item['price'] = (string) ( $item['discount'] * - 1 );

				$subtotal -= $item['discount'];
			} else {
				/** @var WC_Product $product */
				$product           = $item->get_product();
				$sku               = is_object( $product ) ? $product->get_sku() : '';
				$cur_item['name']  = $item['name'];
				$cur_item['qty']   = $item['qty'];
				$cur_item['price'] = $order->get_item_subtotal( $item, false );

				$subtotal += $order->get_item_subtotal( $item, false ) * $item['qty'];

				$cur_item['sku'] = $sku;
			}
			$all_items[] = $cur_item;
		}

		$shipping_total = wc()->cart->get_shipping_total();

		/**
		 * Payment values.
		 */

		// SDM bank requires AMOUNT field length = 12.
		// But leading spaces cause an error.
		// And field with leading zeroes outputs as is: 000000000xxx.
		// Testing shows that dynamic length is OK.
		$amount = $subtotal + $shipping_total;

		$currency = \SDM_Codes::get_currency_code( get_woocommerce_currency() );

		// SDM bank requires CURRENCY field length = 3 and value = 643 (Russian roubles).
		if ( 643 !== $currency ) {
			$message = __( 'SDM Bank supports payment only in Rubles', 'woocommerce-gateway-sdm' );
			wc_add_notice( __( 'Payment error: ', 'woocommerce-gateway-sdm' ) . $message, 'error' );

			return [
				'result'   => 'failure',
				'redirect' => '',
			];
		}

		// SDM bank requires ORDER field length = 6 - 32.
		// ORDER must be unique during a 24 hours.
		// We take last 6 digits from $order_id and add 6 random digits.
		$order_id = sprintf( '%06d%06d', (int) $order_id % 1000000, wp_rand( 0, 999999 ) );

		$desc       = '';
		$first_item = true;
		foreach ( $all_items as $cur_item ) {
			if ( ! $first_item ) {
				$desc .= '; ';
			}
			if ( 'coupon' === $cur_item['name'] ) {
				$desc .= __( 'Coupon', 'woocommerce-gateway-sdm' );
				$desc .= ' = ' . $cur_item['price'];

				$first_item = false;
				continue;
			}
			if ( '' !== $cur_item['sku'] ) {
				$desc .= '(' . $cur_item['sku'] . ') ';
			}
			// SDM bank does not accept currency symbol and space (!) at the end of $desc field.
			$desc .= $cur_item['name'] . ' * ' . $cur_item['qty'] . ' = ' . $cur_item['price'];

			$first_item = false;
		}

		// SDM bank does not accept currency symbol and space(!) at the end of $desc field.
		$desc = trim( $desc );

		// SDM bank requires DESC field length 1-50.
		$desc = $this->ellipsis( $desc, 50 );
		if ( '' === $desc ) {
			$message = __( 'Description must be in 1-50 characters', 'woocommerce-gateway-sdm' );
			wc_add_notice( __( 'Payment error: ', 'woocommerce-gateway-sdm' ) . $message, 'error' );

			return [
				'result'   => 'failure',
				'redirect' => '',
			];
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

		/**
		 * End of payment values.
		 */

		$body   = [
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
		];
		$result = wp_remote_post(
			'https://kagg.eu/?auth=sdm',
			[
				'method'      => 'POST',
				'redirection' => 1,
				'httpversion' => '1.0',
				'blocking'    => true,
				'headers'     => [],
				'body'        => $body,
				'cookies'     => [],
			]
		);
		if ( is_wp_error( $result ) ) {
			return [
				'result'   => 'failure',
				'redirect' => '',
			];
		}

		$mac     = $result['body'];
		$mac     = json_decode( $mac );
		$success = $mac->success;
		if ( ! $success ) {
			return [
				'result'   => 'failure',
				'redirect' => '',
			];
		}
		$mac = $mac->data;

		if ( isset( $_SERVER['HTTPS'] ) ) {
			$scheme = sanitize_text_field( wp_unslash( $_SERVER['HTTPS'] ) );
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

		// wp_sanitize_redirect() removes spaces from query strings.
		parse_str( $query, $parsed_query );
		$parsed_query = rawurlencode_deep( $parsed_query );
		$link         = add_query_arg( $parsed_query, $link );

		return [
			'result'   => 'success',
			'redirect' => $link,
		];
	}

	/**
	 * Function to check response from SDM bank interface
	 *
	 * @return bool
	 */
	public function check_response() {
		require_once $this->includes_path . 'class-sdm-codes.php';

		// phpcs:disable WordPress.Security.NonceVerification.Missing
		if ( isset( $_POST['RRN'] ) ) {
			isset( $_POST['Function'] ) ? $function = sanitize_text_field( wp_unslash( $_POST['Function'] ) ) : $function = '';

			isset( $_POST['Result'] ) ? $result = intval( $_POST['Result'] ) : $result = - 1;

			isset( $_POST['RC'] ) ? $rc = sanitize_text_field( wp_unslash( $_POST['RC'] ) ) : $rc = - 1;

			isset( $_POST['Amount'] ) ? $amount = sanitize_text_field( wp_unslash( $_POST['Amount'] ) ) : $amount = '';

			isset( $_POST['Currency'] ) ? $currency = sanitize_text_field( wp_unslash( $_POST['Currency'] ) ) : $currency = '';

			isset( $_POST['Order'] ) ? $order_id = sanitize_text_field( wp_unslash( $_POST['Order'] ) ) : $order_id = 0;

			isset( $_POST['TRType'] ) ? $trtype = sanitize_text_field( wp_unslash( $_POST['TRType'] ) ) : $trtype = '';

			isset( $_POST['RRN'] ) ? $rrn = sanitize_text_field( wp_unslash( $_POST['RRN'] ) ) : $rrn = '';

			isset( $_POST['IntRef'] ) ? $int_ref = sanitize_text_field( wp_unslash( $_POST['IntRef'] ) ) : $int_ref = '';

			isset( $_POST['AuthCode'] ) ? $auth_code = sanitize_text_field( wp_unslash( $_POST['AuthCode'] ) ) : $auth_code = 0;

			isset( $_POST['Fee'] ) ? $fee = sanitize_text_field( wp_unslash( $_POST['Fee'] ) ) : $fee = '';

			isset( $_POST['Time'] ) ? $time = sanitize_text_field( wp_unslash( $_POST['Time'] ) ) : $time = '';

			isset( $_POST['P_Sign'] ) ? $mac = sanitize_text_field( wp_unslash( $_POST['P_Sign'] ) ) : $mac = '';
			// phpcs:enable WordPress.Security.NonceVerification.Missing

			if ( self::$log_enabled ) {
				$message = __( 'Response from SDM bank', 'woocommerce-gateway-sdm' ) . "\n";

				$message .= 'Function="' . $function . '"' . "\n";
				$message .= 'Result="' . $result . '" - ' . \SDM_Codes::get_result_message( $result ) . "\n";
				$message .= 'RC="' . $rc . '" - ' . \SDM_Codes::get_detailed_message( $rc ) . "\n";
				$message .= 'Amount="' . $amount . '"' . "\n";
				$message .= 'Currency="' . $currency . '"' . "\n";
				$message .= 'Order="' . $order_id . '"' . "\n";
				$message .= 'TRType="' . $trtype . '"' . "\n";
				$message .= 'RRN="' . $rrn . '"' . "\n";
				$message .= 'IntRef="' . $int_ref . '"' . "\n";
				$message .= 'AuthCode="' . $auth_code . '" - ' . \SDM_Codes::get_additional_message( $auth_code ) . "\n";
				$message .= 'Fee="' . $fee . '"' . "\n";
				$message .= 'Time="' . $time . '"' . "\n";
				$message .= 'P_Sign="' . $mac . '"' . "\n";
				self::log( $message );
			}

			$order_id = substr( $order_id, 0, 6 ); // Drop random part.

			if ( 0 === $order_id ) {
				return false;
			}
			$post = get_post( $order_id );
			if ( ! $post ) {
				return false;
			}
			if ( 'shop_order' !== $post->post_type ) {
				return false;
			}

			$order = wc_get_order( $order_id );
			if ( $order->has_status( 'completed' ) || $order->has_status( 'processing' ) ) {
				return false;
			}

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
				$note = __( 'SDM bank payment error: ', 'woocommerce-gateway-sdm' ) . '<br>';

				$note .= 'Result=' . $result . ' - ' . \SDM_Codes::get_result_message( $result ) . ';<br>';
				$note .= 'RC=' . $rc . ' - ' . \SDM_Codes::get_detailed_message( $rc ) . ';<br>';
				$note .= 'AuthCode=' . $auth_code . ' - ' . \SDM_Codes::get_additional_message( $auth_code ) . ';';
				$order->add_order_note( $note );

				return null;
			}
		}

		return null;
	}

	/**
	 * Validate Merchant name field.
	 *
	 * @param string $key   Name of the field.
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
	 * @param string $key   Name of the field.
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
	 * @param string $key   Name of the field.
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
	 * @param string $key   Name of the field.
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
	 * @param string $key   Name of the field.
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
	 * @param string $key   Name of the field.
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
	 * @param string $key   Name of the field.
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
	 * @param string $key   Name of the field.
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
	 * @param string $key   Name of the field.
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
	 * @param string $key   Name of the field.
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
	 * @param string $key   Name of the field.
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
	 * @param int    $length Desired length.
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
	 * @param string $key   Key of the field.
	 * @param string $value Value of the field.
	 * @param int    $min   Minimum length.
	 * @param int    $max   Maximum length.
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
			$this->errors = [];

			return null;
		}

		return $value;
	}

	/**
	 * Load plugin text domain.
	 */
	public function sdm_load_textdomain() {
		load_plugin_textdomain(
			'woocommerce-gateway-sdm',
			false,
			plugin_basename( $this->plugin_path ) . trailingslashit( '/languages' )
		);
	}

	/**
	 * Add SDM gateway class in WooCommerce payment methods.
	 *
	 * @param array $methods - array of WooCommerce payment methods.
	 *
	 * @return array
	 */
	public function add_sdm_gateway_class( $methods ) {
		$methods[] = 'WC_Gateway_SDM';

		return $methods;
	}

	/**
	 * Logging method.
	 *
	 * @param string $message Log message.
	 * @param string $level   Optional. Default 'info'
	 *                        emergency|alert|critical|error|warning|notice|info|debug.
	 */
	public static function log( $message, $level = 'info' ) {
		if ( self::$log_enabled ) {
			if ( empty( self::$log ) ) {
				self::$log = wc_get_logger();
			}
			self::$log->log(
				$level,
				$message,
				[
					'source' => 'sdm',
				]
			);
		}
	}
}
