<?php

/**
 * Clean data on activation / deactivation
 */

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly  
 
register_activation_hook( __FILE__, 'richpostaccordion_activation');

function richpostaccordion_activation() {

	if( ! current_user_can ( 'activate_plugins' ) ) {
		return;
	} 
	add_option( 'richpostaccordion_license_status', 'invalid' );
	add_option( 'richpostaccordion_license_key', '' ); 

}

register_uninstall_hook( __FILE__, 'richpostaccordion_uninstall');

function richpostaccordion_uninstall() {

	delete_option( 'richpostaccordion_license_status' );
	delete_option( 'richpostaccordion_license_key' ); 
	
}