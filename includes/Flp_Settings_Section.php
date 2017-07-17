<?php

/**
 * Created by PhpStorm.
 * User: Adrian
 * Date: 2017-02-26
 * Time: 11:27 AM
 */
class Flp_Settings_Section
{
    /*
     * section_id string
     * section_title string
     * fields array('field_id', 'field_label' , 'field_type', $value(S))
     */
    var $form;
    var $section_id;
    var $section_title;
    var $fields;
    var $defaults;
    var $is_premium;

    /**
     * Flp_Settings_Section constructor.
     * @param $form Flp_Form
     * @param $section_id String
     * @param $section_title String
     * @param $fields array(Flp_Field)
     * @param $premium bool
     */
    function __construct($section_id, $section_title, $fields, $form, $premium=false)
    {
        $this->form = $form;
        $this->section_id = $section_id;
        $this->section_title = $section_title;
        $this->fields = $fields;
        $this->defaults = get_option(FormLift::$option_key);
        $this->is_premium = $premium;

    }

    /**
     * give's the Flp_Setttings_Header class this sections header
     *
     * @param $headers
     * @return mixed
     */
    public function add_header($headers)
    {
        array_push($headers, array($this->section_id, $this->section_title));

        return $headers;
    }

    /**
     * Builds the sections form elements and the spits out the HTML
     *
     * @return string
     */
    public function create_section()
    {
        $content = '';

        foreach ($this->fields as $field) {
            if ($field->type == FLP_COLOR) {

                $field = $this->create_color_field($field->id, $field->label);
                $content.= self::wrap_row(self::wrap_label_cell($field[0]).self::wrap_input_cell($field[1]));

            } elseif ($field->type == FLP_INPUT) {

                $field = $this->create_text_field($field->id, $field->label);
                $content.= self::wrap_row(self::wrap_label_cell($field[0]).self::wrap_input_cell($field[1]));

            } elseif ($field->type == FLP_SELECT) {

                $field = $this->create_drop_down_field($field->id, $field->label, $field->values);
                $content.= self::wrap_row(self::wrap_label_cell($field[0]).self::wrap_input_cell($field[1]));

            } elseif ($field->type == FLP_TEXT) {

                $field = $this->create_textarea_field($field->id, $field->label);
                $content.= self::wrap_row(self::wrap_label_cell($field[0]).self::wrap_input_cell($field[1]));

            } elseif ($field->type == FLP_SECRET) {

                $field = $this->create_secret_field($field->id, $field->label);
                $content.= self::wrap_row(self::wrap_label_cell($field[0]).self::wrap_input_cell($field[1]));

            } elseif ($field->type == FLP_RADIO) {

                $field = $this->create_radio_field($field->id, $field->label, $field->values);
                $content.= self::wrap_row(self::wrap_label_cell($field[0]).self::wrap_input_cell($field[1]));

            } elseif ($field->type == FLP_MULTI) {

                $field = $this->create_multi_text_field($field->label, $field->sub_fields);
                $content.= self::wrap_row(self::wrap_label_cell($field[0]).self::wrap_input_cell($field[1]));

            } elseif ($field->type == FLP_CHECKBOX){
                $field = $this->create_checkbox_field($field->id, $field->label);
                $content.= self::wrap_row(self::wrap_label_cell($field[0]).self::wrap_input_cell($field[1]));

            } elseif ($field->type == FLP_HIDDEN){

                $field = $this->create_hidden_field($field->id);
                $content.= self::wrap_row(self::wrap_label_cell($field[0]).self::wrap_input_cell($field[1]));

            } elseif ($field->type == FLP_BUTTON){

                $field = $this->create_button($field->id, $field->label);
                $content.= self::wrap_row( self::wrap_input_cell($field[0]) );

            } elseif ($field->type == FLP_ERROR){

                $field = $this->create_error($field->label);
                $content.= self::wrap_row(self::wrap_input_cell($field[0]));

            } elseif ($field->type == FLP_DATE){

                $field = $this->create_date_field($field->id, $field->label);
                $content.= self::wrap_row(self::wrap_label_cell($field[0]).self::wrap_input_cell($field[1]));

            } elseif ($field->type == FLP_TIME){

                $field = $this->create_time_field($field->id, $field->label);
                $content.= self::wrap_row(self::wrap_label_cell($field[0]).self::wrap_input_cell($field[1]));

            } elseif ($field->type == FLP_NUMBER){

                $field = $this->create_number_field($field->id, $field->label);
                $content.= self::wrap_row(self::wrap_label_cell($field[0]).self::wrap_input_cell($field[1]));

            } elseif ($field->type == FLP_SEPARATOR){

                $content.= $this->create_separator();

            } elseif ($field->type == FLP_TITLE){

                $content.= $this->create_title($field->id, $field->label);
            }
        }

        //$content = self::wrap_table($content);
        $content = self::wrap_section($content);

        return $content;
    }

