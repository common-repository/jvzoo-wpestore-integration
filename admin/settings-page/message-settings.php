<?php

class Ss_Jvzoo_Wpestore_message_settings
{
    /**
     * Ss_Jvzoo_Wpestore_general_settings constructor.
     */
    public function __construct()
    {
        $this->message_settings();
    }

    /**
     * Message Settings
     */
    public function message_settings()
    {

        if (isset($_REQUEST['action']) && 'messagesettings' == $_REQUEST['action']) {
            check_admin_referer('ss-jvzoo-message-settings', '_wpnonce_message-settings');

            $settings = $this->add_message_settings();
            if (is_wp_error($settings)) {
                $add_settings_errors = $settings;
            } else {
                $add_settings_success  = $settings;
            }

            if (isset($add_settings_errors) && is_wp_error($add_settings_errors)) : ?>
                <div class="error">
                    <?php
                    foreach ($add_settings_errors->get_error_messages() as $message)
                        echo "<p>$message</p>";
                    ?>
                </div>
            <?php endif;

            if (isset($add_settings_success)) :
                foreach ($add_settings_success as $msg) {
                    echo '<div id="message" class="updated notice is-dismissible"><p>' . $msg . '</p></div>';
                }
            endif;

        }

        $Ss_Jvzoo_Wpestore_Config = Ss_Jvzoo_Wpestore_Config::getInstance();
        $content_success = $Ss_Jvzoo_Wpestore_Config->getValue('success_message');
        $content_error = $Ss_Jvzoo_Wpestore_Config->getValue('error_message');;
        $settings = array('media_buttons' => false);

        ?>

        <form method="post">

            <input name="action" type="hidden" value="messagesettings">

            <?php wp_nonce_field('ss-jvzoo-message-settings', '_wpnonce_message-settings'); ?>

            <h2><?php echo __('Success message', 'ss-jvzoo-wpestore') ?></h2>

            <p>
                <?php echo __('Please enter a success message that appears when the purchase is complete', 'ss-jvzoo-wpestore') ?>
            </p>

            <?php wp_editor($content_success, 'message-settings-success', $settings); ?>

            <h2><?php echo __('Error message', 'ss-jvzoo-wpestore') ?></h2>

            <p>
                <?php echo __('Please enter a error message that appears when the purchase is complete', 'ss-jvzoo-wpestore') ?>
            </p>

            <?php wp_editor($content_error, 'message-settings-error', $settings); ?>
            <p>
                <input type="submit" value="<?php echo __('Submit', 'ss-jvzoo-wpestore') ?>"
                       class="button button-primary"/>
            </p>

        </form>
        <?php
    }

    /**
     * Add JVZoo api
     * @return array|WP_Error
     */
    function add_message_settings()
    {
        $settings = new stdClass;

        if (isset($_POST['message-settings-success'])) {
            $settings->message_settings_success = wp_unslash(($_POST['message-settings-success']));
        }

        if (isset($_POST['message-settings-error'])) {
            $settings->message_settings_error =  wp_unslash($_POST['message-settings-error']);
        }
 
        $errors = new WP_Error();

        if ( $settings->message_settings_success == '') {
            $errors->add('message-settings-success', __('<strong>ERROR</strong>: Please enter a success message.'), 'ss-jvzoo-wpestore');
        }

        if ($settings->message_settings_error == '') {
            $errors->add('message-settings-error', __('<strong>ERROR</strong>: Please enter a error message.'), 'ss-jvzoo-wpestore');
        }

        if ($errors->get_error_codes())
            return $errors;

        $messages = array();

        $Ss_Jvzoo_Wpestore_Config = Ss_Jvzoo_Wpestore_Config::getInstance();

        $Ss_Jvzoo_Wpestore_Config->setValue('success_message', $settings->message_settings_success );

        $Ss_Jvzoo_Wpestore_Config->setValue('error_message', $settings->message_settings_error );

        $Ss_Jvzoo_Wpestore_Config->saveConfig();

        $messages[] = __('Success message added', 'ss-jvzoo-wpestore');
        $messages[] = __('Error message added', 'ss-jvzoo-wpestore');

        return $messages;
    }
}