<?php
$messages = array();
if (isset($_GET['message'])) {
    switch ($_GET['message']) {
        case 'add':
            $messages[] = '<div id="message" class="updated notice is-dismissible"><p>' . __('New Product map created.', 'ss-jvzoo-wpestore') . '</p></div>';
            break;
    }
}

if (!empty($messages)) {
    foreach ($messages as $msg)
        echo $msg;
}
?>
<div class="wrap">
    <h2><?php _e('Map Product', 'ss-jvzoo-wpestore'); ?> <a href="<?php echo admin_url('admin.php?page=map-product&action=new'); ?>"
                                             class="add-new-h2"><?php _e('Add New', 'ss-jvzoo-wpestore'); ?></a></h2>
    <form method="post">
        <input type="hidden" name="page" value="test_list_table">

        <?php
        $list_table = new ss_map_product_table();
        if( isset($_POST['s']) ){
            $list_table->prepare_items($_POST['s']);
        } else {
            $list_table->prepare_items();
        }
        $list_table->search_box('search', 'search_id');
        $list_table->display();
        echo '</br>';
        ?>

    </form>
</div>
