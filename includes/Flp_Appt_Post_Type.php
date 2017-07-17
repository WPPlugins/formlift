<?php

/**
 * Created by PhpStorm.
 * User: Adrian
 * Date: 2017-02-26
 * Time: 6:47 PM
 *
 * Anything to do with taxonomy, post_type and meta boxes goes here!
 */
class Flp_Appt_Post_Type
{
    function __construct()
    {
        add_action('init', array('Flp_Appt_Post_Type', 'create_post_type'));
        add_filter('manage_flp_booking_link_posts_columns', array('Flp_Appt_Post_Type', 'set_custom_edit_flp_booking_link_columns'));
        add_action('manage_flp_booking_link_posts_custom_column', array('Flp_Appt_Post_Type', 'flp_custom_column'), 10, 2);
        add_filter('post_row_actions', array('Flp_Appt_Post_Type', 'flp_action_row'), 10, 2);
        add_filter('manage_edit-flp_booking_link_sortable_columns', array('Flp_Appt_Post_Type', 'flp_my_sortable_num_subs'));
        add_action('pre_get_posts', array('Flp_Appt_Post_Type', 'flp_slice_orderby'));
        add_action('add_meta_boxes', array('Flp_Appt_Post_Type', 'add_meta_box'));
        add_action('save_post', array('Flp_Appt_Post_Type', 'save'));
        add_shortcode('flp_booking', array('Flp_Appt_Post_Type', 'shortcode'));
    }

    public static function create_post_type()
    {
        //forms
        $labels = array(
            'name' => _('Appointmentcore Booking Link'),
            'singular_name' => _('Appointmentcore Booking Link'),
            'add_new' => _('Add Booking Link'),
            'add_new_item' => _('Add Booking Link'),
            'all_items' => _('All Booking Links'),
            'edit_item' => _('Edit Booking Link'),
            'new_item' => _('New Booking Link'),
            'view' => _('View'),
            'view_item' => _('View Booking Link'),
            'search_items' => _('Search Booking Links'),
            'not_found' => _('No Booking Links Found'),
            'not_found_in_trash' => _('No Booking Links Found In Trash'),
            'archives' => _('Booking Link Archives')
        );

        $args = array(
            'labels' => $labels,
            'public' => false,  // it's not public, it shouldn't have it's own permalink, and so on
            'publicly_queryable' => true,  // you should be able to query it
            'show_ui' => true,  // you should be able to edit it in wp-admin
            'exclude_from_search' => true,  // you should exclude it from search results
            'show_in_nav_menus' => false,  // you shouldn't be able to add it to menus
            'has_archive' => false,  // it shouldn't have archive page
            'rewrite' => false,  // it shouldn't have rewrite rules
            'show_in_admin_bar' => false,
            'menu_icon' => 'dashicons-media-code',
            'capability_type' => 'form',
            'map_meta_cap' => true,
            'capabilities' => array(
                // meta caps (don't assign these to roles)
                'edit_post' => 'edit_form',
                'read_post' => 'read_form',
                'delete_post' => 'delete_form',
                // primitive/meta caps
                'create_posts' => 'create_forms',
                // primitive caps used outside of map_meta_cap()
                'edit_posts' => 'edit_forms',
                'edit_others_posts' => 'manage_forms',
                'publish_posts' => 'manage_forms',
                'read_private_posts' => 'read',
                // primitive caps used inside of map_meta_cap()
                'read' => 'read',
                'delete_posts' => 'manage_forms',
                'delete_private_posts' => 'manage_forms',
                'delete_published_posts' => 'manage_forms',
                'delete_others_posts' => 'manage_forms',
                'edit_private_posts' => 'edit_forms',
                'edit_published_posts' => 'edit_forms'
            ),
            'supports' => array(
                'title',
                'author'
            )
        );

        register_post_type('flp_booking_link', $args);
    }

    public static function set_custom_edit_flp_booking_link_columns($columns)
    {
        $new = array();
        $new['cb'] = $columns['cb'];
        $new['title'] = $columns['title'];
        $new['short_code'] = __('Short-Code', 'short_code_domain');
        $new['num_impressions'] = __('Impressions', 'impress_dom');
        $new['num_subs'] = __('Submissions', 'submiss_dom');
        $new['avg_conversion'] = __('Avg. Conversion Rate', 'converge_dom');
        $new['tracking_date'] = __('Tracking Since', 'tracking_domain');
        return $new;
    }

