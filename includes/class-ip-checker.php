<?php
/**
 * IP Checker Class
 *
 * @package ToolZoo
 */

// Security: Prevent direct access
if (!defined('ABSPATH')) {
    exit;
}

/**
 * Toolzoo_IP_Checker クラス
 */
class Toolzoo_IP_Checker {
    /**
     * Generate HTML output
     *
     * @return string HTML output
     */
    public function render() {
        // Enqueue CSS/JS
        $this->enqueue_assets();

        // Get IP information
        $client_ip = $this->get_client_ip();
        $domain = $this->get_domain_by_ip($client_ip);
        $gateway = $this->get_gateway_info();
        $server_info = $this->get_server_info();

        // Generate HTML
        ob_start();
        ?>
        <div class="toolzoo-ip-checker-container">
            <div class="toolzoo-ip-checker-header">
                <h3><?php esc_html_e('IP-CHECKER', 'toolzoo'); ?></h3>
            </div>

            <div class="toolzoo-ip-checker-content">
                <!-- IP Address -->
                <div class="toolzoo-ip-checker-item">
                    <span class="toolzoo-ip-checker-label"><?php esc_html_e('Your IP Address', 'toolzoo'); ?>:</span>
                    <span class="toolzoo-ip-checker-value <?php echo esc_attr($this->is_private_ip($client_ip) ? 'private' : 'public'); ?>" data-value="<?php echo esc_attr($client_ip); ?>">
                        <?php echo esc_html($client_ip); ?>
                    </span>
                    <button class="toolzoo-ip-checker-copy-btn" data-target="ip"><?php esc_html_e('Copy', 'toolzoo'); ?></button>
                </div>

                <!-- Domain -->
                <div class="toolzoo-ip-checker-item">
                    <span class="toolzoo-ip-checker-label"><?php esc_html_e('Your Domain', 'toolzoo'); ?>:</span>
                    <span class="toolzoo-ip-checker-value" data-value="<?php echo esc_attr($domain); ?>">
                        <?php echo esc_html($domain); ?>
                    </span>
                    <button class="toolzoo-ip-checker-copy-btn" data-target="domain"><?php esc_html_e('Copy', 'toolzoo'); ?></button>
                </div>

                <!-- Gateway -->
                <div class="toolzoo-ip-checker-item">
                    <span class="toolzoo-ip-checker-label"><?php esc_html_e('Gateway', 'toolzoo'); ?>:</span>
                    <span class="toolzoo-ip-checker-value" data-value="<?php echo esc_attr($gateway); ?>">
                        <?php echo esc_html($gateway); ?>
                    </span>
                    <button class="toolzoo-ip-checker-copy-btn" data-target="gateway"><?php esc_html_e('Copy', 'toolzoo'); ?></button>
                </div>

                <!-- User Agent -->
                <div class="toolzoo-ip-checker-item">
                    <span class="toolzoo-ip-checker-label"><?php esc_html_e('User Agent', 'toolzoo'); ?>:</span>
                    <span class="toolzoo-ip-checker-value" data-value="<?php echo esc_attr($server_info['user_agent']); ?>">
                        <?php echo esc_html($server_info['user_agent']); ?>
                    </span>
                    <button class="toolzoo-ip-checker-copy-btn" data-target="useragent"><?php esc_html_e('Copy', 'toolzoo'); ?></button>
                </div>

                <!-- Server Port -->
                <div class="toolzoo-ip-checker-item">
                    <span class="toolzoo-ip-checker-label"><?php esc_html_e('Server Port', 'toolzoo'); ?>:</span>
                    <span class="toolzoo-ip-checker-value">
                        <?php echo esc_html($server_info['server_port']); ?>
                    </span>
                </div>

                <!-- Request Method -->
                <div class="toolzoo-ip-checker-item">
                    <span class="toolzoo-ip-checker-label"><?php esc_html_e('Request Method', 'toolzoo'); ?>:</span>
                    <span class="toolzoo-ip-checker-value">
                        <?php echo esc_html($server_info['request_method']); ?>
                    </span>
                </div>

                <!-- Server Protocol -->
                <div class="toolzoo-ip-checker-item">
                    <span class="toolzoo-ip-checker-label"><?php esc_html_e('Protocol', 'toolzoo'); ?>:</span>
                    <span class="toolzoo-ip-checker-value">
                        <?php echo esc_html($server_info['server_protocol']); ?>
                    </span>
                </div>
            </div>

            <div class="toolzoo-ip-checker-info">
                <p><?php esc_html_e('This tool displays your network connection information for diagnostic purposes.', 'toolzoo'); ?></p>
            </div>
        </div>
        <?php
        return ob_get_clean();
    }

