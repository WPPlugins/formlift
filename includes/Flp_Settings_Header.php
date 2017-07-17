<?php

/**
 * Created by PhpStorm.
 * User: Adrian
 * Date: 2017-02-26
 * Time: 12:22 PM
 */
class Flp_Settings_Header
{
    var $headers = array();
    var $form;

    /**
     * Flp_Settings_Header constructor.
     * @param $form FLp_Form
     */
    public function __construct($form)
    {
        $this->form = $form;
        $this->headers = apply_filters('flp_section_headers', array());
    }

    /**
     * Returns a list oof HTML elements to add to the settings section NAV
     *
     * @return array
     */
    public function get_headers(){

        $content = array();
        //array_pop($this->headers);
        foreach ($this->headers as $header){
            $section_id = $header[0];
            $section_title = $header[1];

            if ($this->form->ID == 0){
                $hide = ($section_id == $this->form->defaults['flp_active_tab'])? " flp-active" : "";
            } elseif ($this->form->get_form_meta('flp_active_tab')){
                $hide = ($section_id == $this->form->get_form_meta('flp_active_tab'))? " flp-active" : "";
            } else {
                $hide = ($section_id == 'flp_form_selection')? " flp-active" : "";
            }
            array_push($content, "<a href='javascript:void(0)' class='flp-tab$hide' onclick='flpOpenSection(event, \"$section_id\")'>$section_title</a>");
        }

        return $content;
    }

    /**
     * Adds another header to the list of headers
     *
     * @param $header string
     */
    public function add_header($header)
    {
        array_push($this->headers, $header);
    }
}