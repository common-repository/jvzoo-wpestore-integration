<?php
/*
Plugin Name: JVZoo WPestore integration
Plugin URI:
Description: This plugin integrates JVZoo and WPestore plugin
Version: 1.0.0
Author: Simon Shrestha
Author URI:
License:     GPL2
License URI: https://www.gnu.org/licenses/gpl-2.0.html
Text Domain: ss-jvzoo-wpestore
Domain Path: /languages
*/


// If this file is called directly, abort.
if (!defined('WPINC')) {
    die;
}

/**
 * The code that runs during plugin activation.
 * This action is documented in includes/class-ss-jvzoo-wpestore-activator.php
 */
function activate_ss_jvzoo_wpestore()
{
    require_once plugin_dir_path(__FILE__) . 'includes/class-ss-jvzoo-wpestore-activator.php';
    Ss_Jvzoo_Wpestore_Activator::activate();
}
require_once plugin_dir_path(__FILE__) . 'includes/ss-jvzoo-estore-configs.php';

register_activation_hook(__FILE__, 'activate_ss_jvzoo_wpestore');

!defined('SS_JVZOO_WPESTORE_ABSPATH') ? define('SS_JVZOO_WPESTORE_ABSPATH', plugin_dir_path(__FILE__)) : NULL;
!defined('SS_JVZOO_WPESTORE_WPDATE_URL') ? define('SS_JVZOO_WPESTORE_WPDATE_URL', plugin_dir_url(__FILE__)) : NULL;

require plugin_dir_path(__FILE__) . 'includes/class-ss-jvzoo-wpestore.php';

function run_ss_jvzoo_wpestore()
{
    $plugin = new Ss_Jvzoo_Wpestore();
}

run_ss_jvzoo_wpestore();
