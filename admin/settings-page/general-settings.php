<?php

class Ss_Jvzoo_Wpestore_general_settings
{
    /**
     * Ss_Jvzoo_Wpestore_general_settings constructor.
     */
    public function __construct()
    {
        $this->load_dependencies();
    }

    /**
     *  Load the required dependencies for this page.
     *
     * @since 1.0.0
     * @access private
     */
    private function load_dependencies()
    {
        require_once plugin_dir_path(dirname(__FILE__)) . 'settings-page/general-settings/debug-settings.php';
        require_once plugin_dir_path(dirname(__FILE__)) . 'settings-page/general-settings/wpmember-secret-word.php';
        require_once plugin_dir_path(dirname(__FILE__)) . 'settings-page/general-settings/membership-level.php';
    }
}
