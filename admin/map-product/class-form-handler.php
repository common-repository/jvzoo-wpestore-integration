<?php

/**
 * Handle the form submissions
 *
 * @package Package
 * @subpackage Sub Package
 */
class Form_Handler
{

    /**
     * Hook 'em all
     */
    public function __construct()
    {
        add_action('admin_init', array($this, 'handle_form'));
    }

    /**
     * Handle the Product new and edit form
     *
     * @return void
     */
    public function handle_form()
    {
        $message = '';

        if (!isset($_REQUEST['productmapaction'])) {
            return;
        }

        if($_REQUEST['productmapaction'] !=  'productmap'){
            return;
        }

        check_admin_referer('ss-product-map', '_wpnonce_product-map');

        if (!current_user_can('read')) {
            wp_die(__('Permission Denied!', 'ss-jvzoo-wpestore'));
        }

        $errors = array();

        $page_url = admin_url('admin.php?page=map-product');

        if ($_REQUEST['action'] == 'new') {
            $page_url_new = admin_url('admin.php?page=map-product&action=new');
        } else if ($_REQUEST['action'] == 'edit') {
            $page_url_new = admin_url('admin.php?page=map-product&action=edit&id=' . $_REQUEST['id']);
        }

        $field_id = isset($_POST['field_id']) ? intval($_POST['field_id']) : 0;

        $wpestore_product_id = isset($_POST['wpestore-product-id']) ? intval($_POST['wpestore-product-id']) : 0;
        $jvzoo_product_id = isset($_POST['jvzoo-product-id']) ? intval($_POST['jvzoo-product-id']) : 0;

        // some basic validation
        if (!$wpestore_product_id) {
            $errors[] = 'WPEstore_id_missing';
        }

        if (!$jvzoo_product_id) {
            $errors[] = 'JVZoo_id_missing';
        }

        // bail out if error found
        if ($errors) {
            $redirect_to = add_query_arg(array('error' => $errors), $page_url_new);
            wp_safe_redirect($redirect_to);
            exit;
        }

        $fields = array(
            'wpestore_product_id' => $wpestore_product_id,
            'jvzoo_product_id' => $jvzoo_product_id,
        );

        // New or edit?

        if (!$field_id) {
            $message = 'add';
            $insert_id = ss_insert_Product($fields);

            if (is_wp_error($insert_id)) {
                $errors[] = 'Could_not_save_into_the_database';
                $redirect_to = add_query_arg(array('error' => $errors), $page_url_new);
            } else {
                $redirect_to = add_query_arg(array('message' => $message), $page_url);
            }

        } else {
            $message = 'edit';
            $fields['id'] = $field_id;

            $insert_id = ss_insert_Product($fields);
            if (is_wp_error($insert_id)) {
                $errors[] = 'Could_not_save_into_the_database';
                $redirect_to = add_query_arg(array('error' => $errors), $page_url_new);
            } else {
                $redirect_to = add_query_arg(array('message' => $message), $page_url_new);
            }
        }

        wp_safe_redirect($redirect_to);
        exit;
    }
}

new Form_Handler();
