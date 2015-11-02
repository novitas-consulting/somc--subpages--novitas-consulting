<?php

/**
 * The plugin bootstrap file
 *
 *
 * @link              http://www.novitas.as
 * @since             1.0.0
 * @package           Somc_Subpages_Novitas_Consulting
 *
 * @wordpress-plugin
 * Plugin Name:       Somc_Subpages_Novitas_Consulting
 * Plugin URI:        somc-subpages_novitas_consulting
 * Description: 	  Plugin that can be used as a Wordpress Widget and as a Wordpress Shortcode. Fetches and display a list of sub pages on any page that the shortcode (below) is placed on. And a customisable Widget allows a list of sub pages (of the current page) to be displayed in the sidebar.
 * Version:           1.0.0
 * Author:            Andrew Pool
 * Author URI:        http://www.novitas.as
 * License:           GPL-2.0+
 * License URI:       http://www.gnu.org/licenses/gpl-2.0.txt
 * Text Domain:       somc-subpages_novitas_consulting
 * Domain Path:       /languages
 */

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

/**
 * The core plugin class that is used to define the public-facing site hooks.
 */
require plugin_dir_path( __FILE__ ) . 'includes/class-somc-subpages_novitas_consulting.php';

/**
 * Begins execution of the plugin.
 *
 * @since    1.0.0
 */
function run_somc_subpages_novitas_consulting() {

	$plugin = new Somc_Subpages_Novitas_Consulting();
	$plugin->run();

}
run_somc_subpages_novitas_consulting();
