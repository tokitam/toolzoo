<?php
/**
 * Length Unit Converter Class
 *
 * @package ToolZoo
 */

// Security: Prevent direct access
if (!defined('ABSPATH')) {
    exit;
}

/**
 * Toolzoo_Length_Unit クラス
 */
class Toolzoo_Length_Unit {
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
        <div class="toolzoo-length-container" id="toolzoo-length-unit">

            <div class="toolzoo-length-header">
                <h3><?php esc_html_e('Length Unit Converter', 'toolzoo'); ?></h3>
            </div>

            <!-- 早見表 -->
            <div class="toolzoo-length-reference">
                <h4><?php esc_html_e('Reference Table', 'toolzoo'); ?></h4>
                <table class="toolzoo-length-reference-table">
                    <thead>
                        <tr>
                            <th><?php esc_html_e('Reference Value', 'toolzoo'); ?></th>
                            <th><?php esc_html_e('Feet (ft)', 'toolzoo'); ?></th>
                            <th><?php esc_html_e('Sun', 'toolzoo'); ?></th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td><?php esc_html_e('1 Meter', 'toolzoo'); ?></td>
                            <td>3.2808 ft</td>
                            <td>33.0000 <?php esc_html_e('sun', 'toolzoo'); ?></td>
                        </tr>
                        <tr>
                            <td><?php esc_html_e('1 Foot', 'toolzoo'); ?></td>
                            <td>—</td>
                            <td>10.0584 <?php esc_html_e('sun', 'toolzoo'); ?></td>
                        </tr>
                        <tr>
                            <td><?php esc_html_e('1 Sun', 'toolzoo'); ?></td>
                            <td>0.0994 ft</td>
                            <td>—</td>
                        </tr>
                    </tbody>
                </table>
                <p class="toolzoo-length-note">
                    <?php esc_html_e('* 1 sun = 1/33 m (Japanese Measurement Act) / 1 foot = 0.3048 m (International definition)', 'toolzoo'); ?>
                </p>
            </div>

            <!-- 自由変換エリア -->
            <div class="toolzoo-length-converter">
                <h4><?php esc_html_e('Unit Converter', 'toolzoo'); ?></h4>
                <p class="toolzoo-length-converter-hint">
                    <?php esc_html_e('Enter a value in any field to automatically convert to all other units.', 'toolzoo'); ?>
                </p>

                <!-- メートル法 -->
                <div class="toolzoo-length-group-label"><?php esc_html_e('Metric System', 'toolzoo'); ?></div>

                <div class="toolzoo-length-input-row">
                    <label class="toolzoo-length-input-label" for="toolzoo-length-km">
                        <?php esc_html_e('Kilometer', 'toolzoo'); ?>
                    </label>
                    <input type="number" id="toolzoo-length-km" class="toolzoo-length-input-field" min="0" step="any">
                    <span class="toolzoo-length-input-unit">km</span>
                </div>

                <div class="toolzoo-length-input-row">
                    <label class="toolzoo-length-input-label" for="toolzoo-length-meter">
                        <?php esc_html_e('Meter', 'toolzoo'); ?>
                    </label>
                    <input type="number" id="toolzoo-length-meter" class="toolzoo-length-input-field" min="0" step="any" value="1">
                    <span class="toolzoo-length-input-unit">m</span>
                </div>

                <div class="toolzoo-length-input-row">
                    <label class="toolzoo-length-input-label" for="toolzoo-length-cm">
                        <?php esc_html_e('Centimeter', 'toolzoo'); ?>
                    </label>
                    <input type="number" id="toolzoo-length-cm" class="toolzoo-length-input-field" min="0" step="any">
                    <span class="toolzoo-length-input-unit">cm</span>
                </div>

                <!-- ヤード・ポンド法 -->
                <div class="toolzoo-length-group-label"><?php esc_html_e('Imperial', 'toolzoo'); ?></div>

                <div class="toolzoo-length-input-row">
                    <label class="toolzoo-length-input-label" for="toolzoo-length-mile">
                        <?php esc_html_e('Mile', 'toolzoo'); ?>
                    </label>
                    <input type="number" id="toolzoo-length-mile" class="toolzoo-length-input-field" min="0" step="any">
                    <span class="toolzoo-length-input-unit">mile</span>
                </div>

