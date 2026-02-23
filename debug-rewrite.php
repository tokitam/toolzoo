<?php
/**
 * Debug script to check if rewrite rules are registered
 *
 * Usage: Call via HTTP: /wp-content/plugins/toolzoo/debug-rewrite.php
 * Or include this in the WordPress admin to debug rewrite rules
 */

// Load WordPress
require_once(dirname(dirname(dirname(dirname(__FILE__)))) . '/wp-load.php');

echo "<h1>ToolZoo Rewrite Rules Debug</h1>";

// Check if we have $wp_rewrite global
global $wp_rewrite;

echo "<h2>Current Rewrite Rules:</h2>";
echo "<pre>";
print_r($wp_rewrite->rules);
echo "</pre>";

echo "<h2>Checking for toolzoo rules:</h2>";
$found_toolzoo = false;
foreach ($wp_rewrite->rules as $pattern => $rewrite) {
    if (strpos($pattern, 'toolzoo') !== false || strpos($rewrite, 'toolzoo_tool') !== false) {
        echo "Found ToolZoo rule: <br>";
        echo "Pattern: " . esc_html($pattern) . "<br>";
        echo "Rewrite: " . esc_html($rewrite) . "<br><br>";
        $found_toolzoo = true;
    }
}

if (!$found_toolzoo) {
    echo "<strong style='color: red;'>No ToolZoo rewrite rules found!</strong><br>";
    echo "You may need to:<br>";
    echo "1. Deactivate and reactivate the ToolZoo plugin<br>";
    echo "2. Visit Settings > Permalinks and save (to flush rewrite rules)<br>";
}

echo "<h2>Query Variables Registered:</h2>";
echo "<pre>";
print_r($wp_rewrite->queryreplace);
echo "</pre>";

echo "<h2>Plugin Status:</h2>";
$plugin_file = WP_PLUGIN_DIR . '/toolzoo/toolzoo.php';
if (file_exists($plugin_file)) {
    echo "Plugin file exists: YES<br>";
    $active_plugins = get_option('active_plugins', array());
    if (in_array('toolzoo/toolzoo.php', $active_plugins)) {
        echo "Plugin is active: YES<br>";
    } else {
        echo "Plugin is active: NO<br>";
    }
} else {
    echo "Plugin file exists: NO<br>";
}

echo "<h2>ToolZoo Plugin Directory:</h2>";
echo "Directory: " . esc_html(WP_PLUGIN_DIR . '/toolzoo/') . "<br>";
if (file_exists(WP_PLUGIN_DIR . '/toolzoo/')) {
    echo "Directory exists: YES<br>";
}

echo "<h2>Template File:</h2>";
$template_file = WP_PLUGIN_DIR . '/toolzoo/template-toolzoo.php';
if (file_exists($template_file)) {
    echo "Template file exists: YES<br>";
} else {
    echo "Template file exists: NO<br>";
}
