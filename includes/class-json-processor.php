<?php
/**
 * JSON Processor Class
 *
 * @package ToolZoo
 */

// Security: Prevent direct access
if (!defined('ABSPATH')) {
    exit;
}

/**
 * Toolzoo_JSON_Processor クラス
 */
class Toolzoo_JSON_Processor {
    /**
     * Generate HTML output
     *
     * @return string HTML output
     */
    public function render() {
        // Enqueue CSS/JS
        $this->enqueue_assets();

        // Generate HTML
        ob_start();
        ?>
        <div class="toolzoo-json-container" id="toolzoo-json-processor">
            <div class="toolzoo-json-header">
                <h3><?php esc_html_e('JSON Processor', 'toolzoo'); ?></h3>
                <p class="toolzoo-json-description">
                    <?php esc_html_e('Paste or enter JSON data to validate, format, minify, and view its structure. Use the tabs to switch between Pretty Print (formatted), Minify (compressed), and Tree View (hierarchical structure).', 'toolzoo'); ?>
                </p>
            </div>

            <div id="toolzoo-json-error" class="toolzoo-json-error" style="display: none;"></div>

            <div class="toolzoo-json-tabs">
                <div class="toolzoo-json-tab active" data-tab="pretty">
                    <?php esc_html_e('Pretty Print', 'toolzoo'); ?>
                </div>
                <div class="toolzoo-json-tab" data-tab="minify">
                    <?php esc_html_e('Minify', 'toolzoo'); ?>
                </div>
                <div class="toolzoo-json-tab" data-tab="tree">
                    <?php esc_html_e('Tree View', 'toolzoo'); ?>
                </div>
                <div class="toolzoo-json-tab" data-tab="raw">
                    <?php esc_html_e('Raw', 'toolzoo'); ?>
                </div>
            </div>

            <div class="toolzoo-json-main">
                <div class="toolzoo-json-input-area">
                    <div class="toolzoo-json-label">
                        <h4><?php esc_html_e('Input', 'toolzoo'); ?></h4>
                    </div>
                    <textarea
                        id="toolzoo-json-input"
                        class="toolzoo-json-input"
                        placeholder="<?php esc_attr_e('Paste or enter JSON...', 'toolzoo'); ?>"
                        rows="15"
                        spellcheck="false"
                        autocomplete="off"
                    ></textarea>
                    <div class="toolzoo-json-input-buttons">
                        <button id="toolzoo-json-paste" class="toolzoo-json-button">
                            <?php esc_html_e('Paste', 'toolzoo'); ?>
                        </button>
                        <button id="toolzoo-json-clear" class="toolzoo-json-button">
                            <?php esc_html_e('Clear', 'toolzoo'); ?>
                        </button>
                        <button id="toolzoo-json-copy-input" class="toolzoo-json-button">
                            <?php esc_html_e('Copy', 'toolzoo'); ?>
                        </button>
                        <button id="toolzoo-json-download" class="toolzoo-json-button">
                            <?php esc_html_e('Download', 'toolzoo'); ?>
                        </button>
                    </div>
                </div>

                <div class="toolzoo-json-output-area">
                    <div class="toolzoo-json-label">
                        <h4><?php esc_html_e('Output', 'toolzoo'); ?></h4>
                    </div>
                    <textarea
                        id="toolzoo-json-output"
                        class="toolzoo-json-output"
                        readonly
                        rows="15"
                    ></textarea>
                    <div class="toolzoo-json-output-buttons">
                        <button id="toolzoo-json-copy-output" class="toolzoo-json-button">
                            <?php esc_html_e('Copy', 'toolzoo'); ?>
                        </button>
                        <button id="toolzoo-json-download-output" class="toolzoo-json-button">
                            <?php esc_html_e('Download', 'toolzoo'); ?>
                        </button>
                        <button id="toolzoo-json-clear-output" class="toolzoo-json-button">
                            <?php esc_html_e('Clear', 'toolzoo'); ?>
                        </button>
                    </div>
                </div>
            </div>

            <div class="toolzoo-json-status">
                <span id="toolzoo-json-status-text">
                    <?php esc_html_e('Ready', 'toolzoo'); ?>
                </span>
            </div>
        </div>
        <?php
        return ob_get_clean();
    }

    /**
     * Enqueue CSS/JS
     */
    private function enqueue_assets() {
        // CSS
        wp_enqueue_style(
            'toolzoo-json-css',
            TOOLZOO_PLUGIN_URL . 'assets/css/json.css',
            array(),
            TOOLZOO_VERSION
        );

        // JavaScript
        wp_enqueue_script(
            'toolzoo-json-js',
            TOOLZOO_PLUGIN_URL . 'assets/js/json.js',
            array(),
            TOOLZOO_VERSION,
            true
        );
    }
}
