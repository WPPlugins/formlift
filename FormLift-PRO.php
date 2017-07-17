<?php
/*
 * Plugin Name: FormLift
 * Description: The Ultimate Web Form Solution for WordPress and Infusionsoft. Style your web forms, create personalized pages, and create epic automation with them too.
 * Version: 6.4.12
 * Author: Adrian Tobey
 * Plugin URI: https://formlift.net
 * Author URI: https://formlift.net/blog
 *
 * Copyright (c) Training Business Pros 2016
 * 25 Lesmill Road, Toronto, Ontario, July 2016
 * License: GPLv2
 *
 * For Support Please send emails to info@formlift.net or visit https://formlift.net/contact-us
*/

define('FLP_DOWNLOAD_URL', 'http://formlift.net/formlift-plugin-download/metadata.json');
define('FLP_PLUGIN_NAME', 'formlift');
define('FLP_TUTORIALS_URL', 'http://formlift.net/video-tutorials');
define('FLP_SETTINGS_PAGE_URL', get_admin_url().'edit.php?post_type=infusion_form&page=default_settings_page');
define('FLP_SITE_URL', 'http://formlift.net');
define('FLP_PLUGIN_DIR_URI', plugin_dir_url(__FILE__));

foreach (glob(plugin_dir_path(__FILE__) . "includes/*.php") as $filename)
{
    include $filename;
}

/**
 * Class FormLift
 *
 * Responsible for initializing the plugin including scripts, style sheets, constants, and shortcodes
 */
class FormLift
{
    public static $version = '6.4.12';
    public static $js_version = '6.4.16';
    public static $css_version = '6.4.13';
    public static $version_id = 6500;
    public static $version_key = "formlift_version";
    public static $option_key = 'formlift_options';

    /**
     * FormLift constructor.
     *
     * adds all actions and registers the activation/deactivation hooks!
     */
    public function __construct()
    {
        register_activation_hook(__FILE__, array('FormLift', 'activate'));
        register_deactivation_hook(__FILE__, array('FormLift', 'deactivate'));
        add_action('wp_enqueue_scripts', array('FormLift', 'add_form_scripts'));
        add_action('wp_enqueue_scripts', array('FormLift', 'add_form_styles'));
        add_action('wp_enqueue_scripts', array('FormLift', 'add_constants'));
        add_action('admin_enqueue_scripts', array('FormLift', 'add_admin_scripts'));
        add_action('admin_enqueue_scripts', array('FormLift', 'add_admin_styles'));
        add_action('admin_enqueue_scripts', array('FormLift', 'add_form_scripts'));
        add_action('admin_enqueue_scripts', array('FormLift', 'add_form_styles'));
        add_action('admin_enqueue_scripts', array('FormLift', 'add_constants'));
        add_shortcode('infusion_form', array('FormLift', 'flp_form_shortcode'));
        add_shortcode('infusion_field', array('FormLift', 'flp_field_shortcode'));
    }

    /**
     * Returns the current Version of FormLift from the database. If the version does not exist, then
     * its an old version of FormLift so set it to 0 a perform necessary updates
     *
     * @return int|mixed
     */
    public static function flp_get_current_version()
    {
        if (!get_option(self::$version_key)) {
            return 0;
        } else {
            return get_option(self::$version_key);
        }
    }

    /**
     * Runs when plugin is first activated. Gives permissions to edit forms only to admin
     *
     * @return void
     */
    public static function activate()
    {
        if (is_user_logged_in() && current_user_can('activate_plugins')) {
            $admin = get_role('administrator');
            $admin->add_cap('edit_form');
            $admin->add_cap('edit_forms');
            $admin->add_cap('delete_form');
            $admin->add_cap('manage_forms');
            $admin->add_cap('read_form');
            $admin->add_cap('read_form');
            $admin->add_cap('create_forms');
        }
    }

    /**
     * Runs when deactivating FormLift. Removes capabilities and dequeues scripts
     *
     * @return void
     */
    public static function deactivate()
    {
        if (is_user_logged_in() && current_user_can('delete_plugins')) {
            $admin = get_role('administrator');
            $admin->remove_cap('edit_form');
            $admin->remove_cap('delete_form');
            $admin->remove_cap('manage_forms');
            $admin->remove_cap('read_forms');
            $admin->remove_cap('create_forms');
        }
    }

