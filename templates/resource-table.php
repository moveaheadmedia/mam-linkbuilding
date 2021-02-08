<?php

use MAM\Plugin\Config;
use MAM\Plugin\Services\Admin\Clients;
use MAM\Plugin\Services\Admin\Resources;

global $_id;
?>
    <div class="container">
        <br/>
        <button class="btn btn-default" type="submit" data-toggle="collapse" href="#filters" role="button" aria-expanded="false" aria-controls="filters">Filters</button>
        <div class="filters collapse" id="filters">
            <?php
            // init $filters
            $filters = array();
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

            // if $filters exist in the user account load it
            $filters_columns_raw = get_field('filters', 'user_' . get_current_user_id());
            if ($filters_columns_raw) {
                $f_array = json_decode($filters_columns_raw, true);
                if (is_array($f_array) && !empty($f_array)) {
                    $_filters = $f_array;
                    if (isset($_filters['da']) && $_filters['da'] != '') {
                        $dArray = explode(' - ', $_filters['da']);
                        if (is_array($dArray)) {
                            $filters['da'] = $dArray[0];
                            $filters['da1'] = $dArray[1];
                        }
                    }
                    if (isset($_filters['dr']) && $_filters['dr'] != '') {
                        $dArray = explode(' - ', $_filters['dr']);
                        if (is_array($dArray)) {
                            $filters['dr'] = $dArray[0];
                            $filters['dr1'] = $dArray[1];
                        }
                    }
                    if (isset($_filters['rd']) && $_filters['rd'] != '') {
                        $filters['rd'] = $_filters['rd'];
                    }
                    if (isset($_filters['tr']) && $_filters['tr'] != '') {
                        $filters['tr'] = $_filters['tr'];
                    }
                    if (isset($_filters['price']) && $_filters['price'] != '') {
                        $dArray = explode(' - ', $_filters['price']);
                        if (is_array($dArray)) {
                            $filters['price'] = $dArray[0];
                            $filters['price1'] = $dArray[1];
                        }
                    }
                    if (isset($_filters['the_client'])) {
                        $filters['the_client'] = $_filters['the_client'];
                    }
                    if (isset($_filters['anchoer_text'])) {
                        $filters['anchoer_text'] = $_filters['anchoer_text'];
                    }
                    if (isset($_filters['target_url'])) {
                        $filters['target_url'] = $_filters['target_url'];
                    }
                    if (isset($_filters['sectors'])) {
                        $filters['sectors'] = $_filters['sectors'];
                    }
                }
            }
            ?>
            <form class="filters" method="post" action="<?php echo esc_url(admin_url('admin-post.php')); ?>">
                <input type="hidden" name="action" value="resource_filter_hook">
                <input type="hidden" name="current-page" value="<?php echo Config::getInstance()->actual_url; ?>">
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

                            <p>
                                <label for="da">DA:</label>
                                <input type="text" id="da" name="da" readonly style="border:0; color:#f6931f; font-weight:bold;" data-value="[<?php echo $filters['da']; ?>, <?php echo $filters['da1']; ?>]" value="<?php echo $filters['da']; ?> - <?php echo $filters['da1']; ?>">
                            </p>
                            <div id="daSlider"></div>
                        </div>
                    </div>

                    <div class="col-md-6">
                        <div class="form-group">

                            <p>
                                <label for="dr">DR:</label>
                                <input type="text" id="dr" name="dr" readonly style="border:0; color:#f6931f; font-weight:bold;" data-value="[<?php echo $filters['dr']; ?>,<?php echo $filters['dr1']; ?>]" value="<?php echo $filters['dr']; ?> - <?php echo $filters['dr1']; ?>">
                            </p>
                            <div id="drSlider"></div>

                        </div>
                    </div>

                    <div class="col-md-12">
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <p>
                                        <label for="price">Price:</label>
                                        <input type="text" id="price" name="price" readonly style="border:0; color:#f6931f; font-weight:bold;" data-value="[<?php echo $filters['price']; ?>,<?php echo $filters['price1']; ?>]" value="<?php echo $filters['price']; ?> - <?php echo $filters['price1']; ?>">
                                    </p>
                                    <div id="priceSlider"></div>
                                </div>
                            </div>
                            <div class="col-md-6"></div>
                        </div>
                    </div>

                    <div class="col-md-6">
                        <div class="form-group">
                            <p>
                                <label for="rd">Minimum RD</label><br/>
                                <input type="text" class="form-control" id="rd" readonly style="border:0; color:#f6931f; font-weight:bold;" name="rd" value="<?php echo $filters['rd']; ?>" data-value="<?php echo $filters['rd']; ?>" placeholder="0">
                            </p>
                            <div id="rdSlider"></div>
                        </div>
                    </div>

                    <div class="col-md-6">
                        <div class="form-group">
                            <p>
                                <label for="tr">Minimum TR</label><br/>
                                <input type="text" class="form-control" id="tr" name="tr" readonly style="border:0; color:#f6931f; font-weight:bold;" value="<?php echo $filters['tr']; ?>" data-value="<?php echo $filters['tr']; ?>" placeholder="0">
                            </p>
                            <div id="trSlider"></div>
                        </div>
                    </div>

                    <div class="col-md-12">
                        <button type="submit" class="btn btn-default">Submit</button>
                        <input type="reset" class="btn btn-default" value="Reset"/>
                    </div>
                </div>

            </form>
        </div>
    </div>

