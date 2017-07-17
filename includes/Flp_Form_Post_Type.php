<?php

/**
 * Created by PhpStorm.
 * User: Adrian
 * Date: 2017-02-26
 * Time: 6:47 PM
 *
 * Anything to do with taxonomy, post_type and meta boxes goes here!
 */
class Flp_Form_Post_Type
{
    function __construct()
    {
        add_action( 'init', array('Flp_Form_Post_Type', 'create_form_lift_post_type' ));
        add_filter( 'manage_infusion_form_posts_columns', array('Flp_Form_Post_Type', 'set_custom_edit_infusion_form_columns' ));
        add_action( 'manage_infusion_form_posts_custom_column' , array('Flp_Form_Post_Type','flp_custom_column'), 10, 2 );
        add_filter( 'post_row_actions', array('Flp_Form_Post_Type','flp_action_row'), 10, 2);
        add_filter( 'manage_edit-infusion_form_sortable_columns', array('Flp_Form_Post_Type', 'flp_my_sortable_num_subs' ));
        add_action( 'pre_get_posts', array('Flp_Form_Post_Type','flp_slice_orderby') );
        add_action( 'add_meta_boxes', array('Flp_Form_Post_Type', 'add_meta_box'));
        add_action( 'save_post', array('Flp_Form_Post_Type', 'save' ));
    }

    /**
     * Add the FormLift post type as well as add the campaign taxonomy
     */
    public static function create_form_lift_post_type()
    {
        //forms
        $labels = array(
            'name'                  => _('FormLift'),
            'singular_name'         => _('Infusionsoft Form'),
            'add_new'               => _('Add Form'),
            'add_new_item'          => _('Add Form'),
            'all_items'             => _('All Forms'),
            'edit_item'             => _('Edit Form'),
            'new_item'              => _('New Form'),
            'view'                  => _('View'),
            'view_item'             => _('View Form'),
            'search_items'          => _('Search Forms'),
            'not_found'             => _('No Forms Found'),
            'not_found_in_trash'    => _('No Forms Found In Trash'),
            'archives'              => _('Form Archives')
        );

        $args = array(
            'labels'               => $labels,
            'public'               => false, // it's not public, it shouldn't have it's own permalink, and so on
            'publicly_queryable'   => true,  // you should be able to query it
            'show_ui'              => true,  // you should be able to edit it in wp-admin
            'exclude_from_search'  => true,  // you should exclude it from search results
            'show_in_nav_menus'    => false, // you shouldn't be able to add it to menus
            'has_archive'          => false, // it shouldn't have archive page
            'rewrite'              => false, // it shouldn't have rewrite rules
            'show_in_admin_bar'    => false,
            'menu_icon'            => FLP_PLUGIN_DIR_URI . 'assets/icon-20x20.png',
            'capability_type'      => 'form',
            'map_meta_cap'         => true,
            'capabilities'         => array(
                // meta caps (don't assign these to roles)
                'edit_post'              => 'edit_form',
                'read_post'              => 'read_form',
                'delete_post'            => 'delete_form',
                // primitive/meta caps
                'create_posts'           => 'create_forms',
                // primitive caps used outside of map_meta_cap()
                'edit_posts'             => 'edit_forms',
                'edit_others_posts'      => 'manage_forms',
                'publish_posts'          => 'manage_forms',
                'read_private_posts'     => 'read',
                // primitive caps used inside of map_meta_cap()
                'read'                   => 'read',
                'delete_posts'           => 'manage_forms',
                'delete_private_posts'   => 'manage_forms',
                'delete_published_posts' => 'manage_forms',
                'delete_others_posts'    => 'manage_forms',
                'edit_private_posts'     => 'edit_forms',
                'edit_published_posts'   => 'edit_forms'
            ),
            'supports' => array(
                'title',
                'author'
            )
        );

        register_post_type( 'infusion_form', $args);

        //campaigns
        $labels = array(
            'name'              => _x( 'Campaign', 'taxonomy general name', 'textdomain' ),
            'singular_name'     => _x( 'Campaign', 'taxonomy singular name', 'textdomain' ),
            'search_items'      => __( 'Search Campaigns', 'textdomain' ),
            'all_items'         => __( 'All Campaigns', 'textdomain' ),
            'parent_item'       => __( 'Parent Campaign', 'textdomain' ),
            'parent_item_colon' => __( 'Parent Campaign:', 'textdomain' ),
            'edit_item'         => __( 'Edit Campaign', 'textdomain' ),
            'update_item'       => __( 'Update Campaign', 'textdomain' ),
            'add_new_item'      => __( 'Add New Campaign', 'textdomain' ),
            'new_item_name'     => __( 'New Campaign Name', 'textdomain' ),
            'menu_name'         => __( 'Campaigns', 'textdomain' ),
        );
        $args = array(
            'hierarchical'      => true,
            'labels'            => $labels,
            'show_ui'           => true,
            'show_admin_column' => true,
            'query_var'         => true,
            'rewrite'           => array( 'slug' => 'campaign' ),
        );
        register_taxonomy(
            'campaigns',
            array('infusion_form','flp_booking_link'),
            $args
        );
    }

