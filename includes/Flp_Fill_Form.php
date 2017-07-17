<?php

/**
 * Created by PhpStorm.
 * User: Adrian
 * Date: 2017-02-26
 * Time: 7:05 PM
 */
class Flp_Fill_Form
{
    function __construct()
    {
        add_action( 'admin_menu', array('Flp_Fill_Form', 'add_fill_form_page' ));
    }

    /**
     * Adds a Submenu page that allows you to fill out a form.
     */
    public static function add_fill_form_page(){
        add_submenu_page(
            'edit.php?post_type=infusion_form',
            'Fill Out Form',
            'Fill Out Form',
            'manage_forms',
            'fill_form',
            array( 'Flp_Fill_Form', 'fill_out_form_page')
        );
    }

    /**
     * If no id is passed in $_GET, display a list of all forms. Otherwise display the form associated with that ID
     */
    public static function fill_out_form_page()
    {
        $ID = $_GET['form_id'];
        $site_url = get_site_url();
        if (null == $ID || "" == $ID){
            echo "<h1>Please select a form to fill out</h1>";
            echo "<table>";
            $forms = Flp_Utils::flp_get_forms();
            foreach ($forms as $form){
                echo "<tr><td><a class='row-title' href='$site_url/wp-admin/edit.php?post_type=infusion_form&page=fill_form&form_id={$form->ID}'>".$form->post_title."</a></td></tr>";
            }
            echo "</table>";
        } else {
            $form = get_post($ID);
            echo "<h1>".$form->post_title."</h1>";
            echo "<div style='width: 30vw'>";
            echo do_shortcode("[infusion_form id={$ID}]");
            echo "</div>";
            echo "<a href='$site_url/wp-admin/edit.php?post_type=infusion_form&page=fill_form'>Back to all forms.</a>";
        }
    }
}