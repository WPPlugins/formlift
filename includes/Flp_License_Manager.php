<?php

/**
 * Created by PhpStorm.
 * User: Adrian
 * Date: 2017-02-05
 * Time: 12:05 PM
 */
// This is the secret key for API authentication. You configured it in the settings menu of the license manager plugin.
define('FLP_SPECIAL_SECRET_KEY', '58975594bd7ea9.91283673'); //Rename this constant name so it is specific to your plugin or theme.

// This is the URL where API query request will be sent to. This should be the URL of the site where you have installed the main license manager plugin. Get this value from the integration help page.
define('FLP_LICENSE_SERVER_URL', 'http://formlift.net'); //Rename this constant name so it is specific to your plugin or theme.

// This is a value that will be recorded in the license manager data so you can identify licenses for this item/product.
define('FLP_ITEM_REFERENCE', 'FormLift-PRO'); //Rename this constant name so it is specific to your plugin or theme.

class Flp_License_Manager
{

    function __construct()
    {
        add_filter('flp_save_settings', array('Flp_License_Manager', 'do_license'));
        add_action('flp_verify_license', array('Flp_License_Manager', 'verify_license'));
    }

    /**
     * Runs the appropriate function to activate or deactivate a license.
     *
     * @param $options array
     * @return mixed array
     */
    public static function do_license($options)
    {
        if (isset($options['activate_license'])) {
            Flp_License_Manager::activate_license($options);
        } elseif (isset($options['deactivate_license'])){
            Flp_License_Manager::deactivate_license($options);
        }

        return $options;
    }

    /**
     * Upon the activate button being clicked in the license manager form, send a message to formlift.net to see whether
     * the license is valid or not.
     *
     * @param $array array the license key
     * @return void
     */
    public static function activate_license($array)
    {

        $license_key = $array['flp_license_key'];

        if (Flp_Utils::option_exists('flp_license_key')){
            Flp_Utils::flp_update_option('flp_license_response', 'License already activated.');
            return;
        }

        $api_params = array(
            'slm_action' => 'slm_activate',
            'secret_key' => FLP_SPECIAL_SECRET_KEY,
            'license_key' => $license_key,
            'registered_domain' => $_SERVER['SERVER_NAME'],
            'item_reference' => urlencode(FLP_ITEM_REFERENCE),
        );

        $query = esc_url_raw(add_query_arg($api_params, FLP_LICENSE_SERVER_URL));
        $response = wp_remote_get($query, array('timeout' => 20, 'sslverify' => false));

        if (is_wp_error($response)) {
            Flp_Utils::flp_update_option('flp_license_response', 'Oops, something went wrong...');
            return;
        }

        $license_data = json_decode(wp_remote_retrieve_body($response));

        if ($license_data->result == 'success' ) {
            Flp_Utils::flp_update_option('flp_license_response', 'The following message was returned from the server: ' . $license_data->message);
            $notice = new Flp_Notice('Congrats! You are now running FormLift Premium!', FLP_NOTICE_SUCCESS, true);
            Flp_Utils::flp_update_option('flp_license_key', $license_key);
            Flp_Utils::flp_update_option('flp_is_active', "true");

            //start verifying license again
            wp_schedule_event( time() + 120, 'daily', 'flp_verify_license');
        } else {
            Flp_Utils::flp_update_option('flp_license_response', 'Something went wrong, the following message was returned from the server: ' . $license_data->message);
            $notice = new Flp_Notice('Hmmm, something is not quite right... see the message below for details', FLP_NOTICE_ERROR, true);
        }
    }

    /**
     * Sends a deactivation message to formlift.net to remove this website from the associated list of activated sites
     * it then deactivates the license on this domain
     *
     * @param $array array the license key
     * @return void
     */
    public static function deactivate_license($array)
    {
        $license_key = $array['flp_license_key'];

        $api_params = array(
            'slm_action' => 'slm_deactivate',
            'secret_key' => FLP_SPECIAL_SECRET_KEY,
            'license_key' => $license_key,
            'registered_domain' => $_SERVER['SERVER_NAME'],
            'item_reference' => urlencode(FLP_ITEM_REFERENCE),
        );

        $query = esc_url_raw(add_query_arg($api_params, FLP_LICENSE_SERVER_URL));
        $response = wp_remote_get($query, array('timeout' => 20, 'sslverify' => false));

        if (is_wp_error($response)) {
            Flp_Utils::flp_update_option('flp_license_response', 'Oops, something went wrong...');
        }

        $license_data = json_decode(wp_remote_retrieve_body($response));

        wp_clear_scheduled_hook( 'flp_verify_license' );

        Flp_Utils::flp_update_option('flp_license_response', 'The following message was returned from the server: ' . $license_data->message);
        delete_option('flp_license_key');
        delete_option('flp_is_active');
    }

    /**
     * Once daily asks formlift.net if the license used by the application is still valid. If it is not, then it
     * deactivates the license on the installation and show a message as to the reason of deactivation.
     *
     * @return void
     */
    public static function verify_license(){

        $api_params = array(
            'slm_action' => 'slm_check',
            'secret_key' => FLP_SPECIAL_SECRET_KEY,
            'license_key' => get_option('flp_license_key'),
            'registered_domain' => $_SERVER['SERVER_NAME'],
            'item_reference' => urlencode(FLP_ITEM_REFERENCE),
        );

        $query = esc_url_raw(add_query_arg($api_params, FLP_LICENSE_SERVER_URL));
        $response = wp_remote_get($query, array('timeout' => 20, 'sslverify' => false));
        $license_data = json_decode(wp_remote_retrieve_body($response));

        if ($license_data->result == 'error' || $license_data->status == 'blocked' || $license_data->status == 'expired'){
            if ($license_data->status == 'expired'){
                Flp_Utils::flp_update_option('flp_license_response', 'Your license has expired. If you wish to renew your license please <a href="https://formlift.net">contact us</a>.');
            } elseif ($license_data->status == 'blocked'){
                Flp_Utils::flp_update_option('flp_license_response', 'Your license has been blocked. This may have happened if you requested a refund. To reactivate your license please <a href="https://formlift.net">contact us</a>.');
            }
            delete_option('flp_license_key');
            delete_option('flp_is_active');
            //license has expired, no longer need to check status
            wp_clear_scheduled_hook( 'flp_verify_license' );

        } else {
            Flp_Utils::flp_update_option('flp_license_response', 'Your license is currently active.');
        }
    }

    /**
     * Returns the form code for the license manager in the settings panel
     *
     * @return string
     */
    public static function create_form()
    {
        $license = get_option('flp_license_key');
        $message = get_option('flp_license_response');
        $option_key = FormLift::$option_key;
        $manager =  "$message
        <table class='form-table'>
            <tr>
                <th style='width:100px;'><label for='flp_license_key'>License Key</label></th>
                <td ><input class='regular-text' type='text' id='flp_license_key' name='{$option_key}[flp_license_key]'  value='$license' ></td>
            </tr>
        </table>
        <p class='submit'>
            <input type='submit' name='{$option_key}[activate_license]' value='Activate' class='button-primary' />
            <input type='submit' name='{$option_key}[deactivate_license]' value='Deactivate' class='button' />
        </p>
        ";
        return $manager;
    }

    /**
     * Returns whether FormLift is actually licensed or not
     *
     * @return bool
     */
    public static function is_licensed(){
        $key = get_option('flp_license_key');
        $active = get_option('flp_is_active');
        return isset($key) and  $active == 'true';
    }
}