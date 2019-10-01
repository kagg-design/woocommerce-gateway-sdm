<?php
/**
 * Plugin Name: WooCommerce SDM Bank Gateway
 * Plugin URI:
 * Description: WooCommerce gateway to make payments via SDM bank.
 * Author: KAGG Design
 * Version: 1.8
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
 * @author  KAGG Design
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

/**
 * This plugin directory url.
 */
define( 'SDM_GATEWAY_URL', plugin_dir_url( __FILE__ ) );

/**
 * Init SDM Gateway class on plugin load.
 */
function init_sdm_gateway_class() {
	static $plugin;

	if ( ! class_exists( 'WooCommerce' ) ) {
		return;
	}

	if ( ! isset( $plugin ) ) {
		// Require main class of the plugin.
		require_once dirname( __FILE__ ) . '/includes/class-wc-gateway-sdm.php';

		$plugin = new WC_Gateway_SDM();
	}
}

add_action( 'plugins_loaded', 'init_sdm_gateway_class' );

/**
 * Function to check if request to WordPress is related to this WooCommerce SDM Bank Gateway plugin.
 * If POST contains RRN - it is response from SDM bank, process it.
 * If GET contains sdm_gateway - it is self hook from this plugin,
 * we have to create form and make POST request to the bank.
 * This function cannot be a method of the class WC_Gateway_SDM, as this class extends WC_Payment_Gateway,
 * but WC_Payment_Gateway is not initialized if we are not on a WooCommerce page.
 */
function check_for_sdm() {
	if ( isset( $_POST['RRN'] ) ) {
		// Start the gateways.
		WC()->payment_gateways();
		do_action( 'check_sdm_gateway' );
	}

	if ( isset( $_GET['sdm_gateway'] ) ) {
		if ( isset( $_GET['_wpnonce'] ) && wp_verify_nonce( sanitize_key( $_GET['_wpnonce'] ), 'sdm_gateway' ) ) {
			$action = sanitize_text_field( wp_unslash( $_GET['sdm_gateway'] ) );
			if ( 'post' === $action ) {
				if ( isset( $_GET['sdm_mode'] ) ) {
					$sdm_mode = sanitize_text_field( wp_unslash( $_GET['sdm_mode'] ) );
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
					$request = [];
					foreach ( $_GET as $a => $b ) {
						if ( ( 'sdm_gateway' !== $a ) && ( 'sdm_mode' !== $a ) && ( '_wpnonce' !== $a ) ) {
							$request[ $a ] = $b;
							?>
							<input
									type="hidden" name="<?php echo esc_attr( $a ); ?>"
									value="<?php echo esc_attr( $b ); ?>">
							<?php
						}
					}
					kagg_write_log( '***Request***' );
					kagg_dump( $request );
					?>
				</form>
				<script type="text/javascript">
					document.getElementById( 'sdm_form' ).submit();
				</script>
				<?php
				die();
			}
		} else {
			wp_safe_redirect( home_url() );
			die();
		}
	}

	return false;
}

add_action( 'init', 'check_for_sdm', 1000 );
