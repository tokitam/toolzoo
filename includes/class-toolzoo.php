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

        // Setup rewrite rules
        add_action('init', array($this, 'register_rewrite_rules'));

        // Setup template loading (for toolzoo path-based URLs)
        add_filter('template_include', array($this, 'load_toolzoo_template'), 999);

        // Prevent 404 when toolzoo tool is accessed
        add_action('template_redirect', array($this, 'handle_toolzoo_request'));

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
        require_once TOOLZOO_PLUGIN_DIR . 'includes/class-bmi-calculator.php';
        require_once TOOLZOO_PLUGIN_DIR . 'includes/class-ip-checker.php';
        require_once TOOLZOO_PLUGIN_DIR . 'includes/class-length-unit.php';
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
        add_shortcode('toolzoo_bmi', array($this, 'bmi_shortcode'));
        add_shortcode('toolzoo_ip', array($this, 'ip_shortcode'));
        add_shortcode('toolzoo_length_unit', array($this, 'length_unit_shortcode'));
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
     * BMI calculator shortcode
     *
     * @param array $atts Shortcode attributes
     * @return string HTML output
     */
    public function bmi_shortcode($atts) {
        $calculator = new Toolzoo_BMI_Calculator();
        return $calculator->render();
    }

    /**
     * IP Checker shortcode
     *
     * @param array $atts Shortcode attributes
     * @return string HTML output
     */
    public function ip_shortcode($atts) {
        $checker = new Toolzoo_IP_Checker();
        return $checker->render();
    }

    /**
     * Length Unit Converter shortcode
     *
     * @param array $atts Shortcode attributes
     * @return string HTML output
     */
    public function length_unit_shortcode($atts) {
        $converter = new Toolzoo_Length_Unit();
        return $converter->render();
    }

    /**
     * Register rewrite rules for path-based access
     *
     * @public This method is called during plugin activation
     */
    public function register_rewrite_rules() {
        // Define available tools
        $tools = array(
            'password',
            'nengo',
            'worldclock',
            'json',
            'bmi',
            'ip',
            'length'
        );

        // Register query variable (must be called on init)
        add_rewrite_tag('%toolzoo_tool%', '([^&]+)');

        // Register rewrite rule with lower priority to override default rules
        add_rewrite_rule(
            '^toolzoo/(' . implode('|', $tools) . ')/?$',
            'index.php?toolzoo_tool=$matches[1]',
            'top'
        );

        // Debug log (for troubleshooting)
        if (defined('WP_DEBUG_LOG') && WP_DEBUG_LOG) {
            error_log('ToolZoo: Rewrite rules registered');
            error_log('ToolZoo: Rewrite tag added for toolzoo_tool');

            // Show current rewrite rules (for debugging)
            global $wp_rewrite;
            error_log('ToolZoo: Current rules = ' . print_r($wp_rewrite->rules, true));
        }
    }

    /**
     * Load toolzoo template based on query variable
     *
     * @param string $template The template path
     * @return string The modified template path
     */
    public function load_toolzoo_template($template) {
        // Check if toolzoo_tool query variable is set
        $tool = get_query_var('toolzoo_tool');

        if (!empty($tool)) {
            // Validate tool name to prevent directory traversal
            $allowed_tools = array(
                'password',
                'nengo',
                'worldclock',
                'json',
                'bmi',
                'ip',
                'length'
            );

            if (in_array($tool, $allowed_tools, true)) {
                $toolzoo_template = TOOLZOO_PLUGIN_DIR . 'template-toolzoo.php';

                // Check if template file exists
                if (file_exists($toolzoo_template)) {
                    return $toolzoo_template;
                }
            }
        }

        return $template;
    }

    /**
     * Handle ToolZoo requests - prevent 404 and output tool content
     */
    public function handle_toolzoo_request() {
        global $wp_query;

        // Get the tool name from query variable
        $tool = get_query_var('toolzoo_tool');

        if (empty($tool)) {
            return;
        }

        // Validate tool name
        $allowed_tools = array(
            'password',
            'nengo',
            'worldclock',
            'json',
            'bmi',
            'ip',
            'length'
        );

        if (!in_array($tool, $allowed_tools, true)) {
            return;
        }

        // Mark this as not a 404
        status_header(200);
        $wp_query->is_404 = false;

        // Set up post data
        $wp_query->queried_object = null;
        $wp_query->queried_object_id = null;

        // Load and display the template
        $template_file = TOOLZOO_PLUGIN_DIR . 'template-toolzoo.php';

        if (file_exists($template_file)) {
            // Load template with WordPress context
            get_header();
            include $template_file;
            get_footer();
            exit;
        }
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
