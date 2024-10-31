<?php
/*
Plugin Name: Redpen Widget
Plugin URI:  https://wordpress.org/plugins/redpen-web-widget/
Description: The plugin installs a widget that helps you collect feedback, support request and bugs in a WordPress website.
Version:     1.1.0
Author:      Ajmera Infotech Inc.
Author URI:  https://www.ajmerainfotech.com
License:     GPLv2
License URI: https://www.gnu.org/licenses/gpl-2.0.html
Requires PHP: 7.0
Requires at Least: 4.7
Tested up to: 5.8
*/

if (is_admin()) $redpen_web_widget_page = new Redpen();

register_deactivation_hook(__FILE__, 'remove_redpen_web_widget_script');
register_uninstall_hook(__FILE__, 'remove_redpen_script_options');

function add_async_attribute_to_redpen_script($tag, $handle)
{
    // add script handles to the array below
    $scripts_to_async = array(
        'redpen-widget-script'
    );

    foreach ($scripts_to_async as $async_script) {
        if ($async_script === $handle) {
            return str_replace(' src', 'defer src', $tag);
        }
    }
    return $tag;
}
add_filter('script_loader_tag', 'add_async_attribute_to_redpen_script', 10, 2);

/* This function will add the script to <head> tag of all the pages of the wordpress site */
function add_redpen_script_code()
{
    $plugin_options = get_option('redpen_option_name');
    $widgetId = get_option('widget_Id');

    if (!empty($widgetId)) {
        wp_enqueue_script('redpen-widget-script', 'https://app.redpen.ai/redpenWebWidget.js', array(), false, true);
        wp_add_inline_script('redpen-widget-script', 'window.redpenWidgetConfig = {"widgetId":"' . $widgetId . '"}', 'before');
    }
}
add_action('wp_enqueue_scripts', 'add_redpen_script_code');

function remove_redpen_script_options()
{
    delete_option('widget_Id');
}

function remove_redpen_web_widget_script()
{
    remove_action('wp_footer', 'add_redpen_script_code');
    update_option('redpen_plugin_notice', 'false');
}

class Redpen
{
    /**
     * Holds the values to be used in the fields callbacks
     */
    private $options;
    /**
     * Start up
     */
    public function __construct()
    {
        add_action('admin_menu', array(
            $this,
            'add_plugin_page'
        ));
        add_action('admin_init', array(
            $this,
            'page_init'
        ));
        add_action('admin_notices', array(
            $this,
            'redpen_plugin_admin_notices'
        ));
        add_action('admin_print_styles', array(
            $this,
            'add_redpen_plugin_styles'
        ));
        add_action('admin_print_scripts', array(
            $this,
            'add_redpen_plugin_scripts'
        ));
    }

    public function add_redpen_plugin_scripts()
    {
        wp_enqueue_script('redpen_web_widget_js', plugins_url('/js/redpen-web-widget.js', __FILE__));
    }

    public function add_redpen_plugin_styles()
    {
        wp_enqueue_style('redpen_web_widget_css', plugins_url('/css/redpen-web-widget.css', __FILE__));
    }

    public function redpen_plugin_admin_notices()
    {
        $isMessageShown = get_option('redpen_plugin_notice');
        if ((!$isMessageShown || $isMessageShown == 'false') && !is_plugin_active('plugin-directory/plugin-file.php')) {
            echo "<div><p>Your redpen plugin is successfully installed. Please go to Settings for the <b>Redpen</b> plugin to add your Service Connection Id to run redpen web widget on your website.</p></div>";
            update_option('redpen_plugin_notice', 'true');
        }
    }

    /**
     * Add options page
     */
    public function add_plugin_page()
    {
        add_menu_page('redpen Setting Page', 'Redpen Widget', 'manage_options', 'redpen-web-widget-plugin', array(
            $this,
            'create_redpen_admin_page'
        ), plugins_url('/img/fav-icon.png', __FILE__));
    }

