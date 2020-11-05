<?php


namespace MAM\Plugin\Services\Admin;


use WP_Query;
use MAM\Plugin\Config;
use MAM\Plugin\Services\ServiceInterface;

class Sectors implements ServiceInterface
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

        add_action('init', array($this, 'create_sector_taxonomies'), 0);
    }

    /**
     * init property post type info (to be called by wordpress)
     */
    public static function create_sector_taxonomies()
    {
        // Add new taxonomy, NOT hierarchical (like tags)
        $labels = array(
            'name' => _x( 'Sectors', 'taxonomy general name' ),
            'singular_name' => _x( 'Sector', 'taxonomy singular name' ),
            'search_items' =>  __( 'Search Sectors' ),
            'popular_items' => __( 'Popular Sectors' ),
            'all_items' => __( 'All Sectors' ),
            'parent_item' => null,
            'parent_item_colon' => null,
            'edit_item' => __( 'Edit Sector' ),
            'update_item' => __( 'Update Sector' ),
            'add_new_item' => __( 'Add New Sector' ),
            'new_item_name' => __( 'New Sector Name' ),
            'separate_items_with_commas' => __( 'Separate Sectors with commas' ),
            'add_or_remove_items' => __( 'Add or remove Sectors' ),
            'choose_from_most_used' => __( 'Choose from the most used Sectors' ),
            'menu_name' => __( 'Sectors' ),
        );

        register_taxonomy('sector',['resources', 'order'],array(
            'hierarchical' => false,
            'labels' => $labels,
            'show_ui' => true,
            'update_count_callback' => '_update_post_term_count',
            'query_var' => true,
            'rewrite' => array( 'slug' => 'sector' ),
        ));
    }
}