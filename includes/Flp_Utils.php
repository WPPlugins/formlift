<?php

/**
 * Created by PhpStorm.
 * User: Adrian
 * Date: 2017-02-26
 * Time: 1:01 PM
 */

/**
 * a collection of useful tools that make life easy
 */
class Flp_Utils
{
    function __construct()
    {
        add_action( 'wp_ajax_flp_get_form_via_ajax', array('Flp_Utils', 'flp_get_form_via_ajax' ));
    }

    /**
     * Gets a default setting based on the ID given
     *
     * @param $id string
     * @return string
     */
    public static function get_setting($id)
    {
        $option = get_option(FormLift::$option_key);

        if (isset($option[$id])){
            return $option[$id];
        } else {
            return '';
        }
    }

    /**
     * Gets some user data from the cookies or using the GET method
     *
     * @param $field string
     * @return string
     */
    public static function get_cookie_or_param($field)
    {
        if (isset($_GET[$field])){
            return $_GET[$field];
        } elseif (isset($_COOKIE[$field])){
            return $_COOKIE[$field];
        } else {
            return '';
        }
    }

    /**
     * returns true if an option exists, false otherwise
     *
     * @param $option_name
     * @return bool
     */
    public static function option_exists($option_name){
        $option = get_option($option_name);
        return !empty($option);
    }

    /**
     * function to switch the options array key with another key
     * i.e. switch options['key1'] with options['key2']
     *
     * @param $old_option_name String
     * @param $new_option_name String
     */
    public static function flp_switch_style_option_name($old_option_name, $new_option_name){
        $formlift_options = get_option(FormLift::$option_key);
        $value = $formlift_options[$old_option_name];
        if (isset($value)){
            unset($formlift_options[$old_option_name]);
            $formlift_options[$new_option_name] = $value;
            self::flp_update_option(FormLift::$option_key, $formlift_options);
        }

    }

    /**
     * function to switch and array value with another if its value is currently another given value
     * i.e. options['key1'] = 0 switch to options['key1'] = 0
     *
     * @param $option_name String
     * @param $old_val String
     * @param $new_val String
     */
    public static function flp_switch_style_option_value($option_name, $old_val, $new_val){
        $formlift_options = get_option(FormLift::$option_key);
        if (isset($formlift_options[$option_name]) and $formlift_options[$option_name] == $old_val){
            $formlift_options[$option_name] = $new_val;
            self::flp_update_option(FormLift::$option_key, $formlift_options);
        } else {
            //if it doesn't exist in the DB create it to be safe.
            $formlift_options[$option_name] = $new_val;
            self::flp_update_option(FormLift::$option_key, $formlift_options);
        }
    }

    /**
     * Updates an option with given name. If the update fails then it creates the option
     *
     * @param $name string
     * @param $value string
     */
    public static function flp_update_option($name, $value){
        if (!update_option($name, $value)){
            add_option($name, $value);
        }
    }

    /**
     * return an array of all the posts of type infusion_form
     *
     * @return array Posts
     */
    public static function flp_get_forms()
    {
        $args = array('numberposts' => '-1', 'post_type'=>'infusion_form' );
        $postslist = get_posts( $args );
        return $postslist;
    }

    /**
     * return an array of all the posts of type infusion_form in their FormLift form config
     *
     * @return array Flp_Form
     */
    public static function get_forms()
    {
        $args = array('numberposts' => '-1', 'post_type'=>'infusion_form' );
        $postslist = get_posts( $args );
        $formlist = array();
        foreach ($postslist as $post){
            $form = new Flp_Form($post->ID);
            array_push($formlist, $form);
        }
        return $formlist;
    }

    /**
     * Returns a list of ID => Name for each form
     *
     * @return array int|string
     */
    public static function flp_get_form_dropdown()
    {
        $forms = self::flp_get_forms(); //array(Post)
        $list = array();
        foreach ($forms as $form){
            $list[$form->ID] = $form->post_title;
        }
        return $list;
    }

    /**
     * switch the names of form meta to avoid conflict
     *
     * @param $new_key string
     * @param $old_key string
     */
    public static function flp_mass_switch_option_key( $old_key, $new_key){
        $forms = self::flp_get_forms();
        foreach ($forms as $form){
            $meta = get_post_meta($form->ID, $old_key, true);
            if (isset($meta)){
                delete_post_meta($form->ID, $old_key);
                self::flp_update_meta($form->ID, $new_key, $meta);
            }
        }
    }

