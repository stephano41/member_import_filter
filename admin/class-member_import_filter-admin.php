<?php

/**
 * The admin-specific functionality of the plugin.
 *
 * @link       https://stevenzhangshao@gmail.com
 * @since      1.0.0
 *
 * @package    Member_import_filter
 * @subpackage Member_import_filter/admin
 */

/**
 * The admin-specific functionality of the plugin.
 *
 * Defines the plugin name, version, and two examples hooks for how to
 * enqueue the admin-specific stylesheet and JavaScript.
 *
 * @package    Member_import_filter
 * @subpackage Member_import_filter/admin
 * @author     Steven Zhang <stevenzhangshao@gmail.com>
 */
class Member_import_filter_Admin {

	/**
	 * The ID of this plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string    $plugin_name    The ID of this plugin.
	 */
	private $plugin_name;

	/**
	 * The version of this plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string    $version    The current version of this plugin.
	 */
	private $version;

	/**
	 * Initialize the class and set its properties.
	 *
	 * @since    1.0.0
	 * @param      string    $plugin_name       The name of this plugin.
	 * @param      string    $version    The version of this plugin.
	 */
	public function __construct( $plugin_name, $version ) {

		$this->plugin_name = $plugin_name;
		$this->version = $version;
		add_action('admin_menu', array( $this, 'addPluginAdminMenu' ), 9);    
		add_action('admin_init', array( $this, 'registerAndBuildFields' ));

	}

	/**
	 * Register the stylesheets for the admin area.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_styles() {

		/**
		 * This function is provided for demonstration purposes only.
		 *
		 * An instance of this class should be passed to the run() function
		 * defined in Member_import_filter_Loader as all of the hooks are defined
		 * in that particular class.
		 *
		 * The Member_import_filter_Loader will then create the relationship
		 * between the defined hooks and the functions defined in this
		 * class.
		 */

