jQuery(document).ready(function($) {
    function toggleDiscountFields() {
        let selected = $('#pr_discount_type').val();

        $('.pr_discount_field_wrap').hide();

        if (selected === 'percentage') {
            $('.discount-percentage').fadeIn();
        } else if (selected === 'fixed_discount_per_item') {
            $('.fixed-discount-per-item').fadeIn();
        } else if (selected === 'fixed_discount_cart') {
            $('.fixed_discount_cart').fadeIn();
        }
    }

    toggleDiscountFields();

    $('#pr_discount_type').on('change', function () {
        toggleDiscountFields();
    });
});




