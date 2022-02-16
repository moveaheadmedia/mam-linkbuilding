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
                    $resource = get_field('resource_url', $id);
                    ?>
                    <h2>Order Details</h2>
                    <ul>
                        <?php $id = get_the_ID(); ?>
                        <li><b>Order ID:</b> <?php the_title(); ?></li>
                        <li><b>Client:</b> <a data-type="iframe" href="<?php echo get_the_permalink($client); ?>" target="_blank"
                                              data-fancybox><?php echo get_the_title($client); ?></a></li>
                        <li><b>Agency:</b> <a data-type="iframe" href="<?php echo get_the_permalink(get_field('website', $id)); ?>" target="_blank"
                                              data-fancybox><?php echo get_the_title($agency); ?></a></li>
                        <?php

                        $fields = get_fields();

                        if( $fields ): ?>
                                <?php foreach( $fields as $name => $value ): ?>
                            <?php if($name == 'client'){ continue; } ?>
                            <?php if(trim($value) == ''){ continue; } ?>
                                    <li><b><?php echo $name; ?></b> <?php echo $value; ?></li>
                                <?php endforeach; ?>
                        <?php endif; ?>

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
