<?php
/**
 * toolzoo_all Shortcode Class
 *
 * @package ToolZoo
 */

// Security: Prevent direct access
if (!defined('ABSPATH')) {
    exit;
}

/**
 * Toolzoo_All_Shortcode class
 *
 * Displays all tools as clickable cards that link to individual tool pages
 */
class Toolzoo_All_Shortcode {
    /**
     * Render shortcode output
     *
     * @param array $atts Shortcode attributes
     * @return string HTML output
     */
    public function render($atts) {
        // Check if a specific tool is requested via query parameter
        $tool_id = isset($_GET['tool']) ? sanitize_text_field($_GET['tool']) : '';

        if ($tool_id) {
            return $this->render_single_tool($tool_id);
        }

        // Enqueue assets
        $this->enqueue_assets();

        // Get tools list
        $tools = $this->get_tools_list();

        // Generate HTML
        return $this->generate_html($tools);
    }

    /**
     * Get tools list
     *
     * @return array Array of tool information
     */
    private function get_tools_list() {
        $tools = Toolzoo_Constants::get_tools_list();

        // Filter out meta tools (all and links)
        $tools = array_filter($tools, function($tool) {
            return !in_array($tool['id'], array('all', 'links'));
        });

        // Remap to shortcode-friendly format
        $result = array();
        foreach ($tools as $tool) {
            $result[] = array(
                'id'          => $tool['id'],
                'name'        => $tool['name'],
                'description' => $tool['description'],
                'icon'        => $tool['emoji'],
                'slug'        => $tool['slug'],
                'class'       => $tool['class'],
            );
        }

        return $result;
    }

    /**
     * Get tool page URL
     *
     * @param string $tool_id Tool ID
     * @return string URL
     */
    private function get_tool_url($tool_id) {
        // Find tool page (parent page: toolzoo)
        $parent_page = get_page_by_path('toolzoo');

        if ($parent_page) {
            // Find child page
            $tool_page = get_page_by_path('toolzoo/' . $tool_id);
            if ($tool_page) {
                return get_permalink($tool_page->ID);
            }
        }

        // Fallback: add query parameter to current page
        return add_query_arg('tool', $tool_id, get_permalink());
    }

    /**
     * Generate HTML
     *
     * @param array $tools Tools list
     * @return string HTML
     */
    private function generate_html($tools) {
        ob_start();
        ?>
        <div class="toolzoo-all-container">
            <div class="toolzoo-all-grid">
                <?php foreach ($tools as $tool) : ?>
                    <?php $this->render_tool_card($tool); ?>
                <?php endforeach; ?>
            </div>
        </div>
        <?php
        return ob_get_clean();
    }

    /**
     * Render tool card
     *
     * @param array $tool Tool information
     */
    private function render_tool_card($tool) {
        $tool_url = $this->get_tool_url($tool['id']);
        ?>
        <div class="toolzoo-all-card">
            <a href="<?php echo esc_url($tool_url); ?>" class="toolzoo-all-card-link">
                <div class="toolzoo-all-card-header">
                    <span class="toolzoo-all-icon" aria-hidden="true"><?php echo esc_html($tool['icon']); ?></span>
                    <h3 class="toolzoo-all-title"><?php echo esc_html($tool['name']); ?></h3>
                </div>

                <div class="toolzoo-all-card-body">
                    <p class="toolzoo-all-description">
                        <?php echo esc_html($tool['description']); ?>
                    </p>

                    <span class="toolzoo-all-btn">
                        <?php esc_html_e('Use Tool', 'toolzoo'); ?>
                        <span class="toolzoo-all-arrow" aria-hidden="true">→</span>
                    </span>
                </div>
            </a>
        </div>
        <?php
    }

    /**
     * Render single tool based on tool ID
     *
     * @param string $tool_id Tool ID
     * @return string HTML output
     */
    private function render_single_tool($tool_id) {
        $tool = Toolzoo_Constants::get_tool_by_id($tool_id);

        if (!$tool) {
            return '<p>' . esc_html__('Tool not found.', 'toolzoo') . '</p>';
        }

        // Verify class exists
        if (!class_exists($tool['class'])) {
            return '<p>' . sprintf(esc_html__('Tool class not found: %s', 'toolzoo'), esc_html($tool['class'])) . '</p>';
        }

        // Render the tool
        $instance = new $tool['class']();
        return $instance->render();
    }

    /**
     * Enqueue assets
     */
    private function enqueue_assets() {
        // CSS
        wp_enqueue_style(
            'toolzoo-all-css',
            TOOLZOO_PLUGIN_URL . 'assets/css/all.css',
            array(),
            TOOLZOO_VERSION
        );
    }
}
