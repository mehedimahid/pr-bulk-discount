<?php

class PRBulkDiscount{
    public function __construct() {
//        $this->condition_persing();
    add_filter('woocommerce_product_data_tabs', [$this, 'add_product_data_tabs']);
    add_action('woocommerce_product_data_panels', [$this, 'add_product_data_tabs_panel']);
    add_action('save_post', [$this, 'save_pr_bulk_discount'],1,2);
    //show frontend in shop page under product desc.
    add_action('woocommerce_single_product_summary',[$this, 'pr_single_product_summary'],9);
    //show frontend in shop page under product title
    add_action('woocommerce_after_shop_loop_item_title', [$this, 'pr_single_product_summary'],5);
    add_action( 'woocommerce_before_calculate_totals', [ $this, 'pr_show_subtotal' ] );
//    add_action( 'woocommerce_cart_totals_before_order_total', [ $this, 'pr_show_bulk_discount_in_cart' ] );

    }
    public function pr_show_bulk_discount_in_cart()
    {
        $subtotal = floatval( WC()->cart->get_subtotal() );
        $total    = floatval( WC()->cart->get_total( 'edit' ) ); // 'edit' দিলে formatted price ছাড়া raw amount দেয়
        $fees     = floatval( WC()->cart->get_fee_total() );

        $discount_total = $subtotal - $total + $fees;

        if ( $discount_total > 0 ) {
            ?>
            <tr class="cart-discount bulk-discount">
                <th><?php _e( 'Bulk Discount', 'woocommerce' ); ?></th>
                <td data-title="<?php esc_attr_e( 'Bulk Discount', 'woocommerce' ); ?>">
                    -<?php echo wc_price( $discount_total ); ?>
                </td>
            </tr>
            <?php
        }
    }

    public function pr_show_subtotal($cart)
    {
        if ( is_admin() && ! defined( 'DOING_AJAX' ) ) {
        return;
        }
        if ( did_action( 'woocommerce_before_calculate_totals' ) >= 2 ) {
            return;
        }
        $total_discount = 0 ;
        foreach ( $cart->get_cart() as $cart_item ) {
            $product = $cart_item['data'];
            $is_enable = $this->cart_enable($product->get_id());
            if($is_enable) {
                $discount_amount =  $this->calculate_discount($product->get_id(), $cart_item);
                $cart_item['data']->set_price(floatval($discount_amount));
//                $total_discount += $discount_amount;
//                $cart_item['data']->add_fee( __( 'Bulk Discount', 'woocommerce' ), -$discount_total );
            }
        }
        if ($total_discount > 0) {
            $cart->add_fee( __( 'Bulk Discount', 'woocommerce' ), -$total_discount );
        }
    }
    public function calculate_discount($post_id, $cart_item){
        $quantity = $cart_item['quantity'];
        $product = $cart_item['data'];
        $price = $product->get_price();
        $discounts = $this->condition_persing($post_id);
        krsort($discounts);
        $discounts_value = 0;
        foreach ($discounts as $key => $value) {
            if($quantity >= $key) {
                $discounts_value  = $value;
                break;
            }
        }
        $price = $price - $discounts_value; //origin price থেকে discount বাদ
//        $price = $quantity*$discounts_value;//total discount value
        return $price;
    }
    public function cart_enable($post_id){

        $discounts = $this->condition_persing($post_id);
         return !empty($discounts);
    }
    public function pr_single_product_summary()
    {
        global $product;
        if (!$product instanceof WC_Product) {
            return ;
        }
        $post_id = $product->get_id();
        $discounts = $this->condition_persing($post_id);

        if (!empty($discounts)) {
            echo '<div class="pr_single_product_summary">';
            echo "<span>Bulk Discount Available</span>";
            foreach ($discounts as $qty => $val) {
                echo '<div class="pr_single_product_summary_item">';
                echo "Buy {$qty}+ items — Get {$val} off<br/>";
                echo "</div>";
            }
            echo "</div>";
        }
    }
    //
    public function condition_persing($post_id)
    {
        $bulk_discount_value = get_post_meta($post_id,'pr_bulk_discount_key',true);
        $separate_conditions  = explode('|',$bulk_discount_value);
        if(empty($separate_conditions)){
            return [];
        }
        $discount = [];
        foreach($separate_conditions as $condition){
            if(empty($condition)){
                continue;
            }
            list($quantity , $value) = array_pad(explode(':',$condition,2), 2, 0);
            if(!$value){
                continue;
            }
            $discount[intval($quantity)] = floatval($value);
        }
//        error_log(print_r($discount, true) . "\n\n", 3, __DIR__ . '/log.txt');

        return $discount;

    }
    //
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
        $bulk_discount_value = sanitize_text_field($_REQUEST['pr_discount_percentage']);
        $bulk_discount_type = sanitize_text_field($_REQUEST['pr_discount_type']);

        update_post_meta($post_id, "pr_bulk_discount_key", $bulk_discount_value);
        update_post_meta($post_id, "pr_bulk_discount_type_key", $bulk_discount_type);
    }
    public function add_product_data_tabs_panel()
    {
        $post_id = get_the_ID();
        $bulk_discount_type = get_post_meta($post_id, "pr_bulk_discount_type_key", true);
        $bulk_discount_value = get_post_meta($post_id, "pr_bulk_discount_key", true);
//        $parsed_discounts = $this->condition_persing($post_id);

        ?>
        <div id="pr_bulk_discount_options" class="panel woocommerce_options_panel">
            <div class="options_group">
                <span class="description"><?php _e('Select the type of discount.', 'Product-Badge'); ?></span>
                <p class="form-field">
                    <label for="pr_discount_type"><?php _e('Discount Type', 'Product-Badge'); ?></label>
                    <select id="pr_discount_type" name="pr_discount_type" class="select">
                        <option value="percentage" <?php selected($bulk_discount_type, "percentage") ?>>Percentage(%)</option>
                        <option value="fixed_discount_per_item"<?php selected($bulk_discount_type, "fixed_discount_per_item") ?>>Fixed Discount - Per Item</option>
                        <option value="fixed_discount_cart"<?php selected($bulk_discount_type, "fixed_discount_cart") ?>>Fixed Amount Discount On Cart</option>
                    </select>
                </p>
                <p class="form-field pr_discount_field_wrap" id="pr_discount_percentage_wrap">
                    <label for="pr_discount_percentage"><?php _e('Enter Your Discount', 'Product-Badge'); ?></label>
                    <textarea id="pr_discount_percentage" name="pr_discount_percentage" class="textarea" placeholder="Example : 10:5|20:10(Quantity:Discount%)"><?php echo esc_textarea($bulk_discount_value); ?></textarea>
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


