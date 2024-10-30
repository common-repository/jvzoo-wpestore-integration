<?php

/**
 * Get all Product
 *
 * @param $args array
 *
 * @return array
 */
function ss_get_all_Product($args = array(), $search = null)
{
    global $wpdb;

    $defaults = array(
        'number' => 20,
        'offset' => 0,
        'orderby' => 'id',
        'order' => 'ASC',
    );

    $args = wp_parse_args($args, $defaults);
    $cache_key = 'Product-all';
    $items = wp_cache_get($cache_key, 'ss-jvzoo-wpestore');

    if (false === $items) {
        if ($search == null) {
            $items = $wpdb->get_results('SELECT * FROM ' . $wpdb->prefix . 'map_products ORDER BY ' . $args['orderby'] . ' ' . $args['order'] . ' LIMIT ' . $args['offset'] . ', ' . $args['number']);
        } else {
            $search = trim($search);
            $items = $wpdb->get_results('SELECT * FROM ' . $wpdb->prefix . 'map_products where `wpestore_product_id` LIKE "%' . $search . '%" OR `jvzoo_product_id` LIKE "%' . $search . '%" ORDER BY ' . $args['orderby'] . ' ' . $args['order'] . ' LIMIT ' . $args['offset'] . ', ' . $args['number']);
        }
        wp_cache_set($cache_key, $items, 'ss-jvzoo-wpestore');
    }

    return $items;
}

/**
 * Fetch all Product from database
 *
 * @return array
 */
function ss_get_Product_count()
{
    global $wpdb;

    return (int)$wpdb->get_var('SELECT COUNT(*) FROM ' . $wpdb->prefix . 'map_products');
}

/**
 * Fetch a single Product from database
 *
 * @param int $id
 *
 * @return array
 */
function ss_get_Product($id = 0)
{
    global $wpdb;

    return $wpdb->get_row($wpdb->prepare('SELECT * FROM ' . $wpdb->prefix . 'map_products WHERE id = %d', $id));
}


/**
 * Insert a new Product
 *
 * @param array $args
 */
function ss_insert_Product($args = array())
{
    global $wpdb;

    $defaults = array(
        'id' => null,
        'wpestore_product_id' => '',
        'jvzoo_product_id' => '',

    );

    $args = wp_parse_args($args, $defaults);

    $table_name = $wpdb->prefix . 'map_products';

    // some basic validation
    if (empty($args['wpestore_product_id'])) {
        return new WP_Error('no-wpestore-product-id', __('No WP estore product id provided.', 'ss-jvzoo-wpestore'));
    }
    if (empty($args['jvzoo_product_id'])) {
        return new WP_Error('no-jvzoo-product-id', __('No JVZoo product id provided.', 'ss-jvzoo-wpestore'));
    }

    // remove row id to determine if new or update
    $row_id = (int)$args['id'];
    unset($args['id']);

    if (!$row_id) {

        $args['date'] = current_time('mysql');

        // insert a new
        if ($wpdb->insert($table_name, $args)) {
            return $wpdb->insert_id;
        }

    } else {

        // do update method here
        if ($wpdb->update($table_name, $args, array('id' => $row_id))) {
            return $row_id;
        }
    }

    return false;
}
