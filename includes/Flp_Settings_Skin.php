<?php

/**
 * Created by PhpStorm.
 * User: Adrian
 * Date: 2017-02-26
 * Time: 1:47 PM
 */
class Flp_Settings_Skin
{
    var $form;
    var $sections = array();
    var $headers;

    /**
     * creates and adds all the settings sections to the settings panel.
     *
     * Flp_Settings_Skin constructor.
     * @param null $form Flp_Form
     */
    function __construct($form=null)
    {
        if(empty($form)){
            $form = new Flp_Form();
            $this->form = $form;
        } else {
            $this->form = $form;
            //add_filter('flp_required_fields', array($this, 'get_form_required_fields'));
        }
        $infusionsoft_settings = new Flp_Settings_Section('flp_infusionsoft', 'Infusionsoft API', Flp_Settings::get_infusionsoft_settings(), $form);
        $button_settings = new Flp_Settings_Section('flp_button_settings', 'Button CSS', Flp_Settings::get_button_fields(), $form);
        $input_settings = new Flp_Settings_Section('flp_input_settings', 'Input CSS', Flp_Settings::get_input_fields(), $form);
        $label_settings = new Flp_Settings_Section('flp_label_settings', 'Label CSS', Flp_Settings::get_label_fields(), $form);
        $form_settings = new Flp_Settings_Section('flp_form_settings', 'Form CSS', Flp_Settings::get_form_fields(), $form);
        $field_settings = new Flp_Settings_Section('flp_field_settings', 'Field CSS', Flp_Settings::get_field_fields(), $form);
        $error_settings = new Flp_Settings_Section('flp_error_settings', 'Error Settings & CSS', Flp_Settings::get_error_fields(), $form);
        $captcha_settings = new Flp_Settings_Section('flp_google_captcha', 'Google Captcha', Flp_Settings::get_google_settings(), $form, true);
        $tracking_Settings = new Flp_Settings_Section('flp_tracking_settings', 'Tracking Settings', Flp_Settings::get_tracking_fields(), $form);
        $pop_settings = new Flp_Settings_Section('flp_auto_population_settings', 'Auto Population Settings', Flp_Settings::get_auto_population_settings(), $form);
        $required_field_Settings = new Flp_Settings_Section('flp_required_field_settings', 'Required Fields', Flp_Settings::get_required_field_settings(), $form);

        //build settings panel
        $this->add_section($infusionsoft_settings);
        $this->add_section($captcha_settings);
        $this->add_section($button_settings);
        $this->add_section($input_settings);
        $this->add_section($label_settings);
        $this->add_section($form_settings);
        $this->add_section($field_settings);
        $this->add_section($error_settings);
        $this->add_section($tracking_Settings);
        $this->add_section($pop_settings);
        $this->add_section($required_field_Settings);

        foreach ($this->sections as $section){
            add_filter('flp_section_headers', array($section, 'add_header'));
        }
        add_filter('flp_section_headers', array('Flp_Settings_Skin', 'add_headers'));

        $header = new Flp_Settings_Header($this->form);
        $this->headers = $header->get_headers();
    }

    /**
     * Echos the panel
     *
     * @return string
     */
    function __toString()
    {
        return "<div id='flp-custom-settings' class='flp-settings-page'>".$this->build_navigation().$this->build_settings_window()."</div>";
    }

    /**
     * Builds the side navigation for the settings panel
     *
     * @return string
     */
    private function build_navigation()
    {
        $logo_uri = FLP_PLUGIN_DIR_URI.'assets/icon-256x256.png';
        $content = "<nav id='flp-header' class='flp-nav'><div class='flp-logo'><img width='150' src='$logo_uri'></div>";
        foreach ($this->headers as $header){
            $content.= "$header";
        }
        $content.= "<div style='padding:17px'>".get_submit_button('SAVE CHANGES', 'large')."</div>";
        return $content."</nav>";
    }

    /**
     * Creates container for the settings panel
     *
     * @return string
     */
    private function build_settings_window()
    {
        $content = "<div class='flp-sections-container'>";
        foreach ($this->sections as $section){
            $content.= $section->create_section();
        }
        $content.= self::get_form_preview();
        $content.= self::get_license_section();
        $content.= "</div>";
        return $content;
    }

