<?php


class Flp_Settings
{
    public static $default_settings = array(
        'form_padding_top' => '0px',
        'form_padding_bottom' => '0px',
        'form_padding_right' => '0px',
        'form_padding_left' => '0px',
        'form_margin_top' => '0px',
        'form_margin_bottom' => '0px',
        'form_margin_right' => '0px',
        'form_margin_left' => '0px',
        'form_width' => '100%',
        'form_other' => '',
        'form_text_align' => 'left',
        'button_color' => '#FF0000',
        'button_border_color' => '#FF0000',
        'button_font_color' => '#FFFFFF',
        'button_hover_color' => '#FFFFFF',
        'button_border_hover_color' => '#FF0000',
        'button_font_hover_color' => '#FF0000',
        'button_width' => '100%',
        'button_border_radius' => '2px',
        'button_border_width' => '1px',
        'button_padding' => '5px',
        'button_font_size' => '24px',
        'button_font_weight' => '400',
        'button_font_family' => 'Helvetica',
        'button_align' => 'center',
        'button_other' => '',
        'input_background_color' => '#FFFFFF',
        'input_border_color' => '#BABABA',
        'input_focus_color' => '#1e90ff',
        'input_border_radius' => '2px',
        'input_border_width' => '1px',
        'input_font_color' => '#000000',
        'input_placeholder_color' => '#bfbfbf',
        'input_height' => 'auto',
        'input_width' => '100%',
        'input_padding' => '5px',
        'input_align' => 'none',
        'input_radio_display' => 'block',
        'input_radio_margin' => '5px',
        'input_radio_scale' => '1',
        'input_font_family' => 'Helvetica',
        'input_font_size' => '14px',
        'input_font_weight' => '400',
        'input_other' => '',
        'label_font_family' => 'Helvetica',
        'label_font_size' => '14px',
        'label_font_weight' => '400',
        'labels_remove' => 'no',
        'label_font_color' => '#000000',
        'select_remove'=> 'no',
        'label_other'=> '',
        'error_border_radius' => '3px',
        'error_background_color' => '#ff7f7f',
        'error_border_color' => '#ff7f7f',
        'error_font_color' => '#FFFFFF',
        'error_font_family' => 'Helvetica',
        'error_other' => '',
        'error_font_size' => '14px',
        'field_margin_top' => '5px',
        'field_margin_bottom' => '5px',
        'field_margin_left' => '0px',
        'field_margin_right' => '0px',
        'field_padding_top' => '0px',
        'field_padding_left' => '0px',
        'field_padding_right' => '0px',
        'field_padding_bottom' => '0px',
        'field_width' => '100%',
        'field_other' => '',
        'email_error' => "- Please enter a valid email.",
        'name_error' => "should contain letters only.",
        'phone_error' => "- Please enter a valid phone number.",
        'input_error' => "is a required field!",
        'auto_fill_conditions' => '2',
        'val_conditions' => '2',
        'flp_active_tab' => 'flp-form-license',
        'display_type' => '1'
    );

    function __construct()
    {
        add_action( 'init', array('Flp_Settings', 'register_settings' ));
    }

    /**
     * Returns a list of Flp_Fields for the Button Settings Section
     *
     * @return array
     */
    public static function get_button_fields()
    {
        return array(
            new Flp_Field(FLP_COLOR, 'button_color', 'Color'),
            new Flp_Field(FLP_COLOR, 'button_border_color', 'Border Color'),
            new Flp_Field(FLP_COLOR, 'button_font_color', 'Font Color'),
            new Flp_Field(FLP_COLOR, 'button_hover_color', 'Hover Color'),
            new Flp_Field(FLP_COLOR, 'button_border_hover_color', 'Border Hover Color'),
            new Flp_Field(FLP_COLOR, 'button_font_hover_color', 'Hover Font Color'),
            new Flp_Field(FLP_INPUT, 'button_width', 'Width'),
            new Flp_Field(FLP_INPUT, 'button_border_width', 'Border Width'),
            new Flp_Field(FLP_INPUT, 'button_border_radius', 'Border Radius'),
            new Flp_Field(FLP_INPUT, 'button_padding', 'Top & Bottom Padding'),
            new Flp_Field(FLP_INPUT, 'button_font_family', 'Font Family'),
            new Flp_Field(FLP_INPUT, 'button_font_size', 'Font Size'),
            new Flp_Field(FLP_INPUT, 'button_font_weight', 'Font Weight'),
            new Flp_Field(FLP_SELECT, 'button_align', 'Alignment', array(
                'left' => 'Left',
                'center' => 'Center',
                'right' => 'Right'
            )),
            new Flp_Field(FLP_TEXT, 'button_other', 'Custom CSS')
        );
    }

