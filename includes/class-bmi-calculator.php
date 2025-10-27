<?php
/**
 * BMI Calculator Class
 *
 * @package ToolZoo
 */

// Security: Prevent direct access
if (!defined('ABSPATH')) {
    exit;
}

/**
 * Toolzoo_BMI_Calculator クラス
 */
class Toolzoo_BMI_Calculator {
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
        <div class="toolzoo-bmi-container" id="toolzoo-bmi-calculator">
            <div class="toolzoo-bmi-header">
                <h3><?php esc_html_e('BMI Calculation', 'toolzoo'); ?></h3>
            </div>

            <div id="toolzoo-bmi-error-message" class="toolzoo-bmi-error"></div>

            <div class="toolzoo-bmi-input-group">
                <div class="toolzoo-bmi-input-wrapper">
                    <label class="toolzoo-bmi-input-label" for="toolzoo-bmi-height">
                        <?php esc_html_e('Height', 'toolzoo'); ?>
                    </label>
                    <input
                        type="number"
                        id="toolzoo-bmi-height"
                        class="toolzoo-bmi-input-field"
                        min="100"
                        max="250"
                        value="170"
                        step="0.5"
                    >
                    <span class="toolzoo-bmi-input-unit"><?php esc_html_e('cm', 'toolzoo'); ?></span>
                </div>

                <div class="toolzoo-bmi-input-wrapper">
                    <label class="toolzoo-bmi-input-label" for="toolzoo-bmi-weight">
                        <?php esc_html_e('Weight', 'toolzoo'); ?>
                    </label>
                    <input
                        type="number"
                        id="toolzoo-bmi-weight"
                        class="toolzoo-bmi-input-field"
                        min="20"
                        max="200"
                        value="60"
                        step="0.5"
                    >
                    <span class="toolzoo-bmi-input-unit"><?php esc_html_e('kg', 'toolzoo'); ?></span>
                </div>

                <button id="toolzoo-bmi-calculate-btn" class="toolzoo-bmi-button">
                    <?php esc_html_e('Calculate', 'toolzoo'); ?>
                </button>
            </div>

            <div id="toolzoo-bmi-results-container" class="toolzoo-bmi-results-container">
                <div class="toolzoo-bmi-result-item">
                    <strong><?php esc_html_e('BMI', 'toolzoo'); ?>:</strong>
                    <span id="toolzoo-bmi-value" class="toolzoo-bmi-value">--</span>
                </div>
                <div class="toolzoo-bmi-result-item">
                    <strong><?php esc_html_e('Category', 'toolzoo'); ?>:</strong>
                    <span id="toolzoo-bmi-category" class="toolzoo-bmi-category">--</span>
                </div>
                <div class="toolzoo-bmi-result-item">
                    <strong><?php esc_html_e('Ideal Weight', 'toolzoo'); ?>:</strong>
                    <span id="toolzoo-bmi-ideal-weight" class="toolzoo-bmi-ideal-weight">--</span>
                </div>
            </div>

            <div class="toolzoo-bmi-info-section">
                <h4><?php esc_html_e('About BMI', 'toolzoo'); ?></h4>

                <h5><?php esc_html_e('BMI Formula', 'toolzoo'); ?></h5>
                <p><?php esc_html_e('BMI = Weight (kg) ÷ Height (m)²', 'toolzoo'); ?></p>

                <h5><?php esc_html_e('BMI Categories', 'toolzoo'); ?></h5>
                <table class="toolzoo-bmi-info-table">
                    <thead>
                        <tr>
                            <th><?php esc_html_e('BMI Range', 'toolzoo'); ?></th>
                            <th><?php esc_html_e('Category', 'toolzoo'); ?></th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td><?php esc_html_e('BMI &lt; 18.5', 'toolzoo'); ?></td>
                            <td><?php esc_html_e('Underweight', 'toolzoo'); ?></td>
                        </tr>
                        <tr>
                            <td><?php esc_html_e('18.5 ≤ BMI &lt; 25', 'toolzoo'); ?></td>
                            <td><?php esc_html_e('Normal Weight', 'toolzoo'); ?></td>
                        </tr>
                        <tr>
                            <td><?php esc_html_e('25 ≤ BMI &lt; 30', 'toolzoo'); ?></td>
                            <td><?php esc_html_e('Overweight', 'toolzoo'); ?></td>
                        </tr>
                        <tr>
                            <td><?php esc_html_e('30 ≤ BMI', 'toolzoo'); ?></td>
                            <td><?php esc_html_e('Obese', 'toolzoo'); ?></td>
                        </tr>
                    </tbody>
                </table>

                <h5><?php esc_html_e('About Ideal Weight', 'toolzoo'); ?></h5>
                <p>
                    <?php esc_html_e('Ideal weight is calculated as the weight range corresponding to a BMI of 18.5 to 25. The most commonly cited ideal BMI is 22. The ideal weight range displayed is based on these BMI standards.', 'toolzoo'); ?>
                </p>
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
            'toolzoo-bmi-css',
            TOOLZOO_PLUGIN_URL . 'assets/css/bmi.css',
            array(),
            TOOLZOO_VERSION
        );

        // JavaScript
        wp_enqueue_script(
            'toolzoo-bmi-js',
            TOOLZOO_PLUGIN_URL . 'assets/js/bmi.js',
            array(),
            TOOLZOO_VERSION,
            true
        );

        // Localize script for translations
        wp_localize_script(
            'toolzoo-bmi-js',
            'toolzooBmiL10n',
            array(
                'invalidInput' => __('Height must be between 100-250 cm and weight between 20-200 kg.', 'toolzoo'),
                'underweight' => __('Underweight', 'toolzoo'),
                'normal' => __('Normal Weight', 'toolzoo'),
                'overweight' => __('Overweight', 'toolzoo'),
                'obese' => __('Obese', 'toolzoo'),
            )
        );
    }
}
