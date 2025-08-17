<?php
/*
Plugin Name: PR Bulk Discount
Plugin URI:
Description: A simple plugin that shows a welcome message to new visitors.
Version: 1.0
Author: Mehedi Hasan
Author URI: https://github.com/mehedimahid
License: GPL2
*/
use PR\PRBulkDiscount ;
define("PRPLUGINDIRURL", plugin_dir_url(__FILE__));
define("PLUGINDIRPATH", plugin_dir_path(__FILE__));
define( 'PR_DIR_PATH', plugin_dir_path( __FILE__ ) );
require_once PR_DIR_PATH . 'vendor/autoload.php';
new PRBulkDiscount();