    /**
     * Custom colunm for the Form list
     *
     * @param $columns array
     * @return array
     */
    public static function set_custom_edit_infusion_form_columns($columns)
    {
        $new = array();
        $new['cb'] = $columns['cb'];
        $new['title'] = $columns['title'];
        $new['short_code'] = __( 'Short-Code', 'short_code_domain' );
        $new['num_impressions'] = __('Impressions', 'impress_dom');
        $new['num_subs'] = __( 'Submissions', 'submiss_dom' );
        $new['avg_conversion'] = __('Avg. Conversion Rate', 'converge_dom');
        $new['campaigns'] =  __('Campaigns', 'campaign_domain');
        $new['tracking_date'] = __('Tracking Since', 'tracking_domain');
        return $new;
    }

    /**
     * What to display for each custum column
     *
     * @param $column
     * @param $post_id
     */
    public static function flp_custom_column( $column, $post_id ) {
        $site_url = get_site_url();
        switch ( $column ) {
            case 'short_code' :
                $terms = "<input id='form_shortcode_area_{$post_id}' type='text' style='width:100%' value='[infusion_form id=\"{$post_id}\"]' readonly/><button type='button' style='width:100%' onclick='copy_shortcode(\"#form_shortcode_area_{$post_id}\")'>COPY SHORTCODE</button>";
                if ( is_string( $terms ) )
                    echo $terms;
                else
                    _e( 'Unable to get short code :(', 'short_code_domain' );
                break;
            case 'num_subs' :
                $num = get_post_meta($post_id, 'num_submissions', true);
                $num_subs = (!empty($num))?$num:0;
                $terms = sprintf('<h3>%s</h3>',$num_subs);
                if ( is_string( $terms ) )
                    echo $terms;
                else
                    _e( 'no submissions.' , 'submiss_dom');
                break;
            case 'num_impressions' :
                $num = get_post_meta($post_id, 'num_impressions', true);
                $num_subs = (!empty($num))?$num:0;
                $terms = sprintf('<h3>%s</h3>',$num_subs);
                if ( is_string( $terms ) )
                    echo $terms;
                else
                    _e( 'no impressions' , 'submiss_dom');
                break;
            case 'avg_conversion' :
                $num = get_post_meta($post_id, 'conversion_rate', true);
                $num_subs = (!empty($num))?$num:0;
                $terms = sprintf('<h3>%s&#37;</h3>',$num_subs);
                if ( is_string( $terms ) )
                    echo $terms;
                else
                    _e( 'no conversions' , 'converge_dom');
                break;
            case 'campaigns' :
                $taxonomy = "campaigns";
                $post_type = get_post_type($post_id);
                $terms = get_the_terms($post_id, $taxonomy);

                if (!empty($terms) ) {
                    foreach ( $terms as $term )
                        $post_terms[] ="<a href='$site_url/wp-admin/edit.php?post_type={$post_type}&{$taxonomy}={$term->slug}'> " .esc_html(sanitize_term_field('name', $term->name, $term->term_id, $taxonomy, 'edit')) . "</a>";
                    echo join('<br />', $post_terms );
                }
                else echo '<i>No Campaign Set. </i>';
                break;
            case 'tracking_date' :
                if (get_post_meta($post_id,'tracking_date',true)){
                    $date = get_post_meta($post_id,'tracking_date',true);
                    echo $date;
                } else {
                    echo '<i>Tracking date was not set properly...</i>';
                }
                break;

        }
    }

