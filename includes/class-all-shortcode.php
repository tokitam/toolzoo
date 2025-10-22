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
        return array(
            array(
                'id'          => 'password',
                'name'        => __('Password Generator', 'toolzoo'),
                'description' => __('A tool that generates 20 random passwords at once. You can customize the length and character types (numbers, uppercase, lowercase, symbols). Generated passwords can be copied individually or all at once.', 'toolzoo'),
                'icon'        => '🔒',
                'slug'        => 'password',
            ),
            array(
                'id'          => 'nengo',
                'name'        => __('Japanese Era List', 'toolzoo'),
                'description' => __('Displays a correspondence table between Japanese era names (Meiji to Reiwa) and Western calendar years. A convenient tool for converting and checking era years. The search function allows you to quickly find the desired year.', 'toolzoo'),
                'icon'        => '📅',
                'slug'        => 'nengo',
            ),
        );
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
        $tools = $this->get_tools_list();
        $tool = null;

        // Find the tool
        foreach ($tools as $t) {
            if ($t['id'] === $tool_id) {
                $tool = $t;
                break;
            }
        }

        if (!$tool) {
            return '<p>' . esc_html__('Tool not found.', 'toolzoo') . '</p>';
        }

        // Render the appropriate tool
        switch ($tool_id) {
            case 'password':
                $generator = new Toolzoo_Password_Generator();
                return $generator->render();

            case 'nengo':
                $list = new Toolzoo_Nengo_List();
                return $list->render();

            default:
                return '<p>' . esc_html__('Tool not available.', 'toolzoo') . '</p>';
        }
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
