<?php


namespace MAM\Plugin\Services\Admin;


use Exception;
use MAM\Plugin\Services\WPAPI\Endpoint;
use MAM\Plugin\Services\ServiceInterface;

class Export implements ServiceInterface
{

    /**
     * @var Endpoint
     */
    private $endpoint_api;

    /**
     * @var array
     */
    private $errors;

    public function __construct()
    {
        $this->endpoint_api = new Endpoint();
    }

    /**
     * @inheritDoc
     */
    public function register()
    {
        add_action( 'plugins_loaded', [$this, 'add_custom_fields']);
        try {
            $this->endpoint_api->add_endpoint('mam-export')->with_template('mam-export.php')->register_endpoints();
        } catch (Exception $e) {
            $this->errors[] = $e->getMessage();
        }
    }


    public static function add_custom_fields()
    {
        if (function_exists('acf_add_local_field_group')) {
            acf_add_local_field_group(array(
                'key' => 'group_5fa3a1ef98721',
                'title' => 'Export',
                'fields' => array(
                    array(
                        'key' => 'field_5fa3a200d681c',
                        'label' => 'Export',
                        'name' => '',
                        'type' => 'message',
                        'instructions' => '',
                        'required' => 0,
                        'conditional_logic' => 0,
                        'wrapper' => array(
                            'width' => '',
                            'class' => '',
                            'id' => '',
                        ),
                        'message' => '<p><a href="'.site_url().'/mam-export/?export=resources" target="_blank" class="button button-primary button-large">Export Resources</a> <a href="'.site_url().'/mam-export/?export=orders" target="_blank" class="button button-primary button-large">Export Orders</a></p>',
                        'new_lines' => '',
                        'esc_html' => 0,
                    ),
                ),
                'location' => array(
                    array(
                        array(
                            'param' => 'options_page',
                            'operator' => '==',
                            'value' => 'import-export',
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