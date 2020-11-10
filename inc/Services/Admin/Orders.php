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
        add_filter('manage_order_posts_columns', array($this, 'set_custom_edit_order_columns'));
        add_action('manage_order_posts_custom_column', array($this, 'custom_order_column'), 10, 2);
        add_filter('manage_edit-order_sortable_columns', array($this, 'set_custom_order_sortable_columns'));
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
        register_post_type('order', $args);
    }

    /**
     * init post type template file single-property.php
     * @param $template string the template file path
     * @return string the template file path
     */
    function init_order_template($template)
    {
        global $post;
        if ('order' == $post->post_type) {
            $theme_files = array('single-order.php', 'mam/single-order.php');
            $exists_in_theme = locate_template($theme_files, false);
            if ($exists_in_theme != '') {
                return $exists_in_theme;
            } else {
                return $this->plugin_path . 'templates/single-order.php';
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
        if (is_post_type_archive('order')) {
            $theme_files = array('archive-order.php', 'mam/archive-order.php');
            $exists_in_theme = locate_template($theme_files, false);
            if ($exists_in_theme != '') {
                return $exists_in_theme;
            } else {
                return $this->plugin_path . 'templates/archive-order.php';
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
                'key' => 'group_5fa3777e3766b',
                'title' => 'Order Info',
                'fields' => array(
                    array(
                        'key' => 'field_5fa377873f656',
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
                        'key' => 'field_5fa377b93f658',
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
                        'key' => 'field_5fa377f43f659',
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
                        'key' => 'field_5fa377b93f8658',
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
                        'key' => 'field_5fa377a43f657',
                        'label' => 'Resource',
                        'name' => 'resource',
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
                            0 => 'resources',
                        ),
                        'taxonomy' => '',
                        'allow_null' => 0,
                        'multiple' => 0,
                        'return_format' => 'id',
                        'ui' => 1,
                    ),
                    array(
                        'key' => 'field_5fa3780e3f65b',
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
                        'key' => 'field_5fa388c994097',
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
                        'display_format' => 'F j, Y',
                        'return_format' => 'Y-m-d',
                        'first_day' => 1,
                    ),
                    array(
                        'key' => 'field_5fa38b4f034eb',
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
                        'choices' => array(
                            'USD' => 'USD',
                            'GBP' => 'GBP',
                            'AUD' => 'AUD',
                            'EUR' => 'EUR',
                            '=========' => '=========',
                            'AED' => 'AED',
                            'AFN' => 'AFN',
                            'ALL' => 'ALL',
                            'AMD' => 'AMD',
                            'ANG' => 'ANG',
                            'AOA' => 'AOA',
                            'ARS' => 'ARS',
                            'AWG' => 'AWG',
                            'AZN' => 'AZN',
                            'BAM' => 'BAM',
                            'BBD' => 'BBD',
                            'BDT' => 'BDT',
                            'BGN' => 'BGN',
                            'BHD' => 'BHD',
                            'BIF' => 'BIF',
                            'BMD' => 'BMD',
                            'BND' => 'BND',
                            'BOB' => 'BOB',
                            'BOV' => 'BOV',
                            'BRL' => 'BRL',
                            'BSD' => 'BSD',
                            'BTN' => 'BTN',
                            'BWP' => 'BWP',
                            'BYN' => 'BYN',
                            'BZD' => 'BZD',
                            'CAD' => 'CAD',
                            'CDF' => 'CDF',
                            'CHE' => 'CHE',
                            'CHF' => 'CHF',
                            'CHW' => 'CHW',
                            'CLF' => 'CLF',
                            'CLP' => 'CLP',
                            'CNY' => 'CNY',
                            'COP' => 'COP',
                            'COU' => 'COU',
                            'CRC' => 'CRC',
                            'CUC' => 'CUC',
                            'CUP' => 'CUP',
                            'CVE' => 'CVE',
                            'CZK' => 'CZK',
                            'DJF' => 'DJF',
                            'DKK' => 'DKK',
                            'DOP' => 'DOP',
                            'DZD' => 'DZD',
                            'EGP' => 'EGP',
                            'ERN' => 'ERN',
                            'ETB' => 'ETB',
                            'FJD' => 'FJD',
                            'FKP' => 'FKP',
                            'GEL' => 'GEL',
                            'GHS' => 'GHS',
                            'GIP' => 'GIP',
                            'GMD' => 'GMD',
                            'GNF' => 'GNF',
                            'GTQ' => 'GTQ',
                            'GYD' => 'GYD',
                            'HKD' => 'HKD',
                            'HNL' => 'HNL',
                            'HRK' => 'HRK',
                            'HTG' => 'HTG',
                            'HUF' => 'HUF',
                            'IDR' => 'IDR',
                            'ILS' => 'ILS',
                            'INR' => 'INR',
                            'IQD' => 'IQD',
                            'IRR' => 'IRR',
                            'ISK' => 'ISK',
                            'JMD' => 'JMD',
                            'JOD' => 'JOD',
                            'JPY' => 'JPY',
                            'KES' => 'KES',
                            'KGS' => 'KGS',
                            'KHR' => 'KHR',
                            'KMF' => 'KMF',
                            'KPW' => 'KPW',
                            'KRW' => 'KRW',
                            'KWD' => 'KWD',
                            'KYD' => 'KYD',
                            'KZT' => 'KZT',
                            'LAK' => 'LAK',
                            'LBP' => 'LBP',
                            'LKR' => 'LKR',
                            'LRD' => 'LRD',
                            'LSL' => 'LSL',
                            'LYD' => 'LYD',
                            'MAD' => 'MAD',
                            'MDL' => 'MDL',
                            'MGA' => 'MGA',
                            'MKD' => 'MKD',
                            'MMK' => 'MMK',
                            'MNT' => 'MNT',
                            'MOP' => 'MOP',
                            'MRU[11]' => 'MRU[11]',
                            'MUR' => 'MUR',
                            'MVR' => 'MVR',
                            'MWK' => 'MWK',
                            'MXN' => 'MXN',
                            'MXV' => 'MXV',
                            'MYR' => 'MYR',
                            'MZN' => 'MZN',
                            'NAD' => 'NAD',
                            'NGN' => 'NGN',
                            'NIO' => 'NIO',
                            'NOK' => 'NOK',
                            'NPR' => 'NPR',
                            'NZD' => 'NZD',
                            'OMR' => 'OMR',
                            'PAB' => 'PAB',
                            'PEN' => 'PEN',
                            'PGK' => 'PGK',
                            'PHP' => 'PHP',
                            'PKR' => 'PKR',
                            'PLN' => 'PLN',
                            'PYG' => 'PYG',
                            'QAR' => 'QAR',
                            'RON' => 'RON',
                            'RSD' => 'RSD',
                            'RUB' => 'RUB',
                            'RWF' => 'RWF',
                            'SAR' => 'SAR',
                            'SBD' => 'SBD',
                            'SCR' => 'SCR',
                            'SDG' => 'SDG',
                            'SEK' => 'SEK',
                            'SGD' => 'SGD',
                            'SHP' => 'SHP',
                            'SLL' => 'SLL',
                            'SOS' => 'SOS',
                            'SRD' => 'SRD',
                            'SSP' => 'SSP',
                            'STN[13]' => 'STN[13]',
                            'SVC' => 'SVC',
                            'SYP' => 'SYP',
                            'SZL' => 'SZL',
                            'THB' => 'THB',
                            'TJS' => 'TJS',
                            'TMT' => 'TMT',
                            'TND' => 'TND',
                            'TOP' => 'TOP',
                            'TRY' => 'TRY',
                            'TTD' => 'TTD',
                            'TWD' => 'TWD',
                            'TZS' => 'TZS',
                            'UAH' => 'UAH',
                            'UGX' => 'UGX',
                            'USN' => 'USN',
                            'UYI' => 'UYI',
                            'UYU' => 'UYU',
                            'UYW' => 'UYW',
                            'UZS' => 'UZS',
                            'VES' => 'VES',
                            'VND' => 'VND',
                            'VUV' => 'VUV',
                            'WST' => 'WST',
                            'XAF' => 'XAF',
                            'XAG' => 'XAG',
                            'XAU' => 'XAU',
                            'XBA' => 'XBA',
                            'XBB' => 'XBB',
                            'XBC' => 'XBC',
                            'XBD' => 'XBD',
                            'XCD' => 'XCD',
                            'XDR' => 'XDR',
                            'XOF' => 'XOF',
                            'XPD' => 'XPD',
                            'XPF' => 'XPF',
                            'XPT' => 'XPT',
                            'XSU' => 'XSU',
                            'XTS' => 'XTS',
                            'XUA' => 'XUA',
                            'XXX' => 'XXX',
                            'YER' => 'YER',
                            'ZAR' => 'ZAR',
                            'ZMW' => 'ZMW',
                            'ZWL' => 'ZWL',
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
                        'key' => 'field_5fa38b67034ec',
                        'label' => 'Price',
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
                        'key' => 'field_5fa38bdf034ed',
                        'label' => 'DA',
                        'name' => 'da',
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
                        'key' => 'field_5fa38bed034ee',
                        'label' => 'RD',
                        'name' => 'rd',
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
                        'key' => 'field_5fa3895294098',
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
                        'display_format' => 'F j, Y',
                        'return_format' => 'Y-m-d',
                        'first_day' => 1,
                    ),
                    array(
                        'key' => 'field_5fa3896594099',
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
                        'display_format' => 'F j, Y',
                        'return_format' => 'Y-m-d',
                        'first_day' => 1,
                    ),
                    array(
                        'key' => 'field_5fa38c03034ef',
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
                        'key' => 'field_5fa389779409a',
                        'label' => 'We Paid',
                        'name' => 'we_paid',
                        'type' => 'date_picker',
                        'instructions' => '',
                        'required' => 0,
                        'conditional_logic' => 0,
                        'wrapper' => array(
                            'width' => '',
                            'class' => '',
                            'id' => '',
                        ),
                        'display_format' => 'F j, Y',
                        'return_format' => 'Y-m-d',
                        'first_day' => 1,
                    ),
                    array(
                        'key' => 'field_5fa38c2b034f0',
                        'label' => 'Dollar Price',
                        'name' => 'dollar_price',
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
                        'key' => 'field_5fa38c33034f1',
                        'label' => 'Baht Price',
                        'name' => 'baht_price',
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
                        'key' => 'field_5fa38a1d92332',
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
                            'Ongoing' => 'Ongoing',
                            'Refused' => 'Refused',
                            'Completed' => 'Completed',
                        ),
                        'default_value' => false,
                        'allow_null' => 0,
                        'multiple' => 0,
                        'ui' => 0,
                        'return_format' => 'value',
                        'ajax' => 0,
                        'placeholder' => '',
                    ),
                ),
                'location' => array(
                    array(
                        array(
                            'param' => 'post_type',
                            'operator' => '==',
                            'value' => 'order',
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

        if (is_admin() && 'Add title' == $input && 'order' == $post_type)
            return 'Order Number';

        return $input;
    }

    /**
     * Add columns to the clients admin table
     * @param $columns array list of columns
     * @return array list of columns
     */
    public function set_custom_edit_order_columns($columns)
    {
        unset($columns['date']);
        $columns['title'] = __('ID');
        $columns['client'] = __('Client');
        $columns['text'] = __('Anchor Text');
        $columns['target'] = __('Target URL');
        $columns['niche'] = __('Niche');
        $columns['resource'] = __('Resource');
        $columns['notes'] = __('Notes');
        $columns['sent_to_writers'] = __('Writers');
        $columns['price'] = __('price');
        $columns['da'] = __('DA');
        $columns['rd'] = __('RD');
        $columns['articles_sent_to_the_sites'] = __('Sent');
        $columns['live_link_received'] = __('Live Link Received');
        $columns['we_paid'] = __('Paid');
        $columns['status'] = __('Status');
        return $columns;
    }

    /**
     * Add data to the client custom columns
     */
    public function custom_order_column($column, $post_id)
    {
        switch ($column) {
            case 'client' :
                $client = get_field('client', $post_id);
                $client_name = get_the_title($client);
                $client_url = get_the_permalink($client);
                if (is_string($client_name))
                    echo '<a href="' . $client_url . '" target="_blank">' . $client_name . '</a>';
                else
                    _e('Unable to get client');
                break;
            case 'text' :
                $anchor_text = get_field('anchor_text', $post_id);
                if (($anchor_text))
                    echo $anchor_text;
                else
                    _e('-');
                break;
            case 'target' :
                $target_url = get_field('target_url', $post_id);
                if (($target_url))
                    echo $target_url;
                else
                    _e('-');
                break;
            case 'niche' :
                $niche = get_field('niche', $post_id);
                if (($niche))
                    echo $niche;
                else
                    _e('-');
                break;
            case 'resource' :
                $resource = get_field('resource', $post_id);
                $resource_name = get_the_title($resource);
                $resource_url = get_the_permalink($resource);
                if (($resource))
                    echo '<a href="' . $resource_url . '" target="_blank">' . $resource_name . '</a>';
                else
                    echo '-';
                break;
            case 'notes' :
                $notes = get_field('notes', $post_id);
                if (($notes))
                    echo $notes;
                else
                    _e('-');
                break;
            case 'sent_to_writers' :
                $sent_to_writers = get_field('sent_to_writers', $post_id);
                if (($sent_to_writers))
                    echo $sent_to_writers;
                else
                    _e('-');
                break;
            case 'price' :
                $price = get_field('price', $post_id);
                $currency = get_field('currency', $post_id);
                if (!$currency) {
                    $currency = 'USD';
                }
                if (($price)) {
                    echo $price . ' ' . $currency;
                } else {
                    _e('-');
                }
                break;
            case 'da' :
                $da = get_field('da', $post_id);
                if (($da))
                    echo $da;
                else
                    _e('-');
                break;
            case 'rd' :
                $rd = get_field('rd', $post_id);
                if (($rd))
                    echo $rd;
                else
                    _e('-');
                break;
            case 'articles_sent_to_the_sites' :
                $articles_sent_to_the_sites = get_field('articles_sent_to_the_sites', $post_id);
                if (($articles_sent_to_the_sites))
                    echo $articles_sent_to_the_sites;
                else
                    _e('-');
                break;
            case 'live_link_received' :
                $live_link_received = get_field('live_link_received', $post_id);
                $live_link = get_field('live_link', $post_id);
                $live_link_received_string = '<a href="' . $live_link . '" target="_blank">' . $live_link_received . '</a>';
                if (($live_link_received))
                    echo $live_link_received_string;
                else
                    _e('-');
                break;
            case 'we_paid' :
                $we_paid = get_field('we_paid', $post_id);
                $usd = get_field('dollar_price', $post_id);
                $thb = get_field('baht_price', $post_id);
                $prices = '';
                if (($we_paid)) {
                    $prices = $usd;
                    if (is_string($thb)) {
                        $prices = $usd . ' / ' . $thb;
                    }
                } else {
                    if (is_string($thb)) {
                        $prices = $thb;
                    }
                }
                if (($we_paid))
                    echo $we_paid . '<br />' . $prices;
                else
                    _e('-');
                break;
            case 'status' :
                $status = get_field('status', $post_id);
                if (($status))
                    echo $status;
                else
                    _e('-');
                break;
        }
    }

    /**
     * Make columns in the clients admin table sortable
     */
    public function set_custom_order_sortable_columns($columns)
    {
        $columns['client'] = 'client';
        $columns['text'] = 'text';
        $columns['target'] = 'target';
        $columns['niche'] = 'niche';
        $columns['resource'] = 'resource';
        $columns['notes'] = 'notes';
        $columns['sent_to_writers'] = 'sent_to_writers';
        $columns['price'] = 'price';
        $columns['da'] = 'da';
        $columns['rd'] = 'rd';
        $columns['articles_sent_to_the_sites'] = 'articles_sent_to_the_sites';
        $columns['live_link_received'] = 'live_link_received';
        $columns['we_paid'] = 'we_paid';
        $columns['prices'] = 'prices';
        $columns['status'] = 'status';
        return $columns;
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

        if ($filters['client'] != '') {
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
            'post_type' => 'order',
            'meta_query' => $meta_query
        );

        // query
        return new WP_Query($args);
    }

    /**
     * update the resource custom fields
     * @param $orderID int the order id
     * @param $resourceID int the resource id
     * @param $clientID int the client id
     * @param $orderData array the custom fields data
     */
    public static function update_order($orderID, $resourceID, $clientID, $orderData)
    {

        update_field('client', $clientID, $orderID);
        update_field('resource', $resourceID, $orderID);

        if (isset($orderData['Anchor Text'])) {
            update_field('anchor_text', $orderData['Anchor Text'], $orderID);
        }
        if (isset($orderData['Target URL'])) {
            update_field('target_url', $orderData['Target URL'], $orderID);
        }
        if (isset($orderData['Notes'])) {
            update_field('notes', $orderData['Notes'], $orderID);
        }
        if (isset($orderData['Niche'])) {
            update_field('niche', $orderData['Niche'], $orderID);
        }
        if (isset($orderData['Sent To Writers'])) {
            update_field('sent_to_writers', $orderData['Sent To Writers'], $orderID);
        }
        if (isset($orderData['Currency'])) {
            update_field('currency', $orderData['Currency'], $orderID);
        }
        if (isset($orderData['Price'])) {
            update_field('price', $orderData['Price'], $orderID);
        }
        if (isset($orderData['DA'])) {
            update_field('da', $orderData['DA'], $orderID);
        }
        if (isset($orderData['RD'])) {
            update_field('rd', $orderData['RD'], $orderID);
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
        if (isset($orderData['Paid'])) {
            update_field('we_paid', $orderData['Paid'], $orderID);
        }
        if (isset($orderData['USD Price'])) {
            update_field('dollar_price', $orderData['USD Price'], $orderID);
        }
        if (isset($orderData['THB Price'])) {
            update_field('baht_price', $orderData['THB Price'], $orderID);
        }
        if (isset($orderData['Status'])) {
            update_field('status', $orderData['Status'], $orderID);
        }
        if (isset($orderData['Sectors'])) {
            $sectors = explode(', ', $orderData['Sectors']);
            wp_set_post_terms($orderID, $sectors, 'sector');
        }
    }
}