    /**
     * Adds ajax_url to wp_head as well as google site_key
     *
     * @return void
     */
    public static function add_constants()
    {
        wp_localize_script( 'formlift-form-functions', 'flp_ajax_object', array( 'ajax_url' => admin_url( 'admin-ajax.php' ) ));
        $key = Flp_Utils::get_setting('google_captcha_key');
        wp_localize_script( 'formlift-form-functions', 'flp_google_site_key', array('key' => $key));
    }

    /**
     * Runs the Form Scripts for form init
     *
     * @return void
     */
    public static function add_form_scripts()
    {
        wp_enqueue_script('jquery');
        wp_register_script( 'formlift-form-functions', plugins_url( 'js/flp-form-functions.js', __FILE__ ), array(), self::$js_version);
        wp_enqueue_script( 'formlift-timezone-functions', plugins_url( 'js/resources/jstz.js', __FILE__ ), array(), self::$js_version);
        wp_enqueue_script( 'formlift-form-functions' );
        wp_enqueue_script( 'formlift-form-init-functions', plugins_url( 'js/flp-form-init-functions.js', __FILE__ ), array(), self::$js_version);
        wp_enqueue_script( 'formlift-form-checker-functions', plugins_url( 'js/flp-validity-checker-functions.js', __FILE__ ), array(), self::$js_version);
        wp_enqueue_script( 'formlift-recaptcha', 'https://www.google.com/recaptcha/api.js?onload=flpOnloadCallback&render=explicit');
        wp_enqueue_script('jquery-ui-datepicker');
    }

    /**
     * Adds the admin scripts to the backend for init functions
     *
     * @return void
     */
    public static function add_admin_scripts()
    {
        wp_enqueue_script( 'formlift-form-redirect-functions', plugins_url( 'js/admin/flp-create-redirect-init.js', __FILE__ ), array(), self::$js_version );
        wp_enqueue_script( 'formlift-notice-functions', plugins_url( 'js/admin/flp-notice-functions.js', __FILE__ ), array(), self::$js_version );
        wp_enqueue_script( 'flp-script-handle', plugins_url('js/admin/formlift-admin.js', __FILE__ ), array( 'wp-color-picker' ), self::$js_version, true );
        wp_enqueue_script( 'flp-time-picker', 'https://cdnjs.cloudflare.com/ajax/libs/jquery-timepicker/1.10.0/jquery.timepicker.min.js');
        wp_enqueue_script( 'wp-color-picker-alpha',  plugins_url( 'js/resources/wp-color-picker-alpha.js', __FILE__), array( 'wp-color-picker' ), '1.2.3');
    }

    /**
     * Adds the admin css to backend
     *
     * @return void
     */
    public static function add_admin_styles()
    {
        wp_enqueue_style( 'flp-admin-css', plugins_url('css/admin.css', __FILE__ ), array(), self::$css_version );
        wp_enqueue_style( 'flp-admin-settings-css', plugins_url('css/settings-page-css.css', __FILE__ ), array(), self::$css_version );
        wp_enqueue_style( 'wp-color-picker' );
        wp_enqueue_style( 'flp-time-picker-style', 'https://cdnjs.cloudflare.com/ajax/libs/jquery-timepicker/1.10.0/jquery.timepicker.min.css');
    }

    /**
     * register styles for front end
     *
     * @return void
     */
	public static function add_form_styles()
    {
		wp_enqueue_style('jquery-ui-datepicker-css', "https://code.jquery.com/ui/1.12.1/themes/base/jquery-ui.css");
	}

    /**
     * Returns the form HTML and init to display on a page, if a non valid Id is displayed show a message that the form
     * is not associated with any current form in the data base
     *
     * @param $atts array Only contains the ID of the form to display
     * @return string the shortcode
     */
	public static function flp_form_shortcode($atts)
    {
		$atters = shortcode_atts( array(
			'id' => ''
		), $atts);
        $post_type = get_post_type($atters['id']);
        if ('infusion_form' == $post_type){
            $form = new Flp_Form($atters['id']);
            $the_content = $form->shortcode();
        } else {
            $the_content = "The ID {$atters['id']} is not related to any Infusionsoft Form...";
        }
        return $the_content;
	}

