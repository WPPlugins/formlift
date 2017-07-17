<?php/** * Created by PhpStorm. * User: adrian * Date: 2016-10-28 * Time: 6:11 PM */class Flp_Form{	var $ID;	var $form_meta;	var $defaults;	var $form_name;	var $form_code;	public function __construct( $id=null )	{	    if (empty($id)){            $id = 0;            $this->form_meta = get_option(FormLift::$option_key);        } else {            $this->form_meta = get_post_meta($id, FormLift::$option_key, true);            $this->form_code = get_post_meta($id, 'form_code', true);        }        $this->ID = $id;        $this->defaults = get_option(FormLift::$option_key);        $this->form_name = "infusion-form-".$id;	}    /**     * Reloads the meta for the form if the form settings change after creation     *     * @deprecated     */	public function reload_meta()    {        $this->form_meta = get_post_meta($this->ID, FormLift::$option_key, true);        $this->form_code = get_post_meta($this->ID, 'form_code', true);        $this->defaults = get_option(FormLift::$option_key);    }    /**     * Updates the form's meta array     *     * @param $key string     * @param $value string     */	public function update_meta($key, $value)    {        $this->form_meta[$key] = $value;    }	/**     * Returns the forms style meta value. If none exists, return the empty string     *	 * @param $meta_key string	 * @return mixed	 */	public function get_form_meta($meta_key)    {        return (!empty($this->form_meta[$meta_key]))? $this->form_meta[$meta_key]: '';    }    /**     * Returns the forms style meta value. If none exists, return the default option.     *     * @param $meta_key string     * @return mixed     */    public function get_style_meta($meta_key)    {        $default = (!empty($this->defaults[$meta_key]))? $this->defaults[$meta_key]: Flp_Settings::$default_settings[$meta_key];        $setting = (!empty($this->form_meta[$meta_key]))? $this->form_meta[$meta_key]: $default;        return Flp_Utils::clean_setting($setting);    }    /**     * Returns the settings array of a form     *     * @return mixed     */    public function get_all_settings()    {        return $this->form_meta;    }    /**     * This function is used to determine if this form is being called upon within the specified date range     * or whether or not it's submission count is under that which is specified by the user.     *     * @return bool     */    private function display()    {        $condition = $this->get_form_meta('display_condition');        if ($condition == '1'){            return true;        } elseif ($condition == '2'){            $start_date = $this->get_form_meta('start_date');            $start_time = $this->get_form_meta('start_time');            $end_date = $this->get_form_meta('end_date');            $end_time = $this->get_form_meta('end_time');            $time_zone = get_option('timezone_string');            date_default_timezone_set($time_zone);            $start_date_time = strtotime($start_date.' '.$start_time);            $end_date_time = strtotime($end_date.' '.$end_time);            $now = strtotime('now');            return $start_date_time <= $now && $now <= $end_date_time;        } elseif ($condition == '3'){            $sub = get_post_meta($this->ID, 'num_submissions', true);            $max = $this->get_form_meta('max_submissions');            return intval($sub) < intval($max);        } elseif ($condition == '4'){            return false;        } else {            return true;        }    }	/**return a string of html characters representing the form	 *	 * @return string	 */	public function get_form_code()	{	    return "<!-- This Web Form is powered by FormLift http://formlift.net -->".$this->form_code;	}    /**     * Init the form via shortcode call.     *     * @return mixed|String     */	public function shortcode()    {	    if ($this->display()){            $the_content = $this->get_style_sheet();            $the_content.= $this->get_form_code();            $the_content.= $this->get_front_end_starter_starter_script();            return $the_content;        } else {	        return $this->get_form_meta('no_display_msg');        }    }    /**     * This is used to get the code for the HTML editor.     *     * @return mixed|string     */	public function get_encoded_form_code()    {	    $code = $this->form_code;	    $code = htmlspecialchars($code);	    //$code = preg_replace('/&([a-zA-Z]*);/', '$1;', $code);	    return $code;    }	/**     * Get the stylesheet for a form and populate it with the meta from the form's meta array.     *	 * @return String	 */	public function get_style_sheet()	{	    $style = "		<style type='text/css'>		    		    .g-recaptcha-response{		        display: none;		    }		            @media (max-width: 500px) {                .$this->form_name .infusion-submit .inf-button,                .$this->form_name .infusion-field, .$this->form_name .infusion-submit,                .$this->form_name .infusion-field textarea,			    .$this->form_name .infusion-field select,			    .$this->form_name .infusion-field .infusion-field-input-container,			    .$this->form_name .infusion-field .infusion-radio{                    width: 100% !important;                    float:none !important;                }                .$this->form_name .infusion-field .infusion-radio .infusion-option{                    display: block !important;                }                            }							.$this->form_name {			    box-sizing: border-box;			    padding: {$this->get_style_meta('form_padding_top')} {$this->get_style_meta('form_padding_right')} {$this->get_style_meta('form_padding_bottom')} {$this->get_style_meta('form_padding_left')};				width: {$this->get_style_meta('form_width')};				margin: {$this->get_style_meta('form_margin_top')} {$this->get_style_meta('form_margin_right')} {$this->get_style_meta('form_margin_bottom')} {$this->get_style_meta('form_margin_left')};				text-align: {$this->get_style_meta('form_text_align')};				display: inline-block;				{$this->get_style_meta('form_other')}			}						.$this->form_name div.infusion-submit,			.$this->form_name div.infusion-field {			    box-sizing: border-box;			    width: {$this->get_style_meta('field_width')};				padding: {$this->get_style_meta('field_padding_top')} {$this->get_style_meta('field_padding_right')} {$this->get_style_meta('field_padding_bottom')} {$this->get_style_meta('field_padding_left')};				margin: {$this->get_style_meta('field_margin_top')} {$this->get_style_meta('field_margin_right')} {$this->get_style_meta('field_margin_bottom')} {$this->get_style_meta('field_margin_left')};				display: inline-block;				{$this->get_style_meta('field_other')}			}						.$this->form_name .infusion-field textarea,			.$this->form_name .infusion-field select,			.$this->form_name .infusion-field .infusion-field-input-container {			    box-sizing: border-box;			    vertical-align: middle;				padding: {$this->get_style_meta('input_padding')};				height: {$this->get_style_meta('input_height')};				width: {$this->get_style_meta('input_width')};				max-width: 100%;				border: {$this->get_style_meta('input_border_width')} solid {$this->get_style_meta('input_border_color')} ;				border-radius: {$this->get_style_meta('input_border_radius')};				background: {$this->get_style_meta('input_background_color')};				color: {$this->get_style_meta('input_font_color')};				font-family: {$this->get_style_meta('input_font_family')};				font-size: {$this->get_style_meta('input_font_size')};				font-weight: {$this->get_style_meta('input_font_weight')};				float: {$this->get_style_meta('input_align')};				{$this->get_style_meta('input_other')}			}						.$this->form_name .infusion-field textarea:focus,			.$this->form_name .infusion-field select:focus,			.$this->form_name .infusion-field .infusion-field-input-container:focus{			    outline:none;			    transition: 0.4s;			    box-shadow: 0 0 1px 1px {$this->get_style_meta('input_focus_color')};			}						.$this->form_name .infusion-field .infusion-field-input-container::-moz-placeholder,			.$this->form_name .infusion-field textarea::-moz-placeholder{				color: {$this->get_style_meta('input_placeholder_color')};			}			.$this->form_name .infusion-field .infusion-field-input-container::-webkit-input-placeholder,			.$this->form_name .infusion-field textarea::-webkit-input-placeholder{				color: {$this->get_style_meta('input_placeholder_color')};			}			.$this->form_name .infusion-field .infusion-field-input-container:-ms-input-placeholder,			.$this->form_name .infusion-field textarea:-ms-input-placeholder{				color: {$this->get_style_meta('input_placeholder_color')};			}						.$this->form_name .infusion-field select {			    box-sizing: border-box;				-webkit-appearance: menulist ;				-moz-appearance: menulist ;				appearance: menulist ;            }						.$this->form_name .infusion-field .infusion-radio{				width: {$this->get_style_meta('input_width')};				display: inline-block;                float: {$this->get_style_meta('input_align')};            }                        .$this->form_name .infusion-field .infusion-radio .infusion-option {				display: {$this->get_style_meta('input_radio_display')};				margin-top: {$this->get_style_meta('input_radio_margin')};				padding:0;            }                        .$this->form_name .infusion-field .infusion-radio .infusion-option input[type='radio'],			.$this->form_name .infusion-option input[type='checkbox']{				margin-right: 5px;				transform:scale({$this->get_style_meta('input_radio_scale')});				vertical-align:middle;        	}        	        	.$this->form_name .infusion-field label{        		display:inline;				vertical-align:middle;				line-height: 25px;				color: {$this->get_style_meta('label_font_color')};				font-family: {$this->get_style_meta('label_font_family')};				font-size: {$this->get_style_meta('label_font_size')};				font-weight: {$this->get_style_meta('label_font_weight')};				{$this->get_style_meta('label_other')}        	}        	        	.$this->form_name .infusion-submit {        		text-align: {$this->get_style_meta('button_align')};        	}        	        	.$this->form_name .infusion-submit .inf-button {        	    box-sizing: border-box;        		display:inline-block;				text-align: center;				vertical-align: middle;				width:{$this->get_style_meta('button_width')};				border: {$this->get_style_meta('button_border_width')} solid {$this->get_style_meta('button_border_color')};              				border-radius: {$this->get_style_meta('button_border_radius')};           				padding-top: {$this->get_style_meta('button_padding')};              				padding-bottom: {$this->get_style_meta('button_padding')};          				background-color: {$this->get_style_meta('button_color')};        				color: {$this->get_style_meta('button_font_color')};                   				font-size: {$this->get_style_meta('button_font_size')};               				font-family: {$this->get_style_meta('button_font_family')};             				font-weight: {$this->get_style_meta('button_font_weight')}; 				transition-duration:0.2s;				text-decoration: none ;				text-shadow: none;				text-transform: uppercase;				cursor: pointer;				{$this->get_style_meta('button_other')}        	}        	        	.$this->form_name .infusion-submit .inf-button:focus{        	    outline:none;        	}        	        	.$this->form_name .infusion-submit .inf-button:hover {        		border-color: {$this->get_style_meta('button_border_hover_color')};				background-color: {$this->get_style_meta('button_hover_color')};				color: {$this->get_style_meta('button_font_hover_color')};        	}        	        	.$this->form_name .web-form-error {				margin-top: 5px;				text-align: left;				padding: 10px;				border: solid 1px {$this->get_style_meta('error_border_color')};				border-radius: {$this->get_style_meta('error_border_radius')};				background-color: {$this->get_style_meta('error_background_color')};				transition: 0.4s;				{$this->get_style_meta('error_other')};        	}        	        	.$this->form_name .web-form-error ul{				list-style-type: disc;				margin: 0;				padding:  0 0 0 40px;				color: {$this->get_style_meta('error_font_color')};        	}        	        	.$this->form_name .web-form-error .error-text {				display: block;				line-height: 1.5;				margin: 0 0 0 0;				/*vertical-align: center;*/				color: {$this->get_style_meta('error_font_color')};				font-family: {$this->get_style_meta('error_font_family')};				font-size: {$this->get_style_meta('error_font_size')};			}						.$this->form_name .web-form-error-off{				visibility: hidden;				transition: 0.4s;        	}		</style>		";		return $style;	}    /**     * Get the script to lazy load the form.     *     * @return string     */    public function get_front_end_starter_starter_script()    {        if ("" == $this->form_code || empty($this->form_code)){            return "";        }        return "		<script>            flpForms = document.getElementsByClassName('$this->form_name');            flpForm = flpForms[flpForms.length-1];            flpFormObj = {                id: {$this->ID},                form: flpForm,                errors: {$this->get_errors_string()},                requiredIds: {$this->get_required_fields_string()},                labelsOn: '{$this->get_style_meta('labels_remove')}',                selectOn: '{$this->get_style_meta('select_remove')}',                tracking: '{$this->get_style_meta('tracking_method')}',                autoFill: {$this->get_style_meta('auto_fill_conditions')},                overWrite: {$this->get_style_meta('val_conditions')},                captcha: '{$this->get_form_meta('form_has_captcha')}'            };            FLP_FORM_LIST.push(flpFormObj);		</script>";    }	/**	 * Return a string representation of the required fields of a form.	 *	 * @return string	 */	private function get_required_fields_string()	{        $fields = $this->get_style_meta('required_fields');		$required_fields = (!empty($fields))? explode(',',$fields):array(			'inf_field_Email',		);		$fields = "{";		foreach ($required_fields as $key => $value){			if (($key + 1) == count($required_fields)){				$fields .= "'{$value}':0";			} else {				$fields .= "'{$value}':0,";			}		}		$fields .= "}";		return "{$fields}";	}	/**return a string rep of the error messages for a from	 *	 * @return string	 */	private function get_errors_string()	{		$phone_error = $this->get_style_meta('phone_error');		$email_error = $this->get_style_meta('email_error');		$input_error = $this->get_style_meta('input_error');		$name_error = $this->get_style_meta('name_error');		$postal_error = $this->get_style_meta('postal_error');		return "{'postal_error': '$postal_error', 'name_error':'$name_error','phone_error': '$phone_error', 'email_error': '$email_error', 'input_error': '$input_error'}";	}    /**     * This is only ever used in one place. The settings preview form     * used to overwrite the default form code.     *     * @param $code     */	public function set_form_code($code){		$this->form_code = $code;	}    /**     * Used to change the form's meta style options at runtime.     *     * @param $array     */	public function set_form_meta($array){		$this->form_meta = $array;	}}