    /**
     * Creates an error as a an element if something is not right.
     *
     * @param $label
     * @return array
     */
    private function create_error($label)
    {
        return array(
            "<div class='flp-error'>$label</div>"
        );
    }

    /**
     * Adds a section Title within a settings section
     *
     * @param $id string
     * @param $title string
     * @return string
     */
    private function create_title($id, $title)
    {
        return "<h2 id='$id'>$title</h2>";
    }

    /**
     * Creates a line separator
     *
     * @return string
     */
    private function create_separator()
    {
        return "<hr/>";
    }

    /**
     * Creates a date-picker field
     *
     * @param $key string
     * @param $title string
     * @return array
     */
    private function create_date_field($key, $title)
    {
        $option_key = FormLift::$option_key;
        $value = $this->form->get_form_meta($key);
        return array(
            "<label for='$key'>$title</label>",
            "<input class='flp-input' placeholder='{$this->defaults[$key]}' id='$key' name='{$option_key}[$key]' value='$value' data-type=\"date\" data-changemonth=\"true\" data-changeyear=\"true\" data-mindate=\"0\" data-maxdate=\"+2Y\"/>"
        );
    }

    /**
     * Creates a wp-color-picker field
     *
     * @param $key string
     * @param $title string
     * @return array
     */
    private function create_color_field($key, $title)
    {
        $option_key = FormLift::$option_key;
        $value = $this->form->get_form_meta($key);
        return array(
            "<label for='$key'>$title</label>",
            "<input placeholder='{$this->defaults[$key]}' class='flp-color' data-alpha=\"true\" id='$key' name='{$option_key}[$key]' value='$value' />"
        );
    }

    /**
     * Creates a plain text field
     *
     * @param $key string
     * @param $title string
     * @return array
     */
    private function create_text_field($key, $title)
    {
        $option_key = FormLift::$option_key;
        $value = $this->form->get_form_meta($key);
        return array(
            "<label for='$key'>$title</label>",
            "<input class='flp-input' placeholder='{$this->defaults[$key]}' id='$key' name='{$option_key}[$key]' value='$value' />"
        );
    }

    /**
     * Creates a password type field
     *
     * @param $key
     * @param $title
     * @return array
     */
    private function create_secret_field($key, $title)
    {
        $option_key = FormLift::$option_key;
        $value = $this->form->get_form_meta($key);
        return array(
            "<label for='$key'>$title</label>",
            "<input type='password' class='flp-input' placeholder='{$this->defaults[$key]}' id='$key' name='{$option_key}[$key]' value='$value' />"
        );
    }

    /**
     * creates a time picker field
     *
     * @param $key string
     * @param $title string
     * @return array
     */
    private function create_time_field($key, $title)
    {
        $option_key = FormLift::$option_key;
        $value = $this->form->get_form_meta($key);
        return array(
            "<label for='$key'>$title</label>",
            "<input class='flp-input' placeholder='{$this->defaults[$key]}' id='$key' name='{$option_key}[$key]' value='$value' /><script>jQuery('#$key').timepicker();</script>"
        );
    }

    /**
     * creates a number type field
     *
     * @param $key string
     * @param $title string
     * @return array
     */
    private function create_number_field($key, $title)
    {
        $option_key = FormLift::$option_key;
        $value = $this->form->get_form_meta($key);
        return array(
            "<label for='$key'>$title</label>",
            "<input class='flp-input' placeholder='{$this->defaults[$key]}' id='$key' name='{$option_key}[$key]' value='$value' type='number'/>"
        );
    }

    /**
     * Creates a hidden field
     *
     * @param $key string
     * @return array
     */
    private function create_hidden_field($key)
    {
        $option_key = FormLift::$option_key;
        $value = $this->form->get_style_meta($key);
        return array(
            "",
            "<input type='hidden' id='$key' name='{$option_key}[$key]' value='$value' />"
        );
    }

    /**
     * Creates a button
     *
     * @param $key string
     * @param $title string
     * @return array
     */
    private function create_button($key, $title){
        $option_key = FormLift::$option_key;
        $button = "<input type='submit' name='{$option_key}[{$key}]' value='$title' class='button-primary'>";
        return array(
            $button
        );
    }

