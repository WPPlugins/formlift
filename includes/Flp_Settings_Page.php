<?php/** * Created by PhpStorm. * User: Adrian * Date: 2016-10-29 * Time: 3:20 PM */class Flp_Settings_Page{    function __construct()    {        add_action( 'admin_menu' , array('Flp_Settings_Page' , 'add_this_page') );    }    /**     * adds the settings page to the menu     */    public static function add_this_page()    {        add_submenu_page(            'edit.php?post_type=infusion_form',            'Settings',            'Settings',            'manage_forms',            'default_settings_page',            array('Flp_Settings_Page', 'create_page')        );    }    /**     * displays the setting HTML and a notice if FormLift is not licensed     */    public static function create_page()    {        ?>        <form method="post" action="options.php">            <?php settings_fields( FormLift::$option_key ); ?>            <?php $page = new Flp_Settings_skin(); echo $page; ?>        </form>        <?php    }}