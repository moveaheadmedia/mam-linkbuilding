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
        add_filter('mam-resources-filtered-posts-ids', array($this, 'filtered_posts_ids'));
        add_action('acf/init', array($this, 'add_resources_custom_fields'));
        add_shortcode('mam-resources-listing', [$this, 'mam_resources_listing']);
        add_filter('gettext', array($this, 'custom_enter_title'));

    }

    /**
     * Change Add Title Text
     * @param $input string the input name
     * @return string
     * @noinspection PhpUnused
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
     * @param $template string the template path
     * @return string the template path
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
     * @param $template string the template path
     * @return string the template path
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
                        'key' => 'field_5ffe5b1fba79a',
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
                        'key' => 'field_5ffe5ae3ba796',
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
                        'key' => 'field_5ffe5af4ba797',
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
                        'key' => 'field_5ffe5afaba798',
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
                        'key' => 'field_5ffe5b24ba79b',
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
                    [
                        'key' => 'field_5fa364dcbdb39',
                        'label' => 'Currency',
                        'name' => 'currency',
                        'type' => 'select',
                        'instructions' => '',
                        'required' => 0,
                        'conditional_logic' => 0,
                        'wrapper' => [
                            'width' => '',
                            'class' => '',
                            'id' => '',
                        ],
                        'choices' => Config::getInstance()->currencies,
                        'default_value' => false,
                        'allow_null' => 0,
                        'multiple' => 0,
                        'ui' => 0,
                        'return_format' => 'value',
                        'ajax' => 0,
                        'placeholder' => '',
                    ],
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
                    array(
                        'key' => 'field_5ffe5abeba794',
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
                            'Confirmed' => 'Confirmed',
                            'Do not use' => 'Do not use',
                            'Raw' => 'Raw',
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
                        'key' => 'field_5ffe5b0aba799',
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
        if(!isset($filters['start'])){
            $filters['start'] = 0;
        }
        if(!isset($filters['length'])){
            $filters['length'] = -1;
        }
        if(!isset($filters['order']['field'])){
            $filters['order']['field'] = 'date';
        }
        if(!isset($filters['order']['dir'])){
            $filters['order']['dir'] = 'DESC';
        }
        $meta_query = array();
        if (isset($filters['da']) && $filters['da'] != '') {
            $meta_query[] = [
                'key' => 'da',
                'value' => $filters['da'],
                'compare' => '>=',
                'type' => 'NUMERIC',
            ];
        }

        if (isset($filters['da1']) && $filters['da1'] != '') {
            $meta_query[] = [
                'key' => 'da',
                'value' => $filters['da1'],
                'compare' => '<=',
                'type' => 'NUMERIC',
            ];
        }

        if (isset($filters['dr']) && $filters['dr'] != '') {
            $meta_query[] = [
                'key' => 'dr',
                'value' => $filters['dr'],
                'compare' => '>=',
                'type' => 'NUMERIC',
            ];
        }

        if (isset($filters['dr1']) && $filters['dr1'] != '') {
            $meta_query[] = [
                'key' => 'dr',
                'value' => $filters['dr1'],
                'compare' => '<=',
                'type' => 'NUMERIC',
            ];
        }

        if (isset($filters['price']) && $filters['price'] != '') {
            $meta_query[] = [
                'key' => 'original_price',
                'value' => $filters['price'],
                'compare' => '>=',
                'type' => 'NUMERIC',
            ];
        }

        if (isset($filters['price1']) && $filters['price1'] != '') {
            $meta_query[] = [
                'key' => 'original_price',
                'value' => $filters['price1'],
                'compare' => '<=',
                'type' => 'NUMERIC',
            ];
        }

        if (isset($filters['rd']) && $filters['rd'] != '') {
            $meta_query[] = [
                'key' => 'rd',
                'value' => $filters['rd'],
                'compare' => '>=',
                'type' => 'NUMERIC',
            ];
        }

        if (isset($filters['tr']) && $filters['tr'] != '') {
            $meta_query[] = [
                'key' => 'tr',
                'value' => $filters['tr'],
                'compare' => '>=',
                'type' => 'NUMERIC',
            ];
        }

        $title_filter = '';
        if(isset($filters['search'])){
            foreach ($filters['search'] as $search){
                if($search['name'] == 'title'){
                    $title_filter = $search['value'];
                    continue;
                }
                $meta_query[] = [
                    'key' => $search['name'],
                    'value' => $search['value'],
                    'compare' => 'LIKE'
                ];
            }
        }

        $tax_query = array();
        if (isset($filters['sectors']) && !empty($filters['sectors']))
            $tax_query = array(
                array(
                    'taxonomy' => 'sector',
                    'terms' => $filters['sectors'],
                    'field' => 'term_id',
                )
            );

        $resourceIDs = array();
        if (isset($filters['client']) && !empty($filters['client'])){
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
            'depth'          => 1,
            'offset'          => $filters['start'],
            'posts_per_page' => $filters['length'],
            'post_type' => 'resources',
            'meta_query' => $meta_query,
            'tax_query' => $tax_query,
            'operator' => 'EXISTS',
            'post__not_in' => $resourceIDs,
            'fields' => 'ids',
            'no_found_rows' => true,
        );
        if($title_filter){
            $args['s'] = $title_filter;
        }


        if($filters['order']['field'] == 'date' || $filters['order']['field'] == 'title'){
            $args['orderby'] = $filters['order']['field'];
        }else{
            $args['orderby'] = 'meta_value';
            $args['meta_key'] = $filters['order']['field'];
        }
        $args['order'] = $filters['order']['dir'];

        //add_filter( 'posts_where', 'mam_title_filter', 10, 2 );
        $the_query = new WP_Query($args);
        //remove_filter( 'posts_where', 'mam_title_filter', 10);

        wp_reset_query();
        return $the_query;
    }

    /**
     * Get the properties filtered ids
     * @param $filters array the list of filters
     * @return WP_Query
     */
    public function filtered_posts_ids($filters)
    {
        $filters['start'] = 0;
        $filters['length'] = -1;
        $meta_query = array();
        if (isset($filters['da']) && $filters['da'] != '') {
            $meta_query[] = [
                'key' => 'da',
                'value' => $filters['da'],
                'compare' => '>=',
                'type' => 'NUMERIC',
            ];
        }

        if (isset($filters['da1']) && $filters['da1'] != '') {
            $meta_query[] = [
                'key' => 'da',
                'value' => $filters['da1'],
                'compare' => '<=',
                'type' => 'NUMERIC',
            ];
        }

        if (isset($filters['dr']) && $filters['dr'] != '') {
            $meta_query[] = [
                'key' => 'dr',
                'value' => $filters['dr'],
                'compare' => '>=',
                'type' => 'NUMERIC',
            ];
        }

        if (isset($filters['dr1']) && $filters['dr1'] != '') {
            $meta_query[] = [
                'key' => 'dr',
                'value' => $filters['dr1'],
                'compare' => '<=',
                'type' => 'NUMERIC',
            ];
        }

        if (isset($filters['price']) && $filters['price'] != '') {
            $meta_query[] = [
                'key' => 'original_price',
                'value' => $filters['price'],
                'compare' => '>=',
                'type' => 'NUMERIC',
            ];
        }

        if (isset($filters['price1']) && $filters['price1'] != '') {
            $meta_query[] = [
                'key' => 'original_price',
                'value' => $filters['price1'],
                'compare' => '<=',
                'type' => 'NUMERIC',
            ];
        }

        if (isset($filters['rd']) && $filters['rd'] != '') {
            $meta_query[] = [
                'key' => 'rd',
                'value' => $filters['rd'],
                'compare' => '>=',
                'type' => 'NUMERIC',
            ];
        }

        if (isset($filters['tr']) && $filters['tr'] != '') {
            $meta_query[] = [
                'key' => 'tr',
                'value' => $filters['tr'],
                'compare' => '>=',
                'type' => 'NUMERIC',
            ];
        }

        $title_filter = '';
        if(isset($filters['search'])){
            foreach ($filters['search'] as $search){
                if($search['name'] == 'title'){
                    $title_filter = $search['value'];
                    continue;
                }
                $meta_query[] = [
                    'key' => $search['name'],
                    'value' => $search['value'],
                    'compare' => 'LIKE'
                ];
            }
        }

        $tax_query = array();
        if (isset($filters['sectors']) && !empty($filters['sectors']))
            $tax_query = array(
                array(
                    'taxonomy' => 'sector',
                    'terms' => $filters['sectors'],
                    'field' => 'term_id',
                )
            );

        $resourceIDs = array();
        if (isset($filters['client']) && !empty($filters['client'])){
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
            'depth'          => 1,
            'offset'          => $filters['start'],
            'posts_per_page' => $filters['length'],
            'post_type' => 'resources',
            'meta_query' => $meta_query,
            'tax_query' => $tax_query,
            'operator' => 'EXISTS',
            'post__not_in' => $resourceIDs,
            'fields' => 'ids',
            'no_found_rows' => true,
        );
        if($title_filter){
            $args['s'] = $title_filter;
        }
        // query
        $the_query = new WP_Query($args);
        wp_reset_query();
        return $the_query;
    }

    /**
     * Check if the IP Address is used with multiple resources
     * @param $ip_address string the IP Address that need to be checked if duplicated or not
     * @return false bool true if duplicated false if not
     */
    public static function resource_ip_duplicated($ip_address){
        $meta_query[] = [
            'key' => 'ip_address',
            'value' => $ip_address,
            'compare' => '='
        ];

        $args = array(
            'numberposts' => '-1',
            'posts_per_page' => '-1',
            'posts_per_archive_page' => '-1',
            'post_type' => 'resources',
            'meta_query' => $meta_query,
            'operator' => 'EXISTS'
        );

        $query = new WP_Query($args);
        if($query->post_count > 1){
            return true;
        }
        return false;
    }

    /**
     * Get the meta field name by column name
     * @param $column_name string the column name
     * @return string the meta name
     */
    public static function get_filed_name_by_column_name($column_name)
    {
        switch ($column_name) {
            case "Website":
                return 'title';
            case 'IP Address':
                return 'ip_address';
            case 'Other Info':
                return 'other_info';
            case 'Contact / Email':
                return 'contact__email';
            case 'Social Media':
                return 'social_media';
            case 'New Remarks':
                return 'new_remarks';
            case 'Niche':
                return 'niche';
            case 'Sectors':
                return 'sectors';
            case 'Metrics Update Date':
                return 'metrics_update_date';
            case 'Status':
                return 'status';
            case 'Rating':
                return 'rating';
            case 'Origin File':
                return 'origin_file';
            case 'Secondary Email':
                return 'secondary_email';
            case 'Notes':
                return 'notes';
            case 'Payment Method':
                return 'payment_method';
            case 'Finale Price':
                return 'price';
            case 'Package / Discount':
                return 'package__discount';
            case 'Link Placement Price':
                return 'link_placement_price';
            case 'Adult Price':
                return 'adult_price';
            case 'CBD Price':
                return 'cbd_price';
            case 'Casino Price':
                return 'casino_price';
            case 'Original Price':
                return 'original_price';
            case 'Country':
                return 'country';
            case 'Currency':
                return 'currency';
            case 'Organic Keywords':
                return 'organic_keywords';
            case 'CF':
                return 'cf';
            case 'TF':
                return 'tf';
            case 'PA':
                return 'pa';
            case 'TR':
                return 'tr';
            case 'RD':
                return 'rd';
            case 'DR':
                return 'dr';
            case 'DA':
                return 'da';
            case 'Name':
                return 'contact_name';
            case 'Email':
                return 'email';
        }
        return 'title';
    }

    /**
     * @param $where string the original where
     * @param $wp_query WP_Query the original query
     * @return string the new where
     */
    function mam_title_filter( $where, &$wp_query )
    {
        global $wpdb;
        // 2. pull the custom query in here:
        if ( $search_term = $wp_query->get( 'search_prod_title' ) ) {
            if($search_term != ''){
                $where .= ' AND ' . $wpdb->posts . '.post_title LIKE \'%' . esc_sql( like_escape( $search_term ) ) . '%\'';
            }
        }
        return $where;
    }

    /**
     * Check if resource used with other websites
     * @param $resource string the resources url that need to be checked if used or not
     * @param $websites string the websites comma separated urls that need to be checked if used or not
     * @return false bool true if used false if not
     */
    public static function check_used_resource($resource, $websites)
    {
        $websites = explode(',', $websites);
        foreach ($websites as $website) {

            $meta_query = [];
            $meta_query['relation'] = 'AND';

            $meta_query[] = [
                'key' => 'resource_url',
                'value' => $resource,
                'compare' => 'LIKE'
            ];
            $meta_query[] = [
                'key' => 'target_url',
                'value' => $website,
                'compare' => 'LIKE'
            ];


            // args
            $args = array(
                'numberposts' => -1,
                'posts_per_page' => -1,
                'post_type' => 'lborder',
                'meta_query' => $meta_query
            );

            // query
            $query = new WP_Query($args);
            if ($query->have_posts()) {
                return true;
            }
        }
        return false;
    }
}