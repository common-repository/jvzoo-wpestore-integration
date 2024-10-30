<?php

/**
 * Fired during plugin activation
 *
 * @link       http://test.com
 * @since      1.0.0
 *
 * @package    Ss_Jvzoo_Wpestore
 * @subpackage Ss_Jvzoo_Wpestore/includes
 */

/**
 * Fired during plugin activation.
 *
 * This class defines all code necessary to run during the plugin's activation.
 *5
 * @since      1.0.0
 * @package    Ss_Jvzoo_Wpestore
 * @subpackage Ss_Jvzoo_Wpestore/includes
 * @author     Simon shrestha <cimon77@gmail.com>
 */
class Ss_Jvzoo_Wpestore_Activator
{

    public static $ss_database_version = '1.0';

    public static function get_db_version()
    {
        return self::$ss_database_version;
    }

    /**
     * Short Description. (use period)
     *
     * Long Description.
     *
     * @since    1.0.0
     */
    public static function activate()
    {
        global $wpdb;

        $table_name = $wpdb->prefix . 'map_products';

        $charset_collate = $wpdb->get_charset_collate();

        $sql = "CREATE TABLE $table_name (
                      id mediumint(9) NOT NULL AUTO_INCREMENT,
                      wpestore_product_id mediumint(9) NOT NULL,
                      jvzoo_product_id mediumint(9) NOT NULL,
                      date datetime DEFAULT '0000-00-00 00:00:00' NOT NULL,
                      UNIQUE KEY id (id)
        ) $charset_collate;";

        require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
        dbDelta($sql);

        add_option("ss_jvzoo_wpestore_db_version", self::get_db_version());

        Ss_Jvzoo_Wpestore_Config_Helper::add_options_config_values();

    }
}
