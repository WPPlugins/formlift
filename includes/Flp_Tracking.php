<?php

/**
 * Created by PhpStorm.
 * User: Adrian
 * Date: 2017-02-26
 * Time: 6:44 PM
 */
class Flp_Tracking
{
    function __construct()
    {
        add_action( 'wp_ajax_nopriv_post_submission', array('Flp_Tracking', 'post_submission' ));
        add_action( 'wp_ajax_nopriv_post_impression', array('Flp_Tracking', 'post_impression' ));
        add_action( 'wp_ajax_reset_track_stats', array('Flp_Tracking', 'reset_track_stats' ));
        add_action( 'wp_ajax_reset_all_track_stats', array('Flp_Tracking','reset_all_track_stats'));
        add_action( 'flp_before_save_form', array('Flp_Tracking', 'update_date'));
    }

    /**
     * reset all of the forms stats to 0
     */
    public static function reset_all_track_stats()
    {
        if (is_user_logged_in() and current_user_can('manage_forms')){
            Flp_Utils::flp_mass_update_form_meta('conversion_rate', 0);
            Flp_Utils::flp_mass_update_form_meta('num_impressions', 0);
            Flp_Utils::flp_mass_update_form_meta('num_submissions', 0);
            $date = date('d/m/Y');
            Flp_Utils::flp_mass_update_form_meta('tracking_date', $date);
            wp_die('success');
        } else {
            wp_die('You do not have permission to do this... Nice try though.');
        }
    }

    /*
     * reset one forms stats to 0
     */
    public static function reset_track_stats()
    {
        $post_id = $_POST['id'];
        if (is_user_logged_in() and user_can(get_current_user_id(), 'edit_forms')){
            Flp_Utils::flp_update_meta($post_id,'num_submissions',0);
            Flp_Utils::flp_update_meta($post_id,'num_impressions',0);
            Flp_Utils::flp_update_meta($post_id,'conversion_rate',0);
            $date = date('d/m/Y');
            Flp_Utils::flp_update_meta($post_id,'tracking_date',$date);
            wp_die('success');
        } else {
            wp_die('you do not have permission to do this... Nice try though.');
        }
    }

    /**
     * Register an impression or submission based on the SUB_ACTION passed in the $_POST
     * install a cookie so that an impression isn't logged again based on the same user.
     * same goes for impressions.
     */
    public static function post_impression()
    {
        $id = intval($_POST['id']);
        $imp = get_post_meta($id, 'num_impressions', true);
        if (!current_user_can('edit_forms')){
            if (!isset($_COOKIE["{$id}_impression"])){
                setcookie("{$id}_impression",$id, strtotime( '+1 day' ), COOKIEPATH, COOKIE_DOMAIN);
                Flp_Utils::flp_update_meta($id,'num_impressions',$imp+1);
                self::flp_update_conversion_rate($id);
                wp_die('success');
            }
        }
        wp_die('failure');

    }

    /**
     * register a successful form submission when someone with non-editing capabilities submits a form.
     */
    public static function post_submission()
    {
        $id = intval($_POST['id']);
        $sub = get_post_meta($id, 'num_submissions', true);
        if (!current_user_can('edit_forms')){
            if (!isset($_COOKIE["{$id}_submitted"])) {
                setcookie("{$id}_submitted", $id, strtotime( '+1 day' ), COOKIEPATH, COOKIE_DOMAIN);
                Flp_Utils::flp_update_meta($id, 'num_submissions',$sub+1);
                self::flp_update_conversion_rate($id);
                wp_die('success');
            }
        }
        wp_die('failure');
    }

    /**
     * Update the conversion rate of a form.
     *
     * @param $id int
     */
    private static function flp_update_conversion_rate($id) {
        $imp = get_post_meta($id, 'num_impressions', true);
        $sub = get_post_meta($id, 'num_submissions', true);

        if ( $imp == 0 ) {
            $avg_conv = 0;
        } else {
            $avg_conv = round( ( $sub / $imp ) * 100 );
        }
        Flp_Utils::flp_update_meta($id, 'conversion_rate', $avg_conv);
    }

    /**
     * Update the tracking since date of a form
     *
     * @param $post_id
     */
    public static function update_date($post_id){
        $date = date('d/m/Y');
        if (!get_post_meta($post_id, 'tracking_date')){
            add_post_meta($post_id, 'tracking_date', $date);
        }
    }
}