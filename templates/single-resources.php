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

<?php do_action( 'ocean_before_content_wrap' ); ?>

<div id="content-wrap" class="container clr">

    <?php do_action( 'ocean_before_primary' ); ?>

    <div id="primary" class="content-area clr">

        <?php do_action( 'ocean_before_content' ); ?>

        <div id="content" class="site-content clr">

            <?php do_action( 'ocean_before_content_inner' ); ?>

            <?php
            // Elementor `single` location.
            if ( ! function_exists( 'elementor_theme_do_location' ) || ! elementor_theme_do_location( 'single' ) ) {

                // Start loop.
                while ( have_posts() ) :
                    the_post();
                    ?>
                    <h2>Resource Details</h2>
                    <ul>
                        <?php $id = get_the_ID(); ?>
                        <li><b>Website:</b> <?php the_title(); ?></li>
                        <li><b>Contact Name:</b> <?php echo get_field('contact_name', $id); ?></li>
                        <li><b>Contact Email:</b> <?php echo get_field('email', $id); ?></li>
                        <li><b>Sectors:</b> <?php echo implode(', ', wp_get_object_terms($id, 'sector', array('fields' => 'names'))); ?></li>
                        <li><b>DA:</b> <?php echo get_field('da', $id); ?></li>
                        <li><b>DR:</b> <?php echo get_field('dr', $id); ?></li>
                        <li><b>RD:</b> <?php echo get_field('rd', $id); ?></li>
                        <li><b>TR:</b> <?php echo get_field('tr', $id); ?></li>
                        <li><b>Country:</b> <?php echo get_field('country', $id); ?></li>
                        <li><b>Currency:</b> <?php echo get_field('currency', $id); ?></li>
                        <li><b>Original Price:</b> <?php echo get_field('original_price', $id); ?></li>
                        <li><b>Casino Price:</b> <?php echo get_field('casino_price', $id); ?></li>
                        <li><b>CBD Price:</b> <?php echo get_field('cbd_price', $id); ?></li>
                        <li><b>Adult Price:</b> <?php echo get_field('adult_price', $id); ?></li>
                        <li><b>Link Placement Price:</b> <?php echo get_field('link_placement_price', $id); ?></li>
                        <li><b>Finale Price:</b> <?php echo get_field('price', $id); ?></li>
                        <li><b>Package / Discount:</b> <?php echo get_field('package__discount', $id); ?></li>
                        <li><b>Payment Method:</b> <?php echo get_field('payment_method', $id); ?></li>
                        <li><b>Comments:</b> <?php echo get_field('comments', $id); ?></li>
                        <li><b>Rating:</b> <?php echo get_field('rating', $id); ?></li>
                        <li><b>Origin File:</b> <?php echo get_field('origin_file', $id); ?></li>
                        <li><b>Niche:</b> <?php echo get_field('niche', $id); ?></li>
                    </ul>


                <?php
                endwhile;

            }
            ?>

            <?php do_action( 'ocean_after_content_inner' ); ?>

        </div><!-- #content -->

        <?php do_action( 'ocean_after_content' ); ?>

    </div><!-- #primary -->

    <?php do_action( 'ocean_after_primary' ); ?>

</div><!-- #content-wrap -->

<?php do_action( 'ocean_after_content_wrap' ); ?>

<?php get_footer(); ?>
