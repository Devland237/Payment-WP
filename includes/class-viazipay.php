<?php

require_once 'class-api.php';

function viazipay_init_class()

{

    if (!class_exists('Viazipay_woocommerce')) {

        // define('WOOM_NOTIF_URL', 'wc-api/viazipay_woocommerce/');
        class Viazipay_woocommerce extends WC_Payment_Gateway

        {

            public function __construct()

            {

                $this->id = 'viazipay';

                $this->method_title = 'ViaziPay';

                $this->method_description =  __('Viazipay - étend woocommerce pour permettre les paiements mobiles (MTN Mobile Money, Orange Money, CoinBase, et les paiements par carte bancaire)', 'viazipay');

                $this->icon = plugin_dir_url(__FILE__) . '../assets/ViaziPay.png';

                $this->has_fields = true;

                $this->__init_form_fields_viazipay();

                $this->init_settings();

                foreach ($this->settings as $setting_key => $value) {

                    $this->$setting_key = $value;
                }

                $this->title = 'ViaziPay';

                if (is_admin()) {

                    add_action('woocommerce_update_options_payment_gateways_' . $this->id, array($this, 'process_admin_options'));
                }

                add_action('wp_enqueue_scripts', array($this, 'viazipay_payment_scripts'));
            }

            /**

             * generate field for admin page

             */

            public function __init_form_fields_viazipay()

            {

                $this->form_fields = array(

                    'enabled' => array(

                        'title'       => __('Activer/désactiver'),

                        'label'       => __('Activer la passerelle viazipay'),

                        'type'        => 'checkbox',

                        'description' => '',

                        'default'     => 'no',


                    ),
                    'momo' => array(
                        'title'        => __('MTN', 'viazipay'),
                        'type'         => 'checkbox',
                        'label'        => __('Activer le moyen de paiement MTN', 'viazipay'),
                        'default'      => 'no',
                    ),
                    'fee_support_option_momo' => array(
                        'title'        => '<span class="fee-support-option_momo hidden">Le client devra supporter les frais</span>',
                        'type'         => 'checkbox',
                        'label'        => '<span class="fee_payment_option_momo">Oui</span>',
                        'default'      => 'no',
                        'class'        => 'fee-support-option_momo',
                    ),
                    'om' => array(
                        'title'        => __('Orange', 'viazipay'),
                        'type'         => 'checkbox',
                        'label'        => __('Activer le moyen de paiement Orange', 'viazipay'),
                        'default'      => 'no',
                    ),
                    'fee_support_option_om' => array(
                        'title'        => '<span class="fee-support-option_om hidden">Le client devra supporter les frais</span>',
                        'type'         => 'checkbox',
                        'label'        => '<span class="fee_payment_option_om">Oui</span>',
                        'default'      => 'no',
                        'class'        => 'fee-support-option_om',
                    ),
                    'coinbase' => array(
                        'title'        => __('CoinBase', 'viazipay'),
                        'type'         => 'checkbox',
                        'label'        => __('Activer le moyen de paiement CoinBase', 'viazipay'),
                        'default'      => 'no',
                    ),
                    'fee_support_option_coinbase' => array(
                        'title'        => '<span class="fee-support-option_coinbase hidden">Le client devra supporter les frais</span>',
                        'type'         => 'checkbox',
                        'label'        => '<span class="fee_payment_option_coinbase">Oui</span>',
                        'default'      => 'no',
                        'class'        => 'fee-support-option_coinbase',
                    ),
                    'cart' => array(
                        'title'        => __('Carte bancaire', 'viazipay'),
                        'type'         => 'checkbox',
                        'label'        => __('Activer le moyen de paiement par carte bancaire', 'viazipay'),
                        'default'      => 'no',
                    ),
                    'fee_support_option_cart' => array(
                        'title'        => '<span class="fee-support-option_cart hidden">Le client devra supporter les frais</span>',
                        'type'         => 'checkbox',
                        'label'        => '<span class="fee_payment_option_cart">Oui</span>',
                        'default'      => 'no',
                        'class'        => 'fee-support-option_cart',
                    ),
                    'public_key' => array(

                        'title'       => 'public key',

                        'type'        => 'text'

                    ),

                    'private_key' => array(

                        'title'       => 'private key',

                        'type'        => 'password'

                    ),

                    'description_orange' => array(

                        'title'       => 'description orange',

                        'type'        => 'text'

                    ),

                    'description_mtn' => array(

                        'title'       => 'description MTN',

                        'type'        => 'text'

                    ),

                    'description_coinbase' => array(

                        'title'       => 'description coinbase',

                        'type'        => 'text'

                    ),
                    'description_cart' => array(

                        'title'       => 'description credit cart',

                        'type'        => 'text'

                    ),

                    'label_orange' => array(

                        'title'       => 'label orange',

                        'type'        => 'text',

                        'default'   => 'Orange Money',

                    ),

                    'label_mtn' => array(

                        'title'       => 'label MTN',

                        'type'        => 'text',

                        'default'   => 'MTN Mobile Money',



                    ),

                    'label_coinbase' => array(

                        'title'       => 'label coinbase',

                        'type'        => 'text',

                        'default'   => 'Coinbase',

                    ),
                    'label_cart' => array(

                        'title'       => 'label credit cart',

                        'type'        => 'text',

                        'default'   => 'Credit Cart',

                    )

                );
            }
            /***
             * Detect enabled options
             */


            function detect_viazipay_enabled_change($option_name, $old_value, $new_value)
            {
                if ($option_name === 'woocommerce_viazipay_enabled') {
                    if ($new_value === 'yes') {
                        update_option('woocommerce_viazipay_enabled', 'yes');
                    } else {
                        // La passerelle de paiement a été désactivée
                        // Exécuter les actions nécessaires
                    }
                }
            }
            /**

             *  handling payment and processing the order

             */

            public function process_payment($order_id)
            {
                $order = new WC_Order($order_id);

                $viazipay_options = array();

                if (isset($_POST['viaziopt']) && $_POST['viaziopt']) {

                    $payement_url =  $this->get_payement_url_method($order_id, $_POST['viaziopt']);

                    if ($payement_url) {
                        return array(
                            'result'   => 'success',
                            'redirect' => $payement_url
                        );
                    }
                }

                return array(
                    'result'   => 'failure'
                );
            }

            public function viazipay_receipt_page($order_id)

            {

                die(var_dump("redirect url"));


                if (isset($_GET['opt']) && $_GET['opt']) {

                    $option = $_GET['opt'];

                    $payement_url =  $this->get_payement_url_method($order_id, $option);

                    wp_redirect($payement_url);
                }
            }

            /**

             * Function to generate the url payment for different operators(MTN, Orange, coinBase, credit card)

             */
            public function get_payement_url_method($order_id, $method_value)

            {

                global $woocommerce;

                $order = new WC_Order($order_id);

                $public_key = $this->get_option('public_key');
                $private_key = $this->get_option('private_key');

                switch ($method_value) {

                    case 'momo':
                        $resource = 'mtn';
                        $data = array(

                            'currency'     => $order->get_currency(),

                            'order_id'     => 'VPY-' . $order_id,

                            'amount'       => (float)$order->get_total(),

                            'return_url'   => add_query_arg('om_order', $order_id, esc_url(get_permalink(wc_get_page_id('checkout')))),

                            'cancel_url'   => esc_url(get_permalink(wc_get_page_id('checkout'))),

                            "notif_url" => site_url('/') . 'wp-json/viazipay/v1/notif/?pay=' . $method_value . '&order_id=' . $order_id . '',

                            'lang'         => 'fr',

                            "payer_message" => 'XAF',

                            "payee_note" => 'XAF',

                        );

                        $get_val = ViazizaPay::processPayment($public_key, $private_key, $data, $resource);
                        if (200 === $get_val['status']) {
                            $payement_url = $get_val['datas']['payment_url'];
                            return $payement_url;
                        } else {
                            wc_add_notice(__('Veuillez réessayer.'), 'error');
                            return null;
                        }
                        break;

                    case 'om':

                        $resource = 'orange';

                        $data = array(

                            'currency'     => $order->get_currency(),

                            'order_id'     => 'VPY-' . $order_id,

                            'amount'       => (float)$order->get_total(),

                            'return_url'   => add_query_arg('om_order', $order_id, esc_url(get_permalink(wc_get_page_id('checkout')))),

                            'cancel_url'   => esc_url(get_permalink(wc_get_page_id('checkout'))),

                            "notif_url" => site_url('/') . 'wp-json/viazipay/v1/notif/?pay=' . $method_value . '&order_id=' . $order_id . '',

                            'lang'         => 'fr',

                        );

                        $get_val = ViazizaPay::processPayment($public_key, $private_key, $data, $resource);
                        if (200 === $get_val['status']) {
                            $payement_url = $get_val['datas']['payment_url'];
                            return $payement_url;
                        } else {
                            wc_add_notice(__('Veuillez réessayer.'), 'error');
                            return null;
                        }

                        break;

                    case 'coinbase':
                        $user = wp_get_current_user();

                        $data = array(

                            "currency" => $order->get_currency(),

                            "order_id" => 'VPY-' . $order_id,

                            "amount" => (float)$order->get_total(),

                            "redirect_url" => add_query_arg('om_order', $order_id, esc_url(get_permalink(wc_get_page_id('checkout')))),

                            "cancel_url" => esc_url(get_permalink(wc_get_page_id('checkout'))),

                            "notif_url" => site_url('/') . 'wp-json/viazipay/v1/notif/?pay=' . $method_value . '&order_id=' . $order_id . '',

                            "customer_id" => get_current_user_id(),

                            "customer_name" => $user->user_login,

                            "description" => 'description',

                            "pricing_type" => 'fixed_price',

                        );

                        $get_val = ViazizaPay::processPayment($public_key, $private_key, $data, $method_value);

                        if (200 === $get_val['status']) {
                            $payement_url = $get_val['datas']['payment_url'];
                            return $payement_url;
                        } else {
                            wc_add_notice(__('Le numéro de commande a déjà été pris. Veuillez réessayer!!'), 'error');
                            return null;
                        }

                        break;

                    case  'cart':

                        $user = wp_get_current_user();
                        $data = array(

                            "currency" => $order->get_currency(),

                            "order_id" => 'VPY-' . $order_id,

                            "amount" => (float)$order->get_total(),

                            "redirect_url" => add_query_arg('om_order', $order_id, esc_url(get_permalink(wc_get_page_id('checkout')))),

                            "cancel_url" => esc_url(get_permalink(wc_get_page_id('checkout'))),

                            "notif_url" => site_url('/') . 'wp-json/viazipay/v1/notif/?pay=' . $method_value . '&order_id=' . $order_id . '',

                            "customer_id" => get_current_user_id(),

                            "customer_name" => $user->user_login,

                            "description" => 'description',

                            "pricing_type" => 'fixed_price',
                        );

                        $get_val = ViazizaPay::processPayment($public_key, $private_key, $data, $method_value);
                        if (200 === $get_val['status']) {
                            $payement_url = $get_val['datas']['payment_url'];
                            return $payement_url;
                        } else {
                            wc_add_notice(__('Le numéro de commande a déjà été pris. Veuillez réessayer!!'), 'error');
                            return null;
                        }

                        break;
                }
            }
            /**

             * payement fields to create the form payment option

             */

            public function payment_fields()

            {
                $option_momo = $this->get_option('momo');
                $option_om = $this->get_option('om');
                $option_coinbase = $this->get_option('coinbase');
                $option_cart = $this->get_option('cart');

                $payArray = [
                    'momo' => $option_momo,
                    'om' => $option_om,
                    'coinbase' => $option_coinbase,
                    'cart' => $option_cart
                ];

                $selectedOptions = array_filter($payArray, function ($value) {
                    return $value === 'yes';
                });

?>

                <div class="viazipay_cc-selector" style="display: block;">

                    <div class="viazi_description_method_display">

                        <?php echo $this->method_description; ?>

                    </div>

                    <?php

                    foreach ($selectedOptions as $key => $value) :

                    ?>



                        <div class="viazipay_inner">

                            <div class="viazi_pay">

                                <input class="viazipay_method_check" id="viazi_payment_<?php echo $key ?>" type="radio" name="viaziopt" value="<?php echo $key; ?>" />

                                <label class="viazipay-cc viazi_payment_<?php echo $key ?>" onclick="description_method('viazipay_method_<?php echo $key; ?>')" for="viazi_payment_<?php echo $key ?>">

                                    <img src="<?php echo plugin_dir_url(__FILE__) . '../assets/payement_' . $key . '.png' ?>" width="80" height="80" alt="viazi_payment_<?php echo $key ?>">

                                    <p class="viazipay_method_name">

                                        <?php if ($key == 'om') {

                                            echo $this->get_option('label_orange');
                                        } elseif ($key == 'momo') {

                                            echo $this->get_option('label_mtn');
                                        } elseif ($key == 'coinbase') {

                                            echo $this->get_option('label_coinbase');
                                        } else {
                                            echo $this->get_option('label_cart');
                                        }  ?>

                                    </p>

                                </label>

                            </div>

                            <div class="viazipay_method_woocommerce" id="viazipay_method_<?php echo $key ?>">

                                <p class="viazipay_method_description">

                                    <?php if ($key == 'om') {

                                        $paydescrip = $this->get_option('description_orange');

                                        echo $paydescrip;
                                    } elseif ($key == 'momo') {

                                        $paydescrip = $this->get_option('description_mtn');

                                        echo $paydescrip;
                                    } elseif ($key == 'coinbase') {

                                        $paydescrip = $this->get_option('description_coinbase');

                                        echo $paydescrip;
                                    } else {
                                        $paydescrip = $this->get_option('description_cart');

                                        echo $paydescrip;
                                    }

                                    ?>

                                </p>

                            </div>

                        </div>

                    <?php endforeach; ?>

                </div>

<?php

            }

            public function viazipay_payment_scripts()

            {

                $url = plugin_dir_url(__FILE__) . '/assets/js/woocommerce.js';

                $url = str_replace('/includes/', '', $url);

                wp_register_script('woocommerce_viazipay', $url, array(), '1.0', true);

                wp_enqueue_script('woocommerce_viazipay');
            }

            /**

             * Validate field to verify if the user choose the payment option

             */

            public function viazipay_validate_fields()
            {
                $options = $this->get_option_keys();

                $is_option_selected = false;
                foreach ($options as $option_key) {
                    if (isset($_POST[$option_key]) && $_POST[$option_key] === 'yes') {
                        $is_option_selected = true;
                        break;
                    }
                }

                if (!$is_option_selected) {
                    wc_add_notice(__('Une option de paiement est requise', 'viazipay'), 'error');
                    return false;
                }

                return true;
            }
        }
    }
}


