<?php
/**
 * ToolZoo Links Shortcode Class
 *
 * @package ToolZoo
 */

// Security: Prevent direct access
if (!defined('ABSPATH')) {
    exit;
}

/**
 * Toolzoo_Links class
 *
 * Displays all ToolZoo tools as horizontal links
 */
class Toolzoo_Links {
    /**
     * Render shortcode output
     *
     * @param array $atts Shortcode attributes
     * @return string HTML output
     */
    public function render($atts) {
        // Enqueue CSS
        $this->enqueue_assets();

        // Normalize attributes
        $atts = $this->normalize_atts($atts);

        // Get tools list
        $tools = Toolzoo_Constants::get_tools_list();

        // Filter out the "all" and "links" tools (meta tools)
        $tools = array_filter($tools, function($tool) {
            return !in_array($tool['id'], array('all', 'links'));
        });

        // Generate HTML
        return $this->generate_html($tools, $atts);
    }

    /**
     * Normalize shortcode attributes
     *
     * @param array $atts Raw attributes
     * @return array Normalized attributes
     */
    private function normalize_atts($atts) {
        $defaults = array(
            'style'     => 'icon-text',
            'size'      => 'medium',
            'separator' => 'pipe',
            'class'     => '',
        );

        $atts = shortcode_atts($defaults, $atts, 'toolzoo_links');

        // Validate style
        $valid_styles = array('icon-text', 'icon-only', 'text-only');
        if (!in_array($atts['style'], $valid_styles)) {
            $atts['style'] = 'icon-text';
        }

        // Validate size
        $valid_sizes = array('small', 'medium', 'large');
        if (!in_array($atts['size'], $valid_sizes)) {
            $atts['size'] = 'medium';
        }

        // Validate separator
        $valid_separators = array('dot', 'bullet', 'pipe', 'slash');
        if (!in_array($atts['separator'], $valid_separators)) {
            $atts['separator'] = 'pipe';
        }

        return $atts;
    }

    /**
     * Get separator character
     *
     * @param string $separator Separator type
     * @return string Separator character
     */
    private function get_separator_char($separator) {
        $separators = array(
            'dot'    => '•',
            'bullet' => '▪',
            'pipe'   => '|',
            'slash'  => '/',
        );

        return isset($separators[$separator]) ? $separators[$separator] : '|';
    }

    /**
     * Generate HTML
     *
     * @param array $tools Tools list
     * @param array $atts Attributes
     * @return string HTML
     */
    private function generate_html($tools, $atts) {
        $separator_char = $this->get_separator_char($atts['separator']);

        $classes = array(
            'toolzoo-links',
            'toolzoo-links-' . $atts['style'],
            'toolzoo-links-' . $atts['size'],
        );

        if (!empty($atts['class'])) {
            $classes[] = $atts['class'];
        }

        $class_attr = implode(' ', $classes);

        ob_start();
        ?>
        <div class="<?php echo esc_attr($class_attr); ?>">
            <?php
            $first = true;
            foreach ($tools as $tool) {
                if (!$first) {
                    echo '<span class="toolzoo-links-separator">' . esc_html($separator_char) . '</span>';
                }
                $first = false;

                $this->render_link($tool);
            }
            ?>
        </div>
        <?php
        return ob_get_clean();
    }

    /**
     * Render individual link
     *
     * @param array $tool Tool information
     */
    private function render_link($tool) {
        $url = $this->get_link_url($tool['id']);
        $title = isset($tool['name']) ? $tool['name'] : $tool['id'];
        ?>
        <a href="<?php echo esc_url($url); ?>" class="toolzoo-link" data-tool="<?php echo esc_attr($tool['id']); ?>" title="<?php echo esc_attr($title); ?>">
            <?php if (!empty($tool['emoji'])) : ?>
                <span class="toolzoo-link-icon"><?php echo esc_html($tool['emoji']); ?></span>
            <?php endif; ?>
            <span class="toolzoo-link-text"><?php echo esc_html($tool['name']); ?></span>
        </a>
        <?php
    }

    /**
     * Get link URL for a tool
     *
     * @param string $tool_id Tool ID
     * @return string URL
     */
    private function get_link_url($tool_id) {
        // Try to find child page
        $parent_page = get_page_by_path('toolzoo');

        if ($parent_page) {
            $tool_page = get_page_by_path('toolzoo/' . $tool_id);
            if ($tool_page) {
                return get_permalink($tool_page->ID);
            }
        }

        // Fallback: use query parameter
        return add_query_arg('tool', $tool_id, get_permalink());
    }

    /**
     * Enqueue CSS/JS
     */
    private function enqueue_assets() {
        // CSS
        wp_enqueue_style(
            'toolzoo-links-css',
            TOOLZOO_PLUGIN_URL . 'assets/css/links.css',
            array(),
            TOOLZOO_VERSION
        );

        // JavaScript (optional, for active state detection)
        wp_enqueue_script(
            'toolzoo-links-js',
            TOOLZOO_PLUGIN_URL . 'assets/js/links.js',
            array(),
            TOOLZOO_VERSION,
            true
        );
    }
}
