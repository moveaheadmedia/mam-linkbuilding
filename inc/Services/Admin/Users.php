<?php

namespace MAM\Plugin\Services\Admin;

use MAM\Plugin\Services\ServiceInterface;

class Users implements ServiceInterface
{
    /**
     * @inheritDoc
     */
    public function register()
    {
        add_action('plugins_loaded', [$this, 'add_custom_fields']);
        add_action('admin_post_resource_columns_hook', [$this, 'handle_resource_column']);
        add_action('admin_post_order_columns_hook', [$this, 'order_columns_hook']);

    }

    public static function handle_resource_column()
    {
        update_field('resources_columns', $_POST['resource-order'], 'user_' . get_current_user_id());
        wp_redirect($_POST['current-page']);

    }

    public static function order_columns_hook()
    {
        update_field('orders_columns', $_POST['resource-order'], 'user_' . get_current_user_id());
        wp_redirect($_POST['current-page']);

    }

    public static function add_custom_fields()
    {
        if (function_exists('acf_add_local_field_group')) {
            acf_add_local_field_group(array(
                'key' => 'group_5fff9bcf55ded',
                'title' => 'Columns',
                'fields' => array(
                    array(
                        'key' => 'field_5fff9be5ecde8',
                        'label' => 'Resources',
                        'name' => 'resources_columns',
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
                        'key' => 'field_5fff9bf4ecde9',
                        'label' => 'Orders',
                        'name' => 'orders_columns',
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
                ),
                'location' => array(
                    array(
                        array(
                            'param' => 'user_role',
                            'operator' => '==',
                            'value' => 'all',
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
}