<?php

use MAM\Plugin\Services\Admin\Resources;

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// the finale array that will be converted to json
$json = array();

// Needed in response
$json['draw'] = $_POST['draw'];


// init user columns
$columns_list = array("Website", "Email","IP Address", "Name", "DA", "DR", "RD", "TR", "PA", "TF", "CF", "Organic Keywords", "Country", "Currency",
    "Original Price", "Casino Price", "CBD Price", "Adult Price", "Link Placement Price", "Package / Discount", "Finale Price",
    "Payment Method", "Notes", "Secondary Email", "Origin File", "Rating", "Status", "Metrics Update Date", "Sectors", "Niche", "New Remarks", "Social Media", "Other Info", "Contact / Email");
$resource_columns_raw = get_field('resources_columns', 'user_' . get_current_user_id());
if ($resource_columns_raw) {
    $resource_columns = json_decode($resource_columns_raw, true);
} else {
    $resource_columns = $columns_list;
}


// init filters
$filters = mam_get_filters_from_url($_SERVER['HTTP_REFERER']);
$filters = mam_get_filters_from_table($filters, $resource_columns);
$filters['start'] = $_POST['start'];
$filters['length'] = $_POST['length'];
$filters['order'] = $_POST['order'][0];

$column_order = 1;
if(isset($resource_columns[($filters['order']['column'] - 1)])){
    $column_order = $resource_columns[($filters['order']['column'] - 1)];
}
$filters['order']['field'] = Resources::get_filed_name_by_column_name($column_order);

/**
 * Get the filters from the current URL
 * @param $url string the current URL
 * @return mixed the url parameters
 */
function mam_get_filters_from_url($url){
    $parts = parse_url($url);
    if(isset($parts['query'])){
        parse_str($parts['query'], $query);
        return $query;
    }
    return array();
}

/**
 * Get the filters from the column search
 * @param $filters array filters array
 * @param $resource_columns array columns list array
 * @return array filters with columns
 */
function mam_get_filters_from_table($filters, $resource_columns){
    $_filters = $filters;
    $columns = $_POST['columns'];
    foreach ($columns as $column){
        if($column['search']['value'] != ''){
            $column_order = 1;
            if(isset($resource_columns[$column['data'] - 1])){
                $column_order = $resource_columns[$column['data'] - 1];
            }
            $item = array();
            $item['name'] = Resources::get_filed_name_by_column_name($column_order);
            $item['value'] = $column['search']['value'];
            $_filters['search'][] = $item;
        }
    }
    return $_filters;
}

// init $json['recordsTotal']
$args = array(
    'offset'          => 0,
    'posts_per_page' => -1,
    'post_type' => 'resources',
    'fields' => 'ids',
    'no_found_rows' => true,
);
$the_query_notFiltered = new WP_Query($args);
wp_reset_query();
// Needed in response
$json['recordsTotal'] = $the_query_notFiltered->post_count;


// Needed in response
$the_query_ids = apply_filters('mam-resources-filtered-posts-ids', $filters);
$json['recordsFiltered'] = $the_query_ids->post_count;


// init $json['data']
/**
 * @var WP_Query $the_query
 */
$the_query = apply_filters('mam-resources-filtered-posts', $filters);
$data = array();
if( $the_query->have_posts() ){
    while ( $the_query->have_posts() ) {
        $the_query->the_post();
        $id = get_the_ID();
        if(isset($filters['website'])){
            if(Resources::check_used_resource(get_the_title($id), $filters['website'])){
                continue;
            }
        }

        $itemData = array(
            'Website' => '<a data-type="iframe" href="'. get_the_permalink($id).'" target="_blank" data-fancybox>'. get_the_title($id).'</a>',
            'IP Address' => get_field('ip_address', $id),
            'Email' => get_field('email', $id),
            'Name' => get_field('contact_name', $id),
            'DA' => get_field('da', $id),
            'DR' => get_field('dr', $id),
            'RD' => get_field('rd', $id),
            'TR' => get_field('tr', $id),
            'PA' => get_field('pa', $id),
            'TF' => get_field('tf', $id),
            'CF' => get_field('cf', $id),
            'Organic Keywords' => get_field('organic_keywords', $id),
            'Currency' => get_field('currency', $id),
            'Country' => get_field('country', $id),
            'Original Price' => get_field('original_price', $id),
            'Casino Price' => get_field('casino_price', $id),
            'CBD Price' => get_field('cbd_price', $id),
            'Adult Price' => get_field('adult_price', $id),
            'Link Placement Price' => get_field('link_placement_price', $id),
            'Package / Discount' => get_field('package__discount', $id),
            'Finale Price' => get_field('price', $id),
            'Payment Method' => get_field('payment_method', $id),
            'Notes' => get_field('notes', $id),
            'Secondary Email' => get_field('secondary_email', $id),
            'Origin File' => get_field('origin_file', $id),
            'Rating' => get_field('rating', $id),
            'Status' => get_field('status', $id),
            'Metrics Update Date' => get_field('metrics_update_date', $id),
            'Sectors' => implode(', ', wp_get_object_terms($id, 'sector', array('fields' => 'names'))),
            'Niche' => get_field('niche', $id),
            'New Remarks' => get_field('new_remarks', $id),
            'Social Media' => get_field('social_media', $id),
            'Other Info' => get_field('other_info', $id),
            'Contact / Email' => get_field('contact__email', $id),
        );

        if (Resources::resource_ip_duplicated($itemData['IP Address'])) {
            $itemData['IP Address'] = '<abbr class="text text-danger" title="Duplicated IP Address">'.$itemData['IP Address'].'</abbr>';
        }

        $item = array();
        $item[] = '';
        foreach ($resource_columns as $column) {
            $item[] = $itemData[$column];
        }
        $data[] = $item;
    }
}
$json['data'] = $data;

header('Content-Type: application/json');
echo json_encode($json);
