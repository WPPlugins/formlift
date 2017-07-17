<?php

/**
 * Created by PhpStorm.
 * User: Adrian
 * Date: 2017-04-23
 * Time: 11:14 PM
 */
class Flp_Booking_Link_Box
{
    var $fields = array(
        'apptId' => 'Appointment ID',
        'view' => 'View',
        'width' => 'iFrame Width',
        'height' => 'iFrame Height'
    );
    var $ID;
    var $meta;

    function __construct($ID)
    {
        $this->ID = $ID;
        $this->meta = get_post_meta($this->ID, FormLift::$option_key, true);
    }

    public function create_page()
    {
        foreach ($this->fields as $ID => $label){
            $fields = self::create_field($ID, $label);
            $label_field = Flp_Settings_Section::wrap_label_cell($fields['label']);
            $input_field = Flp_Settings_Section::wrap_input_cell($fields['input']);
            echo Flp_Settings_Section::wrap_row($label_field.$input_field);
        }
    }

    public function create_field($fieldId, $label)
    {
        $value = (isset($this->meta[$fieldId]))? $this->meta[$fieldId] : '';
        $option_key = FormLift::$option_key;
        return array(
            'input' => "<input id='$fieldId' name='{$option_key}[$fieldId]' value='$value' class='flp-input'/>",
            'label' => "<label for='$fieldId'>$label</label>"
        );
    }
}