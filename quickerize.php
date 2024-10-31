<?php
/*
Plugin Name: Quickerize
Version: 1.0.0
Author: Quickerize
Author URI: https://quickerize.com
Description: Instantly increase your revenue with 3x faster page loads.
 */

if (!defined('ABSPATH')) {
    exit;
}
if (!class_exists('QUICKERIZE')) {

    class QUICKERIZE {

        var $plugin_version = '1.0.0';

        function __construct() {
            define('QUICKERIZE_VERSION', $this->plugin_version);
            $this->plugin_includes();
        }

        function plugin_includes() {
            if (is_admin()) {
                add_filter('plugin_action_links', array($this, 'plugin_action_links'), 10, 2);
            }
            add_action('admin_init', array($this, 'settings_api_init'));
            add_action('admin_menu', array($this, 'add_options_menu'));
            add_action('wp_footer', array($this, 'add_tracking_code'));
        }

        function plugin_url() {
            if ($this->plugin_url)
                return $this->plugin_url;
            return $this->plugin_url = plugins_url(basename(plugin_dir_path(__FILE__)), basename(__FILE__));
        }

        function plugin_action_links($links, $file) {
            if ($file == plugin_basename(dirname(__FILE__) . '/quickerize.php')) {
                $links[] = '<a href="options-general.php?page=quickerize-settings">'.__('Settings', 'quickerize').'</a>';
            }
            return $links;
        }
        function add_options_menu() {
            if (is_admin()) {
                add_options_page(__('Quickerize', 'quickerize'), __('Quickerize', 'quickerize'), 'manage_options', 'quickerize-settings', array($this, 'options_page'));
            }
        }
        function settings_api_init(){
            	register_setting( 'quickerizesettings', 'quickerize_settings' );
                
                add_settings_section(
                        'quickerize_code_section', 
                        __('General Settings', 'quickerize'), 
                        array($this, 'quickerize_settings_section_callback'), 
                        'quickerizesettings'
                );
                
                add_settings_field( 
                        'quickerize_id', 
                        __('API Key', 'quickerize'), 
                        array($this, 'quickerize_id_render'), 
                        'quickerizesettings', 
                        'quickerize_code_section' 
                );
        }
        function quickerize_id_render() { 
            $options = get_option('quickerize_settings');            
            ?>
            <input type='text' name='quickerize_settings[quickerize_id]' value='<?php echo $options['quickerize_id']; ?>'>
            <p class="description">Enter the API Key found in Quickerize's control panel.</p>
            <?php
        }

        function options_page() {
            ?>           
            <div class="wrap">               
            <h2>Quickerize - v<?php echo $this->plugin_version; ?></h2> 
            <div class="update-nag">Please visit the <a href="https://dashboard.quickerize.com/integrations/wordpress.php" target="_blank">documentation page</a> for setup instructions.</div>
            <form action='options.php' method='post'>
            <?php
            settings_fields( 'quickerizesettings' );
            do_settings_sections( 'quickerizesettings' );
            submit_button();
            ?>
            </form>
            </div>
            <?php
        }
        
        function add_tracking_code() {
            $options = get_option( 'quickerize_settings' );
            $quickerize_id = $options['quickerize_id'];
            if (isset($quickerize_id) && !empty($quickerize_id)){
				echo wp_get_script_tag(array(
					'type' => "module",
					'src' => "https://cloud.quickerize.com/wordpress/?apikey=$quickerize_id"
				));
			}
        }

    }

    $GLOBALS['quickerize_code'] = new QUICKERIZE();
}
