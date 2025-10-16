<?php
/**
 * Plugin Name: ToolZoo
 * Plugin URI: https://github.com/yourusername/toolzoo
 * Description: A WordPress plugin that provides useful tools including password generation, Japanese era list display, and more.
 * Version: 1.0.0
 * Author: Your Name
 * Author URI: https://yourwebsite.com
 * License: GPL v2 or later
 * License URI: https://www.gnu.org/licenses/gpl-2.0.html
 * Text Domain: toolzoo
 * Domain Path: /languages
 */

// Security: Prevent direct access
if (!defined('ABSPATH')) {
    exit;
}

// Plugin constants definition
define('TOOLZOO_VERSION', '1.0.0');
define('TOOLZOO_PLUGIN_DIR', plugin_dir_path(__FILE__));
define('TOOLZOO_PLUGIN_URL', plugin_dir_url(__FILE__));
define('TOOLZOO_PLUGIN_BASENAME', plugin_basename(__FILE__));

/**
 * Load main class
 */
require_once TOOLZOO_PLUGIN_DIR . 'includes/class-toolzoo.php';

/**
 * Initialize plugin
 */
function toolzoo_init() {
    $toolzoo = new Toolzoo();
    $toolzoo->init();
}
add_action('plugins_loaded', 'toolzoo_init');

/**
 * Plugin activation hook
 */
function toolzoo_activate() {
    // Placeholder for future processing
    flush_rewrite_rules();
}
register_activation_hook(__FILE__, 'toolzoo_activate');

/**
 * Plugin deactivation hook
 */
function toolzoo_deactivate() {
    // Placeholder for future processing
    flush_rewrite_rules();
}
register_deactivation_hook(__FILE__, 'toolzoo_deactivate');
