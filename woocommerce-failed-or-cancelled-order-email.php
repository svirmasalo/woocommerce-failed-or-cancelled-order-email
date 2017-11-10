<?php
/**
* Plugin Name: WooCommerce Failed or Cancelled Order Email
* Plugin URI: https://github.com/svirmasalo/woocommerce-failed-or-cancelled-order-email.git
* Description: This plugin extends WooCommerce emails sent by WordPress / WooCommerce, when order gets cancelled or failed.
* Version: 0.5
*
* Author: Sampo Virmasalo
* Author URI: https://svirmasalo.fi
*
* Text Domain: wc-focoe
* Domain Path: /lang
*
* WC requires at least: 3.1.0
* WC tested up to: 3.2.3
*
* Copyright: © 2017 Sampo Virmasalo
* License: GNU General Public License v3.0
* License URI: http://www.gnu.org/licenses/gpl-3.0.html
*/

/**
* Prevent data leaks
*/
if ( ! defined( 'ABSPATH' ) ) {
    exit; // Exit if accessed directly
}

/**
* Directory path
*/
$dirPath_root = plugin_dir_path(__FILE__);

$dirPaths = array(
	'root' => $dirPath_root,
	'templates' => $dirPath_root.'templates/',
	'includes' => $dirPath_root.'includes/'
);


/**
* Translations
*/
add_action('plugins_loaded', 'WC_FOCOE_Translations');

function WC_FOCOE_Translations() {
	load_plugin_textdomain( 'wc-focoe', false, dirname( plugin_basename(__FILE__) ) . '/lang/' );
}

/**
 * Check if WooCommerce is active
 **/
if ( in_array( 'woocommerce/woocommerce.php', apply_filters( 'active_plugins', get_option( 'active_plugins' ) ) ) ) {

	include_once($dirPaths["includes"].'focoe-functions.php');

}

?>
