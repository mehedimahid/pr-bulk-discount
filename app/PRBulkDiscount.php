<?php

class PRBulkDiscount{
    public function __construct() {
    add_filter('woocommerce_product_data_tabs', [$this, 'add_product_data_tabs']);
    add_action('woocommerce_product_data_panels', [$this, 'add_product_data_tabs_panel']);
    add_action('save_post', [$this, 'save_pr_bulk_discount'],1,2);
    }

    public function save_pr_bulk_discount($post_id, $post){
        $product = wc_get_product($post_id);
        if ( ! is_object( $product ) ) {
            return;
        }
        if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
            return;
        }
        if ( empty( $_REQUEST['woocommerce_meta_nonce'] ) ) {
            return;
        }
        // Check the nonce.
        $nonce = sanitize_text_field( wp_unslash( $_REQUEST['woocommerce_meta_nonce'] ) );
        if ( ! wp_verify_nonce( $nonce, 'woocommerce_save_data' ) ) {
            return;
        }
        $bulk_discount = sanitize_text_field($_REQUEST['pr_discount_percentage']);
        update_post_meta($post_id, "pr_bulk_discount_key", $bulk_discount);
    }
    public function add_product_data_tabs_panel()
    {
        
        ?>
        <div id="pr_bulk_discount_options" class="panel woocommerce_options_panel">
            <div class="options_group">
                <span class="description"><?php _e('Select the type of discount.', 'Product-Badge'); ?></span>
                <p class="form-field">
                    <label for="pr_discount_type"><?php _e('Discount Type', 'Product-Badge'); ?></label>
                    <select id="pr_discount_type" name="pr_discount_type" class="select">
                        <option value="percentage">Percentage(%)</option>
                        <option value="fixed_discount_per_item">Fixed Discount - Per Item</option>
                        <option value="fixed_discount_cart">Fixed Amount Discount On Cart</option>
                    </select>
                </p>
                <p class="form-field pr_discount_field_wrap" id="pr_discount_percentage_wrap">
                    <label for="pr_discount_percentage"><?php _e('Enter Your Discount', 'Product-Badge'); ?></label>
                    <textarea id="pr_discount_percentage" name="pr_discount_percentage" class="textarea" placeholder="Example : 10:5|20:10(Quantity:Discount%)" ></textarea>
                </p>

                    <span>Set quantity-based discount rules using 'Quantity:Discount' pairs separated by | (e.g., 10:5 for 5% or $5 off above 10 items, based on the discount type).</span>

            </div>
        </div>
        <?php
    }

    public function add_product_data_tabs($tabs)
    {
        $tabs['pr_bulk_discount'] = [
            'label'=> __('PR Bulk Discount'),
            'target'   => 'pr_bulk_discount_options',
        ];
        return $tabs;
    }
}
new PRBulkDiscount();