    /**
     * Returns a list of Flp_Fields for the input settings section
     *
     * @return array
     */
    public static function get_input_fields()
    {
        return array(
            new Flp_Field(FLP_COLOR, 'input_background_color', 'Background Color'),
            new Flp_Field(FLP_COLOR, 'input_border_color', 'Border Color'),
            new Flp_Field(FLP_COLOR, 'input_focus_color', 'Focus Color'),
            new Flp_Field(FLP_COLOR, 'input_font_color', 'Font Color'),
            new Flp_Field(FLP_COLOR, 'input_placeholder_color', 'Placeholder Font Color'),
            new Flp_Field(FLP_INPUT, 'input_width', 'Width'),
            new Flp_Field(FLP_INPUT, 'input_height', 'Height'),
            new Flp_Field(FLP_INPUT, 'input_border_width', 'Border Width'),
            new Flp_Field(FLP_INPUT, 'input_border_radius', 'Border Radius'),
            new Flp_Field(FLP_INPUT, 'input_padding', 'Text Padding'),
            new Flp_Field(FLP_INPUT, 'input_font_family', 'Font Family'),
            new Flp_Field(FLP_INPUT, 'input_font_size', 'Font Size'),
            new Flp_Field(FLP_INPUT, 'input_font_weight', 'Font Weight'),
            new Flp_Field(FLP_SELECT, 'input_align', 'Field Alignment', array(
                'none' => 'None',
                'left' => 'Left',
                'right' => 'Right'
            )),
            new Flp_Field(FLP_INPUT, 'input_radio_margin', 'Radio Button Option Spacing'),
            new Flp_Field(FLP_SELECT, 'input_radio_display', 'Radio Button Display Type', array(
                'block' => 'List',
                'inline' => 'Inline'
            )),
            new Flp_Field(FLP_INPUT, 'input_radio_scale', 'Radio button Scale'),
            new Flp_Field(FLP_TEXT, 'input_other', 'Custom CSS')
        );
    }

    /**
     * Returns a list of Flp_Fields for the form settings section
     *
     * @return array
     */
    public static function get_form_fields()
    {
        return array(
            new Flp_Field(FLP_MULTI, 'form_padding', 'Padding', array(), array(
                '&#8679;' => 'form_padding_top',
                '&#8680;' => 'form_padding_right',
                '&#8681;' => 'form_padding_bottom',
                '&#8678;' => 'form_padding_left',
            )),
            new Flp_Field(FLP_MULTI, 'form_margins', 'Margins', array(), array(
                '&#8679;' => 'form_margin_top',
                '&#8680;' => 'form_margin_right',
                '&#8681;' => 'form_margin_bottom',
                '&#8678;' => 'form_margin_left',
            )),
            new Flp_Field(FLP_INPUT, 'form_width', 'Width'),
            new Flp_Field(FLP_SELECT, 'form_text_align', 'Align Contents', array(
                'left' => 'Left',
                'center' => 'Center',
                'right' => 'Right'
            )),
            new Flp_Field(FLP_TEXT, 'form_other', 'Custom CSS'),
        );
    }

    /**
     * Returns a list of Flp_Fields for the field settings section
     *
     * @return array
     */
    public static function get_field_fields()
    {
        return array(
            new Flp_Field(FLP_MULTI, 'field_padding', 'Padding', array(), array(
                '&#8679;' => 'field_padding_top',
                '&#8680;' => 'field_padding_right',
                '&#8681;' => 'field_padding_bottom',
                '&#8678;' => 'field_padding_left',
            )),
            new Flp_Field(FLP_MULTI, 'field_margins', 'Margins', array(), array(
                '&#8679;' => 'field_margin_top',
                '&#8680;' => 'field_margin_right',
                '&#8681;' => 'field_margin_bottom',
                '&#8678;' => 'field_margin_left',
            )),
            new Flp_Field(FLP_INPUT, 'field_width', 'Width'),
            new Flp_Field(FLP_TEXT, 'field_other', 'Custom CSS')
        );
    }

