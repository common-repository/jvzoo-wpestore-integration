<?php
$product = ss_get_Product($id);
?>
<div id="poststuff">
    <div id="post-body" class="metabox-holder">
        <div class="postbox-container">
            <div id="advanced-sortables" class="meta-box-sortables ui-sortable">
                <div class="postbox">

                    <h2 class="hndle">
                        <span><?php echo __('Map Product', 'ss-jvzoo-wpestore'); ?></span>
                    </h2>

                    <div class="inside">
                        <p>
                            <label><?php echo __('WPESTORE Product ID', 'ss-jvzoo-wpestore'); ?>
                                : <?php echo $product->wpestore_product_id; ?></label>
                        </p>

                        <p>
                            <label><?php echo __('JVZOO Product ID', 'ss-jvzoo-wpestore'); ?>
                                : <?php echo $product->jvzoo_product_id; ?></label>
                        </p>
                    </div>
                </div>

            </div>
        </div>
    </div>
    <br class="clear">
</div>


