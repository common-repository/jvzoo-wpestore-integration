<?php

$messages = array();
if (isset($_GET['message'])) {
    switch ($_GET['message']) {
        case 'edit':
            $messages[] = '<div id="message" class="updated notice is-dismissible"><p>' . __(' Product Map Edited.', 'ss-jvzoo-wpestore') . '</p></div>';
            break;
    }
}

if (!empty($messages)) {
    foreach ($messages as $msg)
        echo $msg;
}

if (isset($_REQUEST['error'])) {
    $errors = new WP_Error();
    foreach ($_REQUEST['error'] as $key => $value) {
        if ($value == 'WPEstore_id_missing') {
            $errors->add('wpestore_product_id', __('<strong>ERROR</strong>: WP estore product id is required.', 'ss-jvzoo-wpestore'));
        }
        if (isset($value) && $value == 'JVZoo_id_missing') {
            $errors->add('jvzoo_product_id', __('<strong>ERROR</strong>: JVZoo product id is required.', 'ss-jvzoo-wpestore'));
        }
        if (isset($value) && $value == 'Could_not_save_into_the_database') {
            $errors->add('Could_not_save_into_the_database', __('<strong>ERROR</strong>: Could not store the product map into the database', 'ss-jvzoo-wpestore'));
        }
    }
}
?>
<?php if (isset($errors) && is_wp_error($errors)) : ?>
    <div class="error">
        <?php
        foreach ($errors->get_error_messages() as $message)
            echo "<p>$message</p>";
        ?>
    </div>
<?php endif; ?>

<div class="wrap">
    <h1><?php _e('Edit Product', 'ss-jvzoo-wpestore'); ?></h1>
    <?php $item = ss_get_Product($id); ?>

    <form action="" method="post">
        <input name="productmapaction" type="hidden" value="productmap">
        <table class="form-table">
            <tbody>
            <tr class="row-wpestore-product-id">
                <th scope="row">
                    <label for="wpestore-product-id"><?php _e('WP estore product id', 'ss-jvzoo-wpestore'); ?></label>
                </th>
                <td>
                    <input type="number" name="wpestore-product-id" id="wpestore-product-id" class="regular-text"
                           placeholder="<?php echo esc_attr('', 'ss-jvzoo-wpestore'); ?>"
                           value="<?php echo esc_attr($item->wpestore_product_id); ?>"/>
                </td>
            </tr>
            <tr class="row-jvzoo-product-id">
                <th scope="row">
                    <label for="jvzoo-product-id"><?php _e('JVZoo product id', 'ss-jvzoo-wpestore'); ?></label>
                </th>
                <td>
                    <input type="number" name="jvzoo-product-id" id="jvzoo-product-id" class="regular-text"
                           placeholder="<?php echo esc_attr('', 'ss-jvzoo-wpestore'); ?>"
                           value="<?php echo esc_attr($item->jvzoo_product_id); ?>"/>
                </td>
            </tr>
            </tbody>
        </table>

        <input type="hidden" name="field_id" value="<?php echo $item->id; ?>">

        <?php wp_nonce_field('ss-product-map', '_wpnonce_product-map'); ?>
        <?php submit_button(__('Update', 'ss-jvzoo-wpestore'), 'primary', 'Submit'); ?>

    </form>
</div>