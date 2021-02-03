<?php


namespace MAM\Plugin\Services\Admin;


use Exception;
use MAM\Plugin\Services\WPAPI\Endpoint;
use MAM\Plugin\Services\ServiceInterface;

class ImportResources implements ServiceInterface
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
            $this->endpoint_api->add_endpoint('mam-import-resources')->with_template('mam-import-resources.php')->register_endpoints();
        } catch (Exception $e) {
            $this->errors[] = $e->getMessage();
        }
    }

    public static function add_custom_fields()
    {
        if (function_exists('acf_add_local_field_group')) {
            acf_add_local_field_group(array(
                'key' => 'group_5ffe6c4a79b24',
                'title' => 'Import Resources',
                'fields' => array(
                    array(
                        'key' => 'field_5ffe6c4a7d2cd',
                        'label' => 'Upload File',
                        'name' => 'upload_file_resources',
                        'type' => 'file',
                        'instructions' => 'csv only please follow this format <br />
<a href="https://docs.google.com/spreadsheets/d/1HbInJvGLnuhioqKw2EDugzUq76UlFUHevVftqG8V7rA/edit?usp=sharing" target="_blank">Example Resource Data</a>',
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
                        'key' => 'field_5ffe6c4a7d313',
                        'label' => 'Action',
                        'name' => 'action_resources',
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
                        'key' => 'field_5ffe6c4a7d357',
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
<p><a href="'. site_url() . '/mam-import-resources/" target="_blank" class="button button-primary button-large">Run Resource Import</a></p>',
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