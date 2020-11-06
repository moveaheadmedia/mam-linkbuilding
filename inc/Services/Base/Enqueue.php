<?php


namespace MAM\Plugin\Services\Base;


use MAM\Plugin\Config;
use MAM\Plugin\Services\ServiceInterface;

class Enqueue implements ServiceInterface
{

    /**
     * @var string plugin base url
     */
    private $plugin_url;

    /**
     * @inheritDoc
     */
    public function register()
    {
        // set the baseurl
        $this->plugin_url = Config::getInstance()->plugin_url;

        // add action
        add_action('wp_enqueue_scripts', [$this, 'register_css']);
        add_action('wp_enqueue_scripts', [$this, 'register_js']);
    }

    /**
     * Registers the Plugin stylesheet.
     *
     * @wp-hook admin_enqueue_scripts
     */
    public function register_css()
    {
        wp_register_style('bootstrap', 'https://cdnjs.cloudflare.com/ajax/libs/twitter-bootstrap/4.5.0/css/bootstrap.min.css');
        wp_enqueue_style('bootstrap');

        wp_register_style('bootstrap-select', 'https://cdn.jsdelivr.net/npm/bootstrap-select@1.13.14/dist/css/bootstrap-select.min.css');
        wp_enqueue_style('bootstrap-select');

        wp_register_style('fancybox', 'https://cdnjs.cloudflare.com/ajax/libs/fancybox/3.5.7/jquery.fancybox.min.css');
        wp_enqueue_style('fancybox');

        wp_register_style('datatables', 'https://cdn.datatables.net/1.10.22/css/jquery.dataTables.min.css');
        wp_enqueue_style('datatables');

        wp_register_style('bootstrap-slider', 'https://cdnjs.cloudflare.com/ajax/libs/bootstrap-slider/11.0.2/css/bootstrap-slider.min.css');
        wp_enqueue_style('bootstrap-slider');

        wp_register_style('mam-lb-plugin', $this->plugin_url . 'assets/css/mam-lb-plugin.css');
        wp_enqueue_style('mam-lb-plugin');
    }


    /**
     * Registers the Plugin javascript.
     *
     * @wp-hook admin_enqueue_scripts
     */
    public function register_js()
    {
        wp_deregister_script('jquery');
        wp_register_script('jquery', 'https://cdnjs.cloudflare.com/ajax/libs/jquery/2.2.4/jquery.min.js', false, '2.2.4');
        wp_enqueue_script('jquery');

        wp_register_script('popper', 'https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.11.0/umd/popper.min.js', false);
        wp_enqueue_script('popper');

        wp_register_script('bootstrap', 'https://cdnjs.cloudflare.com/ajax/libs/twitter-bootstrap/4.5.2/js/bootstrap.min.js', array('jquery', 'popper'));
        wp_enqueue_script('bootstrap');

        wp_register_script('bootstrap-select', 'https://cdn.jsdelivr.net/npm/bootstrap-select@1.13.14/dist/js/bootstrap-select.min.js', array('jquery', 'bootstrap', 'popper'));
        wp_enqueue_script('bootstrap-select');

        wp_register_script('fancybox', 'https://cdnjs.cloudflare.com/ajax/libs/fancybox/3.5.7/jquery.fancybox.min.js', array('jquery'));
        wp_enqueue_script('fancybox');

        wp_register_script('datatables', 'https://cdn.datatables.net/1.10.22/js/jquery.dataTables.min.js', array('jquery'));
        wp_enqueue_script('datatables');

        wp_register_script('bootstrap-slider', 'https://cdnjs.cloudflare.com/ajax/libs/bootstrap-slider/11.0.2/bootstrap-slider.min.js', array('jquery', 'bootstrap', 'popper'));
        wp_enqueue_script('bootstrap-slider');

        wp_register_script('mam-lb-plugin', $this->plugin_url . 'assets/js/mam-lb-plugin.js', array('jquery'));
        wp_enqueue_script('mam-lb-plugin');
    }

}