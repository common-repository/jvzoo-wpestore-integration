<?php
if (isset($_REQUEST['action']) && 'addapi' == $_REQUEST['action']) {
    check_admin_referer('ss-jvzoo-add-api', '_wpnonce_add-api');

    $api = add_api();
    if (is_wp_error($api)) {
        $add_api_errors = $api;
    } else {
        $add_api_success = $api;
    }

    if (isset($add_api_errors) && is_wp_error($add_api_errors)) : ?>
        <div class="error">
            <?php
            foreach ($add_api_errors->get_error_messages() as $message)
                echo "<p>$message</p>";
            ?>
        </div>
    <?php endif;

    if (isset($add_api_success)) :
        foreach ($add_api_success as $msg) {
            echo '<div id="message" class="updated notice is-dismissible"><p>' . $msg . '</p></div>';
        }
    endif;
}

/**
 * Add JVZoo api
 * @return array|WP_Error
 */
function add_api()
{
    $api = new stdClass;
    if (isset($_POST['jvzoo-api-key'])) {
        $api->api_key = sanitize_text_field($_POST['jvzoo-api-key']);
    }
    $errors = new WP_Error();

    if ($api->api_key == '') {
        $errors->add('api_key', __('<strong>ERROR</strong>: Please enter a JVZipn secret key.'), 'ss-jvzoo-wpestore');
    }

    if ($errors->get_error_codes())
        return $errors;

    $option_name = 'ss_jvzoo_estore';
    $messages = array();
    if (get_option($option_name) !== false) {

        // The option already exists, so we just update it.
        update_option($option_name, $api->api_key);
        $messages[] = __('Api Updated', 'ss-jvzoo-wpestore');

    } else {

        add_option($option_name, $api->api_key);
        $messages[] = __('Api Added', 'ss-jvzoo-wpestore');
    }

    return $messages;

}

?>
<div class="wrap">
    <h1><?php echo __('JVZipn Secret Key', 'ss-jvzoo-wpestore'); ?></h1>
    <!--    <p>-->
    <!--        --><?php //echo __('You can get your API key by going to JVZoo -> My Account -> Applications' , 'ss-jvzoo-wpestore'); ?>
    <!--    </p>-->
    <form method="post" name="ss-add-api-frm" id="ss-add-api-frm" class="validate" novalidate="novalidate">
        <input name="action" type="hidden" value="addapi">
        <?php wp_nonce_field('ss-jvzoo-add-api', '_wpnonce_add-api'); ?>

        <table class="form-table">
            <tbody>
            <tr class="form-field form-required">
                <th scope="row">
                    <label for="jvzoo-api-key"><?php echo __('Secret key', 'ss-jvzoo-wpestore'); ?> <span
                            class="description"><?php echo __('(required)', 'ss-jvzoo-wpestore'); ?></span></label>
                </th>
                <td class="ss-td-width">
                    <input name="jvzoo-api-key" type="text" id="jvzoo-api-key"
                           value="<?php echo get_option('ss_jvzoo_estore'); ?>" aria-required="true"
                           autocapitalize="none" autocorrect="off" maxlength="60">
                </td>
                <td class="ss-info"
                    title= "1) Log into your JVZoo account  2) Click the My Account tab 3) Go to vendor options and find the JVZIPN Secret Key">
                </td>
            </tr>
            </tbody>
        </table>

        <p class="submit">
            <input type="submit" name="ss-add-api-sub" id="ss-add-api-sub" class="button button-primary"
                   value="<?php echo __('Save secret key', 'ss-jvzoo-wpestore') ?>">
        </p>
    </form>
</div>
