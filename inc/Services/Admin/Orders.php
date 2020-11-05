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
        add_filter('gettext',array($this,'custom_enter_title'));

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

        }
    }

    /**
     * Change Add Title Text
     */
    function custom_enter_title( $input ) {

        global $post_type;

        if( is_admin() && 'Add title' == $input && 'order' == $post_type )
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
                    echo '<a href="'.$client_url.'" target="_blank">'.$client_name.'</a>';
                else
                    _e('Unable to get client');
                break;
            case 'text' :
                $anchor_text = get_field('anchor_text', $post_id);
                if (is_string($anchor_text))
                    echo $anchor_text;
                else
                    _e('Unable to get anchor text');
                break;
            case 'target' :
                $target_url = get_field('target_url', $post_id);
                if (is_string($target_url))
                    echo $target_url;
                else
                    _e('Unable to get target URL');
                break;
            case 'resource' :
                $resource = get_field('resource', $post_id);
                $resource_name = get_the_title($resource);
                $resource_url = get_the_permalink($resource);
                if (is_string($resource_name))
                    echo '<a href="'.$resource_url.'" target="_blank">'.$resource_name.'</a>';
                else
                    _e('Unable to get resource');
                break;
            case 'notes' :
                $notes = get_field('notes', $post_id);
                if (is_string($notes))
                    echo $notes;
                else
                    _e('Unable to get notes');
                break;
            case 'sent_to_writers' :
                $sent_to_writers = get_field('sent_to_writers', $post_id);
                if (is_string($sent_to_writers))
                    echo $sent_to_writers;
                else
                    _e('-');
                break;
            case 'price' :
                $price = get_field('price',  $post_id);
                $currency = get_field('currency',  $post_id);
                if(!$currency){
                    $currency = 'USD';
                }
                if(is_string($price)){
                    echo $price . ' ' . $currency;
                }else{
                    _e( 'Unable to get Original Price' );
                }
                break;
            case 'da' :
                $da = get_field('da', $post_id);
                if (is_string($da))
                    echo $da;
                else
                    _e('-');
                break;
            case 'rd' :
                $rd = get_field('rd', $post_id);
                if (is_string($rd))
                    echo $rd;
                else
                    _e('-');
                break;
            case 'articles_sent_to_the_sites' :
                $articles_sent_to_the_sites = get_field('articles_sent_to_the_sites', $post_id);
                if (is_string($articles_sent_to_the_sites))
                    echo $articles_sent_to_the_sites;
                else
                    _e('-');
                break;
            case 'live_link_received' :
                $live_link_received = get_field('live_link_received', $post_id);
                $live_link = get_field('live_link', $post_id);
                $live_link_received_string = '<a href="'.$live_link.'" target="_blank">'.$live_link_received.'</a>';
                if (is_string($live_link_received_string))
                    echo $live_link_received_string;
                else
                    _e('-');
                break;
            case 'we_paid' :
                $we_paid = get_field('we_paid', $post_id);
                $usd = get_field('dollar_price', $post_id);
                $thb = get_field('baht_price', $post_id);
                $prices = '';
                if(is_string($usd)){
                    $prices = $usd;
                    if(is_string($thb)){
                        $prices = $usd . ' / ' . $thb;
                    }
                }else{
                    if(is_string($thb)){
                        $prices = $thb;
                    }
                }
                if (is_string($we_paid))
                    echo $we_paid . '<br />' . $prices;
                else
                    _e('-');
                break;
            case 'status' :
                $status = get_field('status', $post_id);
                if (is_string($status))
                    echo $status;
                else
                    _e('Unable to get status');
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
     * @return WP_Query
     */
    public function filtered_posts()
    {
        // args
        $args = array(
            'numberposts' => -1,
            'post_type' => 'order'
        );

        // query
        return new WP_Query($args);
    }
}