    /**
     * Returns a list of Flp_Fields for the field settings section
     *
     * @return array
     */
    public static function get_label_fields()
    {
        return array(
            new Flp_Field(FLP_COLOR, 'label_font_color', 'Font Color'),
            new Flp_Field(FLP_INPUT, 'label_font_family', 'Font Family'),
            new Flp_Field(FLP_INPUT, 'label_font_size', 'Font Size'),
            new Flp_Field(FLP_INPUT, 'label_font_weight', 'Font Weight'),
            new Flp_Field(FLP_SELECT, 'labels_remove', 'Remove Input Labels', array(
                'yes' => 'Yes',
                'no' => 'No',
            )),
            new Flp_Field(FLP_SELECT, 'select_remove', 'Remove Drop Down Labels', array(
                'yes' => 'Yes',
                'no' => 'No',
            )),
            new Flp_Field(FLP_TEXT, 'label_other', 'Custom CSS')
        );
    }

    /**
     * Returns a list of Flp_Fields for the error settings section
     *
     * @return array
     */
    public static function get_error_fields()
    {
        return array(
            new Flp_Field(FLP_COLOR, 'error_background_color', 'Background Color'),
            new Flp_Field(FLP_COLOR, 'error_border_color', 'Border Color'),
            new Flp_Field(FLP_COLOR, 'error_font_color', 'Font Color'),
            new Flp_Field(FLP_INPUT, 'error_font_family', 'Font Family'),
            new Flp_Field(FLP_INPUT, 'error_font_size', 'Font Size'),
            new Flp_Field(FLP_INPUT, 'error_border_radius', 'Border Radius'),
            new Flp_Field(FLP_TEXT, 'error_other', 'Custom CSS'),
            new Flp_Field(FLP_TEXT, 'input_error', 'Required Field Error Message'),
            new Flp_Field(FLP_TEXT, 'email_error', 'Invalid Email Error Message'),
            new Flp_Field(FLP_TEXT, 'phone_error', 'Invalid Phone Error Message'),
            new Flp_Field(FLP_TEXT, 'name_error', 'Invalid Name Error Message')
        );
    }

    /**
     * Returns a list of Flp_Fields for the tracking settings section
     *
     * @return array
     */
    public static function get_tracking_fields()
    {
        return array(
            new Flp_Field(FLP_SELECT, 'tracking_method', 'Default Tracking Method', array(
              'page_load' => 'Page Load',
              'mouse_over' => 'Mouse Over',
              'none' => 'None'
            )),
            new Flp_Field(FLP_HIDDEN, 'flp_active_tab', '')
        );
    }

    /**
     * Returns a list of Flp_Fields for the auto population settings section
     *
     * @return array
     */
    public static function get_auto_population_settings()
    {
        return array(
            new Flp_Field(FLP_RADIO, 'auto_fill_conditions', 'When Should Auto-Population Occur?', array(
                '1' => 'Only when URL params are passed',
                '2' => 'When Cookied data exists and/or URL params are passed',
                '3' => 'Never'
            )),
            new Flp_Field(FLP_RADIO, 'val_conditions', 'Auto-Populate If...', array(
                '1' => 'There is already a value or no existing value',
                '2' => 'There is no existing value'
            ))
        );
    }

    /**
     * Returns a list of Flp_Fields for the required fields. If there is a unique form in play then the default array
     * gets overwritten
     *
     * @return array
     */
    public static function get_required_field_settings()
    {
        $fields =  array(
            new Flp_Field(FLP_CHECKBOX, 'required_inf_field_FirstName', 'First Name'),
            new Flp_Field(FLP_CHECKBOX, 'required_inf_field_LastName', 'Last Name'),
            new Flp_Field(FLP_CHECKBOX, 'required_inf_field_Email', 'Email 1'),
            new Flp_Field(FLP_CHECKBOX, 'required_inf_field_Phone', 'Phone 1'),
        );

        $fields = apply_filters('flp_required_fields', $fields);

        return $fields;
    }

    /**
     * Returns a list of Flp_Fields for the Gogle captcha settings
     *
     * @return array
     */
    public  static function get_google_settings()
    {
        $fields = array(
            new Flp_Field(FLP_INPUT, 'google_captcha_key', 'Google Captcha Key')
        );

        return $fields;
    }

    /**
     * Returns a list of Flp_Fields for the Google captcha settings
     *
     * @return array
     */
    public static function get_captcha_settings()
    {

        if(Flp_Utils::get_setting('google_captcha_key')){
            $fields = array(
                new Flp_Field(FLP_CHECKBOX, 'form_has_captcha', 'Turn On Captcha')
            );
        } else {
            $fields = array(
                new Flp_Field(FLP_ERROR, 'form_has_captcha', 'You must first set your Google reCAPTCHA API Key')
            );
        }

        return $fields;
    }

