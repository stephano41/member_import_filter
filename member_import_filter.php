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
	
		$display_name = $usermeta[get_option('fullname_column', 'Full Name')];

		$user_email = null;

		foreach(get_option_as_array('email_columns') as $value){

			if (array_key_exists($value, $usermeta)){
				if (!empty($usermeta[$value])){
					$user_email = $usermeta[$value];
					break;
				}
				
			}
		}

		$display_name_array = explode(" ", $display_name);

		$modded_userdata = array(
			"user_login" => $display_name,
			"user_email" => $user_email,
			"user_pass" => null, 
			"first_name" => $display_name_array[0],
			"last_name" => end($display_name_array),
			"display_name" => $display_name,
			"nickname" => $display_name,
			"role" => get_option('default_role', 'um_member'));
		
		// error_log(print_r($modded_userdata, TRUE));
		
		return $modded_userdata;
	}


	function mod_usermeta($usermeta, $userdata){

		$result = array_intersect_key($usermeta, array_flip(get_option_as_array('metadata_column_names')));

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

	function get_option_as_array(string $option, mixed $default = null){
		$setting = get_option($option, $default);

		return explode(",", str_replace(", ", ",", $setting));
	}

	add_action("is_iu_import_page_before_table", "filter_on_warning");

	function filter_on_warning(){

		?>
		<div class="wrap"> <h3> User import filter is on! </h3> </div>
		<?php
		
	}

}
run_member_import_filter();

