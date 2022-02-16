<?php


namespace MAM\Plugin\Services\Admin;


use Exception;
use MAM\Plugin\Services\WPAPI\Endpoint;
use MAM\Plugin\Services\ServiceInterface;
use ParseCsv\Csv;

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
        add_action('wp_ajax_import_existing_resource', [$this, 'import_existing']);
        add_action('wp_ajax_import_new_resource', [$this, 'import_new']);

        try {
            $this->endpoint_api->add_endpoint('mam-import-resources')->with_template('mam-import-resources.php')->register_endpoints();
        } catch (Exception $e) {
            $this->errors[] = $e->getMessage();
        }
    }

    /**
     * @param $mam_file string the file url
     * @return string|Csv string when error and Csv object on success
     */
    public static function init_import_csv($mam_file)
    {
        $newfile = 'import.csv';
        if (!copy($mam_file, $newfile)) {
            return "Failed to copy $mam_file...\n";
        }
        return new Csv($newfile);
    }

    /**
     *
     * @param $titles array of titles
     * @return array the results array
     */
    public static function init_import_data_titles($titles)
    {
        $result = array();
        foreach ($titles as $key => $value) {
            if(Resources::get_field_name_by_column_name(strtolower($value)) != ''){
                if(strtolower($value) == '' || strtolower($value) == 'status' || strtolower($value) == 'ip address' || strtolower($value) == 'da' || strtolower($value) == 'dr' || strtolower($value) == 'rd' || strtolower($value) == 'tr'){
                    continue;
                }
                if(strpos(strtolower($value), 'sector') !== false){
                    $result['sectors'] = strtolower('sectors');
                }elseif (strtolower($value) == 'url') {
                    $result[$key] = 'website';
                }else{
                    $result[$key] = strtolower($value);
                }
            }
        }
        return $result;
    }

    /**
     *
     * @param $titles array of titles
     * @return array the list of warnings
     */
    public static function init_titles_warnings($titles)
    {
        $_errors = array();
        foreach ($titles as $key=>$value) {
            if($value == ''){
                continue;
            }
            if(Resources::get_field_name_by_column_name(strtolower($value)) == ''){
                $_errors[] = 'Column <b>' . $value . '</b> does not exist,  <small>for more information please check this example file: 
<a href="https://docs.google.com/spreadsheets/d/1HbInJvGLnuhioqKw2EDugzUq76UlFUHevVftqG8V7rA/edit#gid=969667427" target="_blank">Example Resource Data</a></small>';
            }
        }
        return $_errors;
    }

    /**
     *
     * @param $data_finale array row content
     * @return array the results array
     */
    public static function init_import_data($data_finale)
    {
        $result = array();
        foreach ($data_finale as $_row) {
            $row = array();
            $sectors = array();
            foreach ($_row as $key => $value) {
                if(strpos(strtolower($key), 'sector') !== false){
                    if($value == ''){
                        continue;
                    }
                    $sectors[] = $value;
                }elseif(strtolower($key) == 'url' || strtolower($key) == 'website') {
                    $row['website'] = $value;
                }else{
                    $row[strtolower($key)] = $value;
                }
            }
            $row['sectors'] = implode(', ', $sectors);
            if($row['website'] != ''){
                $result[] = $row;
            }
        }

        return $result;
    }


    public static function update_resource($id, $data)
    {
        foreach ($data as $key => $value){
            if ($key == 'sectors') {
                if(!is_array($value)){
                    $sectors = explode(', ', $value);
                }else{
                    $sectors = $value;
                }
                wp_set_post_terms($id, $sectors, 'sector');
            } elseif ($key == 'title') {
                $my_post = array(
                    'ID'           => $id,
                    'post_title'   => $value
                );
                wp_update_post( $my_post );
            }else{
                update_field($key, $value, $id);
            }
        }
        update_field('metrics_update_date', '', $id);
        do_action('update_metrics', $id);
    }

    public static function import_existing(){

        if(!isset($_POST['resourcePostID']) || $_POST['resourcePostID'] == '0'){
            echo 'Failed, Resource Not Found';
            die();
        }

        $postID = $_POST['resourcePostID'];
        ImportResources::update_resource($postID, $_POST);
        echo 'Imported, Successfully';
        die();
    }

    public static function import_new(){

        if(!isset($_POST['title']) || $_POST['title'] == ''){
            echo 'Failed, Resource Title Not Found';
            die();
        }

        $postID = wp_insert_post(array(
            'post_title' => $_POST['title'],
            'post_type' => 'resources',
            'post_status' => 'publish',
        ));
        ImportResources::update_resource($postID, $_POST);
        echo 'Imported, Successfully';
        die();
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
                            'Check only' => 'Check only'
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