    /**
     * Adds the Fill Out Form and Reset Stats actions
     *
     * @param $actions
     * @param $post
     * @return array
     */
    public static function flp_action_row($actions, $post){
        //check for your post type
        if ($post->post_type =="infusion_form"){
            $new_actions = array();
            $new_actions['edit'] = $actions['edit'];
            $new_actions['inline hide-if-no-js'] = $actions['inline hide-if-no-js'];
            $new_actions['trash'] = $actions['trash'];
            $site_url = get_site_url();
            $new_actions['reset_stats'] = "<a style='color:limegreen;cursor:pointer' onclick='resetStats({$post->ID})'>Reset Stats</a>";
            $new_actions['fill_form'] = "<a style='color:blue;' href='$site_url/wp-admin/edit.php?post_type=infusion_form&page=fill_form&form_id={$post->ID}'>Fill out Form</a>";
            return $new_actions;
        } else {
            return $actions;
        }
    }

    /**
     * Allow Forms to be sorted by Conversion Rate, Number of Impressions, and Number of Submissions
     *
     * @param $query
     */
    public static function flp_slice_orderby( $query ) {
        if( ! is_admin() )
            return;

        $orderby = $query->get('orderby');

        if( 'num_subs' == $orderby ) {
            $query->set('meta_key','num_submissions');
            $query->set('orderby','meta_value_num');
        } else if ('num_impressions' == $orderby){
            $query->set('meta_key','num_impressions');
            $query->set('orderby','meta_value_num');
        } else if ('avg_conversion' == $orderby){
            $query->set('meta_key','conversion_rate');
            $query->set('orderby','meta_value_num');
        }
    }

    /**
     * give columns sortable trait
     *
     * @param $columns
     * @return mixed
     */
    public static function flp_my_sortable_num_subs( $columns ) {
        $columns['num_subs'] = 'num_subs';
        $columns['num_impressions'] = 'num_impressions';
        $columns['avg_conversion'] = 'avg_conversion';
        return $columns;
    }

    /**
     * Add the custom meta boxes to the Post Type
     */
    public static function add_meta_box()
    {
        add_meta_box(
            "infusion_meta",
            "Infusionsoft Form",
            array('Flp_Form_Post_Type', "create_meta_page"),
            "infusion_form",
            "normal",
            "high"
        );

        if (Flp_License_Manager::is_licensed()){
            add_meta_box(
                "infusion_redirect",
                "Create Form Based Redirects",
                array('Flp_Form_Post_Type', "create_redirect_page"),
                "infusion_form",
                "normal"
            );
        }

        add_meta_box(
            "infusion_preview",
            "Preview Form",
            array('Flp_Form_Post_Type', "create_preview_box"),
            "infusion_form",
            "side",
            "default"
        );
        add_meta_box(
            "infusion_settings",
            "Form Settings",
            array('Flp_Form_Post_Type', "create_form_settings_box"),
            "infusion_form"
        );
    }

    /**
     * Displays the preview form in the Edit Form area
     *
     * @param $post
     */
    public static function create_preview_box($post){
        $form = new Flp_Form($post->ID);
        $the_content = $form->get_style_sheet();
        $the_content.= "<div class=\"{$form->form_name}\">{$form->get_form_code()}</div>";
        $the_content.= $form->get_front_end_starter_starter_script();
        echo $the_content;
    }

    /**
     * Add the HTML Editor to the post type
     *
     * @param $post
     */
    public static function create_meta_page($post){
        $meta_box = new Flp_Edit_Box($post->ID);
        $meta_box->create_page();
    }

    /**
     * Add the redirect builder to the post type
     *
     * @param $post
     */
    public static function create_redirect_page($post){
        $meta_box = new Flp_Redirect_Box($post->ID);
        $meta_box->create_page();
    }

    /**
     * Add the settings panel to the post type
     *
     * @param $post
     */
    public static function create_form_settings_box($post){
        $meta_box = new Flp_Form_Settings_Skin($post->ID);
        echo $meta_box;
    }

    /**
     * The FormLift save function for individual forms.
     *
     * @param $post_id
     */
    public static function save( $post_id )
    {
        if (!is_user_logged_in() || !current_user_can('edit_form') || (get_post_type($post_id) != "infusion_form") || ! isset( $_POST['infusion_nonce_field'] )
            || ! wp_verify_nonce( $_POST['infusion_nonce_field'], 'save_form' ) ){
            return;
        }

        do_action('flp_before_save_form', $post_id);
        do_action('flp_after_save_form', $post_id);

    }
}