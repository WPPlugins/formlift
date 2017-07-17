<?php

/**
 * Created by PhpStorm.
 * User: Adrian
 * Date: 2017-05-27
 * Time: 10:24 PM
 */
define('FLP_EQUAL', 'Is Equal To');
define('FLP_NOT_EQUAL', 'Is NOT Equal To');
define('FLP_CONTAINS', 'Contains');
define('FLP_NOT_CONTAINS', 'Does NOT Contain');
define('FLP_STARTS_WITH', 'Starts With');
define('FLP_ENDS_WITH', 'Ends With');

class Flp_Redirect_Manager
{
    function __construct()
    {
        add_action( 'plugins_loaded', array( 'Flp_Redirect_Manager', 'perform_redirect' ));
        add_action( 'wp_ajax_flp_get_permalink', array('Flp_Redirect_Manager', 'flp_get_permalink' ));
        add_action( 'flp_before_save_form', array('Flp_Redirect_Manager', 'save_redirect_settings'));
    }

    /**
     * Performs a redirect based upon a user's form submission OR data sent from an infusionsoft email. options for
     * redirection are '=' '!=' '%like%' '!%like%' 'like%' '%like'
     *
     * if certain criteria are not met for a redirect to occur, the user is sent to the default thank you page IF it's set
     * otherwise they are sent to the fallback page (the page where this action is performed)
     *
     * @return void
     */
    function perform_redirect()
    {
        if( isset($_GET['form_action']) && 'formlift_redirect' == $_GET['form_action']){
            $id = $_GET['form_id'];
            //to avoid infinite redirects if redirect to same site
            $query_string = preg_replace("/form_action=formlift_redirect&form_id=([0-9]+)&/","", $_SERVER['QUERY_STRING']);

            $fields = get_post_meta($id, 'flp_fields', true);
            $conditions = get_post_meta($id, 'flp_conditions', true);
            $values = get_post_meta($id, 'flp_values', true);
            $urls = get_post_meta($id, 'flp_urls', true);

            $backup = get_post_meta($id, 'flp_thank_you_page_backup', true);

            $i = 0;

            foreach ($fields as $field){
                $val = Flp_Utils::get_cookie_or_param($field);

                if (!empty($val)){

                    $field_value = urldecode($val);

                    if ($conditions[$i] == FLP_EQUAL){
                        if ($field_value == $values[$i]){
                            wp_redirect( $urls[$i]."?".$query_string );
                            exit;
                        }
                    } elseif ($conditions[$i] == FLP_NOT_EQUAL){
                        if ($field_value != $values[$i]){
                            wp_redirect( $urls[$i]."?".$query_string );
                            exit;
                        }
                    } elseif ($conditions[$i] == FLP_CONTAINS){
                        if (preg_match("/".$values[$i]."/i", $field_value)){
                            wp_redirect( $urls[$i]."?".$query_string );
                            exit;
                        }
                    } elseif ($conditions[$i] == FLP_NOT_CONTAINS){
                        if (!preg_match("/".$values[$i]."/i", $field_value)){
                            wp_redirect( $urls[$i]."?".$query_string );
                            exit;
                        }
                    } elseif ($conditions[$i] == FLP_STARTS_WITH) {
                        if (preg_match("/^" . $values[$i] . ".*/i", $field_value)) {
                            wp_redirect($urls[$i] . "?" . $query_string);
                            exit;
                        }
                    } elseif ($conditions[$i] == FLP_ENDS_WITH) {
                        if (preg_match("/.*" . $values[$i] . "$/i", $field_value)) {
                            wp_redirect($urls[$i] . "?" . $query_string);
                            exit;
                        }
                    }
                }
                $i+=1;
            }
            if (!empty($backup)){
                wp_redirect($backup);
                exit;
            }
        }
    }

    /**
     * returns the permalink for the post via AJAX to give the redirect url.
     *
     * @return void
     */
    public static function flp_get_permalink(){
        $uri = get_permalink(intval($_POST['ID']));
        wp_die($uri."?form_action=formlift_redirect&form_id=".$_POST['post_id']);
    }

    /**
     * Sanitizes the settings for the redirect tool for a single form and then saves them.
     *
     * @param $post_id int the ID of the form being edited
     */
    public static function save_redirect_settings($post_id){

        if(isset($_POST['flp_fields'])){
            $new_items = array();
            foreach ($_POST['flp_urls'] as $url){
                array_push($new_items, sanitize_text_field($url));
            }
            Flp_Utils::flp_update_meta($post_id, 'flp_fields', $_POST['flp_fields']);
            Flp_Utils::flp_update_meta($post_id, 'flp_conditions', $_POST['flp_conditions']);
            Flp_Utils::flp_update_meta($post_id, 'flp_values', $_POST['flp_values']);
            Flp_Utils::flp_update_meta($post_id, 'flp_urls', $new_items);
        } else {
            delete_post_meta($post_id, 'flp_fields');
            delete_post_meta($post_id, 'flp_conditions');
            delete_post_meta($post_id, 'flp_values');
            delete_post_meta($post_id, 'flp_urls');
        }

        if(isset($_POST['flp_thank_you_page_backup'])){
            Flp_Utils::flp_update_meta($post_id, 'flp_thank_you_page_backup', sanitize_text_field($_POST['flp_thank_you_page_backup']));
        } else {
            delete_post_meta($post_id, 'flp_thank_you_page_backup');
        }

        if(isset($_POST['flp_thank_you_page_uri'])){
            Flp_Utils::flp_update_meta($post_id, 'flp_thank_you_page_uri', sanitize_text_field($_POST['flp_thank_you_page_uri']));
            Flp_Utils::flp_update_meta($post_id, 'flp_thank_you_page_id', sanitize_text_field($_POST['flp_thank_you_page_id']));
        }
    }
}