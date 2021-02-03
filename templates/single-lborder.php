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

<div id="content-wrap" class="container clr">

    <?php do_action('ocean_before_primary'); ?>

    <div id="primary" class="content-area clr">

        <?php do_action('ocean_before_content'); ?>

        <div id="content" class="site-content clr">

            <?php do_action('ocean_before_content_inner'); ?>

            <?php
            // Elementor `single` location.
            if (!function_exists('elementor_theme_do_location') || !elementor_theme_do_location('single')) {

                // Start loop.
                while (have_posts()) :
                    the_post();
                    $id = get_the_ID();
                    $client = get_field('client', $id);
                    $agency = get_field('agency', $client);
                    $resource = get_field('resource', $id);
                    ?>
                    <h2>Order Details</h2>
                    <ul>
                        <?php $id = get_the_ID(); ?>
                        <li><b>Order ID:</b> <?php the_title(); ?></li>
                        <li><b>Client:</b> <a data-type="iframe" href="<?php echo get_the_permalink($client); ?>" target="_blank"
                                              data-fancybox><?php echo get_the_title($client); ?></a></li>
                        <li><b>Agency:</b> <a data-type="iframe" href="<?php echo get_the_permalink(get_field('website', $id)); ?>" target="_blank"
                                              data-fancybox><?php echo get_the_title($agency); ?></a></li>
                        <li><b>Anchor Text:</b> <?php echo get_field('anchor_text', $id);; ?></li>
                        <li><b>Anchor Text Type:</b> <?php echo get_field('anchor_text_type', $id);; ?></li>
                        <li><b>Target URL:</b> <?php echo get_field('target_url', $id);; ?></li>
                        <li><b>Niche:</b> <?php echo get_field('niche', $id);; ?></li>
                        <li><b>Resource:</b>
                            <?php if ($resource) { ?>
                                <a data-type="iframe" href="<?php echo get_the_permalink($resource); ?>" target="_blank" data-fancybox><?php echo get_the_title($resource); ?></a>
                            <?php } else { ?>
                                <a href="<?php echo site_url(); ?>/add-order/?id=<?php echo $id; ?>" class="btn btn-primary" target="_blank">Add Resource</a>
                            <?php } ?></li>
                        <li><b>Comments:</b> <?php if ($resource) {
                                echo get_field('comments', $resource);
                            } else {
                                echo '-';
                            } ?></li>
                        <li><b>Notes:</b> <?php if (get_field('notes', $id)) {
                                echo get_field('notes', $id);
                            } else {
                                echo '-';
                            } ?></li>
                        <li><b>Sent to Writers:</b> <?php if (get_field('sent_to_writers', $id)) {
                                echo get_field('sent_to_writers', $id);
                            } else {
                                echo '-';
                            } ?></li>
                        <li><b>Price:</b>
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
                            } ?></li>
                        <li><b>DA:</b> <?php if (get_field('da', $id)) {
                                echo get_field('da', $id);
                            } else {
                                echo '-';
                            } ?></li>
                        <li><b>RD:</b> <?php if (get_field('rd', $id)) {
                                echo get_field('rd', $id);
                            } else {
                                echo '-';
                            } ?></li>
                        <li><b>Country:</b> <?php if (get_field('country', $id)) {
                                echo get_field('country', $id);
                            } else {
                                echo '-';
                            } ?></li>
                        <li><b>Article Sent to the site:</b> <?php if (get_field('articles_sent_to_the_sites', $id)) {
                                echo get_field('articles_sent_to_the_sites', $id);
                            } else {
                                echo '-';
                            } ?></li>
                        <li><b>Live Link:</b>
                            <div style="display: none;" id="live-link-<?php echo $id; ?>">
                                <p><?php echo get_field('live_link', $id); ?></p>
                            </div>
                            <?php if (get_field('live_link_received', $id)) { ?>
                                <a href="live-link-<?php echo $id; ?>" data-fancybox
                                   data-src="#live-link-<?php echo $id; ?>"><?php echo get_field('live_link_received', $id); ?></a>
                            <?php } else {
                                echo '-';
                            } ?></li>
                        <li><b>Paid:</b> <?php if (get_field('we_paid', $id)) {
                                echo get_field('we_paid', $id);
                            } else {
                                echo '-';
                            } ?></li>
                        <li><b>Status:</b> <?php if (get_field('status', $id)) {
                                echo get_field('status', $id);
                            } else {
                                echo '-';
                            } ?></li>
                    </ul>
                    <a class="btn btn-primary" href="<?php echo site_url(); ?>/add-order/?id=<?php echo $id; ?>&change=1">Change Order</a>

                <?php
                endwhile;
            }
            ?>

            <?php do_action('ocean_after_content_inner'); ?>

        </div><!-- #content -->

        <?php do_action('ocean_after_content'); ?>

    </div><!-- #primary -->

    <?php do_action('ocean_after_primary'); ?>

</div><!-- #content-wrap -->

<?php do_action('ocean_after_content_wrap'); ?>

<?php get_footer(); ?>
