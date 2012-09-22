<?php
/*
Plugin Name: Organize Series Addon: Custom Post Type Support
Plugin URI: http://organizeseries.com
Version: 0.1
Description: This plugin is an addon for the Organize Series plugin that gives custom post type support so you can use Organize Series with other WordPress post types.  After activating, visit the <a href="/wp-admin/options-general.php?page=orgseries_options_page" title="Series Options Page Link">Series Options Page</a> to select which custom post types you'd like series activated for.
Author: Darren Ethier
Author URI: http://www.unfoldingneurons.com
*/

$os_cpt_ver = '0.1';

/* LICENSE */
//"Organize Series Plugin" and all addons for it created by this author are copyright (c) 2007-2012 Darren Ethier. This program is free software; you can redistribute it and/or
// modify it under the terms of the GNU General Public License
// as published by the Free Software Foundation; either version 2
// of the License, or (at your option) any later version.
//
// This program is distributed in the hope that it will be useful,
// but WITHOUT ANY WARRANTY; without even the implied warranty of
// MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
// GNU General Public License for more details.
//
// You should have received a copy of the GNU General Public License
// along with this program; if not, write to the Free Software
// Foundation, Inc., 51 Franklin Street, Fifth Floor, Boston, MA  02110-1301, USA.
//
//It goes without saying that this is a plugin for WordPress and I have no interest in developing it for other platforms so please, don't ask ;).

$os_cpt_path = WP_PLUGIN_DIR.'/organize-series-cpt-support/';
$os_cpt_url = WP_PLUGIN_URL.'/organize-series-cpt-support/';

//let's define some constants
define('OS_CPT_PATH', $os_cpt_path);
define('OS_CPT_URL', $os_cpt_url);
define('OS_CPT_VER', $os_cpt_ver);

//Automatic Upgrades stuff
if ( file_exists(WP_PLUGIN_DIR . '/organize-series/inc/pue-client.php') ) {
	//let's get the client api key for updates
	$series_settings = get_option('org_series_options');
	$api_key = $series_settings['orgseries_api'];
	$host_server_url = 'http://organizeseries.com';
	$plugin_slug = 'organize-series-cpt-support';
	$options = array(
		'apikey' => $api_key,
		'lang_domain' => 'organize-series'
	);
	
	require( WP_PLUGIN_DIR . '/organize-series/inc/pue-client.php' );
	$check_for_updates = new PluginUpdateEngineChecker($host_server_url, $plugin_slug, $options);
}

//get files we need.
require_once(OS_CPT_PATH . '/class/OS_CPT_Support.class.php');
//load the addon
$os_cpt_support = new OS_CPT_Support();