<?php


namespace MAM\Plugin\Services\Admin;


use WP_Query;
use MAM\Plugin\Config;
use MAM\Plugin\Services\ServiceInterface;

class Clients implements ServiceInterface
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

        add_action('init', array($this, 'init_client_post_type'), 0);
        add_filter('single_template', array($this, 'init_client_template'));
        add_filter('template_include', array($this, 'archive_template'));
        add_filter('mam-clients-filtered-posts', array($this, 'filtered_posts'));
        add_action('acf/init', array($this, 'add_clients_custom_fields'));

        // Admin table
        add_filter('manage_client_posts_columns', array($this, 'set_custom_edit_client_columns'));
        add_action('manage_client_posts_custom_column', array($this, 'custom_client_column'), 10, 2);
        add_filter('manage_edit-client_sortable_columns', array($this, 'set_custom_client_sortable_columns'));
    }

    /**
     * init property post type info (to be called by wordpress)
     */
    public static function init_client_post_type()
    {
        $labels = array(
            'name' => _x('Clients', 'Post Type General Name'),
            'singular_name' => _x('Client', 'Post Type Singular Name'),
            'menu_name' => __('Clients'),
            'name_admin_bar' => __('Client'),
            'archives' => __('Item Archives'),
            'attributes' => __('Item Attributes'),
            'parent_item_colon' => __('Parent Client:'),
            'all_items' => __('All Clients'),
            'add_new_item' => __('Add New Client'),
            'add_new' => __('Add New'),
            'new_item' => __('New Client'),
            'edit_item' => __('Edit Client'),
            'update_item' => __('Update Client'),
            'view_item' => __('View Client'),
            'view_items' => __('View Client'),
            'search_items' => __('Search Client'),
            'not_found' => __('Not found'),
            'not_found_in_trash' => __('Not found in Trash'),
            'featured_image' => __('Featured Image'),
            'set_featured_image' => __('Set featured image'),
            'remove_featured_image' => __('Remove featured image'),
            'use_featured_image' => __('Use as featured image'),
            'insert_into_item' => __('Insert into'),
            'uploaded_to_this_item' => __('Uploaded to this Client'),
            'items_list' => __('Items list'),
            'items_list_navigation' => __('Items list navigation'),
            'filter_items_list' => __('Filter Clients list'),
        );
        $args = array(
            'label' => __('Client'),
            'description' => __('Client post type by MAM Linkbuilding'),
            'labels' => $labels,
            'supports' => array('title', 'custom-fields'),
            'hierarchical' => false,
            'public' => true,
            'show_ui' => true,
            'show_in_menu' => true,
            'menu_icon' => 'dashicons-buddicons-buddypress-logo',
            'menu_position' => 5,
            'show_in_admin_bar' => true,
            'show_in_nav_menus' => true,
            'can_export' => true,
            'has_archive' => true,
            'exclude_from_search' => false,
            'publicly_queryable' => true,
            'capability_type' => 'page',
        );
        register_post_type('client', $args);
    }

    /**
     * init post type template file single-property.php
     */
    function init_client_template($template)
    {
        global $post;
        if ('client' == $post->post_type) {
            $theme_files = array('single-client.php', 'mam/single-client.php');
            $exists_in_theme = locate_template($theme_files, false);
            if ($exists_in_theme != '') {
                return $exists_in_theme;
            } else {
                return $this->plugin_path . 'templates/single-client.php';
            }
        }
        return $template;
    }

    /**
     * Add columns to the clients admin table
     */
    public function set_custom_edit_client_columns($columns)
    {
        unset($columns['date']);
        $columns['website'] = __('Website');
        $columns['agency'] = __('Agency');
        $columns['date'] = __('Date');
        return $columns;
    }

    /**
     * Make columns in the clients admin table sortable
     */
    public function set_custom_client_sortable_columns($columns)
    {
        $columns['agency'] = 'agency';
        $columns['website'] = 'website';
        return $columns;
    }

    /**
     * Add data to the client custom columns
     */
    public function custom_client_column($column, $post_id)
    {
        switch ($column) {
            case 'website' :
                $website = get_field('website', $post_id);
                if (is_string($website))
                    echo $website;
                else
                    _e('Unable to get website');
                break;
            case 'agency' :
                $agency = get_field('agency', $post_id);
                $agency_name = get_the_title($agency);
                if (is_string($agency_name))
                    echo $agency_name;
                else
                    _e('Unable to get agency');
                break;
        }
    }

    /**
     * add property archive template
     */
    public function archive_template($template)
    {
        if (is_post_type_archive('client')) {
            $theme_files = array('archive-client.php', 'mam/archive-client.php');
            $exists_in_theme = locate_template($theme_files, false);
            if ($exists_in_theme != '') {
                return $exists_in_theme;
            } else {
                return $this->plugin_path . 'templates/archive-client.php';
            }
        }
        return $template;
    }

    /**
     * add clients post type custom fields (using ACF Pro)
     */
    public function add_clients_custom_fields()
    {
        if (function_exists('acf_add_local_field_group')) {
            acf_add_local_field_group(array(
                'key' => 'group_5fa35633daba0',
                'title' => 'Client Details',
                'fields' => array(
                    array(
                        'key' => 'field_5fa3563dd291f',
                        'label' => 'Agency',
                        'name' => 'agency',
                        'type' => 'post_object',
                        'instructions' => '',
                        'required' => 0,
                        'conditional_logic' => 0,
                        'wrapper' => array(
                            'width' => '',
                            'class' => '',
                            'id' => '',
                        ),
                        'post_type' => array(
                            0 => 'agency',
                        ),
                        'taxonomy' => '',
                        'allow_null' => 0,
                        'multiple' => 0,
                        'return_format' => 'id',
                        'ui' => 1,
                    ),
                    array(
                        'key' => 'field_5fa35f9dc5586',
                        'label' => 'Website',
                        'name' => 'website',
                        'type' => 'url',
                        'instructions' => '',
                        'required' => 0,
                        'conditional_logic' => 0,
                        'wrapper' => array(
                            'width' => '',
                            'class' => '',
                            'id' => '',
                        ),
                        'default_value' => '',
                        'placeholder' => '',
                    ),
                ),
                'location' => array(
                    array(
                        array(
                            'param' => 'post_type',
                            'operator' => '==',
                            'value' => 'client',
                        ),
                    ),
                ),
                'menu_order' => 0,
                'position' => 'normal',
                'style' => 'default',
                'label_placement' => 'top',
                'instruction_placement' => 'label',
                'hide_on_screen' => '',
                'active' => true,
                'description' => '',
            ));

        }
    }

    /**
     * Get the properties filtered
     * @param $filters array
     * @return WP_Query
     */
    public function filtered_posts($filters)
    {
        $meta_query = [];
        $meta_query['relation'] = 'AND';

        if (isset($filters['agency']) && $filters['agency'] != '') {
            $meta_query[] = [
                'key' => 'agency',
                'value' => $filters['agency'],
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
        $query = new WP_Query($args);
        //wp_reset_query();
        return $query;
    }

    /**
     * init property post type info (to be called by wordpress)
     * @param $client_id int the client id
     * @param $resource_url string the resource url
     *
     * @return bool true if the resource already used for this client, false if the resource is not used
     */
    public static function check_client_resource($client_id, $resource_url)
    {
        $filters = array();
        $filters['client'] = $client_id;
        /**
         * @var $orders WP_Query
         */
        $orders = apply_filters('mam-orders-filtered-posts', $filters);
        if ($orders->have_posts()) {
            while ($orders->have_posts()) {
                $orders->the_post();
                $_order_res = get_field('resource_url', get_the_ID());
                $_order_res = str_replace('https://', '', $_order_res);
                $_order_res = str_replace('http://', '', $_order_res);
                $_order_res = strtolower($_order_res);
                $resource_url = str_replace('https://', '', $resource_url);
                $resource_url = str_replace('http://', '', $resource_url);
                $resource_url = strtolower($resource_url);
                if($_order_res == $resource_url){
                    return true;
                }
            }
        }
        return false;
    }
}