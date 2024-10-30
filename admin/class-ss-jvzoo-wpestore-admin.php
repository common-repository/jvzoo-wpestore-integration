<?php

/**
 * Class Ss_Jvzoo_Wpestore_Admin
 */
class Ss_Jvzoo_Wpestore_Admin
{
    /**
     * The ID of this plugin.
     *
     * @since    1.0.0
     * @access   private
     * @var      string $plugin_name The ID of this plugin.
     */
    private $plugin_name;

    /**
     * The version of this plugin.
     *
     * @since    1.0.0
     * @access   private
     * @var      string $version The current version of this plugin.
     */
    private $version;

    /**
     * Initialize the class and set its properties.
     *
     * @since    1.0.0
     * @param      string $plugin_name The name of this plugin.
     * @param      string $version The version of this plugin.
     */
    public function __construct($plugin_name, $version)
    {

        $this->plugin_name = $plugin_name;
        $this->version = $version;
        $this->load_dependencies();
    }

    /**
     * Enqueue admin styles
     */
    public function enqueue_styles()
    {
        wp_enqueue_style($this->plugin_name, plugin_dir_url(__FILE__) . 'css/ss-jvzoo-wpestore-admin.css', false);
    }
    
    public function create_menu()
    {
        add_menu_page($this->plugin_name, __('JVZoo WPestore Settings', 'ss-jvzoo-wpestore'), 'administrator', __FILE__, array($this, 'add_jvzipn_secret_key_page'));
        add_submenu_page(__FILE__, __('JVZIPN Secret Key', 'ss-jvzoo-wpestore'), __('JVZIPN Secret Key', 'ss-jvzoo-wpestore'), 'manage_options', __FILE__, array($this, 'add_jvzipn_secret_key_page'));
        add_submenu_page(__FILE__, __('Shortcode', 'ss-jvzoo-wpestore'), __('Shortcode', 'ss-jvzoo-wpestore'), 'manage_options', 'JVZoo-WPestore-shortcode', array($this, 'shortcode_settings_page'));
        add_submenu_page(__FILE__, __('Map Product', 'ss-jvzoo-wpestore'), __('Map Product', 'ss-jvzoo-wpestore'), 'manage_options', 'map-product', array($this, 'map_product_plugin_page'));
        add_submenu_page(__FILE__, __('Settings', 'ss-jvzoo-wpestore'), __('Settings', 'ss-jvzoo-wpestore'), 'manage_options', 'map-product-settings', array($this, 'map_product_settings_page'));
    }

    public function add_jvzipn_secret_key_page()
    {
        require_once plugin_dir_path(dirname(__FILE__)) . 'admin/add-jvzipn-secret-key.php';
    }

    public function shortcode_settings_page()
    {
        require_once plugin_dir_path(dirname(__FILE__)) . 'admin/shortcode-settings-page.php';
    }

    /**
     * Handles the plugin page
     *
     * @return void
     */
    public function map_product_plugin_page()
    {
        $action = isset($_GET['action']) ? $_GET['action'] : 'list';

        $id = isset($_GET['id']) ? intval($_GET['id']) : 0;

        switch ($action) {
            case 'view':

                $template = dirname(__FILE__) . '/map-product/view/Product-single.php';
                break;

            case 'edit':
                $template = dirname(__FILE__) . '/map-product/view/Product-edit.php';
                break;

            case 'new':
                $template = dirname(__FILE__) . '/map-product/view/Product-new.php';
                break;

            default:
                $template = dirname(__FILE__) . '/map-product/view/Product-list.php';
                break;
        }

        if (file_exists($template)) {
            include $template;
        }
    }

    public function map_product_settings_page()
    {
        require_once plugin_dir_path(dirname(__FILE__)) . 'admin/settings-page-menu.php';
    }

    /**
     *  Load the required dependencies for this plugin.
     *
     * @since 1.0.0
     * @access private
     */
    private function load_dependencies()
    {
        require_once plugin_dir_path(dirname(__FILE__)) . 'admin/map-product/class-Product-list-table.php';
        require_once plugin_dir_path(dirname(__FILE__)) . 'admin/map-product/class-form-handler.php';
        require_once plugin_dir_path(dirname(__FILE__)) . 'admin/map-product/Product-functions.php';
    }

}