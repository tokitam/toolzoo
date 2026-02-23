<?php
/**
 * Weight Unit Converter Class
 *
 * @package ToolZoo
 */

// Security: Prevent direct access
if (!defined('ABSPATH')) {
    exit;
}

/**
 * Toolzoo_Weight_Unit クラス
 */
class Toolzoo_Weight_Unit {
    /**
     * Generate HTML output
     *
     * @return string HTML output
     */
    public function render() {
        $this->enqueue_assets();

        ob_start();
        ?>
        <div class="toolzoo-weight-container" id="toolzoo-weight-unit">

            <div class="toolzoo-weight-header">
                <h3><?php esc_html_e('Weight Unit Converter', 'toolzoo'); ?></h3>
            </div>

            <!-- 早見表 -->
            <div class="toolzoo-weight-reference">
                <h4><?php esc_html_e('Reference Table', 'toolzoo'); ?></h4>
                <table class="toolzoo-weight-reference-table">
                    <thead>
                        <tr>
                            <th><?php esc_html_e('Reference Value', 'toolzoo'); ?></th>
                            <th><?php esc_html_e('Pounds (lb)', 'toolzoo'); ?></th>
                            <th><?php esc_html_e('Kan', 'toolzoo'); ?></th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td><?php esc_html_e('1 Kilogram', 'toolzoo'); ?></td>
                            <td>2.2046 lb</td>
                            <td>0.2667 <?php esc_html_e('kan', 'toolzoo'); ?></td>
                        </tr>
                        <tr>
                            <td><?php esc_html_e('1 Pound', 'toolzoo'); ?></td>
                            <td>—</td>
                            <td>0.1210 <?php esc_html_e('kan', 'toolzoo'); ?></td>
                        </tr>
                        <tr>
                            <td><?php esc_html_e('1 Kan', 'toolzoo'); ?></td>
                            <td>8.2673 lb</td>
                            <td>—</td>
                        </tr>
                    </tbody>
                </table>
                <p class="toolzoo-weight-note">
                    <?php esc_html_e('* 1 kan = 3.75 kg (Japanese Measurement Act) / 1 pound = 0.45359237 kg (International definition)', 'toolzoo'); ?>
                </p>
            </div>

            <!-- 自由変換エリア -->
            <div class="toolzoo-weight-converter">
                <h4><?php esc_html_e('Unit Converter', 'toolzoo'); ?></h4>
                <p class="toolzoo-weight-converter-hint">
                    <?php esc_html_e('Enter a value in any field to automatically convert to all other units.', 'toolzoo'); ?>
                </p>

                <div class="toolzoo-weight-input-row">
                    <label class="toolzoo-weight-input-label" for="toolzoo-weight-kg">
                        <?php esc_html_e('Kilogram', 'toolzoo'); ?>
                    </label>
                    <input type="number" id="toolzoo-weight-kg" class="toolzoo-weight-input-field" min="0" step="any" value="1">
                    <span class="toolzoo-weight-input-unit">kg</span>
                </div>

                <div class="toolzoo-weight-input-row">
                    <label class="toolzoo-weight-input-label" for="toolzoo-weight-lb">
                        <?php esc_html_e('Pound', 'toolzoo'); ?>
                    </label>
                    <input type="number" id="toolzoo-weight-lb" class="toolzoo-weight-input-field" min="0" step="any">
                    <span class="toolzoo-weight-input-unit">lb</span>
                </div>

                <div class="toolzoo-weight-input-row">
                    <label class="toolzoo-weight-input-label" for="toolzoo-weight-kan">
                        <?php esc_html_e('Kan', 'toolzoo'); ?>
                    </label>
                    <input type="number" id="toolzoo-weight-kan" class="toolzoo-weight-input-field" min="0" step="any">
                    <span class="toolzoo-weight-input-unit"><?php esc_html_e('kan', 'toolzoo'); ?></span>
                </div>

                <div class="toolzoo-weight-btn-row">
                    <button id="toolzoo-weight-clear-btn" class="toolzoo-weight-clear-btn">
                        <?php esc_html_e('Clear', 'toolzoo'); ?>
                    </button>
                </div>
            </div>

            <!-- 尺貫法 重量単位 -->
            <div class="toolzoo-weight-shakkan" id="toolzoo-weight-shakkan">
                <h4><?php esc_html_e('Japanese Traditional Weight Units (Shakkan-ho)', 'toolzoo'); ?></h4>
                <table class="toolzoo-weight-info-table">
                    <thead>
                        <tr>
                            <th><?php esc_html_e('Unit', 'toolzoo'); ?></th>
                            <th><?php esc_html_e('Relation', 'toolzoo'); ?></th>
                            <th><?php esc_html_e('In Kilograms', 'toolzoo'); ?></th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td><?php esc_html_e('1 Mo (毛)', 'toolzoo'); ?></td>
                            <td><?php esc_html_e('= 1/10 rin', 'toolzoo'); ?></td>
                            <td>≈ 0.00000375 kg (3.75 mg)</td>
                        </tr>
                        <tr>
                            <td><?php esc_html_e('1 Rin (厘)', 'toolzoo'); ?></td>
                            <td><?php esc_html_e('= 1/10 bu', 'toolzoo'); ?></td>
                            <td>≈ 0.0000375 kg (37.5 mg)</td>
                        </tr>
                        <tr>
                            <td><?php esc_html_e('1 Bu (分)', 'toolzoo'); ?></td>
                            <td><?php esc_html_e('= 1/10 monme', 'toolzoo'); ?></td>
                            <td>≈ 0.000375 kg (375 mg)</td>
                        </tr>
                        <tr>
                            <td><?php esc_html_e('1 Monme (匁)', 'toolzoo'); ?></td>
                            <td><?php esc_html_e('= 1/1000 kan', 'toolzoo'); ?></td>
                            <td>= 0.00375 kg (3.75 g)</td>
                        </tr>
                        <tr>
                            <td><?php esc_html_e('1 Ryo (両)', 'toolzoo'); ?></td>
                            <td><?php esc_html_e('= 10 monme', 'toolzoo'); ?></td>
                            <td>= 0.0375 kg (37.5 g)</td>
                        </tr>
                        <tr>
                            <td><?php esc_html_e('1 Kin (斤)', 'toolzoo'); ?></td>
                            <td><?php esc_html_e('= 160 monme', 'toolzoo'); ?></td>
                            <td>= 0.600 kg (600 g)</td>
                        </tr>
                        <tr>
                            <td><?php esc_html_e('1 Kan (貫)', 'toolzoo'); ?></td>
                            <td><?php esc_html_e('= 1000 monme', 'toolzoo'); ?></td>
                            <td>= 3.75 kg (3750 g)</td>
                        </tr>
                    </tbody>
                </table>
                <p class="toolzoo-weight-note">
                    <?php esc_html_e('* 1 monme = 3.75 g (as defined by the Japanese Measurement Act)', 'toolzoo'); ?>
                </p>
            </div>

            <!-- ヤード・ポンド法 重量単位 -->
            <div class="toolzoo-weight-imperial" id="toolzoo-weight-imperial">
                <h4><?php esc_html_e('Imperial Weight Units', 'toolzoo'); ?></h4>
                <table class="toolzoo-weight-info-table">
                    <thead>
                        <tr>
                            <th><?php esc_html_e('Unit', 'toolzoo'); ?></th>
                            <th><?php esc_html_e('Relation', 'toolzoo'); ?></th>
                            <th><?php esc_html_e('In Kilograms', 'toolzoo'); ?></th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td><?php esc_html_e('1 Grain (gr)', 'toolzoo'); ?></td>
                            <td>—</td>
                            <td>≈ 0.0000648 kg (64.8 mg)</td>
                        </tr>
                        <tr>
                            <td><?php esc_html_e('1 Dram (dr)', 'toolzoo'); ?></td>
                            <td>= 27.34 gr</td>
                            <td>≈ 0.001772 kg (1.772 g)</td>
                        </tr>
                        <tr>
                            <td><?php esc_html_e('1 Ounce (oz)', 'toolzoo'); ?></td>
                            <td>= 16 dr</td>
                            <td>≈ 0.028350 kg (28.35 g)</td>
                        </tr>
                        <tr>
                            <td><?php esc_html_e('1 Pound (lb)', 'toolzoo'); ?></td>
                            <td>= 16 oz</td>
                            <td>= 0.45359 kg (453.59 g)</td>
                        </tr>
                        <tr>
                            <td><?php esc_html_e('1 Stone (st)', 'toolzoo'); ?></td>
                            <td>= 14 lb</td>
                            <td>≈ 6.350 kg</td>
                        </tr>
                        <tr>
                            <td><?php esc_html_e('1 Hundredweight (cwt)', 'toolzoo'); ?></td>
                            <td>= 100 lb</td>
                            <td>= 45.359 kg</td>
                        </tr>
                        <tr>
                            <td><?php esc_html_e('1 Short Ton (US ton)', 'toolzoo'); ?></td>
                            <td>= 2000 lb</td>
                            <td>≈ 907.185 kg</td>
                        </tr>
                        <tr>
                            <td><?php esc_html_e('1 Long Ton (UK ton)', 'toolzoo'); ?></td>
                            <td>= 2240 lb</td>
                            <td>≈ 1016.047 kg</td>
                        </tr>
                    </tbody>
                </table>
                <p class="toolzoo-weight-note">
                    <?php esc_html_e('* 1 pound = 0.45359237 kg (exact, by the International Yard and Pound Agreement of 1959)', 'toolzoo'); ?>
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
            'toolzoo-weight-unit-css',
            TOOLZOO_PLUGIN_URL . 'assets/css/weight-unit.css',
            array(),
            TOOLZOO_VERSION
        );

        wp_enqueue_script(
            'toolzoo-weight-unit-js',
            TOOLZOO_PLUGIN_URL . 'assets/js/weight-unit.js',
            array(),
            TOOLZOO_VERSION,
            true
        );
    }
}
