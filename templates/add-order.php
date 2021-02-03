<?php

use MAM\Plugin\Config;
use MAM\Plugin\Services\Admin\Orders;

get_header(); ?>
<?php
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
        foreach ($orderData as $key=>$value){
            if($key == 'Resource URL' && $value == ''){
                break;
            }
            ?>
            <li><b><?php echo $key; ?>:</b> <?php echo $value; ?></li>
        <?php } ?>
    </ul>
</div>


<div class="container">
    <br/>
    <button class="btn btn-default" type="submit" data-toggle="collapse" href="#filters" role="button" aria-expanded="false" aria-controls="filters">Filters</button>
    <div class="filters collapse" id="filters">
        <?php
        $filters = array();
        $filters['client'] = $client;
        $filters['da'] = 0;
        $filters['da1'] = 100;
        $filters['dr'] = 0;
        $filters['dr1'] = 100;
        $filters['rd'] = 0;
        $filters['tr'] = 0;
        $filters['price'] = 0;
        $filters['price1'] = 3000;
        $filters['the_client'] = '';
        $filters['anchoer_text'] = '';
        $filters['target_url'] = '';
        $filters['sectors'] = array();
        if (isset($_GET['da'])) {
            $dArray = explode(',', $_GET['da']);
            $filters['da'] = $dArray[0];
            $filters['da1'] = $dArray[1];
        }
        if (isset($_GET['dr'])) {
            $dArray = explode(',', $_GET['dr']);
            $filters['dr'] = $dArray[0];
            $filters['dr1'] = $dArray[1];
        }
        if (isset($_GET['rd'])) {
            $filters['rd'] = $_GET['rd'];
        }
        if (isset($_GET['tr'])) {
            $filters['tr'] = $_GET['tr'];
        }
        if (isset($_GET['price'])) {
            $dArray = explode(',', $_GET['price']);
            $filters['price'] = $dArray[0];
            $filters['price1'] = $dArray[1];
        }
        if (isset($_GET['the_client'])) {
            $filters['the_client'] = $_GET['the_client'];
        }
        if (isset($_GET['anchoer_text'])) {
            $filters['anchoer_text'] = $_GET['anchoer_text'];
        }
        if (isset($_GET['target_url'])) {
            $filters['target_url'] = $_GET['target_url'];
        }
        if (isset($_GET['sectors'])) {
            $filters['sectors'] = $_GET['sectors'];
        }
        ?>
        <form method="get" action="">
            <input type="hidden" name="id" value="<?php echo $_id; ?>"/>
            <div class="row">

                <div class="col-md-12">
                    <div class="form-group">
                        <?php $sectors = get_terms('sector'); ?>
                        <label for="sectors">Sectors</label>
                        <select name="sectors[]" title="Select sectors" data-live-search="true" multiple="multiple"
                                class="form-control selectpicker" id="sectors">
                            <?php foreach ($sectors as $sector) { ?>
                                <option value="<?php echo $sector->term_id; ?>" <?php if (in_array($sector->term_id, $filters['sectors'])) { ?> selected <?php } ?>><?php echo $sector->name; ?></option>
                            <?php } ?>
                        </select>
                    </div>
                </div>

                <div class="col-md-6">
                    <div class="form-group">
                        <label for="da">DA</label><br/>
                        <b>0</b> <input type="text" id="da" name="da" class="slider" value="" data-slider-min="0"
                                        data-slider-max="100" data-slider-step="1" data-slider-value="[<?php echo $filters['da']; ?>,<?php echo $filters['da1']; ?>]"/>
                        <b>100</b>
                    </div>
                </div>

                <div class="col-md-6">
                    <div class="form-group">
                        <label for="dr">DR</label><br/>
                        <b>0</b> <input id="dr" type="text" name="dr" class="slider" value="" data-slider-min="0"
                                        data-slider-max="100" data-slider-step="1" data-slider-value="[<?php echo $filters['dr']; ?>,<?php echo $filters['dr1']; ?>]"/>
                        <b>100</b>
                    </div>
                </div>

                <div class="col-md-12">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="price">Price</label><br/>
                                <b>0</b> <input id="price" type="text" name="price" class="slider" value=""
                                                data-slider-min="0"
                                                data-slider-max="3000" data-slider-step="1"
                                                data-slider-value="[<?php echo $filters['price']; ?>,<?php echo $filters['price1']; ?>]"/>
                                <b>3000</b>
                            </div>
                        </div>
                        <div class="col-md-6"></div>
                    </div>
                </div>

                <div class="col-md-6">
                    <div class="form-group">
                        <label for="rd">Minimum RD</label><br/>
                        <input type="number" class="form-control" id="rd" name="rd" value="<?php echo $filters['rd']; ?>" placeholder="0">
                    </div>
                </div>

                <div class="col-md-6">
                    <div class="form-group">
                        <label for="tr">Minimum TR</label><br/>
                        <input type="number" class="form-control" id="tr" name="tr" value="<?php echo $filters['tr']; ?>" placeholder="0">
                    </div>
                </div>

                <div class="col-md-12">
                    <button type="submit" class="btn btn-default">Submit</button>
                    <a href="<?php echo site_url(); ?>/resources/" class="btn btn-default">Clear</a>
                </div>
            </div>

        </form>
    </div>