    /**
     * Options page callback
     */
    public function create_redpen_admin_page()
    {
        // Set class property
        $this->options = get_option('redpen_option_name');
?>
        <div class="redpen-wrap">
            <h2 class="redpen-section-header"><a href="http://redpen.ai/" target="_blank"><img src="<?php echo esc_url(plugins_url('/img/redpen-logo.png', __FILE__)); ?>" /></a></h2>
            <h2 class="feedback-tool"><img class="redpen-iconimg" src="<?php echo esc_url(plugins_url('/img/redpen-icon.png', __FILE__)); ?>" /> Redpen Widget</a></h2>
            <form method="post" id="webWidgetForm" class="redpen-section" action="options.php">

                <?php
                // This prints out all hidden setting fields
                settings_fields('redpen_option');
                settings_fields('redpen-web-widget-plugin-settings');
                do_settings_sections('redpen-settings-admin');
                do_settings_sections('redpen-web-widget-plugin-settings');
                ?>

                <div class="redpen-whitebox">
                    <div class="redpen-box">
                        <h4> Configuration </h4>
                        <p class="redpen-description2"> Add the widget ID in the box below and click "Configure" button to add widget to your website.</p>
                        <h4 class="widgetid">Widget ID</h4>
                        <div class="wrapper">
                            <div class="submit-button">
                           
                           <?php
                                if(get_option('widget_Id')) {
                                $classname = "btnremove";
                                } else {
                                $classname = "btnconfig";
                                }

                            ?>

                            <button id="rpconfigurebtn" class="<?php echo $classname; ?>" >

                               

                                    <?php
                                    if (get_option('widget_Id')) {
                                        echo "Remove";
                                    } else {
                                        echo "Configure";
                                    }
                                    ?>

                                </button>
                            </div>
                        </div>
                        <input class="redpen-service-conn-box" type="text" id="webWidgetId" name="widget_Id" onkeyup="changeTheColorOfRedpenConigurebtn()" value="<?php echo esc_attr(get_option('widget_Id')); ?>" required="" />
                    </div>

                    <div class="redpen-inner-box">
                        <div class="error-text">
                            <a class="redpen-link" href="https://support.redpen.ai/hc/en-us/articles/5031716144781" target="_blank">How to get the Redpen Widget ID?</a>
                        </div>
                    </div>
            </form>
        </div>



        <div class="redpen-whitebox2">
            <div class="redpen-stepbox">
                <div class="redpen-step-border">
                    <div class="redpen-box2">
                        <redpen-h5> HOW TO USE </redpen-h5>
                        <p class="redpen-steptitle"> How to get a Redpen Widget ID?</p>
                        <p class="redpen-desc">Read the <a href="https://support.redpen.ai/hc/en-us/articles/5031716144781" target="_blank" style="color: #f44336;">documentation</a> to get the Widget ID. If you don't have any existing widgets, <a href="https://support.redpen.ai/hc/en-us/articles/5031791700749" target="_blank" style="color:#f44336;">create a new widget</a>. </p>
                    </div>
                </div>

                <div class="redpen-step-border">
                    <div class="redpen-box2">
                        <p class="redpen-steptitle"> How to add/remove the widget to the website.</p>
                        <p class="redpen-desc">To add, paste the widget ID in the text box and click "Configure". To remove, click on the "Remove" button. </p>
                    </div>
                </div>


                <div class="redpen-step-border">
                    <div class="redpen-box2">
                        <p class="redpen-steptitle"> How to use Redpen widget?</p>
                        <p class="redpen-desc">Refer this <a href="https://www.youtube.com/watch?v=DfjkURfrtzM&feature=emb_rel_end" target="_blank" style="color:#f44336;"> video</a>.</p>
                    </div>
                </div>

            </div>
        </div>
        </div>




<?php
    }

    /**
     * Register and add settings
     */
    public function page_init()
    {

        register_setting('redpen-web-widget-plugin-settings', 'widget_Id');

        register_setting(
            'redpen_option', // Option
            'redpen_option_name', // Option name
            array(
                $this,
                'use_field_value'
            ) // use_field_value
        );
        add_settings_section(
            'setting_section_id', // ID
            '', // Title
            array(
                $this,
                'print_section_info'
            ), // Callback
            'redpen-settings-admin'
            // Page
        );
    }

    /**
     * Use each setting field as needed
     *
     * array $input Contains all settings fields as array keys
     */
    public function use_field_value($input)
    {
        $new_input = array();
        if (isset($input['widget_Id'])) {
            if (!empty($input['widget_Id'])) {
                $new_input['widget_Id'] = $input['widget_Id'];
                return $new_input;
            } else {
                $new_input['widget_Id'] = "";
                return $new_input;
            }
        }
    }

    /** 
     * Print the Section text
     */
    public function print_section_info()
    {

        echo '<p class="redpen-description">With Redpen Widget, it is now easy to collect feedback of your website directly to your favorite issues tracking services like Jira, Azure DevOps, GitHub Issues, and more.</p>
                    <p class="redpen-description">Whether it is your development team, stakeholders, or website visitors, Redpen Widget allows your website users to submit their feedback.</p> 
             ';
    }


    /** 
     * Get the settings option array and print one of its values
     */

    public function id_number_callback()
    {
        printf('<input type="text" id="widget_Id" name="redpen_option_name[widget_Id]" value="%s" />', isset($this->options['widget_Id']) ? esc_attr($this->options['widget_Id']) : '');
    }
}

function add_plugin_link($actions, $plugin_file)
{
    static $plugin;
    if (!isset($plugin)) $plugin = plugin_basename(__FILE__);
    if ($plugin == $plugin_file) {
        $actions[] = '<a href="' . esc_url(get_admin_url(null, 'admin.php?page=redpen-web-widget-plugin')) . '">Settings</a>';
        $actions[] = '<a href="https://www.redpen.ai/support/" target="_blank">Support</a>';
    }
    return $actions;
}
add_filter('plugin_action_links', 'add_plugin_link', 10, 5);