    /**
     * update all the forms of a with a certain value
     *
     * @param $key string
     * @param $value mixed
     */
    public static function flp_mass_update_form_meta($key, $value)
    {
        $forms = self::flp_get_forms();
        foreach ($forms as $form){
            self::flp_update_meta($form->ID, $key, $value);
        }
    }

    /**
     * Mass updates the forms style options in the formlift_options array
     *
     * @param $key string
     * @param $value string
     * @param $old_value string
     */
    public static function flp_mass_update_form_style_options($key, $value, $old_value){
        $forms = self::flp_get_forms();
        foreach ($forms as $form){
            $curr_options = get_post_meta($form->ID, FormLift::$option_key, true);
            $curr = $curr_options[$key];
            if ($curr == $old_value){
                $curr_options[$key] = $value;
                self::flp_update_meta($form->ID, FormLift::$option_key, $curr_options);
            }
        }
    }

    /**
     * update a single forms post meta
     *
     * @param $post_id integer
     * @param $key string
     * @param $value mixed
     */
    public static function flp_update_meta($post_id, $key, $value)
    {
        if (!add_post_meta($post_id, $key, $value, true)){
            update_post_meta($post_id, $key, $value);
        }
    }

    /**
     * Gets the required fields in a form based on the html and the returns an array of ID => Label for the required
     * fields tab in the settings panel
     *
     * @param $form Flp_Form
     * @return array(array('id', 'label'))
     */
    public static function find_set_required_fields($form)
    {
        $results = array(); //array(array('id', 'label'))
        $code = $form->get_form_code();

        libxml_use_internal_errors(true);

        $doc = new DOMDocument("1.0", "UTF-8");
        $doc->loadHTML($code);

        libxml_use_internal_errors(false);

        $domTree = new RecursiveIteratorIterator(
            new RecursiveDOMIterator($doc),
            RecursiveIteratorIterator::SELF_FIRST
        );

        foreach ($domTree as $node)
        {
            if ($node->nodeType === XML_ELEMENT_NODE && $node->tagName == 'label') {
                $e = Flp_Form_Builder::get_associated_element($node, $doc);
                if (empty($e) || $e->getAttribute('type') != 'radio'){
                    array_push($results, array(
                        "required_".$node->getAttribute('for'),
                        $node->nodeValue
                    ));
                }
            }
        }

        return $results;
    }

    /**
     * Currently is not used, but was supposed to get the HTML and Stylesheet of a form. But you can't init a form
     * automitically so what's the point really?
     *
     * @return void
     */
    public static function flp_get_form_via_ajax(){

        $id = $_POST['ID'];
        $form = new Flp_Form($id);
        $form->set_form_meta(get_option(FormLift::$option_key));$the_content = $form->get_style_sheet();
        $the_content.= "<div class='{$form->form_name}'>".$form->get_form_code()."</div>";
        $the_content.= $form->get_front_end_starter_starter_script();

        wp_die($the_content);
    }

    /**
     * Gets the list of Infusionsoft web forms via the API and returns them in list format
     *
     * @return array
     */
    public static function get_infusionsoft_webforms(){
        if (Flp_Utils::get_setting('infusionsoft_app_name') && Flp_Utils::get_setting('infusionsoft_api_key')){
            $forms = new Infusionsoft_WebFormService();
            $array = $forms->getMap();
        } else {
            $array = array('Please Select One' => '');
        }
        return $array;
    }

    /**
     * Get's a specific web forms HTML from infusionsoft!
     *
     * @param $id int
     * @return string
     */
    public static function get_infusionsoft_form_html($id){
        $forms = new Infusionsoft_WebFormService();
        return $forms->getHTML($id);
    }

    /**
     * Ensure that the setting's value will not break frontend forms...
     */
    public static function clean_setting($setting_value)
    {
        if (is_string($setting_value)){
            $setting_value = preg_replace('/\'/', '\\\'', $setting_value);
            //$setting_value = preg_replace("/\"/", "\"", $setting_value);
        }
        return $setting_value;
    }

}

$flp_utils = new Flp_Utils();