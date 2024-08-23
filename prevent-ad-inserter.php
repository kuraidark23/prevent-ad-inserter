<?php
/**
 * Plugin Name: Prevent Ad Inserter Installation
 * Description: Prevents the installation and activation of the Ad Inserter plugin.
 * Version: 1.0.0
 * Author: KD23
 * Author URI: https://tudominio.com
 * License: GPL v2 or later
 * License URI: https://www.gnu.org/licenses/gpl-2.0.html
 * Text Domain: prevent-ad-inserter
 * Domain Path: /languages
 */

// If this file is called directly, abort.
if (!defined('WPINC')) {
    die;
}

/**
 *Init Class.
 */
class Prevent_Ad_Inserter {

    /**
     * Init the plugin.
     */
    public function __construct() {
        add_action('admin_init', array($this, 'check_ad_inserter'));
        add_filter('plugin_action_links', array($this, 'modify_plugin_actions'), 10, 4);
        add_action('activate_plugin', array($this, 'block_activation'), 10, 1);
    }

    /**
     * Verify if Ad Inserter is installed and deactivated if necessary.
     */
    public function check_ad_inserter() {
        if (is_plugin_active('ad-inserter/ad-inserter.php')) {
            deactivate_plugins('ad-inserter/ad-inserter.php');
            add_action('admin_notices', array($this, 'deactivation_notice'));
        }
    }

    /**
     * Show a notice when Ad Inserter is deactivated.
     */
    public function deactivation_notice() {
        ?>
        <div class="notice notice-warning is-dismissible">
            <p><?php _e('Ad Inserter has been deactivated because it is not allowed on this site.', 'prevent-ad-inserter'); ?></p>
        </div>
        <?php
    }

    /**
     * Modify the plugin actions available in the list of plugins.
     */
    public function modify_plugin_actions($actions, $plugin_file, $plugin_data, $context) {
        if (strpos($plugin_file, 'ad-inserter.php') !== false) {
            unset($actions['activate']);
            $actions['notice'] = '<span style="color: red;">' . __('No installation of Ad Inserter is allowed.', 'prevent-ad-inserter') . '</span>';
        }
        return $actions;
    }

    /**
     * Block the activation of Ad Inserter.
     */
    public function block_activation($plugin) {
        if (strpos($plugin, 'ad-inserter.php') !== false) {
            wp_die(
                __('No activation of Ad Inserter is allowed on this site.', 'prevent-ad-inserter'),
                __('Plugin blocked', 'prevent-ad-inserter'),
                array('back_link' => true)
            );
        }
    }
}

// Init the plugin
new Prevent_Ad_Inserter();