		wp_enqueue_style( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'css/member_import_filter-admin.css', array(), $this->version, 'all' );

	}

	/**
	 * Register the JavaScript for the admin area.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_scripts() {

		/**
		 * This function is provided for demonstration purposes only.
		 *
		 * An instance of this class should be passed to the run() function
		 * defined in Member_import_filter_Loader as all of the hooks are defined
		 * in that particular class.
		 *
		 * The Member_import_filter_Loader will then create the relationship
		 * between the defined hooks and the functions defined in this
		 * class.
		 */

		wp_enqueue_script( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'js/member_import_filter-admin.js', array( 'jquery' ), $this->version, false );

	}

	public function addPluginAdminMenu() {
		//add_menu_page( $page_title, $menu_title, $capability, $menu_slug, $function, $icon_url, $position );
		add_users_page(  $this->plugin_name, 'User Import Filter', 'manage_options', $this->plugin_name, array( $this, 'displayPluginAdminSettings' ) );
	}


	public function displayPluginAdminSettings() {
		// set this var to be used in the settings-display view
		$active_tab = isset( $_GET[ 'tab' ] ) ? $_GET[ 'tab' ] : 'general';
		if(isset($_GET['error_message'])){
			add_action('admin_notices', array($this,'SettingsMessages'));
			do_action( 'admin_notices', $_GET['error_message'] );
		}
		require_once 'partials/'.$this->plugin_name.'-admin-settings-display.php';
	}
	
	
	public function SettingsMessages($error_message){
		switch ($error_message) {
			case '1':
				$message = __( 'There was an error adding this setting. Please try again.  If this persists, shoot us an email.', 'my-text-domain' );                 
				$err_code = esc_attr( 'plugin_name_example_setting' );                 
				$setting_field = 'plugin_name_example_setting';                 
				break;
		}
		$type = 'error';
		add_settings_error(
				$setting_field,
				$err_code,
				$message,
				$type
			);
	}


	public function registerAndBuildFields() {
		/**
	   * First, we add_settings_section. This is necessary since all future settings must belong to one.
	   * Second, add_settings_field
	   * Third, register_setting
	   */     
		add_settings_section(
		// ID used to identify this section and with which to register options
		'user_import_setting_section', 
		// Title to be displayed on the administration page
		'',  
		// Callback used to render the description of the section
		array( $this, 'display_general_description' ),    
		// Page on which to add this section of options
		'general_settings'                   
		);
		unset($args);
		$args = array (
				'type'      => 'input',
				'subtype'   => 'text',
				'id'    => 'default_role',
				'name'      => 'default_role',
				'required' => 'false',
				'get_options_list' => '',
				'value_type'=>'normal',
				'wp_data' => 'option'
			);
		add_settings_field(
			'default_role',
			'Default import role ID',
			array( $this, 'render_settings_field' ),
			'general_settings',
			'user_import_setting_section',
			$args
		);
	
	
		register_setting(
			'general_settings',
			'default_role',
			array(
				'default' => 'subscriber',
				)
			);
		
		
		unset($args);
		$args = array (
				'type'      => 'input',
				'subtype'   => 'text',
				'id'    => 'metadata_column_names',
				'name'      => 'metadata_column_names',
				'required' => 'false',
				'get_options_list' => '',
				'value_type'=>'normal',
				'wp_data' => 'option'
			);

		add_settings_field(
			'metadata_column_names',
			'Column names to include as metadata only',
			array( $this, 'render_settings_field' ),
			'general_settings',
			'user_import_setting_section',
			$args
		);
	
	
		register_setting(
			'general_settings',
			'metadata_column_names',
			array(
				'default' => 'Ticket Type, Stream preference',
				)
			);

		unset($args);
		$args = array (
				'type'      => 'input',
				'subtype'   => 'text',
				'id'    => 'fullname_column',
				'name'      => 'fullname_column',
				'required' => 'false',
				'get_options_list' => '',
				'value_type'=>'normal',
				'wp_data' => 'option'
			);

		add_settings_field(
			'fullname_column',
			'Full name column name',
			array( $this, 'render_settings_field' ),
			'general_settings',
			'user_import_setting_section',
			$args
		);
	
	
		register_setting(
			'general_settings',
			'fullname_column',
			array(
				'default' => 'Full Name',
				)
			);

		unset($args);
		$args = array (
				'type'      => 'input',
				'subtype'   => 'text',
				'id'    => 'email_columns',
				'name'      => 'email_columns',
				'required' => 'false',
				'get_options_list' => '',
				'value_type'=>'normal',
				'wp_data' => 'option'
			);

		add_settings_field(
			'email_columns',
			'Column names to search for email address',
			array( $this, 'render_settings_field' ),
			'general_settings',
			'user_import_setting_section',
			$args
		);
	
	
		register_setting(
			'general_settings',
			'email_columns',
			array(
				'default' => 'Membership Email Address',

				)
			);

	
	}


	public function display_general_description() {
		// echo(get_option('metadata_column_names'));
		// echo(get_option('default_role'));
		// echo(get_option('fullname_column'));
		// echo(get_option('email_columns'));
	  } 
	
	
	public function render_settings_field($args) {
	/* EXAMPLE INPUT
				'type'      => 'input',
				'subtype'   => '',
				'id'    => $this->plugin_name.'_example_setting',
				'name'      => $this->plugin_name.'_example_setting',
				'required' => 'required="required"',
				'get_option_list' => "",
				'value_type' = serialized OR normal,
				'wp_data'=>(option or post_meta),
				'post_id' =>
		*/     
		if($args['wp_data'] == 'option'){
			$wp_data_value = get_option($args['name']);
		} elseif($args['wp_data'] == 'post_meta'){
			$wp_data_value = get_post_meta($args['post_id'], $args['name'], true );
		}
	
		switch ($args['type']) {
	
			case 'input':
				$value = ($args['value_type'] == 'serialized') ? serialize($wp_data_value) : $wp_data_value;
				switch($args['subtype']){
					case 'checkbox':
						$checked = ($value) ? 'checked' : '';
						echo '<input type="'.$args['subtype'].'" id="'.$args['id'].'" "'.$args['required'].'" name="'.$args['name'].'" size="40" value="1" '.$checked.' />';
						break;
					case 'text':
						$this->render_text_field($args, $value);

						/*<input required="required" '.$disabled.' type="number" step="any" id="'.$this->plugin_name.'_cost2" name="'.$this->plugin_name.'_cost2" value="' . esc_attr( $cost ) . '" size="25" /><input type="hidden" id="'.$this->plugin_name.'_cost" step="any" name="'.$this->plugin_name.'_cost" value="' . esc_attr( $cost ) . '" />*/
						break;
				
					default:
						break;
					}
			default:
				# code...
				break;
		}
	}

	public function render_text_field($args, $value){
		$prependStart = (isset($args['prepend_value'])) ? '<div class="input-prepend"> <span class="add-on">'.$args['prepend_value'].'</span>' : '';
		$prependEnd = (isset($args['prepend_value'])) ? '</div>' : '';
		$step = (isset($args['step'])) ? 'step="'.$args['step'].'"' : '';
		$min = (isset($args['min'])) ? 'min="'.$args['min'].'"' : '';
		$max = (isset($args['max'])) ? 'max="'.$args['max'].'"' : '';
		if(isset($args['disabled'])){
			// hide the actual input bc if it was just a disabled input the informaiton saved in the database would be wrong - bc it would pass empty values and wipe the actual information
			echo $prependStart.'<input type="'.$args['subtype'].'" id="'.$args['id'].'_disabled" '.$step.' '.$max.' '.$min.' name="'.$args['name'].'_disabled" size="40" disabled value="' . esc_attr($value) . '" /><input type="hidden" id="'.$args['id'].'" '.$step.' '.$max.' '.$min.' name="'.$args['name'].'" size="40" value="' . esc_attr($value) . '" />'.$prependEnd;
		} else {
			echo $prependStart.'<input type="'.$args['subtype'].'" id="'.$args['id'].'" "'.$args['required'].'" '.$step.' '.$max.' '.$min.' name="'.$args['name'].'" size="40" value="' . esc_attr($value) . '" />'.$prependEnd;
		}


	}
}