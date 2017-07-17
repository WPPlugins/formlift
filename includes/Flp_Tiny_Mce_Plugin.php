<?php

/**
 * Created by PhpStorm.
 * User: Adrian
 * Date: 2017-06-06
 * Time: 8:16 PM
 */
class Flp_Tiny_Mce_Plugin
{

    function __construct()
    {
        add_action( 'init', array(  $this, 'setup_tinymce_plugin' ) );
        add_action( 'admin_footer', array( $this, 'formlift_modal_choice') );
    }

    function setup_tiny_mce_plugin()
    {
        //check if user has FormLift privileges...
        if ( !current_user_can('edit_forms') ){
            return;
        }

        if ( get_user_option( 'rich_editing' ) !== 'true' ){
            return;
        }

        add_filter( 'mce_external_plugins', array( &$this, 'add_tiny_mce_plugins') );
        add_filter( 'mce_buttons', array( &$this, 'add_tiny_mce_toolbar_button' ) );
    }

    function add_tiny_mce_plugin( $plugins )
    {
        $plugins['formlift_form'] = FLP_PLUGIN_DIR_URI.'js/formlift-tincy-mce.js';
        return $plugins;
    }

    function add_tiny_mce_toolbar_button( $buttons )
    {
        array_push( $buttons, '|', 'formlift_form');
        return $buttons;
    }

    function formlift_modal_choice()
    {
        if ( current_user_can('edit_forms') )
        {
            ?>
            <? add_thickbox(); ?>
            <div id="flp-form-choice-modal" style="display:none;">
                <?php $this->create_drop_down_field('flp-form-choice', 'Choose a form', Flp_Utils::flp_get_form_dropdown()) ?>
            </div>
            <?php
        }
    }

    private function create_drop_down_field( $id, $title, $values)
    {
        $select = "<select id='$id' onclick=''>";
        foreach ($values as $possible_value => $label ){
            $select.= "<option value='$possible_value'>$label</option>";
        }
        $select.= "</select>";
        return "<label for='$id'>$title</label>" . $select;
    }
}