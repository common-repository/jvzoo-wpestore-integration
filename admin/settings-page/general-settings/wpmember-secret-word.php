<?php

class Ss_Jvzoo_Wpestore_general_wpemember_secret_word_settings
{
    public function __construct()
    {
        $this->secret_word();
    }

    public function secret_word()
    {
        if (isset($_REQUEST['action']) && 'secretword' == $_REQUEST['action']) {
            check_admin_referer('ss-secret-word', '_wpnonce_secret-word');

            $settings = $this->add_secret_word_settings();

            if (is_wp_error($settings)) {
                $add_secret_errors = $settings;
            } else {
                $add_secret_success  = $settings;
            }

            if (isset($add_secret_errors) && is_wp_error($add_secret_errors)) : ?>
                <div class="error">
                    <?php
                    foreach ($add_secret_errors->get_error_messages() as $message)
                        echo "<p>$message</p>";
                    ?>
                </div>
            <?php endif;

            if (isset($add_secret_success)) :
                foreach ($add_secret_success as $msg) {
                    echo '<div id="message" class="updated notice is-dismissible"><p>' . $msg . '</p></div>';
                }
            endif;
        }
        $Ss_Jvzoo_Wpestore_Config = Ss_Jvzoo_Wpestore_Config::getInstance();
        $secret_key = $Ss_Jvzoo_Wpestore_Config->getValue('secret_word');
        ?>
        <div id="poststuff">
            <div id="post-body" class="metabox-holder">
                <div class="postbox-container">
                    <div id="advanced-sortables" class="meta-box-sortables ui-sortable">
                        <div class="postbox">

                            <h2 class="hndle">
                                <span><?php echo __('WP eMember secret word', 'ss-jvzoo-wpestore'); ?></span>
                            </h2>

                            <div class="inside">

                                <form method="post" name="secretword" id="secretword">
                                    <?php wp_nonce_field('ss-secret-word', '_wpnonce_secret-word'); ?>
                                    <h4><?php echo __('You can find this secret word in DASHBOARD-> WP eMember -> Settings -> Additional Integration Options', 'ss-jvzoo-wpestore'); ?></h4>
                                    <input name="action" type="hidden" value="secretword">

                                    <p>
                                        <input type="text" id="secret_word" name="secret_word"
                                               class="" size="16" value="<?php echo $secret_key; ?>">

                                    <p>
                                        <input type="submit" name="secretword" id="secretwordsub"
                                               class="button-secondary edd_add_repeatable"
                                               value="<?php echo __('Submit', 'ss-jvzoo-wpestore'); ?>">
                                    </p>

                                </form>
                            </div>
                        </div>

                    </div>
                </div>
            </div>
            <br class="clear">
        </div>

        <?php

    }

    /**
     * Add secret word
     * @return array|WP_Error
     */
    function add_secret_word_settings()
    {
        $settings = new stdClass;
        if (isset($_POST['secret_word'])) {
            $settings->secret_word_settings_success = $_POST['secret_word'];
        }

        $errors = new WP_Error();

        if ( $settings->secret_word_settings_success == '') {
            $errors->add('secret-word-settings-success', __('<strong>ERROR</strong>: Please enter a secret word.'), 'ss-jvzoo-wpestore');
        }

        if ($errors->get_error_codes())
            return $errors;

        $messages = array();

        $Ss_Jvzoo_Wpestore_Config = Ss_Jvzoo_Wpestore_Config::getInstance();

        $Ss_Jvzoo_Wpestore_Config->setValue('secret_word', $settings->secret_word_settings_success );

        $Ss_Jvzoo_Wpestore_Config->saveConfig();

        $messages[] = __('Secret word added', 'ss-jvzoo-wpestore');

        return $messages;
    }
}

new Ss_Jvzoo_Wpestore_general_wpemember_secret_word_settings();