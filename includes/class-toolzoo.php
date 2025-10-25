<?php
/**
 * ToolZoo Main Class
 *
 * @package ToolZoo
 */

// Security: Prevent direct access
if (!defined('ABSPATH')) {
    exit;
}

/**
 * Toolzoo Main Class
 */
class Toolzoo {
    /**
     * Initialize
     */
    public function init() {
        // Load feature classes
        $this->load_classes();

        // Load admin class
        $this->load_admin_class();

        // Register shortcodes
        $this->register_shortcodes();

        // Setup internationalization
        add_action('init', array($this, 'load_textdomain'));
    }

    /**
     * Load feature classes
     */
    private function load_classes() {
        require_once TOOLZOO_PLUGIN_DIR . 'includes/class-constants.php';
        require_once TOOLZOO_PLUGIN_DIR . 'includes/class-password-generator.php';
        require_once TOOLZOO_PLUGIN_DIR . 'includes/class-nengo-list.php';
        require_once TOOLZOO_PLUGIN_DIR . 'includes/class-all-shortcode.php';
        require_once TOOLZOO_PLUGIN_DIR . 'includes/class-worldclock.php';
        require_once TOOLZOO_PLUGIN_DIR . 'includes/class-json-processor.php';
        require_once TOOLZOO_PLUGIN_DIR . 'includes/class-toolzoo-links.php';
    }

    /**
     * Load admin class
     */
    private function load_admin_class() {
        if (is_admin()) {
            require_once TOOLZOO_PLUGIN_DIR . 'includes/class-admin.php';
            new Toolzoo_Admin();
        }
    }

    /**
     * Register shortcodes
     */
    private function register_shortcodes() {
        add_shortcode('toolzoo_password', array($this, 'password_shortcode'));
        add_shortcode('toolzoo_nengo', array($this, 'nengo_shortcode'));
        add_shortcode('toolzoo_all', array($this, 'all_shortcode'));
        add_shortcode('toolzoo_worldclock', array($this, 'worldclock_shortcode'));
        add_shortcode('toolzoo_json', array($this, 'json_shortcode'));
        add_shortcode('toolzoo_links', array($this, 'links_shortcode'));
    }

    /**
     * Password generator shortcode
     *
     * @param array $atts Shortcode attributes
     * @return string HTML output
     */
    public function password_shortcode($atts) {
        $generator = new Toolzoo_Password_Generator();
        return $generator->render();
    }

    /**
     * Japanese era list shortcode
     *
     * @param array $atts Shortcode attributes
     * @return string HTML output
     */
    public function nengo_shortcode($atts) {
        $list = new Toolzoo_Nengo_List();
        return $list->render();
    }

    /**
     * All tools list shortcode
     *
     * @param array $atts Shortcode attributes
     * @return string HTML output
     */
    public function all_shortcode($atts) {
        $shortcode = new Toolzoo_All_Shortcode();
        return $shortcode->render($atts);
    }

    /**
     * World clock shortcode
     *
     * @param array $atts Shortcode attributes
     * @return string HTML output
     */
    public function worldclock_shortcode($atts) {
        $worldclock = new Toolzoo_Worldclock();
        return $worldclock->render();
    }

    /**
     * JSON processor shortcode
     *
     * @param array $atts Shortcode attributes
     * @return string HTML output
     */
    public function json_shortcode($atts) {
        $processor = new Toolzoo_JSON_Processor();
        return $processor->render();
    }

    /**
     * ToolZoo links shortcode
     *
     * @param array $atts Shortcode attributes
     * @return string HTML output
     */
    public function links_shortcode($atts) {
        $links = new Toolzoo_Links();
        return $links->render($atts);
    }

    /**
     * Load translation files
     */
    public function load_textdomain() {
        load_plugin_textdomain(
            'toolzoo',
            false,
            dirname(TOOLZOO_PLUGIN_BASENAME) . '/languages'
        );
    }
}