    /**
     * Creates a checkbox field
     *
     * @param $key string
     * @param $title string
     * @return array
     */
    private function create_checkbox_field( $key, $title)
    {
        $title = utf8_decode($title);
        $option_key = FormLift::$option_key;
        $checked = ($this->form->get_style_meta($key) == 1)? "checked":"";
        return array(
            "<input type='checkbox' id='$key' name='{$option_key}[$key]' value='1' $checked/><label for='$key'>$title</label>",
            ""
        );
    }

    /**
     * Creates a dropdown select menu of options
     *
     * @param $key string
     * @param $title string
     * @param $values array
     * @return array
     */
    private function create_drop_down_field( $key,  $title,  $values)
    {
        $option_key = FormLift::$option_key;
        $value = $this->form->get_style_meta($key);
        $select = "<select id='$key' name='{$option_key}[$key]'>";
        if (key_exists($value, $values)){
            foreach ($values as $possible_value => $label){
                if ($possible_value == $value){
                    $select.= "<option value='$possible_value' selected>$label</option>";
                } else{
                    $select.= "<option value='$possible_value'>$label</option>";
                }
            }
        } else {
            $value = $this->defaults[$key];
            foreach ($values as $possible_value => $label ){
                if ($possible_value == $value){
                    $select.= "<option value='$possible_value' selected>$label</option>";
                } else{
                    $select.= "<option value='$possible_value'>$label</option>";
                }
            }
        }

        $select.= "</select>";
        return array(
            "<label for='$key'>$title</label>",
            $select
        );
    }

    /**
     * Creates side by side small text inputs
     *
     * @param $title string
     * @param $subtitles array
     * @return array
     */
    private function create_multi_text_field( $title, $subtitles)
    {
        $option_key = FormLift::$option_key;
        $table = '';
        foreach ($subtitles as $sub => $key){
            $value = $this->form->get_form_meta($key);
            $table.="<label style='font-size:30px' for='$key'>$sub</label><input class='flp-input' style='width: 50px' placeholder='{$this->defaults[$key]}' id='$key' name='{$option_key}[$key]' value='$value' />";
        }

        return array(
            "<label>$title</label>",
            $table
        );

    }

    /**
     * Creates a textarea
     *
     * @param $key string
     * @param $title string
     * @return array
     */
    private function create_textarea_field($key, $title)
    {
        $option_key = FormLift::$option_key;
        $value = $this->form->get_form_meta($key);
        return array(
            "<label for='$key'>$title</label>",
            "<textarea placeholder='{$this->defaults[$key]}' id='$key' name='{$option_key}[$key]' cols='34'>$value</textarea>"
        );
    }

    /**
     * Creates radio options
     *
     * @param $key string
     * @param $title string
     * @param $values array
     * @return array
     */
    private function create_radio_field($key, $title, $values)
    {
        $option_key = FormLift::$option_key;
        $value = $this->form->get_style_meta($key);
        $the_content = "";
        foreach ($values as $possible_value => $text){
            $text = utf8_decode($text);
            if ($possible_value == $value){
                $the_content.= "<label><input type='radio' name='{$option_key}[$key]' value='$possible_value' checked>$text</label><br/>";
            } else{
                $the_content.= "<label><input type='radio' name='{$option_key}[$key]' value='$possible_value'>$text</label><br/>";
            }
        }
        return array(
            "<label>$title</label>",
            $the_content
        );
    }

    /**
     * Wraps the section in the responsive layout. Also autoatically displays if it was the last section open before saving.
     *
     * @param $content string
     * @return string
     */
    private function wrap_section($content)
    {
        //check to see if this is the active tab
        if ($this->form->ID == 0){
            $hide = ($this->section_id == $this->defaults['flp_active_tab'])? "" : "style='display:none'";
        } elseif ($this->form->get_form_meta('flp_active_tab')){
            $hide = ($this->section_id == $this->form->get_form_meta('flp_active_tab'))? "" : "style='display:none'";
        } else {
            $hide = ($this->section_id == "flp_form_selection")? "" : "style='display:none'";
        }

        return "<div id='_$this->section_id' class='flp-animate-right flp-section' {$hide}><h1>$this->section_title</h1>$content</div>";

    }

    /**
     * Wraps content in the row div
     *
     * @param $content string
     * @return string
     */
    public static function wrap_row($content)
    {
        return "<div class='flp-row'>$content</div>";
    }

    /**
     * Wraps the label element returned by the above functions in a cell
     *
     * @param $content string
     * @return string
     */
    public static function wrap_label_cell($content)
    {
        return "<div class='flp-cell flp-cell-label'>$content</div>";
    }

    /**
     * Wraps the input element returned by the above functions in a cell
     *
     * @param $content string
     * @return string
     */
    public static function wrap_input_cell($content)
    {
        return "<div class='flp-cell flp-cell-input'>$content</div>";
    }

}