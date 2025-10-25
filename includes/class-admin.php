<?php
/**
 * ToolZoo Admin Class
 *
 * @package ToolZoo
 */

// Security: Prevent direct access
if (!defined('ABSPATH')) {
    exit;
}

/**
 * Toolzoo_Admin class
 *
 * Handles admin menu addition and tools list page display
 */
class Toolzoo_Admin {
    /**
     * Constructor
     */
    public function __construct() {
        add_action('admin_menu', array($this, 'add_admin_menu'));
        add_action('admin_enqueue_scripts', array($this, 'enqueue_admin_assets'));
    }

    /**
     * Add menu to admin screen
     */
    public function add_admin_menu() {
        add_menu_page(
            'ToolZoo',                          // Page title
            'ToolZoo',                          // Menu title
            'manage_options',                   // Required capability
            'toolzoo-tools',                    // Menu slug
            array($this, 'render_admin_page'),  // Callback function
            'dashicons-admin-tools',            // Icon
            65                                  // Position
        );
    }

    /**
     * Render admin page
     */
    public function render_admin_page() {
        // Permission check
        if (!current_user_can('manage_options')) {
            wp_die(__('You do not have permission to access this page.', 'toolzoo'));
        }

        // Determine if displaying individual tool
        $tool_id = isset($_GET['tool']) ? sanitize_text_field($_GET['tool']) : '';

        echo '<div class="wrap toolzoo-admin-page">';

        if ($tool_id) {
            $this->render_tool_preview($tool_id);
        } else {
            $this->render_tools_list();
        }

        echo '</div>';
    }

    /**
     * Render tools list
     */
    private function render_tools_list() {
        ?>
        <h1><?php echo esc_html(get_admin_page_title()); ?> - <?php esc_html_e('Useful Tools List', 'toolzoo'); ?></h1>

        <div class="toolzoo-admin-intro">
            <p><?php esc_html_e('This plugin provides a collection of useful tools. Each tool can be embedded in posts or pages using shortcodes.', 'toolzoo'); ?></p>
        </div>

        <div class="toolzoo-tools-grid">
            <?php
            $tools = $this->get_tools_list();
            foreach ($tools as $tool) {
                $this->render_tool_card($tool);
            }
            ?>
        </div>
        <?php
    }

    /**
     * Render individual tool card
     *
     * @param array $tool Tool information
     */
    private function render_tool_card($tool) {
        $admin_url = admin_url('admin.php?page=toolzoo-tools&tool=' . $tool['id']);
        $has_class = !empty($tool['class']);
        ?>
        <div class="toolzoo-tool-card">
            <div class="toolzoo-tool-header">
                <span class="dashicons <?php echo esc_attr($tool['icon']); ?>"></span>
                <h2><?php echo esc_html($tool['name']); ?></h2>
            </div>

            <div class="toolzoo-tool-body">
                <div class="toolzoo-tool-section">
                    <h3><?php esc_html_e('Description', 'toolzoo'); ?></h3>
                    <p><?php echo esc_html($tool['description']); ?></p>
                </div>

                <div class="toolzoo-tool-section">
                    <h3><?php esc_html_e('Shortcode', 'toolzoo'); ?></h3>
                    <div class="toolzoo-shortcode-wrapper">
                        <code class="toolzoo-shortcode"><?php echo esc_html($tool['shortcode']); ?></code>
                        <button
                            class="button button-secondary toolzoo-copy-btn"
                            data-shortcode="<?php echo esc_attr($tool['shortcode']); ?>">
                            <?php esc_html_e('Copy', 'toolzoo'); ?>
                        </button>
                    </div>
                </div>

                <?php if ($has_class) : ?>
                <div class="toolzoo-tool-section">
                    <h3><?php esc_html_e('Preview', 'toolzoo'); ?></h3>
                    <a href="<?php echo esc_url($admin_url); ?>" class="button button-primary">
                        <?php esc_html_e('View in Admin', 'toolzoo'); ?>
                    </a>
                </div>
                <?php endif; ?>
            </div>
        </div>
        <?php
    }

    /**
     * Render individual tool preview
     *
     * @param string $tool_id Tool ID
     */
    private function render_tool_preview($tool_id) {
        $tools = $this->get_tools_list();
        $tool = null;

        foreach ($tools as $t) {
            if ($t['id'] === $tool_id) {
                $tool = $t;
                break;
            }
        }

        if (!$tool) {
            echo '<div class="notice notice-error"><p>' . esc_html__('Tool not found.', 'toolzoo') . '</p></div>';
            return;
        }

        echo '<div class="toolzoo-admin-tool-preview">';
        echo '<h2>' . esc_html($tool['name']) . '</h2>';
        echo '<p><a href="' . esc_url(admin_url('admin.php?page=toolzoo-tools')) . '" class="button">&laquo; ' . esc_html__('Back to List', 'toolzoo') . '</a></p>';
        echo '<hr style="margin: 20px 0;">';

        // Display the tool
        if (class_exists($tool['class'])) {
            $instance = new $tool['class']();
            echo $instance->render();
        } else {
            /* translators: %s: class name */
            echo '<p>' . sprintf(esc_html__('Tool class not found: %s', 'toolzoo'), esc_html($tool['class'])) . '</p>';
        }

        echo '</div>';
    }

    /**
     * Get tools list
     *
     * @return array Array of tool information
     */
    private function get_tools_list() {
        return Toolzoo_Constants::get_tools_list();
    }

    /**
     * Enqueue admin CSS/JS
     *
     * @param string $hook Current page hook
     */
    public function enqueue_admin_assets($hook) {
        // Load only on ToolZoo page
        if ($hook !== 'toplevel_page_toolzoo-tools') {
            return;
        }

        // CSS
        wp_enqueue_style(
            'toolzoo-admin',
            TOOLZOO_PLUGIN_URL . 'assets/css/admin.css',
            array(),
            TOOLZOO_VERSION
        );

        // JavaScript
        wp_enqueue_script(
            'toolzoo-admin',
            TOOLZOO_PLUGIN_URL . 'assets/js/admin.js',
            array('jquery'),
            TOOLZOO_VERSION,
            true
        );

        // When displaying individual tool, also load that tool's assets
        $tool_id = isset($_GET['tool']) ? sanitize_text_field($_GET['tool']) : '';
        if ($tool_id) {
            $this->enqueue_tool_assets($tool_id);
        }
    }

    /**
     * Enqueue individual tool assets
     *
     * @param string $tool_id Tool ID
     */
    private function enqueue_tool_assets($tool_id) {
        $assets = Toolzoo_Constants::get_tool_assets($tool_id);

        if (empty($assets)) {
            return;
        }

        // Enqueue CSS
        if (!empty($assets['css'])) {
            wp_enqueue_style(
                'toolzoo-' . $tool_id,
                TOOLZOO_PLUGIN_URL . $assets['css'],
                array(),
                TOOLZOO_VERSION
            );
        }

        // Enqueue JavaScript
        if (!empty($assets['js'])) {
            wp_enqueue_script(
                'toolzoo-' . $tool_id,
                TOOLZOO_PLUGIN_URL . $assets['js'],
                array(),
                TOOLZOO_VERSION,
                true
            );
        }
    }
}