    /**
     * Special section for the license manager
     *
     * @return string
     */
    private static function get_license_section()
    {
        $option = get_option(FormLift::$option_key);
        $hide = ('flp-form-license' == $option['flp_active_tab'])? "" : "display:none;";
        $content = "<div id='_flp-form-license' class='flp-animate-right flp-section' style='{$hide}max-width:500px;'><h1>License Management</h1>".Flp_License_Manager::create_form()."</div>";
        return $content;
    }

    /**
     * Special section for the orm preview
     *
     * @return string
     */
    private static function get_form_preview()
    {
        $form = new Flp_Form(0);
        $code = '<div class="infusion-form-0">
        <div class="infusion-field">
            <label for="inf_field_FirstName">First Name *</label>
            <input class="infusion-field-input-container" id="inf_field_FirstName" name="inf_field_FirstName" type="text" />
        </div>
        <div class="infusion-field">
            <label for="inf_field_LastName">Last Name *</label>
            <input class="infusion-field-input-container" id="inf_field_LastName" name="inf_field_LastName" type="text" />
        </div>
        <div class="infusion-field">
            <label for="inf_field_Email">Email *</label>
            <input class="infusion-field-input-container" id="inf_field_Email" name="inf_field_Email" type="text" />
        </div>
        <div class="infusion-field">
            <label for="inf_field_Phone1">Phone *</label>
            <input class="infusion-field-input-container" id="inf_field_Phone1" name="inf_field_Phone1" type="text" />
        </div>
        <div class="infusion-field">
            <label for="inf_custom_Dropdown">Drop Down *</label>
            <select id="inf_custom_Dropdown" name="inf_custom_Dropdown"><option value="">Please select one</option><option value="1">Drop Down 1</option><option value="2">Drop Down 2</option></select>
        </div>
        <div class="infusion-field">
            <span class="infusion-option">
                <input id="inf_option_Checkbox" name="inf_option_Checkbox" type="checkbox" value="16110" />
                <label for="inf_option_Checkbox">Checkbox</label>
            </span>
        </div>
        <div class="infusion-field">
            <label for="inf_option_Selectoption">Select option *</label>
            <div class="infusion-radio">
                <span class="infusion-option">
                    <input id="inf_option_Selectoption_16062" name="inf_option_Selectoption" type="radio" value="16062" />
                    <label for="inf_option_Selectoption_16062">Option 1</label>
                </span>
                <span class="infusion-option">
                    <input id="inf_option_Selectoption_16064" name="inf_option_Selectoption" type="radio" value="16064" />
                    <label for="inf_option_Selectoption_16064">Option 2</label>
                </span>
            </div>
        </div>
        <div class="infusion-field">
            <label for="inf_custom_textarea">Textarea</label>
            <textarea  id="inf_custom_textarea" name="inf_custom_textarea" rows="5"></textarea>
        </div>
        <div class="infusion-submit">
            <button type="submit" class="inf-button">Test Form</button>
        </div>
    </div>';

        $form->set_form_code($code);
        $form->set_form_meta(get_option(FormLift::$option_key));
        $option = get_option(FormLift::$option_key);
        $hide = ('flp-form-preview' == $option['flp_active_tab'])? "" : "display:none;";
        $content = "<div id='_flp-form-preview' class='flp-animate-right flp-section' style='{$hide}max-width:500px;'><h1>Form Preview</h1>";
        $content.= "<div id='flp-preview-box'>{$form->shortcode()}</div>";
        $content.="</div>";

        return $content;
    }

    /**
     * Special function to add the headers for the form preview and license sections.
     * this function is called in the class' constructor.
     *
     * @param $headers
     * @return mixed
     */
    public static function add_headers($headers)
    {
        array_unshift($headers, array('flp-form-license', 'License Management'));
        array_push($headers, array('flp-form-preview', 'Form Preview'));
        return $headers;
    }

    /**
     * adds a settings section to the panel
     *
     * @param $section Flp_Settings_Section
     */
    private function add_section($section)
    {
        if ($section->is_premium && Flp_License_Manager::is_licensed()){
            array_push($this->sections,$section);
        } elseif (!$section->is_premium) {
            array_push($this->sections,$section);
        }
    }
}