<?php
$resource_columns = array();
if (is_user_logged_in()) {
    $columns_list = array("IP Address", "Name", "DA", "DR", "RD", "TR", "PA", "TF", "CF", "Organic Keywords", "Country", "Currency",
        "Original Price", "Casino Price", "CBD Price", "Adult Price", "Link Placement Price", "Package / Discount", "Finale Price",
        "Payment Method", "Notes", "Secondary Email", "Origin File", "Rating", "Status", "Metrics Update Date", "Sectors", "Niche");
    $resource_columns_raw = get_field('resources_columns', 'user_' . get_current_user_id());
    if ($resource_columns_raw) {
        $resource_columns = json_decode($resource_columns_raw, true);
    } else {
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
                        <br/>
                        <h2>Showing Columns</h2>
                        <select class="selectpicker" id="columnsList" multiple data-actions-box="true">
                            <?php
                            foreach ($columns_list as $item) {
                                if (!in_array($item, $resource_columns)) {
                                    ?>
                                    <option value="<?php echo $item; ?>"><?php echo $item; ?></option>
                                    <?php
                                } else {
                                    ?>
                                    <option value="<?php echo $item; ?>" selected><?php echo $item; ?></option>
                                    <?php
                                }
                            }
                            ?>
                        </select>
                        <br/>
                        <br/>
                    </div>
                    <div class="rv-list">
                        <h2>Sort Columns</h2>
                        <ul id="sortable2" class="connectedSortable">
                            <?php foreach ($resource_columns as $item) { ?>
                                <li class="ui-state-default" data-value="<?php echo $item; ?>"><?php echo $item; ?></li>
                            <?php } ?>
                        </ul>
                    </div>
                </div>
                <div class="col-md-6">
                </div>
            </div>

            <form class="columns-form" method="post" action="<?php echo esc_url(admin_url('admin-post.php')); ?>">
                <input type="hidden" name="action" value="resource_columns_hook">
                <input type="hidden" name="resource-order" value="">
                <input type="hidden" name="current-page" value="<?php echo Config::getInstance()->actual_url; ?>">
                <button type="submit" class="btn btn-default">Submit</button>
                <button type="reset" class="btn btn-default">Reset</button>
            </form>
        </div>
    </div>
<?php } ?>

<?php
/**
 * @var WP_Query
  */
$the_query = apply_filters('mam-resources-filtered-posts', $filters); ?>
<?php if ($the_query->have_posts()) { ?>
    <div class="container">
        <div class="responsive-table">
            <div class="float-right">
                <a href="#/" class="enterfullscreen btn btn-default" title="Full Screen"><i class="fas fa-expand"></i></a>
                <a href="#/" class="existfullscreen btn btn-default" title="Exit Full Screen"><i class="fas fa-compress"></i></a>
            </div>
            <table class="table datatable">
                <thead class="thead-dark">
                <tr>
                    <?php if (isset($_id) && $_id != '') { ?>
                        <th class="<?php echo sanitize_title('Action'); ?>" scope="col">Action</th>
                    <?php } ?>
                    <th class="<?php echo sanitize_title('Website'); ?>" scope="col">Website</th>
                    <th class="<?php echo sanitize_title('Email'); ?>" scope="col">Email</th>
                    <?php foreach ($resource_columns as $item) { ?>
                        <th class="<?php echo sanitize_title($item); ?>" scope="col"><?php echo $item; ?></th>
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
                    );

                    // Filter used with the client before
                    if (isset($_id) && $_id != '') {
                        $client = get_field('client', $_id);
                        if(Clients::check_client_resource($client, get_the_title($id))){
                            continue;
                        }
                    }
                    ?>
                    <tr>
                        <?php if (isset($_id) && $_id != '') { ?>
                            <td class="<?php echo sanitize_title('Action'); ?>"><a href="<?php echo site_url(); ?>/add-order/?id=<?php echo $_id; ?>&r_id=<?php echo $id; ?>" class="btn btn-primary">Select</a></td>
                        <?php } ?>
                        <td class="<?php echo sanitize_title('website'); ?>">
                            <a data-type="iframe" href="<?php echo get_the_permalink($id); ?>" target="_blank"
                               data-fancybox><?php echo get_the_title($id); ?></a>
                        </td>
                        <td class="<?php echo sanitize_title('email'); ?>"><?php echo $itemData['Email']; ?></td>
                        <?php foreach ($resource_columns as $item) { ?>
                            <?php
                            if ($item == 'IP Address') {
                                if (Resources::resource_ip_duplicated($itemData[$item])) {
                                    ?>

                                    <td class="<?php echo sanitize_title($item); ?>"><abbr class="text text-danger" title="Duplicated IP Address"><?php echo $itemData[$item]; ?></abbr></td>
                                    <?php
                                    continue;
                                }
                            }
                            ?>
                            <td class="<?php echo sanitize_title($item); ?>"><?php echo $itemData[$item]; ?></td>
                        <?php } ?>
                    </tr>
                <?php
                $the_query->reset_postdata();
                } ?>
                </tbody>
                <tfoot>
                <tr>
                    <?php if (isset($_id) && $_id != '') { ?>
                        <th class="<?php echo sanitize_title('Action'); ?>" scope="col">Action</th>
                    <?php } ?>
                    <th class="<?php echo sanitize_title('Website'); ?>" scope="col">Website</th>
                    <th class="<?php echo sanitize_title('Email'); ?>" scope="col">Email</th>
                    <?php foreach ($resource_columns as $item) { ?>
                        <th class="<?php echo sanitize_title($item); ?>" scope="col"><?php echo $item; ?></th>
                    <?php } ?>
                </tr>
                </tfoot>
            </table>
        </div>
    </div>
<?php } ?>