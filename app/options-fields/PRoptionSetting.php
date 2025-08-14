<?php
class PRoptionSetting
{
    public function __construct(){
        add_action('admin_menu', [$this, 'pr_custom_admin_menu']);
        add_action('admin_init', [$this, 'pr_save_custom_setting']);
    }
    public function pr_save_custom_setting()
    {
        if(isset($_POST['pr_discount_type'])){
            update_option('pr_discount_type', sanitize_text_field($_POST['pr_discount_type']));
            echo '<div class="updated"><p>Settings saved successfully!</p></div>';
        }
    }
    public function pr_custom_admin_menu(){
        add_menu_page(
            'PR Bulk Discount Setting',
            "PR Bulk Discount",
            'manage_options',
            'pr-bulk-discount',
            [$this ,"pr_custom_setting_page"],
            'dashicons-admin-generic',
            55
        );
    }
    public function pr_custom_setting_page(){
        $value = get_option('pr_discount_type');
        ?>
        <div class="wrap">
            <h1>Bulk Discount Settings</h1>
            <form method="post" action="<?php echo admin_url('admin.php?page=pr-bulk-discount'); ?>">
                <label for="pr-discount-setting"><strong>Choose Discount Type</strong></label>
                <div class="pr-discount-setting">
                    <p>
                        <label for="pr-discount-type-1">Discount add on Fees</label>
                            <input type="radio" id="pr-discount-type-1" name="pr_discount_type" value="option1" <?php checked($value,'option1')?>>

                    </p>
                    <p>
                        <label for="pr-discount-type-2">Discount add on Price</label>
                        <input type="radio" class="pr-discount-type-2" name="pr_discount_type" value="option2"<?php checked($value,'option2')?>>
                    </p>
                </div>
                <?php submit_button(); ?>
            </form>
        </div>
        <?php
    }
}
new PRoptionSetting();