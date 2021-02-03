<?php


namespace MAM\Plugin\Services\Admin;

use WP_Query;
use MAM\Plugin\Config;
use MAM\Plugin\Services\ServiceInterface;

class Orders implements ServiceInterface
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

        add_action('init', array($this, 'init_order_post_type'), 0);
        add_filter('single_template', array($this, 'init_order_template'));
        add_filter('template_include', array($this, 'archive_template'));
        add_filter('mam-orders-filtered-posts', array($this, 'filtered_posts'));
        add_action('acf/init', array($this, 'add_orders_custom_fields'));
        add_filter('gettext', array($this, 'custom_enter_title'));

        // Admin table
        //add_filter('manage_lborder_posts_columns', array($this, 'set_custom_edit_order_columns'));
        //add_action('manage_lborder_posts_custom_column', array($this, 'custom_order_column'), 10, 2);
        //add_filter('manage_edit-lborder_sortable_columns', array($this, 'set_custom_order_sortable_columns'));
    }

    /**
     * init property post type info (to be called by wordpress)
     */
    public static function init_order_post_type()
    {
        $labels = array(
            'name' => _x('Orders', 'Post Type General Name'),
            'singular_name' => _x('Order', 'Post Type Singular Name'),
            'menu_name' => __('Orders'),
            'name_admin_bar' => __('Order'),
            'archives' => __('Item Archives'),
            'attributes' => __('Item Attributes'),
            'parent_item_colon' => __('Parent Order'),
            'all_items' => __('All Orders'),
            'add_new_item' => __('Add New Order'),
            'add_new' => __('Add New'),
            'new_item' => __('New Order'),
            'edit_item' => __('Edit Order'),
            'update_item' => __('Update Order'),
            'view_item' => __('View Order'),
            'view_items' => __('View Order'),
            'search_items' => __('Search Order'),
            'not_found' => __('Not found'),
            'not_found_in_trash' => __('Not found in Trash'),
            'featured_image' => __('Featured Image'),
            'set_featured_image' => __('Set featured image'),
            'remove_featured_image' => __('Remove featured image'),
            'use_featured_image' => __('Use as featured image'),
            'insert_into_item' => __('Insert into'),
            'uploaded_to_this_item' => __('Uploaded to this Order'),
            'items_list' => __('Items list'),
            'items_list_navigation' => __('Items list navigation'),
            'filter_items_list' => __('Filter Orders list'),
        );
        $args = array(
            'label' => __('Order'),
            'description' => __('Order post type by MAM Linkbuilding'),
            'labels' => $labels,
            'supports' => array('title', 'custom-fields'),
            'hierarchical' => false,
            'public' => true,
            'show_ui' => true,
            'show_in_menu' => true,
            'menu_icon' => 'dashicons-cart',
            'menu_position' => 5,
            'show_in_admin_bar' => true,
            'show_in_nav_menus' => true,
            'can_export' => true,
            'has_archive' => true,
            'exclude_from_search' => false,
            'publicly_queryable' => true,
            'capability_type' => 'page',
        );
        register_post_type('lborder', $args);
    }

    /**
     * init post type template file single-property.php
     * @param $template string the template file path
     * @return string the template file path
     */
    function init_order_template($template)
    {
        global $post;
        if ('lborder' == $post->post_type) {
            $theme_files = array('single-lborder.php', 'mam/single-lborder.php');
            $exists_in_theme = locate_template($theme_files, false);
            if ($exists_in_theme != '') {
                return $exists_in_theme;
            } else {
                return $this->plugin_path . 'templates/single-lborder.php';
            }
        }
        return $template;
    }

    /**
     * add property archive template
     * @param $template string the template file path
     * @return string the template file path
     */
    public function archive_template($template)
    {
        if (is_post_type_archive('lborder')) {
            $theme_files = array('archive-lborder.php', 'mam/archive-lborder.php');
            $exists_in_theme = locate_template($theme_files, false);
            if ($exists_in_theme != '') {
                return $exists_in_theme;
            } else {
                return $this->plugin_path . 'templates/archive-lborder.php';
            }
        }
        return $template;
    }

    /**
     * add clients post type custom fields (using ACF Pro)
     */
    public function add_orders_custom_fields()
    {
        if (function_exists('acf_add_local_field_group')) {
            acf_add_local_field_group(array(
                'key' => 'group_5ffe979d903d2',
                'title' => 'Order Details',
                'fields' => array(
                    array(
                        'key' => 'field_5ffe97d6bdf5a',
                        'label' => 'Client',
                        'name' => 'client',
                        'type' => 'post_object',
                        'instructions' => '',
                        'required' => 1,
                        'conditional_logic' => 0,
                        'wrapper' => array(
                            'width' => '',
                            'class' => '',
                            'id' => '',
                        ),
                        'post_type' => array(
                            0 => 'client',
                        ),
                        'taxonomy' => '',
                        'allow_null' => 0,
                        'multiple' => 0,
                        'return_format' => 'id',
                        'ui' => 1,
                    ),
                    array(
                        'key' => 'field_5ffe981dbdf5b',
                        'label' => 'Anchor Text',
                        'name' => 'anchor_text',
                        'type' => 'text',
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
                        'prepend' => '',
                        'append' => '',
                        'maxlength' => '',
                    ),
                    array(
                        'key' => 'field_5ffe9835bdf5c',
                        'label' => 'Target URL',
                        'name' => 'target_url',
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
                    array(
                        'key' => 'field_5ffe98f4bdf65',
                        'label' => 'Niche',
                        'name' => 'niche',
                        'type' => 'text',
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
                        'prepend' => '',
                        'append' => '',
                        'maxlength' => '',
                    ),
                    array(
                        'key' => 'field_5ffe9847bdf5d',
                        'label' => 'Sent To Writers',
                        'name' => 'sent_to_writers',
                        'type' => 'date_picker',
                        'instructions' => '',
                        'required' => 0,
                        'conditional_logic' => 0,
                        'wrapper' => array(
                            'width' => '',
                            'class' => '',
                            'id' => '',
                        ),
                        'display_format' => 'Y-m-d',
                        'return_format' => 'Y-m-d',
                        'first_day' => 1,
                    ),
                    array(
                        'key' => 'field_5ffe985ebdf5e',
                        'label' => 'Articles Sent To The Sites',
                        'name' => 'articles_sent_to_the_sites',
                        'type' => 'date_picker',
                        'instructions' => '',
                        'required' => 0,
                        'conditional_logic' => 0,
                        'wrapper' => array(
                            'width' => '',
                            'class' => '',
                            'id' => '',
                        ),
                        'display_format' => 'Y-m-d',
                        'return_format' => 'Y-m-d',
                        'first_day' => 1,
                    ),
                    array(
                        'key' => 'field_5ffe988dbdf5f',
                        'label' => 'Live Link Received',
                        'name' => 'live_link_received',
                        'type' => 'date_picker',
                        'instructions' => '',
                        'required' => 0,
                        'conditional_logic' => 0,
                        'wrapper' => array(
                            'width' => '',
                            'class' => '',
                            'id' => '',
                        ),
                        'display_format' => 'Y-m-d',
                        'return_format' => 'Y-m-d',
                        'first_day' => 1,
                    ),
                    array(
                        'key' => 'field_5ffe98a5bdf60',
                        'label' => 'Live Link',
                        'name' => 'live_link',
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
                    array(
                        'key' => 'field_5ffe98b1bdf61',
                        'label' => 'Date Paid',
                        'name' => 'date_paid',
                        'type' => 'date_picker',
                        'instructions' => '',
                        'required' => 0,
                        'conditional_logic' => 0,
                        'wrapper' => array(
                            'width' => '',
                            'class' => '',
                            'id' => '',
                        ),
                        'display_format' => 'Y-m-d',
                        'return_format' => 'Y-m-d',
                        'first_day' => 1,
                    ),
                    array(
                        'key' => 'field_5ffe98c7bdf62',
                        'label' => 'USD Price',
                        'name' => 'usd_price',
                        'type' => 'number',
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
                        'prepend' => '',
                        'append' => '',
                        'min' => '',
                        'max' => '',
                        'step' => '',
                    ),
                    array(
                        'key' => 'field_5ffe98cebdf63',
                        'label' => 'THB Price',
                        'name' => 'thb_price',
                        'type' => 'number',
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
                        'prepend' => '',
                        'append' => '',
                        'min' => '',
                        'max' => '',
                        'step' => '',
                    ),
                    array(
                        'key' => 'field_5ffe98debdf64',
                        'label' => 'Status',
                        'name' => 'status',
                        'type' => 'select',
                        'instructions' => '',
                        'required' => 0,
                        'conditional_logic' => 0,
                        'wrapper' => array(
                            'width' => '',
                            'class' => '',
                            'id' => '',
                        ),
                        'choices' => array(
                            'Done' => 'Done',
                            'Follow up' => 'Follow up',
                            'Ongoing' => 'Ongoing',
                        ),
                        'default_value' => false,
                        'allow_null' => 0,
                        'multiple' => 0,
                        'ui' => 0,
                        'return_format' => 'value',
                        'ajax' => 0,
                        'placeholder' => '',
                    ),
                    array(
                        'key' => 'field_5ffea6e14dd89',
                        'label' => 'Start Date',
                        'name' => 'start_date',
                        'type' => 'date_picker',
                        'instructions' => '',
                        'required' => 0,
                        'conditional_logic' => 0,
                        'wrapper' => array(
                            'width' => '',
                            'class' => '',
                            'id' => '',
                        ),
                        'display_format' => 'Y-m-d',
                        'return_format' => 'Y-m-d',
                        'first_day' => 1,
                    ),
                    array(
                        'key' => 'field_5ffea6e04dd88',
                        'label' => 'Complete Date',
                        'name' => 'complete_date',
                        'type' => 'date_picker',
                        'instructions' => '',
                        'required' => 0,
                        'conditional_logic' => 0,
                        'wrapper' => array(
                            'width' => '',
                            'class' => '',
                            'id' => '',
                        ),
                        'display_format' => 'Y-m-d',
                        'return_format' => 'Y-m-d',
                        'first_day' => 1,
                    ),
                    array(
                        'key' => 'field_5ffe9a45b54dc',
                        'label' => 'Resource URL',
                        'name' => 'resource_url',
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
                    array(
                        'key' => 'field_5ffe979d963ac',
                        'label' => 'IP Address',
                        'name' => 'ip_address',
                        'type' => 'text',
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
                        'prepend' => '',
                        'append' => '',
                        'maxlength' => '',
                    ),
                    array(
                        'key' => 'field_5ffe979d963f4',
                        'label' => 'Email',
                        'name' => 'email',
                        'type' => 'email',
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
                        'prepend' => '',
                        'append' => '',
                    ),
                    array(
                        'key' => 'field_5ffe979d96437',
                        'label' => 'Contact Name',
                        'name' => 'contact_name',
                        'type' => 'text',
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
                        'prepend' => '',
                        'append' => '',
                        'maxlength' => '',
                    ),
                    array(
                        'key' => 'field_5ffe979d96474',
                        'label' => 'DA',
                        'name' => 'da',
                        'type' => 'number',
                        'instructions' => 'metrics',
                        'required' => 0,
                        'conditional_logic' => 0,
                        'wrapper' => array(
                            'width' => '',
                            'class' => '',
                            'id' => '',
                        ),
                        'default_value' => '',
                        'placeholder' => '',
                        'prepend' => '',
                        'append' => '',
                        'min' => '',
                        'max' => '',
                        'step' => '',
                    ),
                    array(
                        'key' => 'field_5ffe979d964b0',
                        'label' => 'DR',
                        'name' => 'dr',
                        'type' => 'number',
                        'instructions' => 'metrics',
                        'required' => 0,
                        'conditional_logic' => 0,
                        'wrapper' => array(
                            'width' => '',
                            'class' => '',
                            'id' => '',
                        ),
                        'default_value' => '',
                        'placeholder' => '',
                        'prepend' => '',
                        'append' => '',
                        'min' => '',
                        'max' => '',
                        'step' => '',
                    ),
                    array(
                        'key' => 'field_5ffe979d964ec',
                        'label' => 'RD',
                        'name' => 'rd',
                        'type' => 'number',
                        'instructions' => 'metrics',
                        'required' => 0,
                        'conditional_logic' => 0,
                        'wrapper' => array(
                            'width' => '',
                            'class' => '',
                            'id' => '',
                        ),
                        'default_value' => '',
                        'placeholder' => '',
                        'prepend' => '',
                        'append' => '',
                        'min' => '',
                        'max' => '',
                        'step' => '',
                    ),
                    array(
                        'key' => 'field_5ffe979d96528',
                        'label' => 'TR',
                        'name' => 'tr',
                        'type' => 'number',
                        'instructions' => 'metrics',
                        'required' => 0,
                        'conditional_logic' => 0,
                        'wrapper' => array(
                            'width' => '',
                            'class' => '',
                            'id' => '',
                        ),
                        'default_value' => '',
                        'placeholder' => '',
                        'prepend' => '',
                        'append' => '',
                        'min' => '',
                        'max' => '',
                        'step' => '',
                    ),
                    array(
                        'key' => 'field_5ffe979d96565',
                        'label' => 'PA',
                        'name' => 'pa',
                        'type' => 'number',
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
                        'prepend' => '',
                        'append' => '',
                        'min' => '',
                        'max' => '',
                        'step' => '',
                    ),
                    array(
                        'key' => 'field_5ffe979d965a1',
                        'label' => 'TF',
                        'name' => 'tf',
                        'type' => 'number',
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
                        'prepend' => '',
                        'append' => '',
                        'min' => '',
                        'max' => '',
                        'step' => '',
                    ),
                    array(
                        'key' => 'field_5ffe979d965dd',
                        'label' => 'CF',
                        'name' => 'cf',
                        'type' => 'number',
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
                        'prepend' => '',
                        'append' => '',
                        'min' => '',
                        'max' => '',
                        'step' => '',
                    ),
                    array(
                        'key' => 'field_5ffe979d96619',
                        'label' => 'Organic Keywords',
                        'name' => 'organic_keywords',
                        'type' => 'number',
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
                        'prepend' => '',
                        'append' => '',
                        'min' => '',
                        'max' => '',
                        'step' => '',
                    ),
                    array(
                        'key' => 'field_5ffe979d96655',
                        'label' => 'Currency',
                        'name' => 'currency',
                        'type' => 'select',
                        'instructions' => '',
                        'required' => 0,
                        'conditional_logic' => 0,
                        'wrapper' => array(
                            'width' => '',
                            'class' => '',
                            'id' => '',
                        ),
                        'choices' => Config::getInstance()->currencies,
                        'default_value' => false,
                        'allow_null' => 0,
                        'multiple' => 0,
                        'ui' => 0,
                        'return_format' => 'value',
                        'ajax' => 0,
                        'placeholder' => '',
                    ),
                    array(
                        'key' => 'field_5ffe979d96691',
                        'label' => 'Original Price',
                        'name' => 'original_price',
                        'type' => 'number',
                        'instructions' => 'first price that they gave us',
                        'required' => 0,
                        'conditional_logic' => 0,
                        'wrapper' => array(
                            'width' => '',
                            'class' => '',
                            'id' => '',
                        ),
                        'default_value' => '',
                        'placeholder' => '',
                        'prepend' => '',
                        'append' => '',
                        'min' => '',
                        'max' => '',
                        'step' => '',
                    ),
                    array(
                        'key' => 'field_5ffe979d966cd',
                        'label' => 'Casino Price',
                        'name' => 'casino_price',
                        'type' => 'number',
                        'instructions' => '0 if it\'s not allowed.
empty if it\'s allowed.
set the price if there is a special price.',
                        'required' => 0,
                        'conditional_logic' => 0,
                        'wrapper' => array(
                            'width' => '',
                            'class' => '',
                            'id' => '',
                        ),
                        'default_value' => '',
                        'placeholder' => '',
                        'prepend' => '',
                        'append' => '',
                        'min' => '',
                        'max' => '',
                        'step' => '',
                    ),
                    array(
                        'key' => 'field_5ffe979d96709',
                        'label' => 'CBD Price',
                        'name' => 'cbd_price',
                        'type' => 'number',
                        'instructions' => '0 if it\'s not allowed.
empty if it\'s allowed.
set the price if there is a special price.',
                        'required' => 0,
                        'conditional_logic' => 0,
                        'wrapper' => array(
                            'width' => '',
                            'class' => '',
                            'id' => '',
                        ),
                        'default_value' => '',
                        'placeholder' => '',
                        'prepend' => '',
                        'append' => '',
                        'min' => '',
                        'max' => '',
                        'step' => '',
                    ),
                    array(
                        'key' => 'field_5ffe979d96745',
                        'label' => 'Adult Price',
                        'name' => 'adult_price',
                        'type' => 'number',
                        'instructions' => '0 if it\'s not allowed.
empty if it\'s allowed.
set the price if there is a special price.',
                        'required' => 0,
                        'conditional_logic' => 0,
                        'wrapper' => array(
                            'width' => '',
                            'class' => '',
                            'id' => '',
                        ),
                        'default_value' => '',
                        'placeholder' => '',
                        'prepend' => '',
                        'append' => '',
                        'min' => '',
                        'max' => '',
                        'step' => '',
                    ),
                    array(
                        'key' => 'field_5ffe979d96781',
                        'label' => 'Link Placement Price',
                        'name' => 'link_placement_price',
                        'type' => 'number',
                        'instructions' => '0 if it\'s not allowed.
empty if it\'s allowed.
set the price if there is a special price.',
                        'required' => 0,
                        'conditional_logic' => 0,
                        'wrapper' => array(
                            'width' => '',
                            'class' => '',
                            'id' => '',
                        ),
                        'default_value' => '',
                        'placeholder' => '',
                        'prepend' => '',
                        'append' => '',
                        'min' => '',
                        'max' => '',
                        'step' => '',
                    ),
                    array(
                        'key' => 'field_5ffe979d967bd',
                        'label' => 'Package / Discount',
                        'name' => 'package__discount',
                        'type' => 'text',
                        'instructions' => 'if they offer for bulk orders',
                        'required' => 0,
                        'conditional_logic' => 0,
                        'wrapper' => array(
                            'width' => '',
                            'class' => '',
                            'id' => '',
                        ),
                        'default_value' => '',
                        'placeholder' => '',
                        'prepend' => '',
                        'append' => '',
                        'maxlength' => '',
                    ),
                    array(
                        'key' => 'field_5ffe979d967f9',
                        'label' => 'Finale Price',
                        'name' => 'price',
                        'type' => 'number',
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
                        'prepend' => '',
                        'append' => '',
                        'min' => '',
                        'max' => '',
                        'step' => '',
                    ),
                    array(
                        'key' => 'field_5ffe979d96835',
                        'label' => 'Payment Method',
                        'name' => 'payment_method',
                        'type' => 'text',
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
                        'prepend' => '',
                        'append' => '',
                        'maxlength' => '',
                    ),
                    array(
                        'key' => 'field_5ffe979d96871',
                        'label' => 'Notes',
                        'name' => 'notes',
                        'type' => 'text',
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
                        'prepend' => '',
                        'append' => '',
                        'maxlength' => '',
                    ),
                    array(
                        'key' => 'field_5ffe979d968ad',
                        'label' => 'Secondary Email',
                        'name' => 'secondary_email',
                        'type' => 'email',
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
                        'prepend' => '',
                        'append' => '',
                    ),
                    array(
                        'key' => 'field_5ffe979d968e9',
                        'label' => 'Origin File',
                        'name' => 'origin_file',
                        'type' => 'text',
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
                        'prepend' => '',
                        'append' => '',
                        'maxlength' => '',
                    ),
                    array(
                        'key' => 'field_5ffe979d96926',
                        'label' => 'Rating',
                        'name' => 'rating',
                        'type' => 'select',
                        'instructions' => '',
                        'required' => 0,
                        'conditional_logic' => 0,
                        'wrapper' => array(
                            'width' => '',
                            'class' => '',
                            'id' => '',
                        ),
                        'choices' => array(
                            'No Rating' => 'No Rating',
                            '★' => '★',
                            '★★' => '★★',
                            '★★★' => '★★★',
                            '★★★★' => '★★★★',
                            '★★★★★' => '★★★★★',
                        ),
                        'default_value' => false,
                        'allow_null' => 0,
                        'multiple' => 0,
                        'ui' => 0,
                        'return_format' => 'value',
                        'ajax' => 0,
                        'placeholder' => '',
                    ),
                    array(
                        'key' => 'field_5ffe979d9699e',
                        'label' => 'Metrics Update Date',
                        'name' => 'metrics_update_date',
                        'type' => 'date_picker',
                        'instructions' => '',
                        'required' => 0,
                        'conditional_logic' => 0,
                        'wrapper' => array(
                            'width' => '',
                            'class' => '',
                            'id' => '',
                        ),
                        'display_format' => 'Y-m-d',
                        'return_format' => 'Y-m-d',
                        'first_day' => 1,
                    ),
                ),
                'location' => array(
                    array(
                        array(
                            'param' => 'post_type',
                            'operator' => '==',
                            'value' => 'lborder',
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
     * Change Add Title Text
     */
    function custom_enter_title($input)
    {

        global $post_type;

        if (is_admin() && 'Add title' == $input && 'lborder' == $post_type)
            return 'Order Number';

        return $input;
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

        if (isset($filters['client']) && $filters['client'] != '') {
            $meta_query[] = [
                'key' => 'client',
                'value' => $filters['client'],
                'compare' => '='
            ];
        }

        if (isset($filters['agency'])) {
            /**
             * @var $clients WP_Query
             */
            $clients = apply_filters('mam-clients-filtered-posts', $filters);
            $clientsIDs = array();
            if ($clients->have_posts()) {
                while ($clients->have_posts()) {
                    $clients->the_post();
                    $clientsIDs[] = get_the_ID();
                }
            }
            if(empty($clientsIDs)){
                $clientsIDs[] = 99999999;
            }
            $meta_query[] = [
                'key' => 'client',
                'value' => $clientsIDs,
                'compare' => 'IN',
            ];
        }

        // args
        $args = array(
            'numberposts' => -1,
            'post_type' => 'lborder',
            'meta_query' => $meta_query
        );

        // query
        return new WP_Query($args);
    }

    /**
     * update the resource custom fields
     * @param $orderID int the order id
     * @param $clientID int the client id
     * @param $orderData array the custom fields data
     */
    public static function update_order($orderID, $clientID, $orderData)
    {

        update_field('client', $clientID, $orderID);

        if (isset($orderData['Anchor Text'])) {
            update_field('anchor_text', $orderData['Anchor Text'], $orderID);
        }
        if (isset($orderData['Anchor Text Type'])) {
            update_field('anchor_text_type', $orderData['Anchor Text Type'], $orderID);
        }
        if (isset($orderData['Target URL'])) {
            update_field('target_url', $orderData['Target URL'], $orderID);
        }
        if (isset($orderData['Niche'])) {
            update_field('niche', $orderData['Niche'], $orderID);
        }
        if (isset($orderData['Sent To Writers'])) {
            update_field('sent_to_writers', $orderData['Sent To Writers'], $orderID);
        }
        if (isset($orderData['Article sent to the site'])) {
            update_field('articles_sent_to_the_sites', $orderData['Article sent to the site'], $orderID);
        }
        if (isset($orderData['Live Link Received'])) {
            update_field('live_link_received', $orderData['Live Link Received'], $orderID);
        }
        if (isset($orderData['Live Link'])) {
            update_field('live_link', $orderData['Live Link'], $orderID);
        }
        if (isset($orderData['Date Paid'])) {
            update_field('date_paid', $orderData['Date Paid'], $orderID);
        }
        if (isset($orderData['USD Price'])) {
            update_field('usd_price', $orderData['USD Price'], $orderID);
        }
        if (isset($orderData['THB Price'])) {
            update_field('thb_price', $orderData['THB Price'], $orderID);
        }
        if (isset($orderData['Status'])) {
            update_field('status', $orderData['Status'], $orderID);
        }
        if (isset($orderData['Start Date'])) {
            update_field('start_date', $orderData['Start Date'], $orderID);
        }
        if (isset($orderData['Complete Date'])) {
            update_field('complete_date', $orderData['Complete Date'], $orderID);
        }

        if (isset($orderData['Resource URL'])) {
            update_field('resource_url', $orderData['Resource URL'], $orderID);
        }
        if (isset($orderData['IP Address'])) {
            update_field('ip_address', $orderData['IP Address'], $orderID);
        }
        if (isset($orderData['Email'])) {
            update_field('email', $orderData['Email'], $orderID);
        }
        if (isset($orderData['Name'])) {
            update_field('contact_name', $orderData['Name'], $orderID);
        }
        if (isset($orderData['DA'])) {
            update_field('da', $orderData['DA'], $orderID);
        }
        if (isset($orderData['DR'])) {
            update_field('dr', $orderData['DR'], $orderID);
        }
        if (isset($orderData['RD'])) {
            update_field('rd', $orderData['RD'], $orderID);
        }
        if (isset($orderData['TR'])) {
            update_field('tr', $orderData['TR'], $orderID);
        }
        if (isset($orderData['PA'])) {
            update_field('pa', $orderData['PA'], $orderID);
        }
        if (isset($orderData['TF'])) {
            update_field('tf', $orderData['TF'], $orderID);
        }
        if (isset($orderData['CF'])) {
            update_field('cf', $orderData['CF'], $orderID);
        }
        if (isset($orderData['Organic Keywords'])) {
            update_field('organic_keywords', $orderData['Organic Keywords'], $orderID);
        }
        if (isset($orderData['Currency'])) {
            update_field('currency', $orderData['Currency'], $orderID);
        }
        if (isset($orderData['Country'])) {
            update_field('country', $orderData['Country'], $orderID);
        }
        if (isset($orderData['Original Price'])) {
            update_field('original_price', $orderData['Original Price'], $orderID);
        }
        if (isset($orderData['Casino Price'])) {
            update_field('casino_price', $orderData['Casino Price'], $orderID);
        }
        if (isset($orderData['CBD Price'])) {
            update_field('cbd_price', $orderData['CBD Price'], $orderID);
        }
        if (isset($orderData['Adult Price'])) {
            update_field('adult_price', $orderData['Adult Price'], $orderID);
        }
        if (isset($orderData['Link Placement Price'])) {
            update_field('link_placement_price', $orderData['Link Placement Price'], $orderID);
        }
        if (isset($orderData['Package / Discount'])) {
            update_field('package__discount', $orderData['Package / Discount'], $orderID);
        }
        if (isset($orderData['Finale Price'])) {
            update_field('price', $orderData['Finale Price'], $orderID);
        }
        if (isset($orderData['Payment Method'])) {
            update_field('payment_method', $orderData['Payment Method'], $orderID);
        }
        if (isset($orderData['Notes'])) {
            update_field('notes', $orderData['Notes'], $orderID);
        }
        if (isset($orderData['Secondary Email'])) {
            update_field('secondary_email', $orderData['Secondary Email'], $orderID);
        }
        if (isset($orderData['Origin File'])) {
            update_field('origin_file', $orderData['Origin File'], $orderID);
        }
        if (isset($orderData['Rating'])) {
            update_field('rating', $orderData['Rating'], $orderID);
        }
        if (isset($orderData['Status'])) {
            update_field('status', $orderData['Status'], $orderID);
        }
        if (isset($orderData['Metrics Update Date'])) {
            update_field('metrics_update_date', $orderData['Metrics Update Date'], $orderID);
        }
        if (isset($orderData['Sectors'])) {
            $sectors = explode(', ', $orderData['Sectors']);
            wp_set_post_terms($orderID, $sectors, 'sector');
        }
    }
}