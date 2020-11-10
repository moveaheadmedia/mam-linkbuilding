<?php get_header(); ?>
<?php $clients = apply_filters('mam-clients-filtered-posts', ''); ?>
<?php if ($clients->have_posts()) { ?>
    <div class="responsive-table">
        <table class="table datatable">
            <thead class="thead-dark">
            <tr>
                <th scope="col">Name</th>
                <th scope="col">Website</th>
                <th scope="col">Agency</th>
                <th scope="col">Total Orders</th>
            </tr>
            </thead>
            <tbody>
            <?php while ($clients->have_posts()) {
                $clients->the_post();
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
                <tr>
                    <td>
                        <a href="<?php the_permalink(); ?>" target="_blank"><?php the_title(); ?></a>
                    </td>
                    <td><?php echo get_field('website', $id); ?></td>
                    <td><a href="<?php echo get_the_permalink($agency); ?>" target="_blank"><?php echo get_the_title($agency); ?></a></td>
                    <td><?php echo $count; ?></td>
                </tr>
            <?php } ?>
            </tbody>
            <tfoot>
            <tr>
                <th scope="col">Name</th>
                <th scope="col">Website</th>
                <th scope="col">Agency</th>
                <th scope="col">Total Orders</th>
            </tr>
            </tfoot>
        </table>
    </div>
<?php } ?>
<?php get_footer(); ?>
