<?php
/**
 * ToolZoo Constants Class
 *
 * Centralized definition of tools and other constants
 *
 * @package ToolZoo
 */

// Security: Prevent direct access
if (!defined('ABSPATH')) {
    exit;
}

/**
 * Toolzoo_Constants class
 *
 * Provides central repository for tool definitions and constants
 */
class Toolzoo_Constants {
    /**
     * Get all tools list
     *
     * @return array Array of tool information
     */
    public static function get_tools_list() {
        return array(
            // All Tools Shortcode
            array(
                'id'          => 'all',
                'name'        => __('All Tools List', 'toolzoo'),
                'description' => __('Displays all available tools as clickable cards. Users can view all ToolZoo tools in one place and access each tool directly from the list.', 'toolzoo'),
                'shortcode'   => '[toolzoo_all]',
                'class'       => '',
                'icon'        => 'dashicons-apps',
                'slug'        => 'all',
                'emoji'       => '📦',
            ),
            // ToolZoo Links Shortcode
            array(
                'id'          => 'links',
                'name'        => __('Tools Links', 'toolzoo'),
                'description' => __('Displays all ToolZoo tools as horizontal links. Great for navigation bars, sidebars, or page headers. Customizable with different styles and sizes.', 'toolzoo'),
                'shortcode'   => '[toolzoo_links]',
                'class'       => '',
                'icon'        => 'dashicons-editor-ul',
                'slug'        => 'links',
                'emoji'       => '🔗',
            ),
            // Password Generator Tool
            array(
                'id'          => 'password',
                'name'        => __('Password Generator', 'toolzoo'),
                'description' => __('A tool that generates 20 random passwords at once. You can customize the length and character types (numbers, uppercase, lowercase, symbols). Generated passwords can be copied individually or all at once.', 'toolzoo'),
                'shortcode'   => '[toolzoo_password]',
                'class'       => 'Toolzoo_Password_Generator',
                'icon'        => 'dashicons-lock',
                'slug'        => 'password',
                'emoji'       => '🔒',
            ),
            // Japanese Era List Tool
            array(
                'id'          => 'nengo',
                'name'        => __('Japanese Era List', 'toolzoo'),
                'description' => __('Displays a correspondence table between Japanese era names (Meiji to Reiwa) and Western calendar years. A convenient tool for converting and checking era years. The search function allows you to quickly find the desired year.', 'toolzoo'),
                'shortcode'   => '[toolzoo_nengo]',
                'class'       => 'Toolzoo_Nengo_List',
                'icon'        => 'dashicons-calendar-alt',
                'slug'        => 'nengo',
                'emoji'       => '📅',
            ),
            // World Clock Tool
            array(
                'id'          => 'worldclock',
                'name'        => __('World Clock', 'toolzoo'),
                'description' => __('Displays current time in 30 major cities around the world. Automatically sorted from your timezone eastward. Updates every second.', 'toolzoo'),
                'shortcode'   => '[toolzoo_worldclock]',
                'class'       => 'Toolzoo_Worldclock',
                'icon'        => 'dashicons-clock',
                'slug'        => 'worldclock',
                'emoji'       => '🌍',
            ),
            // JSON Processor Tool
            array(
                'id'          => 'json',
                'name'        => __('JSON Processor', 'toolzoo'),
                'description' => __('Paste or enter JSON data to validate, format, minify, and view its structure. Use the tabs to switch between Pretty Print (formatted), Minify (compressed), and Tree View (hierarchical structure).', 'toolzoo'),
                'shortcode'   => '[toolzoo_json]',
                'class'       => 'Toolzoo_JSON_Processor',
                'icon'        => 'dashicons-code-standard',
                'slug'        => 'json',
                'emoji'       => '{}',
            ),
        );
    }

    /**
     * Get a single tool by ID
     *
     * @param string $tool_id Tool ID
     * @return array|null Tool information or null if not found
     */
    public static function get_tool_by_id($tool_id) {
        $tools = self::get_tools_list();
        foreach ($tools as $tool) {
            if ($tool['id'] === $tool_id) {
                return $tool;
            }
        }
        return null;
    }

    /**
     * Get tool assets info
     *
     * @param string $tool_id Tool ID
     * @return array Array with 'css' and 'js' keys
     */
    public static function get_tool_assets($tool_id) {
        $assets = array(
            'password' => array(
                'css' => 'assets/css/password.css',
                'js'  => 'assets/js/password.js',
            ),
            'nengo' => array(
                'css' => 'assets/css/nengo.css',
                'js'  => 'assets/js/nengo.js',
            ),
            'worldclock' => array(
                'css' => 'assets/css/worldclock.css',
                'js'  => 'assets/js/worldclock.js',
            ),
            'json' => array(
                'css' => 'assets/css/json.css',
                'js'  => 'assets/js/json.js',
            ),
        );

        return isset($assets[$tool_id]) ? $assets[$tool_id] : array();
    }
}
