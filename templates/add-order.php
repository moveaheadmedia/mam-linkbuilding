<?php use MAM\Plugin\Services\Admin\Orders;

get_header(); ?>
<?php
$_id = '';
if (isset($_GET['id'])) {
    $_id = $_GET['id'];
}
$client = get_field('client', $_id);
$agency = get_field('agency', $client);

$r_id = '';
if (isset($_GET['r_id'])) {
    $r_id = $_GET['r_id'];

    $finalePrice = '-';
    $ogPrice = get_field('original_price', $r_id);
    if ($ogPrice) {
        $finalePrice = $ogPrice ;
    }
    $price = get_field('price', $r_id);
    if ($price) {
        $finalePrice = $price ;
    }
    $orderData = array(
        'ID' => get_the_title($_id),
        'Client Name' => get_the_title($client),
        'Client Website' => get_field('website', $client),
        'Agency' => get_the_title($agency),
        'Anchor Text' => get_field('anchor_text', $_id),
        'Target URL' => get_field('target_url', $_id),
        'Niche' => get_field('niche', $_id),
        'Resource URL' => get_the_title($r_id),
        'Resource Email' => get_field('email', $r_id),
        'DA' => get_field('da', $r_id),
        'RD' => get_field('rd', $r_id),
        'Currency' => get_field('currency', $r_id),
        'Price' => $finalePrice,
        'Status' => 'Ongoing'
    );

    Orders::update_order($_id, $r_id, $client, $orderData);
}

$resource = get_field('resource', $_id);
?>
<div class="container">
    <br/>
    <h2>Order Details</h2>
    <ul>
        <li><b>Order ID:</b> <a href="<?php echo get_permalink($_id); ?>"><?php echo get_the_title($_id); ?></a></li>
        <li><b>Client:</b> <a href="<?php echo get_the_permalink($client); ?>" target="_blank"><?php echo get_the_title($client); ?></a></li>
        <li><b>Agency:</b> <a href="<?php echo get_the_permalink(get_field('website', $_id)); ?>" target="_blank"><?php echo get_the_title($agency); ?></a></li>
        <li><b>Anchor Text:</b> <?php echo get_field('anchor_text', $_id);; ?></li>
        <li><b>Target URL:</b> <?php echo get_field('target_url', $_id);; ?></li>
        <li><b>Niche:</b> <?php echo get_field('niche', $_id);; ?></li>
        <?php if ($resource) { ?>
            <li><b>Resource:</b>
                <?php if ($resource) { ?>
                    <a data-type="iframe" href="<?php echo get_the_permalink($resource); ?>" target="_blank" data-fancybox><?php echo get_the_title($resource); ?></a>
                <?php } else { ?>
                    <a href="<?php echo site_url(); ?>/add-order/?id=<?php echo $_id; ?>" class="btn btn-primary" target="_blank">Add Resource</a>
                <?php } ?></li>
            <li><b>Comments:</b> <?php if ($resource) {
                    echo get_field('comments', $resource);
                } else {
                    echo '-';
                } ?></li>
            <li><b>Notes:</b> <?php if (get_field('notes', $_id)) {
                    echo get_field('notes', $_id);
                } else {
                    echo '-';
                } ?></li>
            <li><b>Sent to Writers:</b> <?php if (get_field('sent_to_writers', $_id)) {
                    echo get_field('sent_to_writers', $_id);
                } else {
                    echo '-';
                } ?></li>
            <li><b>Price:</b>
                <div style="display: none;" id="price-<?php echo $_id; ?>">
                    <?php
                    $currency = get_field('currency', $_id);
                    if (!$currency) {
                        $currency = 'USD';
                    }
                    $finalePrice = '';
                    $price = '-';
                    $price = get_field('price', $_id);
                    if ($price) {
                        echo '<p>Paid Price: ' . $price . ' ' . $currency . '</p>';
                        $finalePrice = $price . ' ' . $currency;
                    }
                    $dollar_price = get_field('dollar_price', $_id);
                    if ($dollar_price) {
                        echo '<p>Price USD: ' . $dollar_price . '</p>';
                    }
                    $baht_price = get_field('baht_price', $_id);
                    if ($baht_price) {
                        echo '<p>Price THB: ' . $baht_price . '</p>';
                    }
                    ?>
                </div>
                <?php if ($finalePrice != '') { ?>
                    <a href="price-<?php echo $_id; ?>" data-fancybox
                       data-src="#price-<?php echo $_id; ?>"><?php echo $finalePrice; ?></a>
                <?php } else {
                    echo '-';
                } ?></li>
            <li><b>DA:</b> <?php if (get_field('da', $_id)) {
                    echo get_field('da', $_id);
                } else {
                    echo '-';
                } ?></li>
            <li><b>RD:</b> <?php if (get_field('rd', $_id)) {
                    echo get_field('rd', $_id);
                } else {
                    echo '-';
                } ?></li>
            <li><b>Article Sent to the site:</b> <?php if (get_field('articles_sent_to_the_sites', $_id)) {
                    echo get_field('articles_sent_to_the_sites', $_id);
                } else {
                    echo '-';
                } ?></li>
            <li><b>Live Link:</b>
                <div style="display: none;" id="live-link-<?php echo $_id; ?>">
                    <p><?php echo get_field('live_link', $_id); ?></p>
                </div>
                <?php if (get_field('live_link_received', $_id)) { ?>
                    <a href="live-link-<?php echo $_id; ?>" data-fancybox
                       data-src="#live-link-<?php echo $_id; ?>"><?php echo get_field('live_link_received', $_id); ?></a>
                <?php } else {
                    echo '-';
                } ?></li>
            <li><b>Paid:</b> <?php if (get_field('we_paid', $_id)) {
                    echo get_field('we_paid', $_id);
                } else {
                    echo '-';
                } ?></li>
            <li><b>Status:</b> <?php if (get_field('status', $_id)) {
                    echo get_field('status', $_id);
                } else {
                    echo '-';
                } ?></li>
        <?php } ?>
    </ul>
