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


/**
 * The code that runs during plugin activation.
 * This action is documented in includes/class-member_import_filter-activator.php
 */
function activate_member_import_filter() {
	require_once plugin_dir_path( __FILE__ ) . 'includes/class-member_import_filter-activator.php';
	Member_import_filter_Activator::activate();
}

/**
 * The code that runs during plugin deactivation.
 * This action is documented in includes/class-member_import_filter-deactivator.php
 */
function deactivate_member_import_filter() {
	require_once plugin_dir_path( __FILE__ ) . 'includes/class-member_import_filter-deactivator.php';
	Member_import_filter_Deactivator::deactivate();
}

register_activation_hook( __FILE__, 'activate_member_import_filter' );
register_deactivation_hook( __FILE__, 'deactivate_member_import_filter' );

/**
 * The core plugin class that is used to define internationalization,
 * admin-specific hooks, and public-facing site hooks.
 */
require plugin_dir_path( __FILE__ ) . 'includes/class-member_import_filter.php';

/**
 * Begins execution of the plugin.
 *
 * Since everything within the plugin is registered via hooks,
 * then kicking off the plugin from this point in the file does
 * not affect the page life cycle.
 *
 * @since    1.0.0
 */
function run_member_import_filter() {

	$plugin = new Member_import_filter();
	$plugin->run();

	add_filter('is_iu_import_userdata', 'mod_userdata', 0, 2);
	add_filter('is_iu_import_usermeta', 'mod_usermeta', 0, 2);

	function mod_userdata($userdata, $usermeta){
	
		$display_name = $usermeta['Full Name'];

		if (array_key_exists("Membership Email Address", $usermeta)){
			$user_email = $usermeta['Membership Email Address'];		
		} elseif (array_key_exists("Ticket Email Address", $usermeta)){
			$user_email = $usermeta['Ticket Email Address'];		
		} else{
			throw new \Exception(implode($usermeta));
		}

		$display_name_array = explode(" ", $display_name);

		$modded_userdata = array(
			"user_login" => str_replace(' ', '', $display_name),
			"user_email" => $user_email,
			"user_pass" => null, 
			"first_name" => $display_name_array[0],
			"last_name" => end($display_name_array),
			"display_name" => $display_name,
			"nickname" => $display_name,
			"role" => get_option('default_role', 'subscriber'));

		return $modded_userdata;
	}


	function mod_usermeta($usermeta, $userdata){
		$result = array_intersect_key($usermeta, array_flip(get_metadata_columns()));
		error_log(print_r($result, TRUE));
		return $result;
	}

	function remove_elements($remove, $original_array){
		foreach($remove as $key => $value){
			if (array_key_exists($key, $original_array)){
				unset($original_array[$key]);
			}
		}
		return $original_array;
	}

	function get_metadata_columns(){
		$metadata_column_names = get_option('metadata_column_names');

		return explode(",", str_replace(", ", ",", $metadata_column_names));
	}

}
run_member_import_filter();




// add_action('admin_menu', 'add_setup_menu');

// function add_setup_menu(){
// 	add_menu_page('User Import Filter', 'User Import Filter', 'manage_options', 'user-import-filter', 'admin_init');
// }

// function admin_init(){
// 	echo "<h1>Hello World!</h1>";
// }