                <div class="toolzoo-length-input-row">
                    <label class="toolzoo-length-input-label" for="toolzoo-length-feet">
                        <?php esc_html_e('Foot', 'toolzoo'); ?>
                    </label>
                    <input type="number" id="toolzoo-length-feet" class="toolzoo-length-input-field" min="0" step="any">
                    <span class="toolzoo-length-input-unit">ft</span>
                </div>

                <div class="toolzoo-length-input-row">
                    <label class="toolzoo-length-input-label" for="toolzoo-length-inch">
                        <?php esc_html_e('Inch', 'toolzoo'); ?>
                    </label>
                    <input type="number" id="toolzoo-length-inch" class="toolzoo-length-input-field" min="0" step="any">
                    <span class="toolzoo-length-input-unit">in</span>
                </div>

                <!-- 尺貫法 -->
                <div class="toolzoo-length-group-label"><?php esc_html_e('Shakkan-ho', 'toolzoo'); ?></div>

                <div class="toolzoo-length-input-row">
                    <label class="toolzoo-length-input-label" for="toolzoo-length-ri">
                        <?php esc_html_e('Ri', 'toolzoo'); ?>
                    </label>
                    <input type="number" id="toolzoo-length-ri" class="toolzoo-length-input-field" min="0" step="any">
                    <span class="toolzoo-length-input-unit"><?php esc_html_e('ri', 'toolzoo'); ?></span>
                </div>

                <div class="toolzoo-length-input-row">
                    <label class="toolzoo-length-input-label" for="toolzoo-length-sun">
                        <?php esc_html_e('Sun', 'toolzoo'); ?>
                    </label>
                    <input type="number" id="toolzoo-length-sun" class="toolzoo-length-input-field" min="0" step="any">
                    <span class="toolzoo-length-input-unit"><?php esc_html_e('sun', 'toolzoo'); ?></span>
                </div>

                <div class="toolzoo-length-input-row">
                    <label class="toolzoo-length-input-label" for="toolzoo-length-bu">
                        <?php esc_html_e('Bu', 'toolzoo'); ?>
                    </label>
                    <input type="number" id="toolzoo-length-bu" class="toolzoo-length-input-field" min="0" step="any">
                    <span class="toolzoo-length-input-unit"><?php esc_html_e('bu', 'toolzoo'); ?></span>
                </div>

                <div class="toolzoo-length-input-row">
                    <label class="toolzoo-length-input-label" for="toolzoo-length-rin">
                        <?php esc_html_e('Rin', 'toolzoo'); ?>
                    </label>
                    <input type="number" id="toolzoo-length-rin" class="toolzoo-length-input-field" min="0" step="any">
                    <span class="toolzoo-length-input-unit"><?php esc_html_e('rin', 'toolzoo'); ?></span>
                </div>

                <div class="toolzoo-length-btn-row">
                    <button id="toolzoo-length-clear-btn" class="toolzoo-length-clear-btn">
                        <?php esc_html_e('Clear', 'toolzoo'); ?>
                    </button>
                </div>
            </div>

            <!-- ヤード・ポンド法 単位体系 -->
            <div class="toolzoo-length-imperial" id="toolzoo-length-imperial">
                <h4><?php esc_html_e('Imperial Units', 'toolzoo'); ?></h4>
                <table class="toolzoo-length-shakkan-table">
                    <thead>
                        <tr>
                            <th><?php esc_html_e('Unit', 'toolzoo'); ?></th>
                            <th><?php esc_html_e('Relation', 'toolzoo'); ?></th>
                            <th><?php esc_html_e('In Meters', 'toolzoo'); ?></th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td><?php esc_html_e('1 Inch (inch)', 'toolzoo'); ?></td>
                            <td><?php esc_html_e('= 1/12 foot', 'toolzoo'); ?></td>
                            <td>= 25.4 mm (2.54 cm)</td>
                        </tr>
                        <tr>
                            <td><?php esc_html_e('1 Foot (foot)', 'toolzoo'); ?></td>
                            <td><?php esc_html_e('= 12 inches', 'toolzoo'); ?></td>
                            <td>= 304.8 mm (30.48 cm)</td>
                        </tr>
                        <tr>
                            <td><?php esc_html_e('1 Yard (yard)', 'toolzoo'); ?></td>
                            <td><?php esc_html_e('= 3 feet', 'toolzoo'); ?></td>
                            <td>= 914.4 mm (91.44 cm)</td>
                        </tr>
                        <tr>
                            <td><?php esc_html_e('1 Chain (chain)', 'toolzoo'); ?></td>
                            <td><?php esc_html_e('= 22 yards', 'toolzoo'); ?></td>
                            <td>= 20.1168 m</td>
                        </tr>
                        <tr>
                            <td><?php esc_html_e('1 Mile (mile)', 'toolzoo'); ?></td>
                            <td><?php esc_html_e('= 80 chains = 1760 yards', 'toolzoo'); ?></td>
                            <td>= 1609.344 m (1.609344 km)</td>
                        </tr>
                    </tbody>
                </table>
                <p class="toolzoo-length-note">
                    <?php esc_html_e('* 1 inch = 25.4 mm (exact, by the International Yard and Pound Agreement of 1959)', 'toolzoo'); ?>
                </p>
            </div>

