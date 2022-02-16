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

        if (isset($filters['date1']) && $filters['date1'] != '' && isset($filters['date2']) && $filters['date2'] != '') {
            $meta_query[] = [
                'key' => 'start_date',
                'value' => array(date('y-m-d', strtotime($filters['date1'])), date('y-m-d', strtotime($filters['date2']))),
                'compare' => 'BETWEEN',
                'type' => 'DATE',
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
            'posts_per_page' => -1,
            'post_type' => 'lborder',
            'meta_query' => $meta_query
        );

        // query
        $query = new WP_Query($args);
        //wp_reset_query();
        return $query;
    }

    /**
     * Get the order post ID by Order ID (or 0 if the Order ID not found)
     * @param $orderID string the order ID
     * @return int post ID when the order is found or 0 when the order is not found
     */
    public static function get_order($orderID)
    {
        if ( ! is_admin() ) {
            require_once( ABSPATH . 'wp-admin/includes/post.php' );
            return post_exists($orderID, '', '', 'lborder');
        }
        return 0;
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

        if (isset($orderData[strtolower('Anchor Text')])) {
            update_field('anchor_text', $orderData[strtolower('Anchor Text')], $orderID);
        }
        if (isset($orderData[strtolower('Anchor Text Type')])) {
            update_field('anchor_text_type', $orderData[strtolower('Anchor Text Type')], $orderID);
        }
        if (isset($orderData[strtolower('Target URL')])) {
            update_field('target_url', $orderData[strtolower('Target URL')], $orderID);
        }
        if (isset($orderData[strtolower('Niche')])) {
            update_field('niche', $orderData[strtolower('Niche')], $orderID);
        }
        if (isset($orderData[strtolower('Sent To Writers')])) {
            update_field('sent_to_writers', $orderData[strtolower('Sent To Writers')], $orderID);
        }
        if (isset($orderData[strtolower('Article sent to the site')])) {
            update_field('articles_sent_to_the_sites', $orderData[strtolower('Article sent to the site')], $orderID);
        }
        if (isset($orderData[strtolower('Live Link Received')])) {
            update_field('live_link_received', $orderData[strtolower('Live Link Received')], $orderID);
        }
        if (isset($orderData[strtolower('Live Link')])) {
            update_field('live_link', $orderData[strtolower('Live Link')], $orderID);
        }
        if (isset($orderData[strtolower('Date Paid')])) {
            update_field('date_paid', $orderData[strtolower('Date Paid')], $orderID);
        }
        if (isset($orderData[strtolower('USD Price')])) {
            update_field('usd_price', $orderData[strtolower('USD Price')], $orderID);
        }
        if (isset($orderData[strtolower('THB Price')])) {
            update_field('thb_price', $orderData[strtolower('THB Price')], $orderID);
        }
        if (isset($orderData[strtolower('Checked')]) && $orderData[strtolower('Checked')] != '') {
            update_field('checked', $orderData[strtolower('Checked')], $orderID);
        }
        if (isset($orderData[strtolower('Status')]) && $orderData[strtolower('Status')] != '') {
            update_field('status', $orderData[strtolower('Status')], $orderID);
        }else{
            update_field('status', 'Ongoing', $orderID);
        }
        if (isset($orderData[strtolower('Start Date')]) && $orderData[strtolower('Start Date')] != '') {
            update_field('start_date', $orderData[strtolower('Start Date')], $orderID);
        }else{
            update_field('start_date', date('Y-m-d'), $orderID);
        }
        if (isset($orderData[strtolower('Complete Date')])) {
            update_field('complete_date', $orderData[strtolower('Complete Date')], $orderID);
        }

        if (isset($orderData[strtolower('Resource URL')])) {
            update_field('resource_url', $orderData[strtolower('Resource URL')], $orderID);
        }
        if (isset($orderData[strtolower('IP Address')])) {
            update_field('ip_address', $orderData[strtolower('IP Address')], $orderID);
        }
        if (isset($orderData[strtolower('Email')])) {
            update_field('email', $orderData[strtolower('Email')], $orderID);
        }
        if (isset($orderData[strtolower('Name')])) {
            update_field('contact_name', $orderData[strtolower('Name')], $orderID);
        }
        if (isset($orderData[strtolower('DA')])) {
            update_field('da', $orderData[strtolower('DA')], $orderID);
        }
        if (isset($orderData[strtolower('DR')])) {
            update_field('dr', $orderData[strtolower('DR')], $orderID);
        }
        if (isset($orderData[strtolower('RD')])) {
            update_field('rd', $orderData[strtolower('RD')], $orderID);
        }
        if (isset($orderData[strtolower('TR')])) {
            update_field('tr', $orderData[strtolower('TR')], $orderID);
        }
        if (isset($orderData[strtolower('PA')])) {
            update_field('pa', $orderData[strtolower('PA')], $orderID);
        }
        if (isset($orderData[strtolower('TF')])) {
            update_field('tf', $orderData[strtolower('TF')], $orderID);
        }
        if (isset($orderData[strtolower('CF')])) {
            update_field('cf', $orderData[strtolower('CF')], $orderID);
        }
        if (isset($orderData[strtolower('Organic Keywords')])) {
            update_field('organic_keywords', $orderData[strtolower('Organic Keywords')], $orderID);
        }
        if (isset($orderData[strtolower('Currency')])) {
            update_field('currency', $orderData[strtolower('Currency')], $orderID);
        }
        if (isset($orderData[strtolower('Country')])) {
            update_field('country', $orderData[strtolower('Country')], $orderID);
        }
        if (isset($orderData[strtolower('Original Price')])) {
            update_field('original_price', $orderData[strtolower('Original Price')], $orderID);
        }
        if (isset($orderData[strtolower('Casino Price')])) {
            update_field('casino_price', $orderData[strtolower('Casino Price')], $orderID);
        }
        if (isset($orderData[strtolower('CBD Price')])) {
            update_field('cbd_price', $orderData[strtolower('CBD Price')], $orderID);
        }
        if (isset($orderData[strtolower('Adult Price')])) {
            update_field('adult_price', $orderData[strtolower('Adult Price')], $orderID);
        }
        if (isset($orderData[strtolower('Link Placement Price')])) {
            update_field('link_placement_price', $orderData[strtolower('Link Placement Price')], $orderID);
        }
        if (isset($orderData[strtolower('Package / Discount')])) {
            update_field('package__discount', $orderData[strtolower('Package / Discount')], $orderID);
        }
        if (isset($orderData[strtolower('Finale Price')])) {
            update_field('price', $orderData[strtolower('Finale Price')], $orderID);
        }
        if (isset($orderData[strtolower('Payment Method')])) {
            update_field('payment_method', $orderData[strtolower('Payment Method')], $orderID);
        }
        if (isset($orderData[strtolower('Notes')])) {
            update_field('notes', $orderData[strtolower('Notes')], $orderID);
        }
        if (isset($orderData[strtolower('Secondary Email')])) {
            update_field('secondary_email', $orderData[strtolower('Secondary Email')], $orderID);
        }
        if (isset($orderData[strtolower('Origin File')])) {
            update_field('origin_file', $orderData[strtolower('Origin File')], $orderID);
        }
        if (isset($orderData[strtolower('Rating')])) {
            update_field('rating', $orderData[strtolower('Rating')], $orderID);
        }
        if (isset($orderData[strtolower('Status')])) {
            update_field('status', $orderData[strtolower('Status')], $orderID);
        }
        if (isset($orderData[strtolower('Metrics Update Date')])) {
            update_field('metrics_update_date', $orderData[strtolower('Metrics Update Date')], $orderID);
        }
        if (isset($orderData[strtolower('Sectors')])) {
            $sectors = explode(', ', $orderData[strtolower('Sectors')]);
            wp_set_post_terms($orderID, $sectors, 'sector');
        }
    }



    /**
     * Get the meta field name by column name
     * @param $column_name string the column name
     * @return string the meta name
     */
    public static function get_field_name_by_column_name($column_name)
    {
        switch ($column_name) {
            case "id":
                return 'title';
            case "client":
                return 'client';
            case "agency":
                return 'agency';
            case strtolower('Anchor Text'):
                return 'anchor_text';
            case strtolower('Anchor Text Type'):
                return 'anchor_text_type';
            case strtolower('Target URL'):
                return 'target_url';
            case strtolower('Niche'):
                return 'niche';
            case strtolower('Sent To Writers'):
                return 'sent_to_writers';
            case strtolower('Article sent to the site'):
                return 'articles_sent_to_the_sites';
            case strtolower('Live Link Received'):
                return 'live_link_received';
            case strtolower('Live Link'):
                return 'live_link';
            case strtolower('Date Paid'):
                return 'date_paid';
            case strtolower('USD Price'):
                return 'usd_price';
            case strtolower('THB Price'):
                return 'thb_price';
            case strtolower('Checked'):
                return 'checked';
            case strtolower('Status'):
                return 'status';
            case strtolower('Start Date'):
                return 'start_date';
            case strtolower('Complete Date'):
                return 'complete_date';
            case strtolower('Resource URL'):
                return 'resource_url';
            case strtolower('IP Address'):
                return 'ip_address';
            case strtolower('Other Info'):
                return 'other_info';
            case strtolower('Contact / Email'):
                return 'contact__email';
            case strtolower('Social Media'):
                return 'social_media';
            case strtolower('New Remarks'):
                return 'new_remarks';
            case strtolower('Sectors'):
                return 'sectors';
            case strtolower('Metrics Update Date'):
                return 'metrics_update_date';
            case strtolower('Rating'):
                return 'rating';
            case strtolower('Origin File'):
                return 'origin_file';
            case strtolower('Secondary Email'):
                return 'secondary_email';
            case strtolower('Notes'):
                return 'notes';
            case strtolower('Payment Method'):
                return 'payment_method';
            case strtolower('Finale Price'):
                return 'price';
            case strtolower('Package / Discount'):
                return 'package__discount';
            case strtolower('Link Placement Price'):
                return 'link_placement_price';
            case strtolower('Adult Price'):
                return 'adult_price';
            case strtolower('CBD Price'):
                return 'cbd_price';
            case strtolower('Casino Price'):
                return 'casino_price';
            case strtolower('Original Price'):
                return 'original_price';
            case strtolower('Country'):
                return 'country';
            case strtolower('Currency'):
                return 'currency';
            case strtolower('Organic Keywords'):
                return 'organic_keywords';
            case strtolower('CF'):
                return 'cf';
            case strtolower('TF'):
                return 'tf';
            case strtolower('PA'):
                return 'pa';
            case strtolower('TR'):
                return 'tr';
            case strtolower('RD'):
                return 'rd';
            case strtolower('DR'):
                return 'dr';
            case strtolower('DA'):
                return 'da';
            case strtolower('Name'):
                return 'contact_name';
            case strtolower('Email'):
                return 'email';
        }
        return '';
    }

    /**
     * Get column value by column name from existing order
     * @param $column_name string the column name
     * @param $_orderPostID int the order post id
     * @return mixed|string
     */
    public static function get_existing_column_text_value($column_name, $_orderPostID)
    {
        if($column_name == 'id'){
            return get_the_title($_orderPostID);
        }
        if($column_name == 'agency'){
            return get_the_title(get_field('agency', get_field('client', $_orderPostID)));
        }
        if($column_name == 'client' || $column_name == 'client name'){
            return get_the_title(get_field('client', $_orderPostID));
        }
        return get_field(Orders::get_field_name_by_column_name($column_name), $_orderPostID);
    }
}