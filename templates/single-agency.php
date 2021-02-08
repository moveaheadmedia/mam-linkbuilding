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
        global $filters;
        $filters = array();
        $filters['agency'] = $_id;
        /**
         * @var $orders WP_Query
         */
        $clients = apply_filters('mam-clients-filtered-posts', $filters);
        $count = $clients->post_count;
        wp_reset_query();

        $orders = apply_filters('mam-orders-filtered-posts', $filters);
        $orderCount = $orders->post_count;
        wp_reset_query();
        ?>
        <div id="content-wrap" class="container clr">

            <?php do_action('ocean_before_primary'); ?>

            <div id="primary" class="content-area clr">

                <?php do_action('ocean_before_content'); ?>

                <div id="content" class="site-content clr">
                    <h2>Agency Details</h2>
                    <ul>
                        <li><b>Name:</b> <?php echo get_the_title($_id); ?></li>
                        <li><b>Total Clients:</b> <?php echo $count; ?></li>
                        <li><b>Total Orders:</b> <?php echo $orderCount; ?></li>
                    </ul>
                    <?php do_action('ocean_after_content_inner'); ?>

                </div><!-- #content -->

                <?php do_action('ocean_after_content'); ?>

            </div><!-- #primary -->

            <?php do_action('ocean_after_primary'); ?>

        </div><!-- #content-wrap -->
        <?php do_action('ocean_after_content_wrap'); ?>
        <?php include('order-table.php'); ?>

    <?php
    endwhile;
}
?>

<?php get_footer(); ?>
