<?php


namespace MAM\Plugin\Services\Admin;


use MAM\Plugin\Services\ServiceInterface;

class Import implements ServiceInterface
{

    /**
     * @inheritDoc
     */
    public function register()
    {
        add_action( 'plugins_loaded', [$this, 'add_option_page']);
        add_action( 'plugins_loaded', [$this, 'add_custom_fields']);
    }

    public static function add_option_page() {
        // Register the option page using ACF
        if ( function_exists( 'acf_add_options_page' ) ) {
            // parent page
            acf_add_options_page(array(
                'page_title' 	=> 'Import / Export',
                'menu_title'	=> 'Import / Export',
                'menu_slug' 	=> 'mam',
                'capability'	=> 'read',
                'redirect'		=> true
            ));

            // child page
            acf_add_options_sub_page(array(
                'page_title' 	=> 'Import / Export',
                'menu_title'	=> 'Import / Export',
                'menu_slug'  => 'import-export',
                'capability'	=> 'read',
                'parent_slug'	=> 'mam'
            ));

        }
    }

    public static function add_custom_fields()
    {
        if (function_exists('acf_add_local_field_group')) {

        }
    }
}