<?php

/**
 * Created by PhpStorm.
 * User: Adrian
 * Date: 2017-03-14
 * Time: 11:27 PM
 *
 *
 * It will need:
 * 1. To be able to inhale HTML code
 * 2. Turn each field-container into a draggable component with Label and options
 * 3. Make each of the attributes editable for each field
 * 4. Add custom fields?
 * 5. Replace Html
 * 6. Ajax request of HTML -> exits in flp_utils?
 * 7.
 */

/**
 * Class Flp_Form_Builder
 *
 * Contains all functions pertaining to the manipulation of a Form's settings and HTML
 */
class Flp_Form_Builder
{
    function __construct()
    {
        add_action('flp_before_save_form', array('Flp_Form_Builder', 'update_form_contents'));
        add_action('flp_before_save_form', array('Flp_Form_Builder', 'import_form'));
        add_action('flp_before_save_form', array('Flp_Form_Builder', 'save_settings'));
        add_action('flp_before_save_form', array('Flp_Form_Builder', 'import_form_settings'));
    }

    /**
     * Parses the HTML from either the HTML editor or the API code and cleans it to comply with FormLift standards
     * and structure.
     *
     * @param $code
     * @param $post_id
     * @return string
     */
    public static function clean_infusionsoft_form_html($code, $post_id){
        if (empty($code) || !isset($code)){
            $code = "Something went wrong...";
        } else {
            $the_form = new Flp_Form($post_id);
            libxml_use_internal_errors(true);

            $doc = new DOMDocument("1.0", "UTF-8");
            $doc->loadHTML($code);

            libxml_use_internal_errors(false);
            $startingNode = $doc->getElementsByTagName('form')->item(0);
            if ($startingNode === Null){
                return "Something went wrong...";
            }
            if ($startingNode->hasAttribute('onsubmit')){
                $startingNode->removeAttribute('onsubmit');
            }

            $doc->saveHTML($startingNode);

            $newDoc = new DOMDocument("1.0", "UTF-8");
            $form = $newDoc->importNode($startingNode);
            $form->setAttribute('class', 'infusion-form-'.$post_id);
            $domTree = new RecursiveIteratorIterator(
                new RecursiveDOMIterator($doc),
                RecursiveIteratorIterator::SELF_FIRST
            );

            /**
             * Iterate recursivly over the DOM tree
             *
             * @param $inputNode DOMElement
             */
            foreach ($domTree as $inputNode){

                if ($inputNode->nodeType === XML_ELEMENT_NODE && $inputNode->tagName == 'label'){
                    /**
                     * @param $newNode DOMElement
                     * @param $e DOMElement
                     */
                    $e = self::get_associated_element($inputNode, $doc); //$e DOMElement
                    if (empty($e)){
                        //then it's the start of a radio selection
                        //add a radio div with the label
                        $div = self::create_container($newDoc);
                        $subDiv = self::create_div_radio($newDoc);
                        $newNode = $newDoc->importNode($inputNode, true);
                        $newNode->removeAttribute('class');
                        $div->appendChild($newNode);
                        $div->appendChild($subDiv);
                        $form->appendChild($div);
                    } elseif ($e->tagName == 'input' && $e->getAttribute('type') == 'radio'){
                        //add it to the last container
                        $span = self::create_span_radio($newDoc);
                        $newNode = $newDoc->importNode($inputNode, true);
                        $e = $newDoc->importNode($e, true);
                        $e->removeAttribute('style');
                        $span->appendChild($e);
                        $span->appendChild($newNode);
                        $form->lastChild->lastChild->appendChild($span);
                    } elseif ($e->tagName == 'input' && $e->getAttribute('type') == 'checkbox'){
                        //create it's own special thing
                        $div = self::create_container($newDoc);
                        $span = self::create_span_radio($newDoc);
                        $newNode = $newDoc->importNode($inputNode, true);
                        $e = $newDoc->importNode($e, true);
                        $e->removeAttribute('style');
                        $span->appendChild($e);
                        $span->appendChild($newNode);
                        $div->appendChild($span);
                        $form->appendChild($div);
                    } else {
                        //than it's a text, a select, or a textarea
                        $div = self::create_container($newDoc);
                        $newNode = $newDoc->importNode($inputNode, true);
                        $e = $newDoc->importNode($e, true);
                        $div->appendChild($newNode);
                        $div->appendChild($e);
                        $e->removeAttribute('style');
                        $e->removeAttribute('cols');
                        if ($e->hasAttribute('onkeydown')) {
                            $e->removeAttribute('onkeydown');
                            $e->setAttribute('data-type', 'date');
                            $e->setAttribute('data-changeMonth', 'true');
                            $e->setAttribute('data-changeYear', 'true');
                            $e->setAttribute('data-minDate', '0');
                            $e->setAttribute('data-maxDate', '14');
                        }
                        $form->appendChild($div);
                    }

                } elseif ($inputNode->nodeType === XML_ELEMENT_NODE && $inputNode->tagName == 'input' && $inputNode->getAttribute('type') == 'hidden'){
                    $inputNode = $newDoc->importNode($inputNode);
                    $newNode = $newDoc->importNode($inputNode, true);
                    $form->appendChild($newNode);
                } elseif ($inputNode->nodeType === XML_ELEMENT_NODE && ($inputNode->tagName == 'input' && $inputNode->getAttribute('type') == 'submit')){
                    $div = self::create_button_container($newDoc);
                    $newNode = $newDoc->importNode($inputNode, true);
                    $innerText = $newNode->getAttribute('value');
                    $submitNode = $newDoc->createElement('button');
                    $submitNode->textContent = $innerText;
                    $submitNode->setAttribute('class', 'inf-button');
                    $submitNode->setAttribute('type', 'submit');
                    $div->appendChild($submitNode);
                    $form->appendChild($div);
                } elseif ($inputNode->nodeType === XML_ELEMENT_NODE && $inputNode->tagName == 'button'){
                    $div = self::create_button_container($newDoc);
                    $newNode = $newDoc->importNode($inputNode, true);
                    $newNode->removeAttribute('style');
                    $newNode->removeAttribute('value');
                    $newNode->setAttribute('class', 'inf-button');
                    $newNode->setAttribute('type', 'submit');
                    $div->appendChild($newNode);
                    $form->appendChild($div);
                }  elseif ($inputNode->nodeType === XML_ELEMENT_NODE && $inputNode->tagName == 'img' && $inputNode->hasAttribute('onclick')){
                    //its captcha
                    $div = self::create_container($newDoc);
                    $newNode = $newDoc->importNode($inputNode, true);
                    $domTree->next();
                    $newNextNode = $newDoc->importNode($domTree->current(), true);
                    $div->appendChild($newNode);
                    $div->appendChild($newNextNode);
                    $form->appendChild($div);
                } elseif ($inputNode->nodeType === XML_ELEMENT_NODE && preg_match('/^(img|h1|h2|h3|h4|h5|p){1}$/',$inputNode->tagName)){
                    $newNode = $newDoc->importNode($inputNode, true);
                    //$div = self::create_container($newDoc);
                    //$div->appendChild($newNode);
                    $form->appendChild($newNode);
                }
            }
            $appName = Flp_Utils::get_setting('infusionsoft_app_name');
            $trackingScript = "<script src='https://$appName.infusionsoft.com/app/webTracking/getTrackingCode'></script>";
            $code = $newDoc->saveHTML($form).$trackingScript;
            $code = preg_replace('/></', '>
            <', $code);
            $code = preg_replace('/(<textarea.*>)\s*(<\/textarea>)/', '$1$2', $code);
        }
        return $code;
    }

    /**
     * Gets the input element of a given label element
     *
     * @param $label DOMElement
     * @param $doc DOMDocument
     * @return DOMElement
     */
    public static function get_associated_element($label, $doc)
    {
        $id = $label->getAttribute('for');
        $domTree = new RecursiveIteratorIterator(
            new RecursiveDOMIterator($doc),
            RecursiveIteratorIterator::SELF_FIRST
        );
        foreach ( $domTree as $node ) {
            if ($node->nodeType === XML_ELEMENT_NODE && $node->hasAttributes() && $node->hasAttribute('id') && $node->getAttribute('id') == $id){
                return $node;
            }
        }
        return Null;
    }

    /**
     * Gets the label element of a given input element
     *
     * @param $input DOMElement
     * @param $doc DOMDocument
     * @return DOMElement
     */
    public static function get_associated_label_element( $input, $doc)
    {
        $id = $input->getAttribute('placeholder');
        $domTree = new RecursiveIteratorIterator(
            new RecursiveDOMIterator($doc),
            RecursiveIteratorIterator::SELF_FIRST
        );
        foreach ( $domTree as $node ) {
            if ($node->nodeType === XML_ELEMENT_NODE && $node->hasAttributes() && $node->hasAttribute('for') && $node->getAttribute('for') == $id){
                return $node;
            }
        }
        return Null;
    }

    /**
     * Creates a radio button section container
     * 2 levels deep
     *
     * @param $doc DOMDocument
     * @return mixed
     */
    private static function create_div_radio($doc)
    {
        $div = $doc->createElement('div');
        $div->setAttribute('class', 'infusion-radio');
        return $div;
    }

    /**
     * Creates a radio input container
     * 3 levels deep
     *
     * @param $doc DOMDocument
     * @return DOMElement
     */
    private static function create_span_radio($doc)
    {
        $div = $doc->createElement('span');
        $div->setAttribute('class', 'infusion-option');
        return $div;
    }

    /**
     * Creates a form element container.
     * 1 level deep
     *
     * @param $doc DOMDocument
     * @return DOMElement
     */
    private static function create_container($doc)
    {
        $div = $doc->createElement('div');
        $div->setAttribute('class', 'infusion-field');
        return $div;
    }

    /**
     * Creates button container
     * 1 level deep
     *
     * @param $doc DOMDocument
     * @return DOMElement
     */
    private static function create_button_container($doc)
    {
        $div = $doc->createElement('div');
        $div->setAttribute('class', 'infusion-submit');
        return $div;
    }

    /**
     * Creates button container
     * 1 level deep
     *
     * @param $doc DOMDocument
     * @param
     * @return DOMElement
     */
    private static function create_label($doc, $text)
    {
        $div = $doc->createElement('label');
        $div->setAttribute('class', 'infusion-submit');
        return $div;
    }

    /**
     * Imports the from from the API and rus the cleaning algorithm
     *
     * @param $post_id
     */
    public static function import_form($post_id)
    {
        if (isset($_POST[FormLift::$option_key]['form_refresh'])){

            $temp_code = Flp_Utils::get_infusionsoft_form_html($_POST[FormLift::$option_key]['infusionsoft_form_id']);
            $temp_code = Flp_Form_Builder::clean_infusionsoft_form_html($temp_code, $post_id);
            Flp_Utils::flp_update_meta($post_id, 'form_code', $temp_code);
        }
    }

    /**
     * Cleans the HTML of a form
     *
     * @param $post_id
     */
    public static function update_form_contents($post_id)
    {
        if (isset($_POST['form_code']) && !isset($_POST[FormLift::$option_key]['form_refresh'])){

            $code = $_POST['form_code'];
            $code = str_replace('\"', '"', $code);
            $code = Flp_Form_Builder::clean_infusionsoft_form_html($code, $post_id);
            $code = utf8_decode($code);
            Flp_Utils::flp_update_meta($post_id, 'form_code', $code);
        }
    }

    /**
     * Gets the form settings of another form and replaces  the settings of this form with them
     *
     * @param $post_id
     */
    public static function import_form_settings($post_id)
    {
        if (isset($_POST[FormLift::$option_key]['do_form_import'])){

            $otherForm = new Flp_Form($_POST[FormLift::$option_key]['import_form_id']);
            $settings = $otherForm->get_all_settings();
            Flp_Utils::flp_update_meta($post_id, FormLift::$option_key, $settings);
        }
    }

    /**
     * Get the required fields given by the settings options and parse them into an array to be used with the javascript
     * validation algorithm
     *
     * @param $options
     * @return string
     */
    public static function flp_compile_required_fields($options){
        $required_fields = array();
        foreach ( $options as $key => $value){
            if ( preg_match('/required/', $key)){
                array_push($required_fields, preg_replace('/required_/','',$key));
            }
        }
        return implode(',',$required_fields);
    }

    /**
     * Save the settings panel settings
     *
     * @param $post_id
     */
    public static function save_settings($post_id)
    {
        if (!empty($_POST[FormLift::$option_key])){
            $_POST[FormLift::$option_key]['required_fields'] = self::flp_compile_required_fields($_POST[FormLift::$option_key]);

            $_POST[FormLift::$option_key] = Flp_Settings::clean_settings($_POST[FormLift::$option_key]);

            Flp_Utils::flp_update_meta($post_id, FormLift::$option_key, $_POST[FormLift::$option_key]);
        } else {
            delete_post_meta($post_id, FormLift::$option_key);
        }
    }
}