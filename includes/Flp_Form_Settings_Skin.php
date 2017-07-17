<?php

/**
 * Created by PhpStorm.
 * User: Adrian
 * Date: 2017-03-02
 * Time: 11:07 PM
 */
class Flp_Form_Settings_Skin
{
    var $ID;
    var $form;
    var $sections = array();
    var $headers;

    public function __construct($post_id)
    {
        $this->ID = $post_id;
        $this->form = new Flp_Form($post_id);

        add_filter('flp_required_fields', array($this, 'get_form_required_fields'));

        $form_selection = new Flp_Settings_Section('flp_form_selection', 'Form Import', Flp_Settings::get_infusionsoft_form_choice_setting(), $this->form);
        $button_settings = new Flp_Settings_Section('flp_button_settings', 'Button CSS', Flp_Settings::get_button_fields(), $this->form, true);
        $input_settings = new Flp_Settings_Section('flp_input_settings', 'Input CSS', Flp_Settings::get_input_fields(), $this->form, true);
        $label_settings = new Flp_Settings_Section('flp_label_settings', 'Label CSS', Flp_Settings::get_label_fields(), $this->form, true);
        $form_settings = new Flp_Settings_Section('flp_form_settings', 'Form CSS', Flp_Settings::get_form_fields(), $this->form, true);
        $field_settings = new Flp_Settings_Section('flp_field_settings', 'Field CSS', Flp_Settings::get_field_fields(), $this->form, true);
        $error_settings = new Flp_Settings_Section('flp_error_settings', 'Error Settings & CSS', Flp_Settings::get_error_fields(), $this->form, true);
        $captcha_settings = new Flp_Settings_Section('flp_captcha_settings', 'Captcha Settings', Flp_Settings::get_captcha_settings(), $this->form, true);
        $tracking_Settings = new Flp_Settings_Section('flp_tracking_settings', 'Tracking Settings', Flp_Settings::get_tracking_fields(), $this->form);
        $display_settings = new Flp_Settings_Section('flp_display_settings', 'Display Settings', Flp_Settings::get_display_settings(), $this->form, true);
        $pop_settings = new Flp_Settings_Section('flp_auto_population_settings', 'Auto Population Settings', Flp_Settings::get_auto_population_settings(), $this->form);
        $required_field_Settings = new Flp_Settings_Section('flp_required_field_settings', 'Required Fields', Flp_Settings::get_required_field_settings(), $this->form);

        //build settings panel
        $this->add_section($form_selection);
        $this->add_section($button_settings);
        $this->add_section($input_settings);
        $this->add_section($label_settings);
        $this->add_section($form_settings);
        $this->add_section($field_settings);
        $this->add_section($error_settings);
        $this->add_section($captcha_settings);
        $this->add_section($display_settings);
        $this->add_section($tracking_Settings);
        $this->add_section($pop_settings);
        $this->add_section($required_field_Settings);

        foreach ($this->sections as $section){
            add_filter('flp_section_headers', array($section, 'add_header'));
        }

        $header = new Flp_Settings_Header($this->form);
        $this->headers = $header->get_headers();
    }

    /**
     * Echo support
     *
     * @return string
     */
    function __toString()
    {
        return "<div id='flp-custom-settings' class='flp-settings-page'>".$this->build_navigation().$this->build_settings_window()."</div>";
    }

    /**
     * Add the header to the navigation
     *
     * @return string
     */
    private function build_navigation()
    {
        $content = "<nav id='flp-header' class='flp-nav'>";
        foreach ($this->headers as $header){
            $content.= "$header";
        }
        return $content."</nav>";
    }

    /**
     * Add the settings window pane
     *
     * @return string
     */
    private function build_settings_window()
    {
        $content = "<div class='flp-sections-container'>";
        foreach ($this->sections as $section){
            $content.= $section->create_section();
        }
        $content.= "</div>";

        return $content;
    }

    /**
     * Get the required fields of the form to display and overwrite default fields
     *
     * @param $fields
     * @return array
     */
    public function get_form_required_fields($fields)
    {
        $new_fields = Flp_Utils::find_set_required_fields($this->form);
        $fields = array(); //overwrite
        foreach ($new_fields as $field){
            if (!empty($field[0])){
                array_push($fields, new Flp_Field(FLP_CHECKBOX, $field[0], $field[1]));
            }
        }
        return $fields;
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