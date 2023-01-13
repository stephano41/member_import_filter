<?php

/**
 * The plugin bootstrap file
 *
 * This file is read by WordPress to generate the plugin information in the plugin
 * admin area. This file also includes all of the dependencies used by the plugin,
 * registers the activation and deactivation functions, and defines a function
 * that starts the plugin.
 *
 * @link              
 * @since             0.0.1
 * @package           Member_import_filter
 *
 * @wordpress-plugin
 * Plugin Name:       Member Import Filter
 * Plugin URI:        
 * Description:       Filters the downloaded QPay membership sheet for import-users-from-csv plugin

 * Version:           1.0.0
 * Author:            Steven Zhang
 * Author URI:        
 * License:           GPL-2.0+
 * License URI:       http://www.gnu.org/licenses/gpl-2.0.txt
 * Text Domain:       member_import_filter
 * Domain Path:       /languages
 */

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

add_filter('is_iu_import_userdata', 'mod_userdata', 0, 2);
add_filter('is_iu_import_usermeta', 'mod_usermeta', 0, 2);

function mod_userdata($userdata, $usermeta){
	error_log(print_r($usermeta, TRUE));
	error_log(print_r($userdata, TRUE));

	$display_name = $usermeta['Full Name'];

	if (array_key_exists("Membership Email Address", $usermeta)){
		$user_email = $usermeta['Membership Email Address'];		
	} elseif (array_key_exists("Ticket Email Address", $usermeta)){
		$user_email = $usermeta['Ticket Email Address'];		
	} else{
		throw new \Exception(implode($usermeta));
	}

	$modded_userdata = array(
		"user_login" => str_replace(' ', '', $display_name),
		"user_email" => $user_email,
		"user_pass" => null, 
		"first_name" => explode(" ", $display_name)[0],
		"last_name" => end(explode(" ", $display_name)),
		"display_name" => $display_name,
		"role" => "um_member");

	return $modded_userdata;
}


function mod_usermeta($usermeta, $userdata){
	$remove = ['Ticket ID',
				'Order ID',
				'Total Paid',
				'Mobile Number',
				'Scanned',
				'Payment',
				'Student ID',
				'Table Number',
				'Purchased',
				'Updated',
				'Dietary Requirements',
				'Student Number',
				'standard_studyYear',
				'Membership Type',
				'Price Paid',	
				'Payment Method',
				'University',
				'Year Level',
				'Status'];

	return remove_elements($remove, $usermeta);
}

function remove_elements($remove, $original_array){
	foreach($remove as $key => $value){
		if (array_key_exists($key, $original_array)){
			unset($original_array[$key]);
		}
	}
	return $original_array;
}