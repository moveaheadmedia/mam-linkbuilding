<?php

use MAM\Plugin\Services\Admin\Orders;
use MAM\Plugin\Services\Admin\Clients;

get_header(); ?>
<?php
global $_id;
$_id = '';
if (isset($_GET['id'])) {
    $_id = $_GET['id'];
}


$r_id = '';
if (isset($_GET['r_id'])) {

    $r_id = $_GET['r_id'];

    $finalePrice = '-';
    $ogPrice = get_field('original_price', $r_id);
    if ($ogPrice) {
        $finalePrice = $ogPrice;
    }
    $price = get_field('price', $r_id);
    if ($price) {
        $finalePrice = $price;
    }

    $client = get_field('client', $_id);
    $agency = get_the_title(get_field('agency', $client));

    $check = true;
    if(Clients::check_client_resource($client, get_the_title($r_id))){
        $check = false;
    }
    if($check){
        $_orderData = array(
            "Resource URL" => get_the_title($r_id),
            "IP Address" => get_field('ip_address', $r_id),
            "Email" => get_field('email', $r_id),
            "Name" => get_field('contact_name', $r_id),
            "DA" => get_field('da', $r_id),
            "DR" => get_field('dr', $r_id),
            "RD" => get_field('rd', $r_id),
            "TR" => get_field('tr', $r_id),
            "PA" => get_field('pa', $r_id),
            "TF" => get_field('tf', $r_id),
            "CF" => get_field('cf', $r_id),
            "Organic Keywords" => get_field('organic_keywords', $r_id),
            "Country" => get_field('country', $r_id),
            "Currency" => get_field('currency', $r_id),
            "Original Price" => get_field('original_price', $r_id),
            "Casino Price" => get_field('casino_price', $r_id),
            "CBD Price" => get_field('cbd_price', $r_id),
            "Adult Price" => get_field('adult_price', $r_id),
            "Link Placement Price" => get_field('link_placement_price', $r_id),
            "Package / Discount" => get_field('package__discount', $r_id),
            "Finale Price" => get_field('price', $r_id),
            "Payment Method" => get_field('payment_method', $r_id),
            "Notes" => get_field('notes', $r_id),
            "Secondary Email" => get_field('secondary_email', $r_id),
            "Origin File" => get_field('origin_file', $r_id),
            "Rating" => get_field('rating', $r_id),
            "Metrics Update Date" => get_field('metrics_update_date', $r_id)
        );
        Orders::update_order($_id, $client, $_orderData);
    }else{
        echo '<div class="container"><div class="alert alert-danger" role="alert">You can not use this resource for this client, resource has been already used.</div></div>';
    }
}

$client = get_field('client', $_id);
$agency = get_the_title(get_field('agency', $client));

$orderData = array(
    "Client Name" => '<a data-type="iframe" href="' . get_the_permalink($client) . '" target="_blank" data-fancybox>' . get_the_title($client) . '</a>',
    "Client Website" => get_field('website', $client),
    "Agency" => $agency,
    "Anchor Text" => get_field('anchor_text', $_id),
    "Anchor Text Type" => get_field('anchor_text_type', $_id),
    "Target URL" => get_field('target_url', $_id),
    "Niche" => get_field('niche', $_id),
    "Sent To Writers" => get_field('sent_to_writers', $_id),
    "Article sent to the site" => get_field('articles_sent_to_the_sites', $_id),
    "Live Link Received" => get_field('live_link_received', $_id),
    "Live Link" => get_field('live_link', $_id),
    "Date Paid" => get_field('date_paid', $_id),
    "USD Price" => get_field('usd_price', $_id),
    "THB Price" => get_field('thb_price', $_id),
    "Checked" => get_field('checked', $_id),
    "Status" => get_field('status', $_id),
    "Start Date" => get_field('start_date', $_id),
    "Complete Date" => get_field('complete_date', $_id),
    "Sectors" => implode(', ', wp_get_object_terms($_id, 'sector', array('fields' => 'names'))),
    "Resource URL" => get_field('resource_url', $_id),
    "IP Address" => get_field('ip_address', $_id),
    "Email" => get_field('email', $_id),
    "Name" => get_field('contact_name', $_id),
    "DA" => get_field('da', $_id),
    "DR" => get_field('dr', $_id),
    "RD" => get_field('rd', $_id),
    "TR" => get_field('tr', $_id),
    "PA" => get_field('pa', $_id),
    "TF" => get_field('tf', $_id),
    "CF" => get_field('cf', $_id),
    "Organic Keywords" => get_field('organic_keywords', $_id),
    "Country" => get_field('country', $_id),
    "Currency" => get_field('currency', $_id),
    "Original Price" => get_field('original_price', $_id),
    "Casino Price" => get_field('casino_price', $_id),
    "CBD Price" => get_field('cbd_price', $_id),
    "Adult Price" => get_field('adult_price', $_id),
    "Link Placement Price" => get_field('link_placement_price', $_id),
    "Package / Discount" => get_field('package__discount', $_id),
    "Finale Price" => get_field('price', $_id),
    "Payment Method" => get_field('payment_method', $_id),
    "Notes" => get_field('notes', $_id),
    "Secondary Email" => get_field('secondary_email', $_id),
    "Origin File" => get_field('origin_file', $_id),
    "Rating" => get_field('rating', $_id),
    "Metrics Update Date" => get_field('metrics_update_date', $_id)
);

$resource = get_field('resource_url', $_id);
?>
<div class="container">
    <br/>
    <h2>Order Details</h2>
    <ul>
        <?php
        foreach ($orderData as $key => $value) {
            if ($key == 'Resource URL' && $value == '') {
                break;
            }
            ?>
            <li><b><?php echo $key; ?>:</b> <?php echo $value; ?></li>
        <?php } ?>
    </ul>
</div>

<?php include 'resource-table.php'; ?>

<?php get_footer(); ?>
