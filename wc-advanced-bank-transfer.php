<?php

/**
 * Plugin Name: Advance Bank Payment Transfer Gateway
 * Plugin URI: https://github.com/developer-ramesh
 * Description: Make Direct Payment in Bank Account and upload the bank payment receipt during checkout.
 * Author: Ramesh Kumar
 * Author URI: https://in.linkedin.com/in/developer-ramesh
 * Version: 1.0.1
 * License: GNU General Public License v3.0
 */

defined('ABSPATH') or exit;


// Make sure WooCommerce is active
if (!in_array('woocommerce/woocommerce.php', apply_filters('active_plugins', get_option('active_plugins')))) {
    return;
}

add_action('plugins_loaded', 'wc_gateway_init', 11);

function wc_gateway_init()
{
    require_once(plugin_basename('classes/wc_gateway_advance_bank_transfer.php'));
}


function wc_add_gateways($gateways)
{
    $gateways[] = 'WC_Gateway_Advance_Bank_Payment_Offline';
    return $gateways;
}
add_filter('woocommerce_payment_gateways', 'wc_add_gateways');


/**
 * Adds plugin page links
 */
function wc_gateway_plugin_links($links)
{

    $plugin_links = array(
        '<a href="' . admin_url('admin.php?page=wc-settings&tab=checkout&section=offline_gateway') . '">' . __('Configure', 'wc-gateway-offline') . '</a>'
    );

    return array_merge($plugin_links, $links);
}
add_filter('plugin_action_links_' . plugin_basename(__FILE__), 'wc_gateway_plugin_links');