    /**What to display for each column
     *
     * @param $column
     * @param $post_id
     */
    public static function flp_custom_column($column, $post_id)
    {
        $site_url = get_site_url();
        switch ($column) {
            case 'short_code' :
                $terms = "<input id='form_shortcode_area_{$post_id}' type='text' style='width:100%' value='[flp_booking id=\"{$post_id}\"]' readonly/><button type='button' style='width:100%' onclick='copy_shortcode(\"#form_shortcode_area_{$post_id}\")'>COPY SHORTCODE</button>";
                if (is_string($terms))
                    echo $terms;
                else
                    _e('Unable to get short code :(', 'short_code_domain');
                break;
            case 'num_subs' :
                $num = get_post_meta($post_id, 'num_submissions', true);
                $num_subs = (!empty($num)) ? $num : 0;
                $terms = sprintf('<h3>%s</h3>', $num_subs);
                if (is_string($terms))
                    echo $terms;
                else
                    _e('no submissions.', 'submiss_dom');
                break;
            case 'num_impressions' :
                $num = get_post_meta($post_id, 'num_impressions', true);
                $num_subs = (!empty($num)) ? $num : 0;
                $terms = sprintf('<h3>%s</h3>', $num_subs);
                if (is_string($terms))
                    echo $terms;
                else
                    _e('no impressions', 'submiss_dom');
                break;
            case 'avg_conversion' :
                $num = get_post_meta($post_id, 'conversion_rate', true);
                $num_subs = (!empty($num)) ? $num : 0;
                $terms = sprintf('<h3>%s&#37;</h3>', $num_subs);
                if (is_string($terms))
                    echo $terms;
                else
                    _e('no conversions', 'converge_dom');
                break;
            case 'campaigns' :
                $taxonomy = "campaigns";
                $post_type = get_post_type($post_id);
                $terms = get_the_terms($post_id, $taxonomy);

                if (!empty($terms)) {
                    foreach ($terms as $term)
                        $post_terms[] = "<a href='$site_url/wp-admin/edit.php?post_type={$post_type}&{$taxonomy}={$term->slug}'> " . esc_html(sanitize_term_field('name', $term->name, $term->term_id, $taxonomy, 'edit')) . "</a>";
                    echo join('<br />', $post_terms);
                } else echo '<i>No Campaign Set. </i>';
                break;
            case 'tracking_date' :
                if (get_post_meta($post_id, 'tracking_date', true)) {
                    $date = get_post_meta($post_id, 'tracking_date', true);
                    echo $date;
                } else {
                    echo '<i>Tracking date was not set properly...</i>';
                }
                break;
        }
    }

    /**
     * Adds the Fill Out Form and Reset Stats actions
     * @param $actions
     * @param $post
     * @return array
     */
    public static function flp_action_row($actions, $post)
    {
        //check for your post type
        if ($post->post_type == "flp_booking_link") {
            $new_actions = array();
            $new_actions['edit'] = $actions['edit'];
            $new_actions['inline hide-if-no-js'] = $actions['inline hide-if-no-js'];
            $new_actions['trash'] = $actions['trash'];
            $site_url = get_site_url();
            $new_actions['reset_stats'] = "<a style='color:limegreen;cursor:pointer' onclick='resetStats({$post->ID})'>Reset Stats</a>";
            $new_actions['fill_form'] = "<a style='color:blue;' href='$site_url/wp-admin/edit.php?post_type=flp_booking_link&page=fill_form&form_id={$post->ID}'>Fill out Form</a>";
            return $new_actions;
        } else {
            return $actions;
        }
    }

    /**
     * Allow Forms to be sorted by Conversion Rate, Number of Impressions, and Number of Submissions
     * @param $query
     */
    public static function flp_slice_orderby($query)
    {
        if (!is_admin())
            return;

        $orderby = $query->get('orderby');

        if ('num_subs' == $orderby) {
            $query->set('meta_key', 'num_submissions');
            $query->set('orderby', 'meta_value_num');
        } else if ('num_impressions' == $orderby) {
            $query->set('meta_key', 'num_impressions');
            $query->set('orderby', 'meta_value_num');
        } else if ('avg_conversion' == $orderby) {
            $query->set('meta_key', 'conversion_rate');
            $query->set('orderby', 'meta_value_num');
        }
    }

    /**
     * give columns sortable trait
     * @param $columns
     * @return mixed
     */
    public static function flp_my_sortable_num_subs($columns)
    {
        $columns['num_subs'] = 'num_subs';
        $columns['num_impressions'] = 'num_impressions';
        $columns['avg_conversion'] = 'avg_conversion';
        return $columns;
    }

    public static function add_meta_box()
    {
        add_meta_box(
            "booking_meta",
            "Appointmentcore Booking Link",
            array('Flp_Appt_Post_Type', "create_meta_page"),
            "flp_booking_link",
            "normal",
            "high"
        );
    }

    /**
     * @param $post
     */
    public static function create_meta_page($post)
    {
        $page = new Flp_Booking_Link_Box($post->ID);
        $page->create_page();
    }

    public static function save($post_id)
    {
        if (!is_user_logged_in() || !current_user_can('edit_form') || (get_post_type($post_id) != "flp_booking_link")) {
            return;
        }

        $date = date('d/m/Y');
        if (!get_post_meta($post_id, 'tracking_date')) {
            add_post_meta($post_id, 'tracking_date', $date);
        }
        if (!empty($_POST[FormLift::$option_key])) {
            Flp_Utils::flp_update_meta($post_id, FormLift::$option_key, $_POST[FormLift::$option_key]);
        } else {
            delete_post_meta($post_id, FormLift::$option_key);
        }

    }

    public static function shortcode($atts)
    {
        $atters = shortcode_atts(array(
            'id' => '0'
        ), $atts);

        if (empty($atters['id'])) {
            return "Error: No Booking Link ID specified";
        } else if (get_post_type($atters['id']) != 'flp_booking_link') {
            return "Error: {$atters['id']} is not related to any FormLift Booking Link";
        }

        $meta = get_post_meta($atts['id'], FormLift::$option_key, true);

        $view = $meta['view'];
        $apptId = $meta['apptId'];
        $width = $meta['width'];
        $height = $meta['height'];

        if ($_GET['contactId'] || $_COOKIE['contactId']) {
            $id = (isset($_GET['contactId'])) ? $_GET['contactId'] : $_COOKIE['contactId'];
            $code = "<iframe src=\"https://www.appointmentcore.com/app/freeslots/$apptId?iframe-view=$view&Id=$id\" width=\"$width\" height=\"$height\" style=\"border:none;\" frameborder=\"0\"></iframe>";
        } else {
            $code = "<iframe src=\"https://www.appointmentcore.com/app/freeslots/$apptId?iframe-view=$view\" width=\"$width\" height=\"$height\" style=\"border:none;\" frameborder=\"0\"></iframe>";
        }
        return $code;
    }
}