    /**
     * Returns a list of Flp_Fields for the Infusionsoft API settings
     *
     * @return array
     */
    public static function get_infusionsoft_settings(){
        $fields = array(
            new Flp_Field(FLP_INPUT, 'infusionsoft_app_name', 'Infusionsoft App Name (e.g xx123)'),
            new Flp_Field(FLP_SECRET, 'infusionsoft_api_key', 'Infusionsoft API Key')
        );

        return $fields;
    }

    /**
     * Returns a list of Flp_Fields for the form import settings
     *
     * @return array
     */
    public static function get_infusionsoft_form_choice_setting()
    {
        try{
            if (Flp_Utils::get_setting('infusionsoft_api_key')){
                $fields = array(
                    new Flp_Field(FLP_SELECT, 'infusionsoft_form_id', 'Import From Infusionsoft', Flp_Utils::get_infusionsoft_webforms()),
                    new Flp_Field(FLP_BUTTON, 'form_refresh', 'Replace Form Code'),
                    new Flp_Field(FLP_SELECT, 'import_form_id', 'Copy Settings From', Flp_Utils::flp_get_form_dropdown()),
                    new Flp_Field(FLP_BUTTON, 'do_form_import', 'Import Form Settings')
                );
            } else {
                $fields = array(
                    new Flp_Field(FLP_ERROR, 'infusionsoft_form_id', 'To import forms you must first connect your Infusionsoft App.'),
                    new Flp_Field(FLP_SELECT, 'import_form_id', 'Copy Settings From', Flp_Utils::flp_get_form_dropdown()),
                    new Flp_Field(FLP_BUTTON, 'do_form_import', 'Import Form Settings')
                );
            }
        } catch (Infusionsoft_Exception $error){
            $fields = array(
                new Flp_Field(FLP_ERROR, 'infusionsoft_form_id', $error->getMessage()),
                new Flp_Field(FLP_SELECT, 'import_form_id', 'Copy Settings From', Flp_Utils::flp_get_form_dropdown()),
                new Flp_Field(FLP_BUTTON, 'do_form_import', 'Import Form Settings')
            );
        }

        return $fields;
    }

    /**
     * Returns a list of Flp_Fields for the form display settings section
     *
     * @return array
     */
    public static function get_display_settings()
    {
        $fields = array(
            new Flp_Field(FLP_SELECT, 'display_condition', 'Display Condition', array(
                '1' => 'Always Show',
                '2' => 'Within Date Range',
                '3' => 'Under Max Submissions',
                '4' => 'Do Not Show'
            )),
            new Flp_Field(FLP_SEPARATOR),
            new Flp_Field(FLP_TITLE, 'date_range', 'Specify Date Range'),
            new Flp_Field(FLP_DATE, 'start_date', 'Start Date'),
            new Flp_Field(FLP_TIME, 'start_time', 'Start Time'),
            new Flp_Field(FLP_DATE, 'end_date', 'End Date'),
            new Flp_Field(FLP_TIME, 'end_time', 'End Time'),
            new Flp_Field(FLP_SEPARATOR),
            new Flp_Field(FLP_NUMBER, 'max_submissions', 'Submission Cut-off'),
            new Flp_Field(FLP_SEPARATOR),
            new Flp_Field(FLP_TEXT, 'no_display_msg', 'Not accepting Submissions Message (Accepts Basic HTML)')
        );

        return $fields;
    }

    /**
     * registers the formlift_options setting
     */
    public static function register_settings()
    {
        register_setting( FormLift::$option_key, FormLift::$option_key , array('Flp_Settings', "save_settings" ));
    }

    /**
     * Function that saves the default settings
     *
     * @param $options
     * @return array
     */
    public static function save_settings($options)
    {
        if (is_user_logged_in() && current_user_can('edit_forms')){

            $options = apply_filters('flp_save_settings', $options);

            foreach ($options as $option => $value) {
                if ($value == "") {
                    $options[$option] = self::$default_settings[$option];
                }
            }
            $options = self::clean_settings($options);
            return $options;

        } else {
            wp_die('Nice try, but you aren\'t allowed to do this');
        }
    }

    /**
     * Cleans the settings so noe one can break the form.
     *
     * @param $array array
     * @return array
     */
    public static function clean_settings($array)
    {
        foreach ($array as $option => $value)
        {
            if (is_string($value)){
                $array[$option] = sanitize_text_field($value);
            } elseif ( (is_string($array))){
                $array[$option] = self::clean_settings($value);
            }
        }

        return $array;
    }
}