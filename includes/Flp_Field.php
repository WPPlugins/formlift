<?php

/**
 * Created by PhpStorm.
 * User: Adrian
 * Date: 2017-02-26
 * Time: 12:27 PM
 */

define('FLP_INPUT', 'input');
define('FLP_COLOR', 'color');
define('FLP_SELECT', 'select');
define('FLP_TEXT', 'textarea');
define('FLP_RADIO', 'radio');
define('FLP_MULTI', 'multi');
define('FLP_CHECKBOX', 'checkbox');
define('FLP_HIDDEN', 'hidden');
define('FLP_BUTTON', 'button');
define('FLP_ERROR', 'error');
define('FLP_DATE', 'date');
define('FLP_TIME', 'time');
define('FLP_SEPARATOR', 'separator' );
define('FLP_TITLE', 'title');
define('FLP_NUMBER', 'number');
define('FLP_SECRET', 'secret');

class Flp_Field
{
    var $id;
    var $label;
    var $type;
    var $values;
    var $sub_fields;

    function __construct($type, $id='', $label='', $values=array(), $sub_fields=array())
    {
        $this->id = $id;
        $this->label = $label;
        $this->type = $type;
        $this->values = $values;
        $this->sub_fields = $sub_fields;
    }
}
