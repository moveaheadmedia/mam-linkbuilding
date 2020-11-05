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
        add_filter('gettext',array($this,'custom_enter_title'));

        // Admin Table
        add_filter('manage_resources_posts_columns', array($this, 'set_custom_edit_resources_columns'));
        add_action( 'manage_resources_posts_custom_column' , array($this, 'custom_resources_column'), 10, 2 );
        add_filter( 'manage_edit-resources_sortable_columns', array($this, 'set_custom_resources_sortable_columns') );
    }



    /**
     * Add columns to the clients admin table
     */
    public function set_custom_edit_resources_columns($columns){
        unset( $columns['date'] );
        unset( $columns['title'] );
        $columns['title'] = __( 'Website URL' );
        $columns['contact'] = __( 'Contact Info' );
        $columns['da'] = __( 'DA' );
        $columns['dr'] = __( 'DR' );
        $columns['rd'] = __( 'RD' );
        $columns['tr'] = __( 'TR' );
        $columns['price'] = __( 'Price' );
        $columns['payment'] = __( 'Payment Method' );
        $columns['rating'] = __( 'Rating' );
        $columns['date'] = __( 'Date' );
        return $columns;
    }

    /**
     * Add data to the client custom columns
     */
    public function custom_resources_column($column, $post_id){
        switch ( $column ) {
            case 'contact' :
                $email = get_field('email',  $post_id);
                $name = get_field('contact_name',  $post_id);
                if ( is_string( $email ) ){
                    if ( is_string( $name ) ){
                        echo $name . ' <' . $email . '>';
                    }else{
                        echo $email;
                    }
                }else{
                    _e( 'Unable to get contact email' );
                }

                break;
            case 'da' :
                $da = get_field('da',  $post_id);
                if ( is_string( $da ) )
                    echo $da;
                else
                    _e( 'Unable to get DA' );
                break;
            case 'dr' :
                $dr = get_field('dr',  $post_id);
                if ( is_string( $dr ) )
                    echo $dr;
                else
                    _e( 'Unable to get DR' );
                break;
            case 'rd' :
                $rd = get_field('rd',  $post_id);
                if ( is_string( $rd ) )
                    echo $rd;
                else
                    _e( 'Unable to get RD' );
                break;
            case 'tr' :
                $tr = get_field('tr',  $post_id);
                if ( is_string( $tr ) )
                    echo $tr;
                else
                    _e( 'Unable to get TR' );
                break;
            case 'price' :
                $originaPrice = get_field('original_price',  $post_id);
                $finalePrice = get_field('price',  $post_id);
                $currency = get_field('currency',  $post_id);
                if(!$currency){
                    $currency = 'USD';
                }
                if(is_string($originaPrice)){
                    if(is_string($finalePrice)){
                        echo $finalePrice . ' ' . $currency;
                    }else{
                        echo $originaPrice . ' ' . $currency;
                    }
                }else{
                    _e( 'Unable to get Original Price' );
                }
                break;
            case 'payment' :
                $payment = get_field('payment_method',  $post_id);
                if ( is_string( $payment ) )
                    echo $payment;
                else
                    _e( 'Unable to get Payment' );
                break;
            case 'rating' :
                $rating = get_field('rating',  $post_id);
                if ( is_string( $rating ) )
                    echo $rating;
                else
                    _e( 'Unable to get Rating' );
                break;
        }
    }

    /**
     * Make columns in the clients admin table sortable
     */
    public function set_custom_resources_sortable_columns($columns){
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
    function custom_enter_title( $input ) {

        global $post_type;

        if( is_admin() && 'Add title' == $input && 'resources' == $post_type )
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
        if ('client' == $post->post_type) {
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
     * @return WP_Query
     */
    public function filtered_posts()
    {
        // args
        $args = array(
            'numberposts' => -1,
            'post_type' => 'resources'
        );

        // query
        return new WP_Query($args);
    }
}