</div>

<?php
$resource_columns = array();
if(is_user_logged_in()) {
    $columns_list = array("IP Address", "Name","DA", "DR", "RD", "TR", "PA", "TF", "CF","Organic Keywords", "Country", "Currency",
        "Original Price", "Casino Price", "CBD Price", "Adult Price", "Link Placement Price", "Package / Discount", "Finale Price",
        "Payment Method","Notes", "Secondary Email", "Origin File", "Rating", "Status", "Metrics Update Date", "Sectors", "Niche");
    $resource_columns_raw = get_field('resources_columns', 'user_'. get_current_user_id() );
    if($resource_columns_raw){
        $resource_columns = json_decode($resource_columns_raw, true);
    }else{
        $resource_columns = $columns_list;
    }
    ?>
    <div class="container">
        <br/>
        <button class="btn btn-default" type="submit" data-toggle="collapse" href="#columns" role="button" aria-expanded="false" aria-controls="columns">Columns</button>
        <div class="table-columns collapse" id="columns">
            <div class="row">
                <div class="col-md-6">
                    <div class="av-list">
                        <br />
                        <h2>Showing Columns</h2>
                        <select class="selectpicker" id="columnsList" multiple data-actions-box="true">
                            <?php
                            foreach ($columns_list as $item) {
                                if (!in_array($item, $resource_columns)) {
                                    ?>
                                    <option value="<?php echo $item; ?>"><?php echo $item; ?></option>
                                    <?php
                                }else{
                                    ?>
                                    <option value="<?php echo $item; ?>" selected><?php echo $item; ?></option>
                                    <?php
                                }
                            }
                            ?>
                        </select>
                        <br />
                        <br />
                    </div>
                    <div class="rv-list">
                        <h2>Sort Columns</h2>
                        <ul id="sortable2" class="connectedSortable">
                            <?php foreach ($resource_columns as $item){ ?>
                                <li class="ui-state-default" data-value="<?php echo $item; ?>"><?php echo $item; ?></li>
                            <?php } ?>
                        </ul>
                    </div>
                </div>
                <div class="col-md-6">
                </div>
            </div>

            <form class="columns-form" method="post" action="<?php echo esc_url( admin_url('admin-post.php') ); ?>">
                <input type="hidden" name="action" value="resource_columns_hook">
                <input type="hidden" name="resource-order" value="">
                <input type="hidden" name="current-page" value="<?php echo Config::getInstance()->actual_url; ?>">
                <button type="submit" class="btn btn-default">Submit</button>
                <button type="reset" class="btn btn-default">Reset</button>
            </form>
        </div>
    </div>
<?php } ?>

<?php $the_query = apply_filters('mam-resources-filtered-posts', $filters); ?>
<?php if ($the_query->have_posts()) { ?>
    <div class="responsive-table">
        <table class="table datatable">
            <thead class="thead-dark">
            <tr>
                <th scope="col">Action</th>
                <th scope="col">Website</th>
                <th scope="col">Email</th>
                <?php foreach ($resource_columns as $item) { ?>
                    <th scope="col"><?php echo $item; ?></th>
                <?php } ?>
            </tr>
            </thead>
            <tbody>
            <?php while ($the_query->have_posts()) {
                $the_query->the_post();
                $id = get_the_ID();
                $_finalePrice = '-';
                $_ogPrice = get_field('original_price', $id);
                if ($_ogPrice) {
                    $_finalePrice = $_ogPrice;
                }
                $_price = get_field('price', $id);
                if ($_price) {
                    $_finalePrice = $_price;
                }
                // filterprice
                if ($_finalePrice > $filters['price'] && $_finalePrice <= $filters['price1']) {

                } else {
                    continue;
                }

                $itemData = array(
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
                    'Sectors' => implode(', ', wp_get_object_terms($id, 'sector', array('fields' => 'names')))
                );
                ?>
                <tr>
                    <td><a href="<?php echo site_url(); ?>/add-order/?id=<?php echo $_id; ?>&r_id=<?php echo $id; ?>" class="btn btn-primary">Select</a></td>
                    <td>
                        <a data-type="iframe" href="<?php the_permalink(); ?>" target="_blank"
                           data-fancybox><?php the_title(); ?></a>
                    </td>
                    <td><?php echo $itemData['Email']; ?></td>
                    <?php foreach ($resource_columns as $item) { ?>
                        <td><?php echo $itemData[$item]; ?></td>
                    <?php } ?>
                </tr>
            <?php } ?>
            </tbody>
            <tfoot>
            <tr>
                <th scope="col">Action</th>
                <th scope="col">Website</th>
                <th scope="col">Email</th>
                <?php foreach ($resource_columns as $item) { ?>
                    <th scope="col"><?php echo $item; ?></th>
                <?php } ?>
            </tr>
            </tfoot>
        </table>
    </div>
    <style>
        tfoot input[type="text"] {
            max-width: 85px;
        }
    </style>
<?php } ?>
<?php get_footer(); ?>
