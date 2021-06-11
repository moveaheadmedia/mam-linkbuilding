<?php

use MAM\Plugin\Config;

get_header();

// Make sure we have filters
function mam_check_filters($_filters)
{
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
    $filters['website'] = '';
    $filters['sectors'] = array();
    if ($filters === $_filters) {
        return false;
    }
    return true;
}

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
$filters['website'] = '';
$filters['sectors'] = array();

if (isset($_GET['da']) && $_GET['da'] != '') {
    $dArray = explode(' - ', $_GET['da']);
    if (is_array($dArray)) {
        $filters['da'] = $dArray[0];
        $filters['da1'] = $dArray[1];
    }
}

if (isset($_GET['dr']) && $_GET['dr'] != '') {
    $dArray = explode(' - ', $_GET['dr']);
    if (is_array($dArray)) {
        $filters['dr'] = $dArray[0];
        $filters['dr1'] = $dArray[1];
    }
}

if (isset($_GET['rd']) && $_GET['rd'] != '') {
    $filters['rd'] = $_GET['rd'];
}

if (isset($_GET['tr']) && $_GET['tr'] != '') {
    $filters['tr'] = $_GET['tr'];
}

if (isset($_GET['price']) && $_GET['price'] != '') {
    $dArray = explode(' - ', $_GET['price']);
    if (is_array($dArray)) {
        $filters['price'] = $dArray[0];
        $filters['price1'] = $dArray[1];
    }
}

if (isset($_GET['the_client'])) {
    $filters['the_client'] = $_GET['the_client'];
}

if (isset($_GET['website'])) {
    $filters['website'] = $_GET['website'];
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
<div class="container">
    <br/>
    <button class="btn btn-default" type="submit" data-toggle="collapse" role="button" aria-expanded="false" aria-controls="filters">Filters</button>
    <div class="filters <?php if (mam_check_filters($filters)) { ?>collapse <?php } ?>" id="filters">
        <form method="get" action="">
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
                            <input type="text" id="da" name="da" style="background-color: #eee;border:0; color:#f6931f; font-weight:bold;" data-value="[<?php echo $filters['da']; ?>, <?php echo $filters['da1']; ?>]" value="<?php echo $filters['da']; ?> - <?php echo $filters['da1']; ?>">
                        </p>
                        <div id="daSlider"></div>
                    </div>
                </div>

                <div class="col-md-6">
                    <div class="form-group">

                        <p>
                            <label for="dr">DR:</label>
                            <input type="text" id="dr" name="dr" style="background-color: #eee;border:0; color:#f6931f; font-weight:bold;" data-value="[<?php echo $filters['dr']; ?>,<?php echo $filters['dr1']; ?>]" value="<?php echo $filters['dr']; ?> - <?php echo $filters['dr1']; ?>">
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
                                    <input type="text" id="price" name="price" style="background-color: #eee;border:0; color:#f6931f;" data-value="[<?php echo $filters['price']; ?>,<?php echo $filters['price1']; ?>]" value="<?php echo $filters['price']; ?> - <?php echo $filters['price1']; ?>">
                                </p>
                                <div id="priceSlider"></div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <p>
                                    <label for="website">Client Website:</label>
                                    <input type="text" id="website" name="website" style="background-color: #eee;border:0; color:#f6931f; font-weight:bold;" value="<?php echo $filters['website']; ?>">
                                </p>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-md-6">
                    <div class="form-group">
                        <p>
                            <label for="rd">Minimum RD</label><br/>
                            <input type="text" class="form-control" id="rd" style="background-color: #eee;border:0; color:#f6931f; font-weight:bold;" name="rd" value="<?php echo $filters['rd']; ?>" data-value="<?php echo $filters['rd']; ?>" placeholder="0">
                        </p>
                        <div id="rdSlider"></div>
                    </div>
                </div>

                <div class="col-md-6">
                    <div class="form-group">
                        <p>
                            <label for="tr">Minimum TR</label><br/>
                            <input type="text" class="form-control" id="tr" name="tr" style="background-color: #eee;border:0; color:#f6931f; font-weight:bold;" value="<?php echo $filters['tr']; ?>" data-value="<?php echo $filters['tr']; ?>" placeholder="0">
                        </p>
                        <div id="trSlider"></div>
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
<br/>
<br/>
<?php
$resource_columns = array();
if (is_user_logged_in()) {
    $columns_list = array("Website", "Email", "IP Address", "Name", "DA", "DR", "RD", "TR", "PA", "TF", "CF", "Organic Keywords", "Country", "Currency",
        "Original Price", "Casino Price", "CBD Price", "Adult Price", "Link Placement Price", "Package / Discount", "Finale Price",
        "Payment Method", "Notes", "Secondary Email", "Origin File", "Rating", "Status", "Metrics Update Date", "Sectors", "Niche", "New Remarks", "Social Media", "Other Info", "Contact / Email");
    $resource_columns_raw = get_field('resources_columns', 'user_' . get_current_user_id());
    if ($resource_columns_raw) {
        $resource_columns = json_decode($resource_columns_raw, true);
    } else {
        $resource_columns = $columns_list;
    }
    ?>
    <div class="container">
        <br/>
        <button class="btn btn-default" type="submit" data-toggle="collapse" role="button" aria-expanded="false" aria-controls="columns">Columns</button>
        <div class="table-columns collapse" id="columns">
            <div class="row">
                <div class="col-md-6">
                    <div class="av-list">
                        <br/>
                        <h2><label for="columnsList">Showing Columns</label></h2>
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
<div class="container">
    <div class="row">
        <div class="col-md-12">
            <form>
                <div class="form-group">
                    <label for="niche">Niche</label>
                    <input type="text" id="niche" name="niche" style="background-color: #eee;border:0; color:#f6931f; font-weight:bold;" value="">
                </div>
            </form>
        </div>
    </div>
    <div class="responsive-table">
        <div class="float-right">
            <a href="#" class="enterfullscreen btn btn-default" title="Full Screen"><i class="fas fa-expand"></i></a>
            <a href="#" class="existfullscreen btn btn-default" title="Exit Full Screen"><i class="fas fa-compress"></i></a>
        </div>
        <table class="table datatable server">
            <thead class="thead-dark">
            <tr>
                <th class="" scope="col"></th>
                <?php foreach ($resource_columns as $item) { ?>
                    <th class="<?php echo sanitize_title($item); ?>" scope="col"><?php echo $item; ?></th>
                <?php } ?>
            </tr>
            </thead>
            <tbody>
            </tbody>
            <tfoot>
            <tr>
                <th scope="col"></th>
                <?php foreach ($resource_columns as $item) { ?>
                    <th class="<?php echo sanitize_title($item); ?>" scope="col"><?php echo $item; ?></th>
                <?php } ?>
            </tr>
            </tfoot>
        </table>
        <button class="btn btn-primary" id="filter">Filter</button>
    </div>
</div>
<?php get_footer(); ?>
