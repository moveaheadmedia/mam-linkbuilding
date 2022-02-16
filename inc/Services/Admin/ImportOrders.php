<?php

namespace MAM\Plugin\Services\Admin;


use Exception;
use MAM\Plugin\Services\WPAPI\Endpoint;
use MAM\Plugin\Services\ServiceInterface;
use ParseCsv\Csv;

class ImportOrders implements ServiceInterface
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
        add_action('plugins_loaded', [$this, 'add_custom_fields']);
        add_action('wp_ajax_import_existing_order', [$this, 'import_existing']);
        add_action('wp_ajax_import_new_order', [$this, 'import_new']);

        try {
            $this->endpoint_api->add_endpoint('mam-import-orders')->with_template('mam-import-orders.php')->register_endpoints();
        } catch (Exception $e) {
            $this->errors[] = $e->getMessage();
        }
    }

    public static function import_existing()
    {

        if (!isset($_POST['title']) || $_POST['title'] == '0') {
            echo 'Failed, Order Not Found';
            die();
        }

        $postID = $_POST['orderPostID'];
        ImportOrders::update_order($postID, $_POST);
        echo 'Imported, Successfully';
        die();
    }

    public static function update_client($clientID, $agencyID, $orderData)
    {
        if (isset($agencyID)) {
            update_field('agency', $agencyID, $clientID);
        }
        if (isset($orderData['client website'])) {
            update_field('website', $orderData['client name'], $clientID);
        }
    }

    public static function update_order($id, $data)
    {
        $client = array();
        $client['client name'] = 'https://' . (string)ImportOrders::domain($data['target_url']);
        $client['client website'] = $client['client name'];

        $clientID = post_exists($client['client name'], '', '', 'client');
        if (!$clientID) {
            $clientID = wp_insert_post(array(
                'post_title' => $client['client name'],
                'post_type' => 'client',
                'post_status' => 'publish',
            ));
        }

        if (isset($data['agency'])) {
            $agencyID = post_exists($data['agency'], '', '', 'agency');
            if (!$agencyID) {
                $agencyID = wp_insert_post(array(
                    'post_title' => $data['agency'],
                    'post_type' => 'agency',
                    'post_status' => 'publish',
                ));
            }
            ImportOrders::update_client($clientID, $agencyID, $client);
        }

        foreach ($data as $key => $value) {
            if ($key == 'title') {
                $my_post = array(
                    'ID' => $id,
                    'post_title' => $value
                );
                wp_update_post($my_post);
            } else {
                update_field($key, $value, $id);
            }
        }

        if(!isset($data['resource_url']) || $data['resource_url'] == ''){
            if(isset($data['live_link']) && $data['live_link'] != ''){
                $resourceURL =  ImportOrders::domain($data['live_link']);
                update_field('resource_url', $resourceURL, $id);
            }else{
                echo 'Warning: No resource URL';
            }
        }

        update_field('client', $clientID, $id);
        update_field('metrics_update_date', '', $id);
        do_action('update_metrics_order', $id);
    }

    public static function import_new()
    {

        if (!isset($_POST['title']) || $_POST['title'] == '') {
            echo 'Failed, Order ID Not Found';
            die();
        }

        $orderID = wp_insert_post(array(
            'post_title' => $_POST['title'],
            'post_type' => 'lborder',
            'post_status' => 'publish',
        ));
        ImportOrders::update_order($orderID, $_POST);

        echo 'Imported, Successfully';
        die();
    }

    public static function add_custom_fields()
    {
        if (function_exists('acf_add_local_field_group')) {
            acf_add_local_field_group(array(
                'key' => 'group_5ffe6ccaccdee',
                'title' => 'Import Orders',
                'fields' => array(
                    array(
                        'key' => 'field_5ffe6ccad0a3d',
                        'label' => 'Upload File',
                        'name' => 'upload_file_orders',
                        'type' => 'file',
                        'instructions' => 'csv only please follow this format <br />
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
                        'key' => 'field_5ffe6ccad0a85',
                        'label' => 'Action',
                        'name' => 'action_orders',
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
                        'key' => 'field_5ffe6ccad0aca',
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
<p><a href="https://mamdevsite.com/mam-lb/mam-import-orders/" target="_blank" class="button button-primary button-large">Run Orders Import</a></p>',
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

    public static function domain($url)
    {
        $url = strtolower($url);
        $host = parse_url($url, PHP_URL_HOST);
        return str_replace('www.', '', $host);
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
        $csv = new Csv($newfile);
        return $csv;
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
            foreach ($_row as $key => $value) {
                if (strpos(strtolower($key), 'sector') !== false) {
                    if ($value == '') {
                        continue;
                    }
                    $sectors[] = $value;
                } else {
                    $row[strtolower($key)] = $value;
                }
            }
            if(!isset($row['id']) || !$row['id']){
                continue;
            }
	        if(strpos($row[strtolower('Target URL')], 'http') === false){
		        $row[strtolower('Target URL')] = 'https://' . $row[strtolower('Target URL')];
	        }
            if ((!isset($row[strtolower('Client Website')]) || $row[strtolower('Client Website')] == '') && (isset($row[strtolower('Target URL')]) && $row[strtolower('Target URL')] != '')) {
                $row[strtolower('Client Website')] = 'https://' . ImportOrders::domain($row[strtolower('Target URL')]);
            }
            $row[strtolower('Client Name')] = ImportOrders::domain($row[strtolower('Client Website')]);
            if (!$row[strtolower('Client Name')]) {
                continue;
            }
            if ((!isset($row[strtolower('Resource URL')]) || $row[strtolower('Resource URL')] == '') && (isset($row[strtolower('Live Link')]) && $row[strtolower('Live Link')] != '')) {
                $row[strtolower('Resource URL')] = ImportOrders::domain($row[strtolower('Live Link')]);
            }
            $result[] = $row;
        }

        return $result;
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
            if (strtolower($value) == '' || strtolower($value) == 'website' || strtolower($value) == 'status' || strtolower($value) == 'ip address' || strtolower($value) == 'da' || strtolower($value) == 'dr' || strtolower($value) == 'rd' || strtolower($value) == 'tr') {
                continue;
            }
            if (Orders::get_field_name_by_column_name(strtolower($value)) != '') {
                $result[$key] = strtolower($value);
            }
        }
        if(!in_array('resource url', $result)){
            $result[] = 'resource url';
        }
        return $result;
    }

    /**
     *
     * @param $titles array of titles
     * @return array the results array
     */
    public static function init_import_data_titles_resource($titles)
    {
        $result = array();
        foreach ($titles as $key => $value) {
            if (Resources::get_field_name_by_column_name(strtolower($value)) != '') {
                if (strtolower($value) == '' || strtolower($value) == 'status' || strtolower($value) == 'ip address' || strtolower($value) == 'da' || strtolower($value) == 'dr' || strtolower($value) == 'rd' || strtolower($value) == 'tr') {
                    continue;
                }
                if (strpos(strtolower($value), 'sector') !== false) {
                    $result['sectors'] = strtolower('sectors');
                } else {
                    $result[$key] = strtolower($value);
                }
            }
        }
        if (!in_array('email', $result)) {
            $result[] = 'email';
        }
        if (!in_array('finale price', $result)) {
            $result[] = 'finale price';
        }
        if (!in_array('sectors', $result)) {
            $result['sectors'] = 'sectors';
        }
        array_unshift($result, 'website');
        return $result;
    }

    /**
     *
     * @param $titles array of titles
     * @return array the list of warnings
     */
    public static function init_titles_warnings($titles)
    {
        $errors = array();
        foreach ($titles as $key => $value) {
            if (Orders::get_field_name_by_column_name(strtolower($value)) == '') {
                if(strpos(strtolower($value), 'sector') !== false){
                    continue;
                }
                $errors[] = 'Column <b>' . $value . '</b> does not exist,  <small>for more information please check this example file: <a href="https://docs.google.com/spreadsheets/d/1qbkVIroox5z2scDVuE0VCryacV3YGdoSC10qvhGoFAQ/edit?usp=sharing" target="_blank">Example Order Data</a></small>';
            }
        }
        return $errors;
    }
}