<?php


namespace MAM\Plugin\Services\Admin;


use Exception;
use MAM\Plugin\Services\WPAPI\Endpoint;
use MAM\Plugin\Services\ServiceInterface;

class Import implements ServiceInterface
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
        add_action( 'plugins_loaded', [$this, 'add_option_page']);
        add_action( 'plugins_loaded', [$this, 'add_custom_fields']);

        try {
            $this->endpoint_api->add_endpoint('mam-import')->with_template('mam-import.php')->register_endpoints();
        } catch (Exception $e) {
            $this->errors[] = $e->getMessage();
        }
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

            acf_add_local_field_group(array(
                'key' => 'group_5fa3aeb87e713',
                'title' => 'Import',
                'fields' => array(
                    array(
                        'key' => 'field_5fa3aec155824',
                        'label' => 'Upload File',
                        'name' => 'upload_file',
                        'type' => 'file',
                        'instructions' => 'csv only please follow this format <br />
<a href="https://docs.google.com/spreadsheets/d/1HbInJvGLnuhioqKw2EDugzUq76UlFUHevVftqG8V7rA/edit?usp=sharing" target="_blank">Example Resource Data</a> <br />
<a href="https://docs.google.com/spreadsheets/d/1qbkVIroox5z2scDVuE0VCryacV3YGdoSC10qvhGoFAQ/edit?usp=sharing" target="_blank">Example Order Data</a>',
                        'required' => 0,
                        'conditional_logic' => 0,
                        'wrapper' => array(
                            'width' => '',
                            'class' => '',
                            'id' => '',
                        ),
                        'return_format' => 'url',
                        'library' => 'all',
                        'min_size' => '',
                        'max_size' => '',
                        'mime_types' => 'csv',
                    ),
                    array(
                        'key' => 'field_5fa3aeee55825',
                        'label' => 'Action',
                        'name' => 'action',
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
                            'Check only' => 'Check only',
                            'Import the file' => 'Import the file',
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
                        'key' => 'field_5fa3af0b55826',
                        'label' => '',
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
                        'message' => '<p>Before you click "Run"<br />
1- Upload the file. <br />
2- Choose the action. <br />
3- Click on "Update" to save the above settings.</p>
<p><a href="'.site_url().'/mam-import/" target="_blank" class="button button-primary button-large">Run</a></p>',
                        'new_lines' => 'wpautop',
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