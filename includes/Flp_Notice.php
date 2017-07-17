<?php

/**
 * Created by PhpStorm.
 * User: Adrian
 * Date: 2017-05-27
 * Time: 11:00 PM
 */
define( 'FLP_NOTICE_ERROR', 'notice-error' );
define( 'FLP_NOTICE_SUCCESS', 'notice-success' );
define( 'FLP_NOTICE_WARNING', 'notice-warning' );
define( 'FLP_NOTICE_INFO', 'notice-info' );

class Flp_Notice
{
    var $message;
    var $dismissible;
    var $is_active;
    var $class;
    var $post;
    var $name;

    /**
     * Flp_Notice constructor.
     * @param $message string
     * @param null $name string
     * @param null $class string
     * @param bool $dismissible bool
     * @param string $post string
     * @param int $time int
     */
    function __construct( $message, $name=null, $class=null, $dismissible=false, $post=null, $time=604800 )
    {
        $this->message = $message;
        //TODO below
        $this->dismissible = $dismissible;
        $this->class = (empty($class))? FLP_NOTICE_SUCCESS : $class;
        $this->post = $post;
        $this->name = $name;
        $this->time = $time;

        Flp_Notice_Manager::add_notice($this);

        $active_notices = get_option(Flp_Notice_Manager::$notice_option);

        if ( $active_notices[$this->name] == true){
            $this->is_active = true;
        }
    }

    private function get_dismiss_form()
    {
        return "<button type='button' name='{$this->name}' data-time='{$this->time}' class='button' onclick='flp_dismiss_notice(this)'>Dismiss</button>";
    }

    /**
     * Echo function support
     *
     * @return string
     */
    function __toString()
    {
        if ((( $_GET['post_type'] == $this->post ) || ( get_post_type( $_GET['post'] ) == $this->post ) || !isset( $this->post )) && $this->is_active ) {
            $notice = "<div class='notice {$this->class}' id='{$this->name}'><div class='flp-notice-icon-container'>
<img src='".FLP_PLUGIN_DIR_URI."assets/icon-30x30.png' class='flp-notice-icon'/></div>
<div class='flp-notice-text-container'><p>{$this->message}</p></div>";
            if ($this->dismissible){
                $notice.="<div class='flp-notice-footer'>{$this->get_dismiss_form()}</div> ";
            }
            $notice.="</div>";
            return $notice;
        } else {
            return '';
        }
    }
}