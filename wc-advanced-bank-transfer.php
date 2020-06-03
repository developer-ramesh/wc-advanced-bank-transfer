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


function uploadInvoice_ajax_load_scripts() {
	// load our jquery file that sends the $.post request
	wp_enqueue_script( "common-ajax", plugin_dir_url( __FILE__ ) . '/includes/js/common.js', array( 'jquery' ) );
 
	// make the ajaxurl var available to the above script
	wp_localize_script( 'common-ajax', 'the_ajax_script', array( 'ajaxurl' => admin_url( 'admin-ajax.php' ) ) );	
}
add_action('wp_print_scripts', 'uploadInvoice_ajax_load_scripts');

function uploadInvoice_ajax_process_request() {
	$wp_upload_dir = wp_upload_dir();
	$path = $wp_upload_dir['path'] . '/';

	$extension = pathinfo( $_FILES['file']['name'], PATHINFO_EXTENSION );
	$valid_formats = array("jpg", "png", "jpeg"); // Supported file types
	if( ! in_array( strtolower( $extension ), $valid_formats ) ){
		return 0;
		die;
	}else{
		$uploadedfile = $_FILES['file'];
    	$upload_overrides = array('test_form' => false);
		$movefile = wp_handle_upload($uploadedfile, $upload_overrides);
		

		$filename = $path.$_FILES['file']['name'];
		$filetype = wp_check_filetype( basename( $filename ), null );
		$wp_upload_dir = wp_upload_dir();
		$attachment = array(
			'guid'           => $wp_upload_dir['url'] . '/' . basename( $filename ),
			'post_mime_type' => $filetype['type'],
			'post_title'     => preg_replace( '/\.[^.]+$/', '', basename( $filename ) ),
			'post_content'   => '',
			'post_status'    => 'inherit'
		);
		// Insert attachment to the database
		$attach_id = wp_insert_attachment( $attachment, $filename, 89700 );

		//require_once( ABSPATH . 'wp-admin/includes/image.php' );
		// Generate meta data
		//$attach_data = wp_generate_attachment_metadata( $attach_id, $filename );
		//wp_update_attachment_metadata( $attach_id, $attach_data );
		echo $attach_id;
		die;
	}
    die();
}
add_action('wp_ajax_invoice_response', 'uploadInvoice_ajax_process_request');