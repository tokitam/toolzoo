<?php
/**
 * ToolZoo Tool Template
 *
 * This template is used to display individual tools via path-based URLs.
 * Example: /toolzoo/password, /toolzoo/nengo, etc.
 *
 * @package ToolZoo
 */

if (!defined('ABSPATH')) {
    exit;
}

// Get the tool name from query variable
$tool = get_query_var('toolzoo_tool');

// Debug: Log the tool name for troubleshooting
if (defined('WP_DEBUG_LOG') && WP_DEBUG_LOG) {
    error_log('ToolZoo Template: Tool = ' . $tool);
    error_log('ToolZoo Template: Current URL = ' . $_SERVER['REQUEST_URI']);
    error_log('ToolZoo Template: Query vars = ' . print_r($_GET, true));
}

// Validate and get the tool content
$tool_content = '';

if (!empty($tool)) {
    switch ($tool) {
        case 'password':
            $generator = new Toolzoo_Password_Generator();
            $tool_content = $generator->render();
            break;

        case 'nengo':
            $list = new Toolzoo_Nengo_List();
            $tool_content = $list->render();
            break;

        case 'worldclock':
            $worldclock = new Toolzoo_Worldclock();
            $tool_content = $worldclock->render();
            break;

        case 'json':
            $processor = new Toolzoo_JSON_Processor();
            $tool_content = $processor->render();
            break;

        case 'bmi':
            $calculator = new Toolzoo_BMI_Calculator();
            $tool_content = $calculator->render();
            break;

        case 'ip':
            $checker = new Toolzoo_IP_Checker();
            $tool_content = $checker->render();
            break;

        default:
            // Invalid tool, return 404
            wp_safe_remote_head(home_url(), array('redirection' => 0));
            status_header(404);
            get_template_part('404');
            exit;
    }
}

// If no tool content, show 404
if (empty($tool_content)) {
    status_header(404);
    get_template_part('404');
    exit;
}

?>
<!DOCTYPE html>
<html <?php language_attributes(); ?>>
<head>
    <meta charset="<?php bloginfo('charset'); ?>">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php wp_title('|', true, 'right'); ?><?php bloginfo('name'); ?></title>
    <link rel="profile" href="https://gmpg.org/xfn/11">
    <?php wp_head(); ?>
    <style>
        body {
            margin: 0;
            padding: 20px;
            background-color: #f5f5f5;
        }

        .toolzoo-container {
            max-width: 1200px;
            margin: 0 auto;
            background-color: white;
            padding: 30px;
            border-radius: 8px;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
        }

        .toolzoo-title {
            text-align: center;
            margin-bottom: 30px;
            color: #333;
        }

        .toolzoo-content {
            min-height: 400px;
        }

        @media (max-width: 768px) {
            body {
                padding: 10px;
            }

            .toolzoo-container {
                padding: 15px;
            }

            .toolzoo-content {
                min-height: auto;
            }
        }
    </style>
</head>
<body <?php body_class('toolzoo-page'); ?>>
    <?php wp_body_open(); ?>

    <div class="toolzoo-container">
        <div class="toolzoo-content">
            <?php echo $tool_content; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
        </div>
    </div>

    <?php wp_footer(); ?>
</body>
</html>
