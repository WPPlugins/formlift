<?php

/**
 * Created by PhpStorm.
 * User: Adrian
 * Date: 2017-02-05
 * Time: 1:13 PM
 */
class Flp_Update_Manager
{
    function __construct()
    {
        add_action( 'plugins_loaded' , array('Flp_Update_Manager', 'update'));
    }

    public static function update(){

        //check if installing
        if (!get_option(FormLift::$option_key)){
            Flp_Utils::flp_update_option(FormLift::$version_key, FormLift::$version_id);
            Flp_Utils::flp_update_option(FormLift::$option_key, Flp_Settings::$default_settings);
        }

        $current_version = intval(FormLift::flp_get_current_version());


        //removed support for < 5900
        if ($current_version < 5900){
            Flp_Utils::flp_update_option(FormLift::$version_key, 5900);
        }if ($current_version < 5910){
            Flp_Utils::flp_update_option(FormLift::$version_key, 5910);
            $license = get_option('flp_license_key');
            if (!empty($license)){
                Flp_Utils::flp_update_option('flp_is_active', 'true');
            }
        }if ($current_version < 5912){
            Flp_Utils::flp_update_option(FormLift::$version_key, 5912);
        }if ($current_version < 5950){
            Flp_Utils::flp_update_option(FormLift::$version_key, 5950);
            Flp_Utils::flp_switch_style_option_value('input_radio_display', '', 'block');
            Flp_Utils::flp_switch_style_option_value('input_align', '', 'none');
            Flp_Utils::flp_switch_style_option_value('input_radio_scale', '', '1');
        }if ($current_version < 6000){
            Flp_Utils::flp_update_option(FormLift::$version_key, 6000);
        }if ($current_version < 6100){
            Flp_Utils::flp_update_option(FormLift::$version_key, 6100);
            wp_schedule_event(time()+60, 'daily', 'flp_verify_license');
            self::update_6_1_0();
        }if ($current_version < 6300){
            Flp_Utils::flp_update_option(FormLift::$version_key, 6300);
            Flp_Utils::flp_switch_style_option_value('input_focus_color', '', Flp_Settings::$default_settings['input_focus_color']);
        }if ($current_version < 6320){
            Flp_Utils::flp_update_option(FormLift::$version_key, 6320);
            self::update_6_2_0();
        }if ($current_version < 6500){
            Flp_Utils::flp_update_option(FormLift::$version_key, 6500);
            self::update_6_4_9();
        }
    }

    /**
     * A notice error displayed when updates come out.
     */
    public function no_license() {
        ?><div class="notice notice-error">
        <p>FormLift-PRO: Please enter your license key to receive updates. <a href="<?php echo get_site_url()?>/wp-admin/edit.php?post_type=infusion_form&page=default_settings_page">Enter Your License Key</a></p>
        <p>If you need a license key please go <a href="http://formlift.net/">here</a>. If you have already purchased FormLift then please check the email you purchased FormLift with to obtain your license key.</p>
        </div>
        <?php
    }

    /**
     * A notice error displayed when updates come out.
     */
    public function admin_notice_update() {
        ?><div class="notice notice-info is-dismissible">
            <h2>FormLift now has Input Based Redirects! WOOHOO! Set them up in the Edit Form area.</h2>
            <p>What does this mean? Well now depending on Different Dropdown or Radio button selections you can send people to different thank you pages!</p>
        </div>
        <?php
    }

    private static function update_6_1_0()
    {
        $forms = Flp_Utils::get_forms();
        foreach ($forms as $form){ //Flp_Form
            $code = htmlspecialchars_decode($form->form_code);
            if (preg_match('/data-mindate/', $code)){
                $code = preg_replace(
                    '/data-mindate.*data-maxdate.{2,6}/',
                    'data-type="date" data-changeYear="true" data-changeMonth="true" data-minDate="0" data-maxDate="90"'
                    ,$code
                );
                $code = htmlentities($code);
                Flp_Utils::flp_update_meta($form->ID, 'form_code', $code);
            }
        }
    }

    private static function update_6_2_0()
    {
        $forms = Flp_Utils::get_forms();
        foreach ($forms as $form) { //Flp_Form
            $code = htmlspecialchars_decode($form->form_code);
            Flp_Utils::flp_update_meta($form->ID, 'form_code', $code);
        }
    }

    private static function update_6_4_9()
    {
        $forms = Flp_Utils::get_forms();
        foreach ($forms as $form) { //Flp_Form
            $code = $form->form_code;
            $code = Flp_Form_Builder::clean_infusionsoft_form_html($code, $form->ID);
            Flp_Utils::flp_update_meta($form->ID, 'form_code', $code);
        }
    }
}