/** Inclusion of my plugin's style file */

function viazipay_enqueue_style()

{

    wp_enqueue_style('style.css', plugin_dir_url(__FILE__) . '../css/style.css');
}

add_action('wp_enqueue_scripts', 'viazipay_enqueue_style');



/** Here I create an endpoint on which the operator's response will be sent.

 * I retrieve this response to perform certain operations.

 */



add_action('rest_api_init', 'viazipay_endpoint');

function viazipay_endpoint()

{

    register_rest_route('viazipay/v1', '/notif', array(

        'methods' => 'POST',

        'callback' => 'viazipay_callback',

    ));
}

/** Execute function when endpoint is called */

function viazipay_callback($payment_data)

{

    global $woocommerce;

    $payment_data = ViazizaPay::pull();

    if (isset($_GET['pay']) && $_GET['pay'] == 'om') {


        if ($payment_data && isset($payment_data['order_id'])) {


            if ($payment_data['status'] == 'SUCCESS') {

                $order_id = (int)$_GET['order_id'];

                $order = new WC_Order($order_id);

                $order->payment_complete();

                $order->update_status('completed');

                $woocommerce->cart->empty_cart();

                $order->reduce_order_stock();

                $order->add_order_note('payment success');

                return __('Your orange payment success');
            } else {

                echo __('orange payment failed');
            }
        }
    } elseif (isset($_GET['pay']) && $_GET['pay'] == 'coinbase') {



        $payment_data = ViazizaPay::pull();

        file_put_contents("dump.json", json_encode($payment_data));

        if ($payment_data && isset($payment_data['order_id'])) {

            if ($payment_data['status'] == 'COMPLETED') {

                $order_id = (int)$_GET['order_id'];

                $order = new WC_Order($order_id);

                $order->payment_complete();

                $order->update_status('completed');

                $woocommerce->cart->empty_cart();

                $order->reduce_order_stock();

                $order->add_order_note('payment success');

                return __('Your coinbase payment success');
            } else {

                echo __('coinbase payment failed');
            }
        }
    } elseif (isset($_GET['pay']) && $_GET['pay'] == 'momo') {


        if ($payment_data && isset($payment_data['order_id'])) {


            if ($payment_data['status'] == 'SUCCESS') {

                $order_id = (int)$_GET['order_id'];

                $order = new WC_Order($order_id);

                $order->payment_complete();

                $order->update_status('completed');

                $woocommerce->cart->empty_cart();

                $order->reduce_order_stock();

                $order->add_order_note('payment success');


                return __('Your MOMO payment success');
            } else {

                echo __('MTN mobile money payment failed');
            }
        }
    } elseif (isset($_GET['pay']) && $_GET['pay'] == 'cart') {



        $payment_data = ViazizaPay::pull();


        file_put_contents("dump.json", json_encode($payment_data));

        if ($payment_data && isset($payment_data['order_id'])) {


            if ($payment_data['status'] == 'COMPLETED') {

                $order_id = (int)$_GET['order_id'];

                $order = new WC_Order($order_id);

                $order->payment_complete();

                $order->update_status('completed');

                $woocommerce->cart->empty_cart();

                $order->reduce_order_stock();

                $order->add_order_note('payment success');

                return __('Your coinbase payment success');
            } else {

                echo __('coinbase payment failed');
            }
        }
    }
}
