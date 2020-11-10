<?php get_header(); ?>
<?php $agencies = apply_filters('mam-agencies-filtered-posts', ''); ?>
<?php if ($agencies->have_posts()) { ?>
    <div class="responsive-table">
        <table class="table datatable">
            <thead class="thead-dark">
            <tr>
                <th scope="col">Name</th>
                <th scope="col">Total Clients</th>
                <th scope="col">Total Orders</th>
            </tr>
            </thead>
            <tbody>
            <?php while ($agencies->have_posts()) {
                $agencies->the_post();
                $_id = get_the_ID();
                $filters = array();
                $filters['agency'] = $_id;
                /**
                 * @var $orders WP_Query
                 */
                $clients = apply_filters('mam-clients-filtered-posts', $filters);
                $count = $clients->post_count;
                $orders = apply_filters('mam-orders-filtered-posts', $filters);
                $orderCount = $orders->post_count;
                ?>
                <tr>
                    <td>
                        <a href="<?php echo get_the_permalink($_id); ?>" target="_blank"><?php echo get_the_title($_id); ?></a>
                    </td>
                    <td><?php echo $count; ?></td>
                    <td><?php echo $orderCount; ?></td>
                </tr>
            <?php } ?>
            </tbody>
            <tfoot>
            <tr>
                <th scope="col">Name</th>
                <th scope="col">Total Clients</th>
                <th scope="col">Total Orders</th>
            </tr>
            </tfoot>
        </table>
    </div>
<?php } ?>
<?php get_footer(); ?>