    /**
     * Displays user data based on information passed from Infusionsoft emails OR data captured by an Infusionsoft form
     * conditionally displays content based on it's existence
     *
     * @param $atts array conditional ID of the user data being requested
     * @param null $content a larger body of text that may or may not exist
     * @return mixed
     */
    public static function flp_field_shortcode($atts, $content=null)
    {
        $atters = shortcode_atts(array(
            'name' => '',
            'value' => '',
            'default' => ''
        ), $atts);

        if ($content) {
            if (!empty($atts['name'])){
                $val = Flp_Utils::get_cookie_or_param($atts['name']);
                if (empty($val)){
                    return '';
                } elseif (!empty($atts['value']) && $val != $atts['value']){
                    return '';
                }
            }

            preg_match_all('/%%[\w\d]+%%/', $content, $matches);
            $actual_matches = $matches[0];

            foreach ($actual_matches as $pattern) {
                $field = str_replace('%%', '', $pattern);
                $value = Flp_Utils::get_cookie_or_param($field);
                if (empty($value)) {
                    return '';
                }
                $content = preg_replace('/' . $pattern . '/', $value, $content);
            }
            $content = do_shortcode($content);
        } else {
            $val = Flp_Utils::get_cookie_or_param($atts['name']);
            if (empty($val)){
                return '';
            } elseif (!empty($atts['value']) && $val != $atts['value']){
                return '';
            }
            $content = $val;
        }
        return $content;
    }
}

/**
 * Create notice manager to handle exceptions, ads, and promotions
 * check for for level compatibility
 *
 * Do Init tasks. Create all instances of classes...
 * Init Infusionsoft SDK connection
 */
$flp_notice_manager = new Flp_Notice_Manager();

if (PHP_MAJOR_VERSION <= 5 && PHP_MINOR_VERSION < 6){
    $flp_invalid_php_notice = new Flp_Notice(
        "Uh oh... your <strong>PHP level must be 5.6 or greater</strong> for the Infusionsoft API to work properly. Please update
        your PHP level to 5.6 or better (7+ is recommended)! You can use 
        <a href='https://wordpress.org/plugins/php-compatibility-checker/'>this plugin</a> to determine if it is safe 
        for you to upgrade your PHP.",
        'flp_php_upgrade',
        FLP_NOTICE_ERROR,
        false
    );
} else {

    if( !class_exists('Infusionsoft_Classloader') ){
        include(plugin_dir_path(__FILE__).'Infusionsoft/infusionsoft.php');
    }

    $FormLift_Update_Manager = new Flp_Update_Manager();
    $flp_form_page = new Flp_Fill_Form();
    $flp_form_builder = new Flp_Form_Builder();
    $flp_post_type = new Flp_Form_Post_Type();
    //$flp_appt_post_type = new Flp_Appt_Post_Type();
    $flp_license_manager = new Flp_License_Manager();
    $flp_settings = new Flp_Settings();
    $flp_settings_page = new Flp_Settings_Page();
    $flp_tracking = new Flp_Tracking();
    $flp_redirect_manager = new Flp_Redirect_Manager();
    $formlift = new FormLift();

    if (Flp_Utils::get_setting('infusionsoft_app_name') && Flp_Utils::get_setting('infusionsoft_api_key'))
    {
        try{
            //Props to Novak Solutions for providing the SDK
            Infusionsoft_AppPool::addApp(new Infusionsoft_App(Flp_Utils::get_setting('infusionsoft_app_name') . '.infusionsoft.com', Flp_Utils::get_setting('infusionsoft_api_key')));
        } catch(Infusionsoft_Exception $e) {
            $flp_sdk_error = new Flp_Notice(
                $e->getMessage(),
                'flp_sdk_error',
                FLP_NOTICE_ERROR,
                false
            );
        }
    } else {
        $flp_settings_url = FLP_SETTINGS_PAGE_URL;
        $flp_no_api_notice = new Flp_Notice(
            "Please connect your Infusionsoft application in the <a href='$flp_settings_url'>settings</a>",
            'flp_api_int',
            FLP_NOTICE_ERROR,
            false
        );
    }

    if (!Flp_License_Manager::is_licensed()){
        $notice = new Flp_Notice(
            "It looks like you're running a free version of FormLift. Do you know about all the other cool 
                things you can do with FormLift when you <a href='https://formlift.net'>license</a> it? 
                <a href='https://formlift.net'>Check it out!</a>",
            'flp_free_version',
            FLP_NOTICE_INFO,
            true,
            'infusion_form',
            604800
        );
    }

    //TODO make 1 month
    $flp_leave_a_review_notice = new Flp_Notice(
        "Are you enjoying FormLift? If you've found that this awesome little tool has made your life easier, why
        not let others know? <a href='https://wordpress.org/support/plugin/formlift/reviews/'>Leave a review</a> so everyone can see the value this brings to the Infusionsoft Community! 
        And also I'd be really grateful :)",
        'flp_leave_review',
        FLP_NOTICE_INFO,
        true,
        'infusion_form',
        1814400
    );
}