<?php

class Ss_Jvzoo_Wpestore
{


    /**
     * The unique identifier of this plugin.
     *
     * @since    1.0.0
     * @access   protected
     * @var      string $plugin_name The string used to uniquely identify this plugin.
     */
    protected $plugin_name;

    /**
     * The current version of the plugin.
     *
     * @since    1.0.0
     * @access   protected
     * @var      string $version The current version of the plugin.
     */
    protected $version;

    /**
     * Define the core functionality of the plugin.
     *
     * Set the plugin name and the plugin version that can be used throughout the plugin.
     * Load the dependencies, define the locale, and set the hooks for the admin area and
     * the public-facing side of the site.
     *
     * @since    1.0.0
     */

    public function __construct()
    {

        $this->plugin_name = 'ss-jvzoo-wpestore';
        $this->version     = '1.0.0';

        $this->load_dependencies();
        $this->set_locale();
        $this->define_admin_hooks();
        $this->define_public_hooks();
        $this->define_shortcode_hooks();

    }

    /**
     *  Load the required dependencies for this plugin.
     *
     * @since 1.0.0
     * @access private
     */
    private function load_dependencies()
    {

        /**
         * The class responsible for defining internationalization functionality
         * of the plugin.
         */
        require_once plugin_dir_path(dirname(__FILE__)) . 'includes/class-ss-jvzoo-wpestore-i18n.php';

        /**
         * This file contains the list of table names
         */
        require_once plugin_dir_path(dirname(__FILE__)) . 'includes/table_names.php';

        /**
         * This file contains the list of defined variables
         */
        require_once plugin_dir_path(dirname(__FILE__)) . 'includes/define_variables.php';

        /**
         * The class responsible for defining all actions that occur in the admin area.
         */
        require_once plugin_dir_path(dirname(__FILE__)) . 'admin/class-ss-jvzoo-wpestore-admin.php';

        /**
         * The class responsible for defining all actions that occur in the public-facing
         * side of the site.
         */
        require_once plugin_dir_path(dirname(__FILE__)) . 'public/class-ss-jvzoo-wpestore-public.php';
    }

    /**
     * Define the locale for this plugin for internationalization.
     *
     * Uses the Ss_Jvzoo_Wpestore_i18n class in order to set the domain and to register the hook
     * with WordPress.
     *
     * @since    1.0.0
     * @access   private
     */
    private function set_locale()
    {
        $plugin_i18n = new Ss_Jvzoo_Wpestore_i18n();
        add_action('plugins_loaded', array(&$plugin_i18n, 'load_plugin_textdomain'));
    }

    private function define_admin_hooks()
    {
        $plugin_admin = new Ss_Jvzoo_Wpestore_Admin($this->get_plugin_name(), $this->get_version());
        add_action('admin_enqueue_scripts', array(&$plugin_admin, 'enqueue_styles'));
        add_action('admin_menu', array(&$plugin_admin, 'create_menu'));
    }

    /**
     * Register all of the hooks related to the public-facing functionality
     * of the plugin.
     *
     *
     * @since    1.0.0
     * @access   private
     */
    private function define_public_hooks()
    {
        $plugin_public = new Ss_Jvzoo_Wpestore_Public($this->get_plugin_name(), $this->get_version());

        add_action('wp_enqueue_scripts', array(&$plugin_public, 'enqueue_styles'));
    }

    /**
     * Register shortcode hooks
     *
     * @since   1.0.0
     * @access private
     */
    private function define_shortcode_hooks()
    {
        add_shortcode('SS-JVZoo-estore', array($this, 'success_page'));
    }

    /**
     * This page handles the payments after the request is received from JVZoo
     * @return string
     */
    public function success_page()
    {
        /**
         * The class responsible for managing success payments
         */
        ob_start();
        echo '<div class="ss-margin">';
        require_once plugin_dir_path(dirname(__FILE__)) . 'includes/class-success-page.php';
        echo '</div>';
        $output = ob_get_contents();
        if ($output) {
            ob_end_clean();
        }

        return $output;
    }

    /**
     * The name of the plugin used to uniquely identify it within the context of
     * WordPress and to define internationalization functionality.
     *
     * @since     1.0.0
     * @return    string    The name of the plugin.
     */
    public function get_plugin_name()
    {
        return $this->plugin_name;
    }

    /**
     * Retrieve the version number of the plugin.
     *
     * @since     1.0.0
     * @return    string    The version number of the plugin.
     */
    public function get_version()
    {
        return $this->version;
    }
}
