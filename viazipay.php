<?php



/**

 * Plugin Name

 *

 * @package           PluginPackage

 * @author            Viaziza Tech

 * @copyright         2023 Viaziza Tech

 * @license           GPL-2.0-or-later

 *

 * @wordpress-plugin

 * Plugin Name:       ViaziPay

 * Plugin URI:        https://viaziza.com

 * Description:       Le plugin ViaziPay pour WooCommerce offre une solution de paiement pratique et sécurisée, intégrant des options de paiement mobile telles que Orange Money, MTN Mobile Money et Coinbase. Permettez à vos clients de régler leurs achats rapidement et facilement via leur compte mobile préféré, offrant ainsi une expérience d'achat fluide et sans friction. 

 * Version:           1.0.1

 * Author:            Viaziza Tech

 * Author URI:        contact@viaziza.com

 * Text Domain:       viazipay

 * License:           GPL v2 or later

 * License URI:       http://www.gnu.org/licenses/gpl-2.0.txt

 * Update URI:        https://example.com/my-plugin/

 */







// define constance

define('VIAZIPAY_MIN_PHP_VERSION', '5.4');



// test

if (!defined('ABSPATH')) {

    exit;
}



if (version_compare(phpversion(), VIAZIPAY_MIN_PHP_VERSION, '<')) {

    _e('Le plugin ViaziPay nécessite php 5.4 ou plus', 'viazipay');

    exit;
}



function viazipay_load_plugin_textdomain()
{

    load_plugin_textdomain('viazipay', FALSE, basename(dirname(__FILE__)) . '/lang/');
}



// includes

include 'includes/class-viazipay.php';



// add hook

add_action('plugins_loaded', 'viazipay_init_class');

add_action('plugins_loaded', 'viazipay_load_plugin_textdomain');





add_filter('woocommerce_payment_gateways', 'add_viazipay_class');

function add_viazipay_class($gateways)

{

    $gateways[] = 'Viazipay_woocommerce';

    return $gateways;
}





add_filter('the_content', 'viazipay_payment_confirmation');

function viazipay_payment_confirmation($content)

{

    if (is_checkout()) {

        if (isset($_GET['om_order'])) {

            $order = new WC_Order($_GET['om_order']);

            if ($order->get_status() == 'completed') {

                $content .= '<p style=" text-align: center; padding: 15px 0; padding: 1em 2em 1em 3.5em; margin: 0 0 2em; position: relative; background-color: #f7f6f7; color: #515151; border-top: 3px solid #cd3d14;">' . __('Commande ternminée!', 'viazipay') . ' <a style="text-decoration: underline; color: #007bff; margin-left: 10px;" href="' . esc_url(wc_get_account_endpoint_url('orders')) . '">' . __('Consultez vos commandes', 'viazipay') . '</a>' . '</p>';
            }
        }
    }

    return $content;
}



function load_custom_admin_scripts() {
    $current_screen = get_current_screen();

    if ( $current_screen->id === 'woocommerce_page_wc-settings' ) {
        wp_enqueue_script( 'custom-admin-script', plugin_dir_url( __FILE__ ) . 'assets/js/woocommerce-custom-fields.js', array( 'jquery' ), '1.0', true );
     }
}
add_action( 'admin_enqueue_scripts', 'load_custom_admin_scripts' );
