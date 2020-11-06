<?php
get_header();
$agencies = array();
$the_query = apply_filters('mam-agencies-filtered-posts', $agencies);
if ($the_query->have_posts()) {
    while ($the_query->have_posts()) {
        ?>

        <?php
    }
}
get_footer();
?>
