<?php
/**
 * The template for displaying all pages, single posts and attachments
 *
 * This is a new template file that WordPress introduced in
 * version 4.3.
 *
 * @package OceanWP WordPress theme
 */

use MAM\Plugin\Config;

get_header(); ?>

<?php do_action('ocean_before_content_wrap'); ?>


<?php do_action('ocean_before_content_inner'); ?>

<?php
// Elementor `single` location.
if (!function_exists('elementor_theme_do_location') || !elementor_theme_do_location('single')) {

    // Start loop.
    while (have_posts()) :
        the_post();
        $_id = get_the_ID();
        $agency = get_field('agency', $_id);
        global $filters;
        $filters = array();
        $filters['client'] = $_id;
        /**
         * @var $orders WP_Query
         */
        $orders = apply_filters('mam-orders-filtered-posts', $filters);
        $count = $orders->post_count;
        wp_reset_query();
        ?>
        <div id="content-wrap" class="container clr">

            <?php do_action('ocean_before_primary'); ?>

            <div id="primary" class="content-area clr">

                <?php do_action('ocean_before_content'); ?>

                <div id="content" class="site-content clr">
                    <h2>Client Details</h2>
                    <ul>
                        <?php $id = get_the_ID(); ?>
                        <li><b>Name:</b> <?php the_title(); ?></li>
                        <li><b>Website:</b> <?php echo get_field('website', $id); ?></li>
                        <li><b>Agency:</b> <a href="<?php echo get_the_permalink($agency); ?>" target="_blank"><?php echo get_the_title($agency); ?></a></li>
                        <li><b>Orders:</b> <?php echo $count; ?></li>
                    </ul>
                    <?php do_action('ocean_after_content_inner'); ?>

                </div><!-- #content -->

                <?php do_action('ocean_after_content'); ?>

            </div><!-- #primary -->

            <?php do_action('ocean_after_primary'); ?>

        </div><!-- #content-wrap -->
        <?php do_action('ocean_after_content_wrap'); ?>

        <?php
        $order_columns = array();
        if (is_user_logged_in()) {
            $columns_list = array("Client Name", "Client Website", "Agency", "Anchor Text", "Anchor Text Type", "Target URL", "Niche", "Sent To Writers",
                "Article sent to the site", "Live Link Received", "Live Link", "Date Paid", "USD Price", "THB Price", "Status",
                "Start Date", "Complete Date", "Sectors", "Resource URL", "IP Address", "Email", "Name", "DA", "DR", "RD", "TR", "PA", "TF", "CF",
                "Organic Keywords", "Country", "Currency", "Original Price", "Casino Price", "CBD Price", "Adult Price", "Link Placement Price",
                "Package / Discount", "Finale Price", "Payment Method", "Notes", "Secondary Email", "Origin File", "Rating",
                "Metrics Update Date");
            $order_columns_raw = get_field('orders_columns', 'user_' . get_current_user_id());
            if ($order_columns_raw) {
                $order_columns = json_decode($order_columns_raw, true);
            } else {
                $order_columns = $columns_list;
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
                                        if (!in_array($item, $order_columns)) {
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
                                <h2>Showing Columns</h2>
                                <ul id="sortable2" class="connectedSortable">
                                    <?php foreach ($order_columns as $item) { ?>
                                        <li class="ui-state-default" data-value="<?php echo $item; ?>"><?php echo $item; ?></li>
                                    <?php } ?>
                                </ul>
                            </div>
                        </div>
                        <div class="col-md-6">
                        </div>
                    </div>

                    <form class="columns-form" method="post" action="<?php echo esc_url(admin_url('admin-post.php')); ?>">
                        <input type="hidden" name="action" value="order_columns_hook">
                        <input type="hidden" name="resource-order" value="">
                        <input type="hidden" name="current-page" value="<?php echo Config::getInstance()->actual_url; ?>">
                        <button type="submit" class="btn btn-default">Submit</button>
                        <button type="reset" class="btn btn-default">Reset</button>
                    </form>
                </div>
            </div>
        <?php } ?>

        <?php $orders = apply_filters('mam-orders-filtered-posts', $filters); ?>
        <?php if ($orders->have_posts()) { ?>
        <div class="container">
            <div class="responsive-table">
                <div class="float-right">
                    <a href="#/" class="enterfullscreen btn btn-default" title="Full Screen"><i class="fas fa-expand"></i></a>
                    <a href="#/" class="existfullscreen btn btn-default" title="Exit Full Screen"><i class="fas fa-compress"></i></a>
                </div>
                <table class="table datatable">
                    <thead class="thead-dark">
                    <tr>
                        <th scope="col"></th>
                        <th scope="col">ID</th>
                        <?php foreach ($order_columns as $item) { ?>
                            <th scope="col"><?php echo $item; ?></th>
                        <?php } ?>
                    </tr>
                    </thead>
                    <tbody>
                    <?php while ($orders->have_posts()) {
                        $orders->the_post();
                        $id = get_the_ID();
                        $client = get_field('client', $id);
                        $agency = get_the_title(get_field('agency', $client));

                        $orderData = array(
                            "Client Name" => '<a data-type="iframe" href="' . get_the_permalink($client) . '" target="_blank" data-fancybox>' . get_the_title($client) . '</a>',
                            "Client Website" => get_field('website', $client),
                            "Agency" => $agency,
                            "Anchor Text" => get_field('anchor_text', $id),
                            "Anchor Text Type" => get_field('anchor_text_type', $id),
                            "Target URL" => get_field('target_url', $id),
                            "Niche" => get_field('niche', $id),
                            "Sent To Writers" => get_field('sent_to_writers', $id),
                            "Article sent to the site" => get_field('articles_sent_to_the_sites', $id),
                            "Live Link Received" => get_field('live_link_received', $id),
                            "Live Link" => get_field('live_link', $id),
                            "Date Paid" => get_field('date_paid', $id),
                            "USD Price" => get_field('usd_price', $id),
                            "THB Price" => get_field('thb_price', $id),
                            "Status" => get_field('status', $id),
                            "Start Date" => get_field('start_date', $id),
                            "Complete Date" => get_field('complete_date', $id),
                            "Sectors" => implode(', ', wp_get_object_terms($id, 'sector', array('fields' => 'names'))),
                            "Resource URL" => get_field('resource_url', $id),
                            "IP Address" => get_field('ip_address', $id),
                            "Email" => get_field('email', $id),
                            "Name" => get_field('contact_name', $id),
                            "DA" => get_field('da', $id),
                            "DR" => get_field('dr', $id),
                            "RD" => get_field('rd', $id),
                            "TR" => get_field('tr', $id),
                            "PA" => get_field('pa', $id),
                            "TF" => get_field('tf', $id),
                            "CF" => get_field('cf', $id),
                            "Organic Keywords" => get_field('organic_keywords', $id),
                            "Country" => get_field('country', $id),
                            "Currency" => get_field('currency', $id),
                            "Original Price" => get_field('original_price', $id),
                            "Casino Price" => get_field('casino_price', $id),
                            "CBD Price" => get_field('cbd_price', $id),
                            "Adult Price" => get_field('adult_price', $id),
                            "Link Placement Price" => get_field('link_placement_price', $id),
                            "Package / Discount" => get_field('package__discount', $id),
                            "Finale Price" => get_field('price', $id),
                            "Payment Method" => get_field('payment_method', $id),
                            "Notes" => get_field('notes', $id),
                            "Secondary Email" => get_field('secondary_email', $id),
                            "Origin File" => get_field('origin_file', $id),
                            "Rating" => get_field('rating', $id),
                            "Metrics Update Date" => get_field('metrics_update_date', $id)
                        );
                        ?>
                        <tr>
                            <td></td>
                            <td>
                                <a data-type="iframe" href="<?php the_permalink(); ?>" target="_blank"
                                   data-fancybox><?php the_title(); ?></a>
                            </td>
                            <?php foreach ($order_columns as $item) { ?>
                                <td><?php echo $orderData[$item]; ?></td>
                            <?php } ?>
                        </tr>
                    <?php } ?>
                    </tbody>
                    <tfoot>
                    <tr>
                        <th scope="col"></th>
                        <th scope="col">ID</th>
                        <?php foreach ($order_columns as $item) { ?>
                            <th scope="col"><?php echo $item; ?></th>
                        <?php } ?>
                    </tr>
                    </tfoot>
                </table>
            </div>
        </div>
    <?php } ?>
    <?php
    endwhile;

}
?>

<?php get_footer(); ?>
