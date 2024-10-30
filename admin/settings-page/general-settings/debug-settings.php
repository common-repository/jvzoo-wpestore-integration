<?php


class Ss_Jvzoo_Wpestore_general_debug_settings
{
    public function __construct()
    {
        $this->debug_settings();
    }

    /**
     * Debug settings function
     */
    public function debug_settings()
    {
        if (isset($_REQUEST['action']) && 'enabledebug' == $_REQUEST['action']) {
            check_admin_referer('ss-enable-debug', '_wpnonce_enable-debug');
            if (isset($_REQUEST['enabledebug'])) {
                $debug = $this->enable_debug();
            }
            if (isset($_REQUEST['reset_debug_file'])) {
                $reset = $this->reset_debug();
            }
        }
        ?>
        <div id="poststuff">
            <div id="post-body" class="metabox-holder">
                <div class="postbox-container">
                    <div id="advanced-sortables" class="meta-box-sortables ui-sortable">
                        <div class="postbox">

                            <h2 class="hndle">
                                <span><?php echo __('Testing and Debugging Settings', 'ss-jvzoo-wpestore'); ?></span>
                            </h2>
                            <div class="inside">
                                <form method="post" name="enabledebug" id="enabledebug">
                                    <?php wp_nonce_field('ss-enable-debug', '_wpnonce_enable-debug'); ?>
                                    <h4><?php echo __('You do not need to use these options unless you are testing the plugin or trying to
                                troubleshoot and issue.', 'ss-jvzoo-wpestore'); ?></h4>
                                    <input name="action" type="hidden" value="enabledebug">
                                    <p>
                                        <input type="checkbox" name="enable_debug_checkbox"
                                               value="1"
                                            <?php checked('1', get_option('ss_jvzoo_estore_enable_debug')); ?> > <?php echo __('enable Debug', 'ss-jvzoo-wpestore'); ?>
                                        <br>
                                    </p>

                                    <p>
                                        <label><?php echo 'Log Files :' ?></label>
                                    <ul>
                                        <li>
                                            <a href="<?php echo SS_JVZOO_WPESTORE_WPDATE_URL . SS_JVZOO_IPN_HANDLE_LOG_FILE ?>"
                                               target="_blank"><?php echo SS_JVZOO_IPN_HANDLE_LOG_FILE; ?></a></li>
                                    </ul>
                                    </p>

                                    <p>
                                        <input type="submit"
                                               onclick="return confirm('Are you sure you want to reset debug file?');"
                                               id="reset_debug_file" name="reset_debug_file"
                                               class="ss_delete_button ss-delete_button action"
                                               value="<?php echo __('Reset debug file', 'ss-jvzoo-wpestore'); ?>">
                                    </p>

                                    <p>
                                        <input type="submit" name="enabledebug" id="enabledebugsub"
                                               class="button-secondary edd_add_repeatable" value="<?php echo __('Submit','ss-jvzoo-wpestore'); ?>">
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
     * Add debug to options
     */
    public function enable_debug()
    {
        $debug = new stdClass;
        if (isset($_POST['enable_debug_checkbox'])) {
            $debug->enable_debug = '1';
        } else {
            $debug->enable_debug = '';
        }
        $option_name = 'ss_jvzoo_estore_enable_debug';
        $messages = array();
        if (get_option($option_name) !== false) {
            // The option already exists, so we just update it.
            update_option($option_name, $debug->enable_debug);
            $messages[] = __('Debug enable', 'ss-jvzoo-wpestore');
        } else {
            add_option($option_name, $debug->enable_debug);
            $messages[] = __('Debug disabled', 'ss-jvzoo-wpestore');
        }
        return $messages;
    }

    /**
     * Reset debug files
     */
    public function reset_debug()
    {
        $retVal = '[' . date('m/d/Y g:i A') . '] ';
// Write timestamp...
        $fp = @fopen(SS_JVZOO_WPESTORE_ABSPATH . SS_JVZOO_IPN_HANDLE_LOG_FILE, 'w');
        if ($fp != FALSE) {
            @fwrite($fp, $retVal);
            @fclose($fp);
        } else {
            $retVal = false;
        }
        return $retVal;
    }
}

new Ss_Jvzoo_Wpestore_general_debug_settings();