<?php


namespace MAM\Plugin\Services\Admin;


use WP_Query;
use MAM\Plugin\Config;
use MAM\Plugin\Services\ServiceInterface;

class Resources implements ServiceInterface
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

        add_action('init', array($this, 'init_resources_post_type'), 0);
        add_filter('single_template', array($this, 'init_resources_template'));
        add_filter('template_include', array($this, 'archive_template'));
        add_filter('mam-resources-filtered-posts', array($this, 'filtered_posts'));
        add_action('acf/init', array($this, 'add_resources_custom_fields'));
        add_shortcode('mam-resources-listing', [$this, 'mam_resources_listing']);
        add_filter('gettext', array($this, 'custom_enter_title'));

        // Admin Table
        add_filter('manage_resources_posts_columns', array($this, 'set_custom_edit_resources_columns'));
        add_action('manage_resources_posts_custom_column', array($this, 'custom_resources_column'), 10, 2);
        add_filter('manage_edit-resources_sortable_columns', array($this, 'set_custom_resources_sortable_columns'));
    }


    /**
     * Add columns to the clients admin table
     */
    public function set_custom_edit_resources_columns($columns)
    {
        unset($columns['date']);
        unset($columns['title']);
        $columns['title'] = __('Website URL');
        $columns['contact'] = __('Contact Info');
        $columns['da'] = __('DA');
        $columns['dr'] = __('DR');
        $columns['rd'] = __('RD');
        $columns['tr'] = __('TR');
        $columns['price'] = __('Price');
        $columns['payment'] = __('Payment Method');
        $columns['rating'] = __('Rating');
        $columns['date'] = __('Date');
        return $columns;
    }

    /**
     * Add data to the client custom columns
     */
    public function custom_resources_column($column, $post_id)
    {
        switch ($column) {
            case 'contact' :
                $email = get_field('email', $post_id);
                $name = get_field('contact_name', $post_id);
                if (is_string($email)) {
                    if (is_string($name)) {
                        echo $name . ' <' . $email . '>';
                    } else {
                        echo $email;
                    }
                } else {
                    _e('Unable to get contact email');
                }

                break;
            case 'da' :
                $da = get_field('da', $post_id);
                if (is_string($da))
                    echo $da;
                else
                    _e('Unable to get DA');
                break;
            case 'dr' :
                $dr = get_field('dr', $post_id);
                if (is_string($dr))
                    echo $dr;
                else
                    _e('Unable to get DR');
                break;
            case 'rd' :
                $rd = get_field('rd', $post_id);
                if (is_string($rd))
                    echo $rd;
                else
                    _e('Unable to get RD');
                break;
            case 'tr' :
                $tr = get_field('tr', $post_id);
                if (is_string($tr))
                    echo $tr;
                else
                    _e('Unable to get TR');
                break;
            case 'price' :
                $originaPrice = get_field('original_price', $post_id);
                $finalePrice = get_field('price', $post_id);
                $currency = get_field('currency', $post_id);
                if (!$currency) {
                    $currency = 'USD';
                }
                if (is_string($originaPrice)) {
                    if (is_string($finalePrice)) {
                        echo $finalePrice . ' ' . $currency;
                    } else {
                        echo $originaPrice . ' ' . $currency;
                    }
                } else {
                    _e('Unable to get Original Price');
                }
                break;
            case 'payment' :
                $payment = get_field('payment_method', $post_id);
                if (is_string($payment))
                    echo $payment;
                else
                    _e('Unable to get Payment');
                break;
            case 'rating' :
                $rating = get_field('rating', $post_id);
                if (is_string($rating))
                    echo $rating;
                else
                    _e('Unable to get Rating');
                break;
        }
    }

    /**
     * Make columns in the clients admin table sortable
     */
    public function set_custom_resources_sortable_columns($columns)
    {
        $columns['contact'] = 'contact';
        $columns['da'] = 'da';
        $columns['dr'] = 'dr';
        $columns['rd'] = 'rd';
        $columns['tr'] = 'tr';
        $columns['price'] = 'price';
        $columns['payment'] = 'payment';
        $columns['rating'] = 'rating';
        return $columns;
    }

    /**
     * Change Add Title Text
     */
    function custom_enter_title($input)
    {

        global $post_type;

        if (is_admin() && 'Add title' == $input && 'resources' == $post_type)
            return 'Website URL';

        return $input;
    }

    /**
     * init resources post type info (to be called by wordpress)
     */
    public static function init_resources_post_type()
    {
        $labels = array(
            'name' => _x('Resources', 'Post Type General Name'),
            'singular_name' => _x('Resources', 'Post Type Singular Name'),
            'menu_name' => __('Resources'),
            'name_admin_bar' => __('Resources'),
            'archives' => __('Item Archives'),
            'attributes' => __('Item Attributes'),
            'parent_item_colon' => __('Parent Resources:'),
            'all_items' => __('All Resources'),
            'add_new_item' => __('Add New Resources'),
            'add_new' => __('Add New'),
            'new_item' => __('New Resources'),
            'edit_item' => __('Edit Resources'),
            'update_item' => __('Update Resources'),
            'view_item' => __('View Resources'),
            'view_items' => __('View Resources'),
            'search_items' => __('Search Resources'),
            'not_found' => __('Not found'),
            'not_found_in_trash' => __('Not found in Trash'),
            'featured_image' => __('Featured Image'),
            'set_featured_image' => __('Set featured image'),
            'remove_featured_image' => __('Remove featured image'),
            'use_featured_image' => __('Use as featured image'),
            'insert_into_item' => __('Insert into'),
            'uploaded_to_this_item' => __('Uploaded to this Resources'),
            'items_list' => __('Items list'),
            'items_list_navigation' => __('Items list navigation'),
            'filter_items_list' => __('Filter Resources list'),
        );
        $args = array(
            'label' => __('Resources'),
            'description' => __('Resources post type by MAM Linkbuilding'),
            'labels' => $labels,
            'supports' => array('title', 'custom-fields'),
            'hierarchical' => false,
            'public' => true,
            'show_ui' => true,
            'show_in_menu' => true,
            'menu_icon' => 'dashicons-database',
            'menu_position' => 5,
            'show_in_admin_bar' => true,
            'show_in_nav_menus' => true,
            'can_export' => true,
            'has_archive' => true,
            'exclude_from_search' => false,
            'publicly_queryable' => true,
            'capability_type' => 'page',
        );
        register_post_type('resources', $args);
    }

    /**
     * init post type template file single-resources.php
     */
    function init_resources_template($template)
    {
        global $post;
        if ('resources' == $post->post_type) {
            $theme_files = array('single-resources.php', 'mam/single-resources.php');
            $exists_in_theme = locate_template($theme_files, false);
            if ($exists_in_theme != '') {
                return $exists_in_theme;
            } else {
                return $this->plugin_path . 'templates/single-resources.php';
            }
        }
        return $template;
    }

    /**
     * add resources archive template
     */
    public function archive_template($template)
    {
        if (is_post_type_archive('resources')) {
            $theme_files = array('archive-resources.php', 'mam/archive-resources.php');
            $exists_in_theme = locate_template($theme_files, false);
            if ($exists_in_theme != '') {
                return $exists_in_theme;
            } else {
                return $this->plugin_path . 'templates/archive-resources.php';
            }
        }
        return $template;
    }

    /**
     * add resources post type custom fields (using ACF Pro)
     */
    public function add_resources_custom_fields()
    {
        if (function_exists('acf_add_local_field_group')) {
            acf_add_local_field_group(array(
                'key' => 'group_5fa363b4f1679',
                'title' => 'Resource Details',
                'fields' => array(
                    array(
                        'key' => 'field_5fa3692473acf',
                        'label' => 'Email',
                        'name' => 'email',
                        'type' => 'email',
                        'instructions' => '',
                        'required' => 1,
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
                        'key' => 'field_5fa3693073ad0',
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
                        'key' => 'field_5fa3695173ad1',
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
                        'key' => 'field_5fa3696e73ad2',
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
                        'key' => 'field_5fa3697573ad3',
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
                        'key' => 'field_5fa3697b73ad4',
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
                        'key' => 'field_5fa364dcbdb39',
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
                        'key' => 'field_5fa3698573ad5',
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
                        'key' => 'field_5fa369a773ad6',
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
                        'key' => 'field_5fa36a1173ad7',
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
                        'key' => 'field_5fa36a1a73ad8',
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
                        'key' => 'field_5fa36a2073ad9',
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
                        'key' => 'field_5fa36a4473ada',
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
                        'key' => 'field_5fa3649fbdb38',
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
                        'key' => 'field_5fa36abd73adb',
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
                        'key' => 'field_5fa3642bbdb35',
                        'label' => 'Comments',
                        'name' => 'comments',
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
                        'key' => 'field_5fa36ad373adc',
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
                        'key' => 'field_5fa36b1f73add',
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
                        'key' => 'field_5fa363f1bdb34',
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
                ),
                'location' => array(
                    array(
                        array(
                            'param' => 'post_type',
                            'operator' => '==',
                            'value' => 'resources',
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
     * [mam-resources-listing] function
     * @param $atts array
     * @return false|string
     */
    public function mam_resources_listing($atts)
    {
        global $a;
        $a = shortcode_atts(array(
            'type' => 'for-sale'
        ), $atts);

        $theme_files = array('mam-resources-listing.php', 'mam/mam-resources-listing.php');
        $exists_in_theme = locate_template($theme_files, false);

        ob_start();
        if ($exists_in_theme != '') {
            /** @noinspection PhpIncludeInspection */
            include $exists_in_theme;
        } else {
            // nope, load the content
            /** @noinspection PhpIncludeInspection */
            include $this->plugin_path . 'templates/mam-resources-listing.php';
        }
        return ob_get_clean();
    }

    /**
     * Get the properties filtered
     * @param $filters array the list of filters
     * @return WP_Query
     */
    public function filtered_posts($filters)
    {
        $meta_query = array();
        if (isset($filters['da']) & $filters['da'] != '') {
            $meta_query[] = [
                'key' => 'da',
                'value' => $filters['da'],
                'compare' => '>=',
                'type' => 'NUMERIC',
            ];
        }

        if (isset($filters['da1']) & $filters['da1'] != '') {
            $meta_query[] = [
                'key' => 'da',
                'value' => $filters['da1'],
                'compare' => '<=',
                'type' => 'NUMERIC',
            ];
        }

        if (isset($filters['dr']) & $filters['dr'] != '') {
            $meta_query[] = [
                'key' => 'dr',
                'value' => $filters['dr'],
                'compare' => '>=',
                'type' => 'NUMERIC',
            ];
        }

        if (isset($filters['dr1']) & $filters['dr1'] != '') {
            $meta_query[] = [
                'key' => 'dr',
                'value' => $filters['dr1'],
                'compare' => '<=',
                'type' => 'NUMERIC',
            ];
        }

        if (isset($filters['rd']) & $filters['rd'] != '') {
            $meta_query[] = [
                'key' => 'rd',
                'value' => $filters['rd'],
                'compare' => '>=',
                'type' => 'NUMERIC',
            ];
        }

        if (isset($filters['tr']) & $filters['tr'] != '') {
            $meta_query[] = [
                'key' => 'tr',
                'value' => $filters['tr'],
                'compare' => '>=',
                'type' => 'NUMERIC',
            ];
        }
        if (isset($filters['sectors']) & !empty($filters['sectors']))
            $tax_query = array(
                array(
                    'taxonomy' => 'sector',
                    'terms' => $filters['sectors'],
                    'field' => 'term_id',
                )
            );

        $resourceIDs = array();
        if (isset($filters['client']) & !empty($filters['client'])){
            $orders = apply_filters('mam-orders-filtered-posts', $filters);
            if ($orders->have_posts()) {
                while ($orders->have_posts()) {
                    $orders->the_post();
                    $resourceIDs[] = get_field('resource', get_the_ID());
                }
            }
        }

        // args
        $args = array(
            'numberposts' => '-1',
            'posts_per_page' => '-1',
            'posts_per_archive_page' => '-1',
            'post_type' => 'resources',
            'meta_query' => $meta_query,
            'tax_query' => $tax_query,
            'operator' => 'EXISTS',
            'post__not_in' => $resourceIDs,
        );

        // query
        return new WP_Query($args);
    }
}