<?php
/**
 * The template for displaying all pages, single posts and attachments
 *
 * This is a new template file that WordPress introduced in
 * version 4.3.
 *
 * @package OceanWP WordPress theme
 */

get_header(); ?>

<?php do_action('ocean_before_content_wrap'); ?>


<?php do_action('ocean_before_content_inner'); ?>

<?php
// Elementor `single` location.
if (!function_exists('elementor_theme_do_location') || !elementor_theme_do_location('single')) {

    // Start loop.
    while (have_posts()) :
        the_post();
        $id = get_the_ID();
        $agency = get_field('agency', $id);
        $filters = array();
        $filters['client'] = $id;
        /**
         * @var $orders WP_Query
         */
        $orders = apply_filters('mam-orders-filtered-posts', $filters);
        $count = $orders->post_count;
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
        <?php if ($orders->have_posts()) { ?>
        <div class="responsive-table">
            <table class="table datatable">
                <thead class="thead-dark">
                <tr>
                    <th scope="col">ID</th>
                    <th scope="col">Client</th>
                    <th scope="col">Anchor</th>
                    <th scope="col">Target</th>
                    <th scope="col">Resource</th>
                    <th scope="col">Comments</th>
                    <th scope="col">Notes</th>
                    <th scope="col">Writers</th>
                    <th scope="col">Price</th>
                    <th scope="col">DA</th>
                    <th scope="col">RD</th>
                    <th scope="col">Sent</th>
                    <th scope="col">Live Link Received</th>
                    <th scope="col">Paid</th>
                    <th scope="col">Status</th>
                    <th scope="col">Sectors</th>
                </tr>
                </thead>
                <tbody>
                <?php while ($orders->have_posts()) {
                    $orders->the_post();
                    $id = get_the_ID();
                    $client = get_field('client', $id);
                    $resource = get_field('resource', $id);
                    ?>
                    <tr>
                        <td>
                            <a data-type="iframe" href="<?php the_permalink(); ?>" target="_blank"
                               data-fancybox><?php the_title(); ?></a>
                        </td>
                        <td><a data-type="iframe" href="<?php echo get_the_permalink($client); ?>" target="_blank"
                               data-fancybox><?php echo get_the_title($client); ?></a></td>
                        <td><?php echo get_field('anchor_text', $id); ?></td>
                        <td><?php echo get_field('target_url', $id); ?></td>
                        <td>
                            <?php if ($resource) { ?>
                                <a data-type="iframe" href="<?php echo get_the_permalink($resource); ?>" target="_blank" data-fancybox><?php echo get_the_title($resource); ?></a>
                            <?php } else { ?>
                                <a href="<?php echo site_url(); ?>/add-order/?id=<?php echo $id; ?>" class="btn btn-primary" target="_blank">Add Resource</a>
                            <?php } ?>
                        </td>
                        <td><?php if ($resource) {
                                echo get_field('comments', $resource);
                            } else {
                                echo '-';
                            } ?></td>
                        <td><?php if (get_field('notes', $id)) {
                                echo get_field('notes', $id);
                            } else {
                                echo '-';
                            } ?></td>
                        <td><?php if (get_field('sent_to_writers', $id)) {
                                echo get_field('sent_to_writers', $id);
                            } else {
                                echo '-';
                            } ?></td>
                        <td>
                            <div style="display: none;" id="price-<?php echo $id; ?>">
                                <?php
                                $currency = get_field('currency', $id);
                                if (!$currency) {
                                    $currency = 'USD';
                                }
                                $finalePrice = '';
                                $price = '-';
                                $price = get_field('price', $id);
                                if ($price) {
                                    echo '<p>Paid Price: ' . $price . ' ' . $currency . '</p>';
                                    $finalePrice = $price . ' ' . $currency;
                                }
                                $dollar_price = get_field('dollar_price', $id);
                                if ($dollar_price) {
                                    echo '<p>Price USD: ' . $dollar_price . '</p>';
                                }
                                $baht_price = get_field('baht_price', $id);
                                if ($baht_price) {
                                    echo '<p>Price THB: ' . $baht_price . '</p>';
                                }
                                ?>
                            </div>
                            <?php if ($finalePrice != '') { ?>
                                <a href="price-<?php echo $id; ?>" data-fancybox
                                   data-src="#price-<?php echo $id; ?>"><?php echo $finalePrice; ?></a>
                            <?php } else {
                                echo '-';
                            } ?>
                        </td>
                        <td><?php if (get_field('da', $id)) {
                                echo get_field('da', $id);
                            } else {
                                echo '-';
                            } ?></td>
                        <td><?php if (get_field('rd', $id)) {
                                echo get_field('rd', $id);
                            } else {
                                echo '-';
                            } ?></td>
                        <td><?php if (get_field('articles_sent_to_the_sites', $id)) {
                                echo get_field('articles_sent_to_the_sites', $id);
                            } else {
                                echo '-';
                            } ?></td>
                        <td>
                            <div style="display: none;" id="live-link-<?php echo $id; ?>">
                                <p><?php echo get_field('live_link', $id); ?></p>
                            </div>
                            <?php if (get_field('live_link_received', $id)) { ?>
                                <a href="live-link-<?php echo $id; ?>" data-fancybox
                                   data-src="#live-link-<?php echo $id; ?>"><?php echo get_field('live_link_received', $id); ?></a>
                            <?php } else {
                                echo '-';
                            } ?>
                        </td>
                        <td><?php if (get_field('we_paid', $id)) {
                                echo get_field('we_paid', $id);
                            } else {
                                echo '-';
                            } ?></td>
                        <td><?php if (get_field('status', $id)) {
                                echo get_field('status', $id);
                            } else {
                                echo '-';
                            } ?></td>
                        <td><?php echo implode(', ', wp_get_object_terms($id, 'sector', array('fields' => 'names'))); ?></td>
                    </tr>
                <?php } ?>
                </tbody>
                <tfoot>
                <tr>
                    <th scope="col">ID</th>
                    <th scope="col">Client</th>
                    <th scope="col">Anchor</th>
                    <th scope="col">Target</th>
                    <th scope="col">Resource</th>
                    <th scope="col">Comments</th>
                    <th scope="col">Notes</th>
                    <th scope="col">Writers</th>
                    <th scope="col">Price</th>
                    <th scope="col">DA</th>
                    <th scope="col">RD</th>
                    <th scope="col">Sent</th>
                    <th scope="col">Live Link Received</th>
                    <th scope="col">Paid</th>
                    <th scope="col">Status</th>
                    <th scope="col">Sectors</th>
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
    <?php
    endwhile;

}
?>

<?php get_footer(); ?>
