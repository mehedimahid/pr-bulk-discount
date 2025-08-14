<?php

class PRoptionSetting
{
    public function __construct(){
        add_action('admin_menu', [$this, 'pr_custom_admin_menu']);

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
        ?>
        <div class="wrap">
            <h1>Bulk Discount Settings</h1>
            <form method="post" action="<?php echo admin_url('admin.php?page=pr-bulk-discount'); ?>">
                <div class="pr-discount-setting">
                    <label><strong>Choose Discount Type</strong></label>
                    <p>
                        <label for="pr-discount-type-1">Discount add on Fees</label>
                        <input type="radio" class="pr-discount-type-1" name="pr-discount-type-1" value="option1">
                    </p>
                    <p>
                        <label for="pr-discount-type-2">Discount add on Price</label>
                        <input type="radio" class="pr-discount-type-2" name="pr-discount-type-2" value="option2">
                    </p>
                </div>
                <?php submit_button(); ?>
            </form>
        </div>
        <?php
    }
}
new PRoptionSetting();