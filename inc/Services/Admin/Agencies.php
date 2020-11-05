<?php


namespace MAM\Plugin\Services\Admin;


use WP_Query;
use MAM\Plugin\Config;
use MAM\Plugin\Services\ServiceInterface;

class Agencies implements ServiceInterface
{

    /**
     * @var string the plugin path
     */
    private $plugin_path;

    /**
     * @inheritDoc
     */
    public function register()
    {
        // set the plugin_path
        $this->plugin_path = Config::getInstance()->plugin_path;

        add_action('init', array($this, 'init_agency_post_type'), 0);
        add_filter('single_template', array($this, 'init_agency_template'));
        add_filter('template_include', array($this, 'archive_template'));
        add_filter('mam-agencies-filtered-posts', array($this, 'filtered_posts'));
        add_action('acf/init', array($this, 'add_agencies_custom_fields'));
        add_shortcode('mam-agencies-listing', [$this, 'mam_agencies_listing']);
    }

    /**
     * init agency post type info (to be called by wordpress)
     */
    public static function init_agency_post_type()
    {
        $labels = array(
            'name' => _x('Agencies', 'Post Type General Name'),
            'singular_name' => _x('Agency', 'Post Type Singular Name'),
            'menu_name' => __('Agencies'),
            'name_admin_bar' => __('Agency'),
            'archives' => __('Item Agencies'),
            'attributes' => __('Item Attributes'),
            'parent_item_colon' => __('Parent Agency:'),
            'all_items' => __('All Agencies'),
            'add_new_item' => __('Add New Agency'),
            'add_new' => __('Add New'),
            'new_item' => __('New Agency'),
            'edit_item' => __('Edit Agency'),
            'update_item' => __('Update Agency'),
            'view_item' => __('View Agency'),
            'view_items' => __('View Agency'),
            'search_items' => __('Search Agency'),
            'not_found' => __('Not found'),
            'not_found_in_trash' => __('Not found in Trash'),
            'featured_image' => __('Featured Image'),
            'set_featured_image' => __('Set featured image'),
            'remove_featured_image' => __('Remove featured image'),
            'use_featured_image' => __('Use as featured image'),
            'insert_into_item' => __('Insert into'),
            'uploaded_to_this_item' => __('Uploaded to this Agency'),
            'items_list' => __('Items list'),
            'items_list_navigation' => __('Items list navigation'),
            'filter_items_list' => __('Filter Agencies list'),
        );
        $args = array(
            'label' => __('Agency'),
            'description' => __('Agency post type by MAM Linkbuilding'),
            'labels' => $labels,
            'supports' => array('title', 'custom-fields'),
            'hierarchical' => false,
            'public' => true,
            'show_ui' => true,
            'show_in_menu' => true,
            'menu_icon' => 'dashicons-admin-home',
            'menu_position' => 5,
            'show_in_admin_bar' => true,
            'show_in_nav_menus' => true,
            'can_export' => true,
            'has_archive' => true,
            'exclude_from_search' => false,
            'publicly_queryable' => true,
            'capability_type' => 'page',
        );
        register_post_type('agency', $args);
    }

    /**
     * init post type template file single-agency.php
     */
    function init_agency_template($template)
    {
        global $post;
        if ('client' == $post->post_type) {
            $theme_files = array('single-agency.php', 'mam/single-agency.php');
            $exists_in_theme = locate_template($theme_files, false);
            if ($exists_in_theme != '') {
                return $exists_in_theme;
            } else {
                return $this->plugin_path . 'templates/single-agency.php';
            }
        }
        return $template;
    }

    /**
     * add agency archive template
     */
    public function archive_template($template)
    {
        if (is_post_type_archive('agency')) {
            $theme_files = array('archive-agency.php', 'mam/archive-agency.php');
            $exists_in_theme = locate_template($theme_files, false);
            if ($exists_in_theme != '') {
                return $exists_in_theme;
            } else {
                return $this->plugin_path . 'templates/archive-agency.php';
            }
        }
        return $template;
    }

    /**
     * add clients post type custom fields (using ACF Pro)
     */
    public function add_agencies_custom_fields()
    {
        if (function_exists('acf_add_local_field_group')) {

        }
    }


    /**
     * [mam-agencies-listing] function
     * @param $atts array
     * @return false|string
     */
    public function mam_agencies_listing($atts)
    {
        global $a;
        $a = shortcode_atts(array(
            'type' => 'for-sale'
        ), $atts);

        $theme_files = array('mam-agencies-listing.php', 'mam/mam-agencies-listing.php');
        $exists_in_theme = locate_template($theme_files, false);

        ob_start();
        if ($exists_in_theme != '') {
            /** @noinspection PhpIncludeInspection */
            include $exists_in_theme;
        } else {
            // nope, load the content
            /** @noinspection PhpIncludeInspection */
            include $this->plugin_path . 'templates/mam-agencies-listing.php';
        }
        return ob_get_clean();
    }

    /**
     * Get the properties filtered
     * @param $getData array
     * @return WP_Query
     */
    public function filtered_posts($getData)
    {
        global $a;

        $status = '';
        $type = 'rental';
        if (isset($a['type'])) {
            $status = 'current';
            if ($a['type'] == 'for-sale') {
                $type = 'residential';
            }
        }

        $meta_query = [];
        $meta_query['relation'] = 'AND';

        if ($status != '') {
            $meta_query[] = [
                'key' => 'status',
                'value' => $status,
                'compare' => '='
            ];
        }

        // args
        $args = array(
            'numberposts' => -1,
            'post_type' => 'client',
            'meta_query' => $meta_query
        );

        // query
        return new WP_Query($args);
    }
}