            <!-- 尺貫法 単位体系 -->
            <div class="toolzoo-length-shakkan" id="toolzoo-length-shakkan">
                <h4><?php esc_html_e('Japanese Traditional Units (Shakkan-ho)', 'toolzoo'); ?></h4>
                <table class="toolzoo-length-shakkan-table">
                    <thead>
                        <tr>
                            <th><?php esc_html_e('Unit', 'toolzoo'); ?></th>
                            <th><?php esc_html_e('Relation', 'toolzoo'); ?></th>
                            <th><?php esc_html_e('In Meters', 'toolzoo'); ?></th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td><?php esc_html_e('1 Rin (厘)', 'toolzoo'); ?></td>
                            <td><?php esc_html_e('= 1/10 bu', 'toolzoo'); ?></td>
                            <td>≈ 0.303 mm</td>
                        </tr>
                        <tr>
                            <td><?php esc_html_e('1 Bu (分)', 'toolzoo'); ?></td>
                            <td><?php esc_html_e('= 1/10 sun', 'toolzoo'); ?></td>
                            <td>≈ 3.03 mm</td>
                        </tr>
                        <tr>
                            <td><?php esc_html_e('1 Sun (寸)', 'toolzoo'); ?></td>
                            <td><?php esc_html_e('= 1/10 shaku', 'toolzoo'); ?></td>
                            <td>≈ 30.3 mm</td>
                        </tr>
                        <tr>
                            <td><?php esc_html_e('1 Shaku (尺)', 'toolzoo'); ?></td>
                            <td><?php esc_html_e('= 10 sun', 'toolzoo'); ?></td>
                            <td>≈ 303.0 mm (30.3 cm)</td>
                        </tr>
                        <tr>
                            <td><?php esc_html_e('1 Ken (間)', 'toolzoo'); ?></td>
                            <td><?php esc_html_e('= 6 shaku', 'toolzoo'); ?></td>
                            <td>≈ 1818 mm (181.8 cm)</td>
                        </tr>
                        <tr>
                            <td><?php esc_html_e('1 Jo (丈)', 'toolzoo'); ?></td>
                            <td><?php esc_html_e('= 10 shaku', 'toolzoo'); ?></td>
                            <td>≈ 3030 mm (3.03 m)</td>
                        </tr>
                        <tr>
                            <td><?php esc_html_e('1 Cho (町)', 'toolzoo'); ?></td>
                            <td><?php esc_html_e('= 60 ken', 'toolzoo'); ?></td>
                            <td>≈ 109.09 m</td>
                        </tr>
                        <tr>
                            <td><?php esc_html_e('1 Ri (里)', 'toolzoo'); ?></td>
                            <td><?php esc_html_e('= 36 cho', 'toolzoo'); ?></td>
                            <td>≈ 3927.3 m (3.927 km)</td>
                        </tr>
                    </tbody>
                </table>
                <p class="toolzoo-length-note">
                    <?php esc_html_e('* 1 shaku = 10/33 m (as defined by the Japanese Measurement Act)', 'toolzoo'); ?>
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
            'toolzoo-length-unit-css',
            TOOLZOO_PLUGIN_URL . 'assets/css/length-unit.css',
            array(),
            TOOLZOO_VERSION
        );

        wp_enqueue_script(
            'toolzoo-length-unit-js',
            TOOLZOO_PLUGIN_URL . 'assets/js/length-unit.js',
            array(),
            TOOLZOO_VERSION,
            true
        );
    }
}
