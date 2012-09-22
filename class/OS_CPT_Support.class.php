<?php
if (!defined('OS_CPT_VER') )
	exit('NO direct script access allowed');

/**
 * Organize Series Custom Post Type Support
 *
 * This is an addon for the Organize Series plugin
 *
 * @package		Organize Series Custom Post Type Support
 * @author		Darren Ethier
 * @copyright	(c)2009-2012 Rough Smooth Engine All Rights Reserved.
 * @license		http://roughsmootheng.in/license-gplv3.htm  * *
 * @link		http://organizeseries.com
 * @version		0.1
 *
 * ------------------------------------------------------------------------
 *
 * OS_CPT_Support
 *
 * Main class that initializes and sets up the addon
 *
 * @package		Organize Series Custom Post Type Support
 * @subpackage	/class/
 * @author		Darren Ethier
 *
 * ------------------------------------------------------------------------
 */

class OS_CPT_Support {

	private $_settings;
	private $_version = OS_CPT_VER;
	private $_os_installed = false;
	private $_os_multi_installed = false;

	/**
	 * constructor
	 * @access public
	 * @return void
	 */
	public function __construct() {
		$this->_init();
		$this->_hooks();
	}

	/**
	 * checks to see what os  plugins are installed and run any required conditionals as well.
	 * @access public
	 * @return void 
	 */
	public function _check_installed_plugins() {
		$this->_os_installed = class_exists('orgSeries') ? true : false;
		$this->_os_multi_installed = class_exists('osMulti') ? true : false;

		if ( !$this->_os_installed ) {
			add_action('admin_notices', array($this, 'installed_warning'));
			add_action('admin_notices', array($this, 'deactivate'));
			return;
		}
	}

	public function installed_warning() {
		$msg = '';
		if ( !$this->_os_installed ) {
			$msg .= '<div id="wpp-message" class="error fade"><p>'.__('The <strong>Organize Series Custom Post Type Support</strong> addon for Organize Series requires the Organize Series plugin to be installed and activated in order to work.  Addons won\'t activate until this condition is met.', 'organize-series-cpt').'</p></div>';
		}
		echo $msg;
	}

	public function deactivate() {
		deactivate_plugins('organize-series-cpt-support/os-cpt-setup.php', true);
	}

	/**
	 * This initializes the addon and contains things that must be run before anything else gets setup.
	 * @return void
	 */
	private function _init() {
		add_action('activate_organize-series-cpt-support/os-cpt-setup.php', array($this, 'install'));
		add_action('init', array($this, 'register_textdomain') );
		add_action( 'plugins_loaded', array($this,'_check_installed_plugins') );
	}

	/**
	 * this checks and makes sure that the initial settings have been set for custom post types (otherwise cpt could break orgseries!)
	 * @access private
	 * @return void
	 */
	private function _do_settings_check() {
		global $orgseries;
		//make sure we have default post_type support added (i.e. posts);
		if ( !isset($orgseries->settings['post_types_for_series'] ) ) {
			$settings = get_option('org_series_options');
			$settings['post_types_for_series'] = array('post');
			update_option('org_series_options', $settings);
		}
	}

	/**
	 * this runs anything happenign during the install of the plugin
	 * @return void
	 */
	public function install() {
		update_option('os_cpt_support_version', $this->_version);
	}

	/**
	 * registers the text domain for localization
	 * @return void
	 */
	public function register_textdomain() {
		$dir = basename(dirname(__FILE__)).'/lang';
		load_plugin_textdomain('organize-series-cpt', false, $dir);
	}

	/**
	 * contains wordpress hooks that need to be added when this plugin is loaded.
	 * @access private
	 * @return void
	 */
	private function _hooks() {
		add_action('admin_init', array($this, 'admin_init_hooks') );
		add_action('plugins_loaded', array($this, 'plugins_loaded_hooks'), 20 );
		add_action('init', array($this, 'main_init' ) );
	}

	/**
	 * all the hooks that need to run in wp's admin_init
	 * @return void
	 */
	public function admin_init_hooks() {
		//hook into organizeseries options page.
		add_settings_field('orgseries_cpt_settings', 'Custom Post Type Support', array(&$this, 'settings_display'), 'orgseries_options_page', 'series_automation_settings');
		add_filter( 'orgseries_options', array(&$this, 'settings_validate'), 10 , 3 );
		$this->_do_settings_check();
	}

	/**
	 * all the hooks/code that needs to run in wp plugins_loaded hook
	 * @return void
	 */
	public function plugins_loaded_hooks() {
		add_filter( 'orgseries_posttype_support', array($this, 'add_post_type_support') );
	}

	/**
	 * all the hooks/code that needs to run in wp's main init hook
	 * @access public
	 * @return void 
	 */
	public function main_init() {
		//nothing yet
		return;
	}

	/**
	 * hook into orgseries_posttype_support and provides the new post_types.
	 */
	public function add_post_type_support($post_types) {
		global $orgseries;
		$post_types = $orgseries->settings['post_types_for_series'];
		return $post_types;
	}

	/**
	 * validate cpt settings from series options page
	 * @param  array $newinput new settings
	 * @param  array $input    old settings
	 * @return array           new settings
	 */
	public function settings_validate($newinput, $input) {
		$newinput['post_types_for_series'] = $input['post_types_for_series'];
		return $newinput;
	}

	/**
	 * html for the settings fields used to set the custom post types that organize series works with.
	 * @return [type] [description]
	 */
	public function settings_display() {
		global $orgseries;
		$org_opt = $orgseries->settings;
		$org_name = 'org_series_options';
		$post_types = get_post_types(array( 'show_ui' => true ));
		$org_opt['post_types_for_series'] = isset($org_opt['post_types_for_series']) ? $org_opt['post_types_for_series'] : array();
		?>
		<p><strong><?php _e('Custom Post Type Support:', 'organize-series-cpt'); ?></strong></p>
		<p><?php _e('Select which WordPress Post Types you would like Organize Series to work with.  Whenever you add a new post type then you need to make sure it\'s selected here', 'organize-series-cpt'); ?></p>
		<?php
		echo '<p><ul>';
		foreach ( $post_types as $post_type ) {
			if ( $post_type == 'series_group')
				continue;  //we want to make sure the series_group addon isn't included in this.
			$checked = in_array($post_type, $org_opt['post_types_for_series']) ? 'checked="checked"' : '';
			?>
			<li><input type="checkbox" name="<?php echo $org_name; ?>[post_types_for_series][]" value="<?php echo $post_type; ?>" <?php echo $checked; ?> /> <?php echo $post_type; ?></li>
			<?php
		}
		echo '</ul></p>';
	}
} //end OS_CPT_Support class