<?php

class Ss_Jvzoo_Wpestore_settings_page_menu
{
    /**
     * Ss_Jvzoo_Wpestore_settings_page_menu constructor.
     * All the settings lies in this class
     */
    public function __construct()
    {
        $this->load_dependencies();
    }

    /**
     *  Load the required dependencies for this plugin.
     *
     * @since 1.0.0
     * @access private
     */
    private function load_dependencies()
    {
        require_once plugin_dir_path(dirname(__FILE__)) . 'admin/settings-page/general-settings.php';
        require_once plugin_dir_path(dirname(__FILE__)) . 'admin/settings-page/message-settings.php';
    }
}

$Ss_Jvzoo_Wpestore_settings_page_menu = new Ss_Jvzoo_Wpestore_settings_page_menu();
$current = (isset($_GET['tab'])) ? $_GET['tab'] : 1;

?>

<h3 class="nav-tab-wrapper">
    <a class="nav-tab <?php echo ($current == 1) ? 'nav-tab-active' : ''; ?>"
       href="admin.php?page=map-product-settings"><?php echo __('General Settings', 'ss-jvzoo-wpestore'); ?></a>
<!--    <a class="nav-tab --><?php //echo ($current == 2) ? 'nav-tab-active' : ''; ?><!--"-->
<!--       href="admin.php?page=map-product-settings&tab=2">--><?php //echo __('Message Settings', 'ss-jvzoo-wpestore'); ?><!--</a>-->
</h3>

<?php
switch ($current) {

//    case '2':
//        $Ss_Jvzoo_Wpestore_message_settings = new Ss_Jvzoo_Wpestore_message_settings();
//        break;

    default:
        $Ss_Jvzoo_Wpestore_general_settings = new Ss_Jvzoo_Wpestore_general_settings();
        break;
}
?>
