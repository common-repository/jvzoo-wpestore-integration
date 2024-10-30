<?php

if (!class_exists('WP_List_Table')) {
    require_once ABSPATH . 'wp-admin/includes/class-wp-list-table.php';
}

/**
 * List table class
 */
class ss_map_product_table extends \WP_List_Table
{

    function __construct()
    {
        parent::__construct(array(
            'singular' => 'Product',
            'plural' => 'Products',
            'ajax' => false
        ));
    }

    function get_table_classes()
    {
        return array('widefat', 'fixed', 'striped', $this->_args['plural']);
    }

    /**
     * Message to show if no designation found
     *
     * @return void
     */
    function no_items()
    {
        _e('No Products found', 'ss-jvzoo-wpestore');
    }

    /**
     * Default column values if no callback found
     *
     * @param  object $item
     * @param  string $column_name
     *
     * @return string
     */
    function column_default($item, $column_name)
    {

        switch ($column_name) {
            case 'wpestore_product_id':
                return $item->wpestore_product_id;

            case 'jvzoo_product_id':
                return $item->jvzoo_product_id;

            default:
                return isset($item->$column_name) ? $item->$column_name : '';
        }
    }

    /**
     * Get the column names
     *
     * @return array
     */
    function get_columns()
    {
        $columns = array(
            'cb' => '<input type="checkbox" />',
            'wpestore_product_id' => __('WPEstore Product ID', 'ss-jvzoo-wpestore'),
            'jvzoo_product_id' => __('JVZoo Product ID', 'ss-jvzoo-wpestore'),
        );

        return $columns;
    }

    /**
     * Render the designation name column
     *
     * @param  object $item
     *
     * @return string
     */
    function column_wpestore_product_id($item)
    {

        $actions = array();
        $actions['edit'] = sprintf('<a href="%s" data-id="%d" title="%s">%s</a>', admin_url('admin.php?page=map-product&action=edit&id=' . $item->id), $item->id, __('Edit this item', 'ss-jvzoo-wpestore'), __('Edit', 'ss-jvzoo-wpestore'));
        $actions['delete'] = sprintf('<a href="%s" class="submitdelete" data-id="%d" title="%s">%s</a>', admin_url('admin.php?page=map-product&action=trash&Product_id=' . $item->id), $item->id, __('Delete this item', 'ss-jvzoo-wpestore'), __('Delete', 'ss-jvzoo-wpestore'));

        return sprintf('<a href="%1$s"><strong>%2$s</strong></a> %3$s', admin_url('admin.php?page=map-product&action=view&id=' . $item->id), $item->wpestore_product_id, $this->row_actions($actions));
    }

    /**
     * Get sortable columns
     *
     * @return array
     */
    function get_sortable_columns()
    {
        $sortable_columns = array(
            'wpestore_product_id' => array('wpestore_product_id', true),
            'jvzoo_product_id' => array('jvzoo_product_id', true),

        );

        return $sortable_columns;
    }

    /**
     * Set the bulk actions
     *
     * @return array
     */
    function get_bulk_actions()
    {
        $actions = array(
            'trash' => __('Move to Trash', 'ss-jvzoo-wpestore'),
        );

        return $actions;
    }

    /** ************************************************************************
     * Optional. You can handle your bulk actions anywhere or anyhow you prefer.
     * For this example package, we will handle it in the class to keep things
     * clean and organized.
     *
     * @see $this->prepare_items()
     **************************************************************************/

    function process_bulk_action()
    {

        global $wpdb; //This is used only if making any database queries

        $table_name = $wpdb->prefix . "map_products";
        
        if ('trash' === $this->current_action()) {

            $ids = isset($_REQUEST['Product_id']) ? $_REQUEST['Product_id'] : array();
            if (is_array($ids)) $ids = implode(',', $ids);

            if (!empty($ids)) {
                if ($wpdb->query("DELETE FROM $table_name WHERE id IN($ids)")) {
                    echo '<div id="message" class="updated notice is-dismissible"><p>' . __('Selected record deleted successfully!', 'ss-jvzoo-wpestore') . '</p></div>';
                }
            }
        }
    }

    /**
     * Render the checkbox column
     *
     * @param  object $item
     *
     * @return string
     */
    function column_cb($item)
    {
        return sprintf(
            ' <input type = "checkbox" name = "Product_id[]" value = "%d" />', $item->id
        );
    }

    /**
     * Set the views
     *
     * @return array
     */
    public function get_views_()
    {
        $status_links = array();
        $base_link = admin_url('admin . php ? page = sample - page');

        foreach ($this->counts as $key => $value) {
            $class = ($key == $this->page_status) ? 'current' : 'status - ' . $key;
            $status_links[$key] = sprintf(' < a href = "%s" class="%s" >%s < span class="count" > (%s)</span ></a > ', add_query_arg(array('status' => $key), $base_link), $class, $value['label'], $value['count']);
        }

        return $status_links;
    }

    /**
     * Prepare the class items
     *
     * @param null $search
     */
    function prepare_items($search = null)
    {

        $columns = $this->get_columns();
        $hidden = array();
        $sortable = $this->get_sortable_columns();

        $this->_column_headers = array($columns, $hidden, $sortable);
        $this->process_bulk_action();

        $per_page = 10;
        $current_page = $this->get_pagenum();
        $offset = ($current_page - 1) * $per_page;
        $this->page_status = isset($_GET['status']) ? sanitize_text_field($_GET['status']) : '2';

        // only ncessary because we have sample data
        $args = array(
            'offset' => $offset,
            'number' => $per_page,
        );

        if (isset($_REQUEST['orderby']) && isset($_REQUEST['order'])) {
            $args['orderby'] = $_REQUEST['orderby'];
            $args['order'] = $_REQUEST['order'];
        }

        $this->items = ss_get_all_Product($args, $search);

        $this->set_pagination_args(array(
            'total_items' => ss_get_Product_count(),
            'per_page' => $per_page
        ));
    }

}
