<?php
/**
 * Volume Unit Converter Class
 *
 * @package ToolZoo
 */

// Security: Prevent direct access
if (!defined('ABSPATH')) {
    exit;
}

/**
 * Toolzoo_Volume_Unit クラス
 */
class Toolzoo_Volume_Unit {
    /**
     * Generate HTML output
     *
     * @return string HTML output
     */
    public function render() {
        $this->enqueue_assets();

        ob_start();
        ?>
        <div class="toolzoo-volume-container" id="toolzoo-volume-unit">

            <div class="toolzoo-volume-header">
                <h3><?php esc_html_e('Volume Unit Converter', 'toolzoo'); ?></h3>
            </div>

            <!-- 早見表 -->
            <div class="toolzoo-volume-reference">
                <h4><?php esc_html_e('Reference Table', 'toolzoo'); ?></h4>
                <table class="toolzoo-volume-reference-table">
                    <thead>
                        <tr>
                            <th><?php esc_html_e('Reference Value', 'toolzoo'); ?></th>
                            <th><?php esc_html_e('Gallons (gal)', 'toolzoo'); ?></th>
                            <th><?php esc_html_e('Sho', 'toolzoo'); ?></th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td><?php esc_html_e('1 Liter', 'toolzoo'); ?></td>
                            <td>0.2642 gal</td>
                            <td>0.5544 <?php esc_html_e('sho', 'toolzoo'); ?></td>
                        </tr>
                        <tr>
                            <td><?php esc_html_e('1 Gallon', 'toolzoo'); ?></td>
                            <td>—</td>
                            <td>2.0985 <?php esc_html_e('sho', 'toolzoo'); ?></td>
                        </tr>
                        <tr>
                            <td><?php esc_html_e('1 Sho', 'toolzoo'); ?></td>
                            <td>0.4765 gal</td>
                            <td>—</td>
                        </tr>
                    </tbody>
                </table>
                <p class="toolzoo-volume-note">
                    <?php esc_html_e('* 1 sho = 1.8039 L (Japanese Measurement Act) / 1 US gallon = 3.785411784 L (International definition)', 'toolzoo'); ?>
                </p>
            </div>

            <!-- 自由変換エリア -->
            <div class="toolzoo-volume-converter">
                <h4><?php esc_html_e('Unit Converter', 'toolzoo'); ?></h4>
                <p class="toolzoo-volume-converter-hint">
                    <?php esc_html_e('Enter a value in any field to automatically convert to all other units.', 'toolzoo'); ?>
                </p>

                <div class="toolzoo-volume-input-row">
                    <label class="toolzoo-volume-input-label" for="toolzoo-volume-liter">
                        <?php esc_html_e('Liter', 'toolzoo'); ?>
                    </label>
                    <input type="number" id="toolzoo-volume-liter" class="toolzoo-volume-input-field" min="0" step="any" value="1">
                    <span class="toolzoo-volume-input-unit">L</span>
                </div>

                <div class="toolzoo-volume-input-row">
                    <label class="toolzoo-volume-input-label" for="toolzoo-volume-gal">
                        <?php esc_html_e('Gallon', 'toolzoo'); ?>
                    </label>
                    <input type="number" id="toolzoo-volume-gal" class="toolzoo-volume-input-field" min="0" step="any">
                    <span class="toolzoo-volume-input-unit">gal</span>
                </div>

                <div class="toolzoo-volume-input-row">
                    <label class="toolzoo-volume-input-label" for="toolzoo-volume-sho">
                        <?php esc_html_e('Sho', 'toolzoo'); ?>
                    </label>
                    <input type="number" id="toolzoo-volume-sho" class="toolzoo-volume-input-field" min="0" step="any">
                    <span class="toolzoo-volume-input-unit"><?php esc_html_e('sho', 'toolzoo'); ?></span>
                </div>

                <div class="toolzoo-volume-btn-row">
                    <button id="toolzoo-volume-clear-btn" class="toolzoo-volume-clear-btn">
                        <?php esc_html_e('Clear', 'toolzoo'); ?>
                    </button>
                </div>
            </div>

            <!-- 尺貫法 体積単位 -->
            <div class="toolzoo-volume-shakkan" id="toolzoo-volume-shakkan">
                <h4><?php esc_html_e('Japanese Traditional Volume Units (Shakkan-ho)', 'toolzoo'); ?></h4>
                <table class="toolzoo-volume-info-table">
                    <thead>
                        <tr>
                            <th><?php esc_html_e('Unit', 'toolzoo'); ?></th>
                            <th><?php esc_html_e('Relation', 'toolzoo'); ?></th>
                            <th><?php esc_html_e('In Liters', 'toolzoo'); ?></th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td><?php esc_html_e('1 Shaku (勺)', 'toolzoo'); ?></td>
                            <td><?php esc_html_e('= 1/10 go', 'toolzoo'); ?></td>
                            <td>≈ 0.018039 L (18.039 mL)</td>
                        </tr>
                        <tr>
                            <td><?php esc_html_e('1 Go (合)', 'toolzoo'); ?></td>
                            <td><?php esc_html_e('= 10 shaku (勺)', 'toolzoo'); ?></td>
                            <td>≈ 0.18039 L (180.39 mL)</td>
                        </tr>
                        <tr>
                            <td><?php esc_html_e('1 Sho (升)', 'toolzoo'); ?></td>
                            <td><?php esc_html_e('= 10 go', 'toolzoo'); ?></td>
                            <td>= 1.8039 L</td>
                        </tr>
                        <tr>
                            <td><?php esc_html_e('1 To (斗)', 'toolzoo'); ?></td>
                            <td><?php esc_html_e('= 10 sho', 'toolzoo'); ?></td>
                            <td>= 18.039 L</td>
                        </tr>
                        <tr>
                            <td><?php esc_html_e('1 Koku (石)', 'toolzoo'); ?></td>
                            <td><?php esc_html_e('= 10 to', 'toolzoo'); ?></td>
                            <td>= 180.39 L</td>
                        </tr>
                    </tbody>
                </table>
                <p class="toolzoo-volume-note">
                    <?php esc_html_e('* 1 sho = 1.8039 L (as defined by the Japanese Measurement Act)', 'toolzoo'); ?>
                </p>
            </div>

            <!-- 米国 体積単位 -->
            <div class="toolzoo-volume-imperial" id="toolzoo-volume-imperial">
                <h4><?php esc_html_e('US Volume Units', 'toolzoo'); ?></h4>
                <table class="toolzoo-volume-info-table">
                    <thead>
                        <tr>
                            <th><?php esc_html_e('Unit', 'toolzoo'); ?></th>
                            <th><?php esc_html_e('Relation', 'toolzoo'); ?></th>
                            <th><?php esc_html_e('In Liters', 'toolzoo'); ?></th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td><?php esc_html_e('1 Fluid Ounce (fl oz)', 'toolzoo'); ?></td>
                            <td>—</td>
                            <td>≈ 0.029574 L (29.574 mL)</td>
                        </tr>
                        <tr>
                            <td><?php esc_html_e('1 Cup', 'toolzoo'); ?></td>
                            <td><?php esc_html_e('= 8 fl oz', 'toolzoo'); ?></td>
                            <td>≈ 0.236588 L (236.588 mL)</td>
                        </tr>
                        <tr>
                            <td><?php esc_html_e('1 Pint (pt)', 'toolzoo'); ?></td>
                            <td><?php esc_html_e('= 2 cups', 'toolzoo'); ?></td>
                            <td>≈ 0.473176 L</td>
                        </tr>
                        <tr>
                            <td><?php esc_html_e('1 Quart (qt)', 'toolzoo'); ?></td>
                            <td><?php esc_html_e('= 2 pints', 'toolzoo'); ?></td>
                            <td>≈ 0.946353 L</td>
                        </tr>
                        <tr>
                            <td><?php esc_html_e('1 Gallon (gal)', 'toolzoo'); ?></td>
                            <td><?php esc_html_e('= 4 quarts', 'toolzoo'); ?></td>
                            <td>= 3.785412 L</td>
                        </tr>
                    </tbody>
                </table>
                <p class="toolzoo-volume-note">
                    <?php esc_html_e('* 1 US gallon = 3.785411784 L / 1 UK gallon = 4.54609 L (British definition)', 'toolzoo'); ?>
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
        wp_enqueue_style(
            'toolzoo-volume-unit-css',
            TOOLZOO_PLUGIN_URL . 'assets/css/volume-unit.css',
            array(),
            TOOLZOO_VERSION
        );

        wp_enqueue_script(
            'toolzoo-volume-unit-js',
            TOOLZOO_PLUGIN_URL . 'assets/js/volume-unit.js',
            array(),
            TOOLZOO_VERSION,
            true
        );
    }
}