    /**
     * Get client IP address
     *
     * @return string IP address or 'Unknown'
     */
    private function get_client_ip() {
        $ip = 'Unknown';

        // CloudFlare
        if (!empty($_SERVER['HTTP_CF_CONNECTING_IP'])) {
            $ip = sanitize_text_field($_SERVER['HTTP_CF_CONNECTING_IP']);
        }
        // X-Forwarded-For (Proxy)
        elseif (!empty($_SERVER['HTTP_X_FORWARDED_FOR'])) {
            $ips = array_map('trim', explode(',', sanitize_text_field($_SERVER['HTTP_X_FORWARDED_FOR'])));
            $ip = $ips[0];
        }
        // X-Real-IP (Proxy)
        elseif (!empty($_SERVER['HTTP_X_REAL_IP'])) {
            $ip = sanitize_text_field($_SERVER['HTTP_X_REAL_IP']);
        }
        // Direct connection
        else {
            $ip = sanitize_text_field($_SERVER['REMOTE_ADDR'] ?? 'Unknown');
        }

        // Validate IP
        if (filter_var($ip, FILTER_VALIDATE_IP)) {
            return $ip;
        }

        return 'Unknown';
    }

    /**
     * Get domain by IP address (reverse DNS lookup)
     *
     * @param string $ip IP address
     * @return string Domain name or 'Not Available'
     */
    private function get_domain_by_ip($ip) {
        if ($ip === 'Unknown') {
            return 'Not Available';
        }

        // Set timeout for gethostbyaddr
        $default_timeout = ini_get('default_socket_timeout');
        ini_set('default_socket_timeout', 2);

        $domain = @gethostbyaddr($ip);

        ini_set('default_socket_timeout', $default_timeout);

        // Check if lookup failed (returns IP if failed)
        if ($domain === $ip) {
            return 'Not Available';
        }

        // Validate domain format
        if (preg_match('/^([a-zA-Z0-9]([a-zA-Z0-9\-]*[a-zA-Z0-9])?\.)+[a-zA-Z0-9]([a-zA-Z0-9\-]*[a-zA-Z0-9])?$/', $domain)) {
            return $domain;
        }

        return 'Not Available';
    }

    /**
     * Get gateway information
     *
     * @return string Gateway interface
     */
    private function get_gateway_info() {
        return sanitize_text_field($_SERVER['GATEWAY_INTERFACE'] ?? 'Not Available');
    }

    /**
     * Get server information
     *
     * @return array Server information
     */
    private function get_server_info() {
        return array(
            'user_agent' => sanitize_text_field($_SERVER['HTTP_USER_AGENT'] ?? 'Not Available'),
            'server_port' => sanitize_text_field($_SERVER['SERVER_PORT'] ?? 'Not Available'),
            'request_method' => sanitize_text_field($_SERVER['REQUEST_METHOD'] ?? 'GET'),
            'server_protocol' => sanitize_text_field($_SERVER['SERVER_PROTOCOL'] ?? 'Not Available'),
            'remote_port' => sanitize_text_field($_SERVER['REMOTE_PORT'] ?? 'Not Available'),
            'server_software' => sanitize_text_field($_SERVER['SERVER_SOFTWARE'] ?? 'Not Available'),
        );
    }

    /**
     * Check if IP is IPv4
     *
     * @param string $ip IP address
     * @return boolean
     */
    private function is_ipv4($ip) {
        return (bool) filter_var($ip, FILTER_VALIDATE_IP, FILTER_FLAG_IPV4);
    }

    /**
     * Check if IP is IPv6
     *
     * @param string $ip IP address
     * @return boolean
     */
    private function is_ipv6($ip) {
        return (bool) filter_var($ip, FILTER_VALIDATE_IP, FILTER_FLAG_IPV6);
    }

    /**
     * Check if IP is private
     *
     * @param string $ip IP address
     * @return boolean
     */
    private function is_private_ip($ip) {
        if ($ip === 'Unknown') {
            return false;
        }

        // IPv4 private ranges
        $private_ipv4 = array(
            '10.0.0.0/8',
            '172.16.0.0/12',
            '192.168.0.0/16',
            '127.0.0.0/8',
        );

        // IPv6 private ranges
        $private_ipv6 = array(
            'fc00::/7',
            '::1/128',
        );

        if ($this->is_ipv4($ip)) {
            return filter_var($ip, FILTER_VALIDATE_IP, FILTER_FLAG_IPV4 | FILTER_FLAG_NO_PRIV_RANGE) === false;
        }

        if ($this->is_ipv6($ip)) {
            return filter_var($ip, FILTER_VALIDATE_IP, FILTER_FLAG_IPV6 | FILTER_FLAG_NO_PRIV_RANGE) === false ||
                   filter_var($ip, FILTER_VALIDATE_IP, FILTER_FLAG_IPV6 | FILTER_FLAG_RESERVED) !== false;
        }

        return false;
    }

    /**
     * Enqueue CSS/JS
     */
    private function enqueue_assets() {
        // CSS
        wp_enqueue_style(
            'toolzoo-ip-checker-css',
            TOOLZOO_PLUGIN_URL . 'assets/css/ip-checker.css',
            array(),
            TOOLZOO_VERSION
        );

        // JavaScript
        wp_enqueue_script(
            'toolzoo-ip-checker-js',
            TOOLZOO_PLUGIN_URL . 'assets/js/ip-checker.js',
            array(),
            TOOLZOO_VERSION,
            true
        );

        // Localize script for translations
        wp_localize_script(
            'toolzoo-ip-checker-js',
            'toolzooIpL10n',
            array(
                'copied' => __('Copied!', 'toolzoo'),
                'copy' => __('Copy', 'toolzoo'),
            )
        );
    }
}