</div>


<?php if (!$resource || isset($_GET['change'])) { ?>
    <div class="container">
        <br/>
        <a class="btn btn-default" data-toggle="collapse" href="#filters" role="button" aria-expanded="false"
           aria-controls="filters"><h2>Filters</h2></a>
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

    <?php $the_query = apply_filters('mam-resources-filtered-posts', $filters); ?>
    <?php if ($the_query->have_posts()) { ?>
        <div class="responsive-table">
            <table class="table datatable">
                <thead class="thead-dark">
                <tr>
                    <th scope="col">Website</th>
                    <th scope="col">Contact Email</th>
                    <th scope="col">DA</th>
                    <th scope="col">DR</th>
                    <th scope="col">RD</th>
                    <th scope="col">TR</th>
                    <th scope="col">Price</th>
                    <th scope="col">Sectors</th>
                    <th scope="col">Action</th>
                    <?php if ($filters['the_client'] != '' & $filters['anchoer_text'] != '' & $filters['target_url'] != '') { ?>
                        <th scope="col">Action</th>
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
                    if ($_finalePrice > $filters['price'] && $_finalePrice <= $filters['price1']) {

                    } else {
                        continue;
                    }
                    ?>
                    <tr>
                        <td>
                            <a data-type="iframe" href="<?php the_permalink(); ?>" target="_blank"
                               data-fancybox><?php the_title(); ?></a>
                            <div style="display: none;">
                                <?php echo get_field('contact_name', $id); ?>
                                <?php echo get_field('comments', $id); ?>
                                <?php echo get_field('rating', $id); ?>
                                <?php echo get_field('origin_file', $id); ?>
                            </div>
                        </td>
                        <td><?php echo get_field('email', $id); ?></td>
                        <td><?php echo get_field('da', $id); ?></td>
                        <td><?php echo get_field('dr', $id); ?></td>
                        <td><?php echo get_field('rd', $id); ?></td>
                        <td><?php echo get_field('tr', $id); ?></td>
                        <td>
                            <div style="display: none;" id="price-<?php echo $id; ?>">
                                <?php
                                $currency = get_field('currency', $id);
                                if (!$currency) {
                                    $currency = 'USD';
                                }
                                $finalePrice = '-';
                                $ogPrice = get_field('original_price', $id);
                                if ($ogPrice) {
                                    echo '<p>Original Price: ' . $ogPrice . ' ' . $currency . '</p>';
                                    $finalePrice = $ogPrice . ' ' . $currency;
                                }
                                $casino_price = get_field('casino_price', $id);
                                if ($casino_price) {
                                    echo '<p>Casino Price: ' . $casino_price . ' ' . $currency . '</p>';
                                }
                                $cbd_price = get_field('cbd_price', $id);
                                if ($cbd_price) {
                                    echo '<p>CBD Price: ' . $cbd_price . ' ' . $currency . '</p>';
                                }
                                $adult_price = get_field('adult_price', $id);
                                if ($adult_price) {
                                    echo '<p>Adult Price: ' . $adult_price . ' ' . $currency . '</p>';
                                }
                                $link_placement_price = get_field('link_placement_price', $id);
                                if ($link_placement_price) {
                                    echo '<p>Link Placement Price: ' . $link_placement_price . ' ' . $currency . '</p>';
                                }
                                $price = get_field('price', $id);
                                if ($price) {
                                    echo '<p>Finale Price: ' . $price . ' ' . $currency . '</p>';
                                    $finalePrice = '<b style="color: brown;">' . $price . ' ' . $currency . '</b>';
                                }
                                $discount_package = get_field('package__discount', $id);
                                if ($discount_package) {
                                    echo '<p>Discount / Package: ' . $discount_package . '</p>';
                                }
                                $payment_method = get_field('payment_method', $id);
                                if ($payment_method) {
                                    echo '<p>Payment Method: ' . $payment_method . '</p>';
                                }
                                ?>
                            </div>
                            <a href="price-<?php echo $id; ?>" data-fancybox
                               data-src="#price-<?php echo $id; ?>"><?php echo $finalePrice; ?></a>
                        </td>
                        <td><?php echo implode(', ', wp_get_object_terms($id, 'sector', array('fields' => 'names'))); ?></td>
                        <td><a href="<?php echo site_url(); ?>/add-order/?id=<?php echo $_id; ?>&r_id=<?php echo $id; ?>" class="btn btn-primary">Select</a></td>
                    </tr>
                <?php } ?>
                </tbody>
                <tfoot>
                <tr>
                    <th scope="col">Website</th>
                    <th scope="col">Contact Email</th>
                    <th scope="col">DA</th>
                    <th scope="col">DR</th>
                    <th scope="col">RD</th>
                    <th scope="col">TR</th>
                    <th scope="col">Price</th>
                    <th scope="col">Sectors</th>
                    <th scope="col">Action</th>
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
<?php } ?>
<?php get_footer(); ?>
