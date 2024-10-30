<?php

class Ss_Jvzoo_Wpestore_general_membership_level_settings
{
    public function __construct()
    {
        $this->membership_level();
    }

    public function membership_level()
    {
        if (isset($_REQUEST['action']) && 'membershiplevel' == $_REQUEST['action']) {
            check_admin_referer('ss-membership-level', '_wpnonce_membership-level');

            $settings = $this->add_membership_level_settings();

            if (is_wp_error($settings)) {
                $membership_level_errors = $settings;
            } else {
                $membership_level_success = $settings;
            }

            if (isset($membership_level_errors) && is_wp_error($membership_level_errors)) : ?>
                <div class="error">
                    <?php
                    foreach ($membership_level_errors->get_error_messages() as $message)
                        echo "<p>$message</p>";
                    ?>
                </div>
            <?php endif;

            if (isset($membership_level_success)) :
                foreach ($membership_level_success as $msg) {
                    echo '<div id="message" class="updated notice is-dismissible"><p>' . $msg . '</p></div>';
                }
            endif;
        }
        $Ss_Jvzoo_Wpestore_Config = Ss_Jvzoo_Wpestore_Config::getInstance();
        $membership_level = $Ss_Jvzoo_Wpestore_Config->getValue('membership_level');


        ?>
        <div id="poststuff">
            <div id="post-body" class="metabox-holder">
                <div class="postbox-container">
                    <div id="advanced-sortables" class="meta-box-sortables ui-sortable">
                        <div class="postbox">

                            <h2 class="hndle">
                                <span><?php echo __('WP eMember Membership level', 'ss-jvzoo-wpestore'); ?></span>
                            </h2>

                            <div class="inside">

                                <form method="post" name="membershiplevel" id="membershiplevel">
                                    <?php wp_nonce_field('ss-membership-level', '_wpnonce_membership-level'); ?>
                                    <h4><?php echo __('You can find this Membership Level in DASHBOARD-> WP eMember -> Membership Level', 'ss-jvzoo-wpestore'); ?></h4>
                                    <input name="action" type="hidden" value="membershiplevel">

                                    <p>
                                        <select name="emember_level">
                                            <option
                                                value="please_select"><?php echo __('Select Level', 'ss-jvzoo-wpestore'); ?>
                                            </option>
                                            <?php
                                            if (class_exists(dbAccess)) {
                                                $all_levels = dbAccess::findAll(WP_EMEMBER_MEMBERSHIP_LEVEL_TABLE, ' id != 1 ', ' id DESC ');
                                                foreach ($all_levels as $level) {
                                                    ?>
                                                    <option
                                                        value="<?php echo $level->id ?>" <?php echo ($membership_level == $level->id) ? "selected='selected'" : ""; ?>><?php echo $level->alias; ?></option>
                                                <?php }
                                            } ?>
                                        </select>
                                    </p>

                                    <p>
                                        <input type="submit" name="membershiplevel" id="membershiplevelsub"
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
     * Add Membership level
     * @return array|WP_Error
     */
    function add_membership_level_settings()
    {
        $settings = new stdClass;
        if (isset($_POST['emember_level'])) {
            $settings->membership_level = $_POST['emember_level'];
        }

        $errors = new WP_Error();

        if ($settings->membership_level == 'please_select') {
            $errors->add('membership_level_error_message', __('<strong>ERROR</strong>: Please select membership '), 'ss-jvzoo-wpestore');
        }

        if ($errors->get_error_codes())
            return $errors;

        $messages = array();

        $Ss_Jvzoo_Wpestore_Config = Ss_Jvzoo_Wpestore_Config::getInstance();

        $Ss_Jvzoo_Wpestore_Config->setValue('membership_level', $settings->membership_level);

        $Ss_Jvzoo_Wpestore_Config->saveConfig();

        $messages[] = __('Membership level saved', 'ss-jvzoo-wpestore');

        return $messages;
    }
}

new Ss_Jvzoo_Wpestore_general_membership_level_settings();