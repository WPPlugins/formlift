<?php/** * Created by PhpStorm. * User: Adrian * Date: 2016-10-29 * Time: 5:54 PM */class Flp_Redirect_Box{    var $ID;    var $form;    public function __construct($post_id)    {        $this->ID = $post_id;        $this->form = new Flp_Form($post_id);    }    /**     * Creates the HTML for the redirect meta box builder.     */    public function create_page()    {        ?>        <h2>To avoid issues when setting up redirects <a target="_blank" href="<?php echo FLP_TUTORIALS_URL?>">watch the tutorial.</a></h2>        <style>            .create-new-redirect-area{                padding:10px;                display: block;            }            .copy-area{                display: block;            }            .text-cell{                padding:5px;            }        </style>        <div class="create-new-redirect-area copy-area">            <label for="thank_you_page_id">Select a local fall-back page </label>            <?php $ID = $this->get_selected(); wp_dropdown_pages( array('name' => 'flp_thank_you_page_id', 'id' => 'thank_you_page_id', 'selected' => $ID, 'value_field' => 'ID', 'class' => 'flp-max-width'))?>        <script>            document.getElementById('thank_you_page_id').onchange = function (){                //console.log(this.value);                flpGetThankYouPageUri(this.value, <?php echo $this->ID ?>);            };        </script>        <input class="flp-input" id="flp-redirect-uri" name="flp_thank_you_page_uri" value="<?php echo $this->get_redirect_uri()?>" readonly/>        <button type="button" class="flp-button flp-copy-shortcode-button" onclick="copy_shortcode('#flp-redirect-uri')">Copy Thank You Page URL</button>        </div>        <div class="create-new-redirect-area">            <label for="flp-redirect-uri-backup" >Default Thank You Page </label>            <input class="flp-input" style="width: 400px;margin-left:15px" id="flp-redirect-uri-backup" name="flp_thank_you_page_backup" value="<?php echo get_post_meta($this->ID, 'flp_thank_you_page_backup', true);?>" placeholder="http://www.example.com"/>        </div>        <div class="create-new-redirect-area">            <button type="button" class="flp-button flp-create-redirect-button" onclick="flpCreateRow(false)">Create Redirect</button>        </div>        <!-- the redirect area        <script src="https://code.jquery.com/jquery-1.12.4.js"></script>        <script src="https://code.jquery.com/ui/1.12.1/jquery-ui.js"></script>-->        <script>            jQuery( function() {                jQuery( ".sortable" ).sortable({                    revert: true                });                jQuery( "ul, li" ).disableSelection();            } );        </script>        <ul id="formlift-redirect-mb-table" class="sortable">        </ul>        <script>            var FORM_ID = <?php echo $this->ID; ?>;            var REDIRECT_OPTIONS = <?php echo $this->get_options(); ?>;            jQuery(document).ready(function () {                flpInitRedirectArea();            });        </script>        <?php    }    /**     * Gets the pre-set redirect settings and returns as a javascript array for later parsing     *     * @return string     */    private function get_options()    {        $fields = get_post_meta($this->ID, 'flp_fields', true);        $conditions = get_post_meta($this->ID, 'flp_conditions', true);        $values = get_post_meta($this->ID, 'flp_values', true);        $urls = get_post_meta($this->ID, 'flp_urls', true);        $output = "[";        $i = 0;        if (!empty($fields)){            foreach ($fields as $field){                $condition = $conditions[$i];                $value = $values[$i];                $url = $urls[$i];                $output.= "{field:'$field', value: '$value', condition: '$condition', url:'$url'}";                $i += 1;                if ($i < count($fields)){                    $output.=",";                }            }        }        return $output."]";    }    /**     * Returns the URI of the redirect     *     * @return mixed|string     */    private function get_redirect_uri(){        $thankyou_uri = get_post_meta($this->ID, 'flp_thank_you_page_uri', true);        if (empty($thankyou_uri)){            $thankyou_uri = get_site_url()."/?form_action=formlift_redirect&form_id=".$this->ID;        }        return $thankyou_uri;    }    /**     * Pre-selects the fallback thank you page     *     * @return int     */    private function get_selected(){        $ID = get_post_meta($this->ID, 'flp_thank_you_page_id', true);        if (empty($ID)){            $ID = get_option( 'page_on_front' );        }        return $ID;    }}