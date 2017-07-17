<?php

/**
 * Created by PhpStorm.
 * User: Adrian
 * Date: 2017-05-27
 * Time: 11:00 PM
 */

/**
 * listener class that store all admin notices and runs them sequentially when admin_notices is called
 */
class Flp_Notice_Manager
{
    static $notices = array();
    static $notice_option = 'flp_active_options';

    function __construct()
    {

        add_action( 'admin_notices', array( 'Flp_Notice_Manager', 'run' ));
        add_action( 'flp_notice_reset', array('Flp_Notice_Manager', 'reactivate_notice'), 10, 1);
        add_action( 'wp_ajax_flp_do_dismiss', array( 'Flp_Notice_Manager', 'do_dismiss' ));

    }

    /**
     * API for the dismissing of notices
     *
     * @param $options
     */
    public static function do_dismiss()
    {
        if (is_user_logged_in() && current_user_can('edit_forms') && $_POST['name'] && $_POST['time']){

            $notice_name = $_POST['name'];
            $current_statuses = get_option(self::$notice_option);
            $current_statuses[$notice_name] = false;
            update_option(self::$notice_option, $current_statuses);
            wp_schedule_single_event(
                time() + intval($_POST['time']),
                'flp_notice_reset',
                array(
                    $notice_name
                )
            );
            wp_die('Success');

        } else {
            wp_die('Nice try, but you aren\'t allowed to do this');
        }
    }

    /**
     * displays the notices
     *
     * @return void
     */
    public static function run()
    {
        foreach (self::$notices as $notice){
            echo $notice;
        }
    }

    /**
     * Adds a notice to the list of notices
     *
     * @param $notice Flp_Notice
     */
    public static function add_notice($notice)
    {
        array_push(self::$notices, $notice);
        self::add_notice_to_db($notice);
    }

    /**
     * Update the status of a notice
     *
     * @param $notice_name string
     * @param $status string
     */
    public static function update_notice_status( $notice_name, $status )
    {
        $notice_statuses = get_option(self::$notice_option);
        $notice_statuses[$notice_name] = $status;
        update_option(self::$notice_option, $notice_statuses);
    }

    /**
     * After 1 month reactivate notice that was deactivated
     *
     * @param $notice_name string
     */
    public static function reactivate_notice( $notice_name )
    {
        self::update_notice_status( $notice_name, true );
    }

    /**
     * Add the notice's status to the DB if it hasn't been already
     *
     * @param $notice Flp_Notice
     */
    public static function add_notice_to_db($notice)
    {
        $notice_statuses = get_option(self::$notice_option);
        if (!isset($notice_statuses[$notice->name])){
            $notice_statuses[$notice->name] = true;
            update_option(self::$notice_option, $notice_statuses);
        }
    }
}