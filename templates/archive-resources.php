<?php get_header(); ?>
<?php $the_query = apply_filters('mam-resources-filtered-posts', 'resources'); ?>
<?php if ($the_query->have_posts()) { ?>
    <div class="container">
        <div class="filters">
            <br />
            <h2>Filter:</h2>
            <?php
            $da = '';
            $dr = '';
            $rd_minimum = '';
            $tr = '';
            $price = '';
            $sectors = array();
            ?>
            <form method="get" action="">
                <div class="form-group">
                    <label for="da">DA</label>
                    <b>0</b> <input type="text" id="da" name="da" class="slider" value="" data-slider-min="0" data-slider-max="100" data-slider-step="1" data-slider-value="[0,100]"/> <b>100</b>
                </div>
                <div class="form-group">
                    <label for="dr">DR</label>
                    <b>0</b> <input id="dr" type="text" name="dr" class="slider" value="" data-slider-min="0" data-slider-max="100" data-slider-step="1" data-slider-value="[0,100]"/> <b>100</b>
                </div>

                <div class="form-group">
                    <label for="exampleInputEmail1">Email address</label>
                    <input type="email" class="form-control" id="exampleInputEmail1" aria-describedby="emailHelp" placeholder="Enter email">
                </div>

            </form>
        </div>
    </div>
    <div class="responsive-table">
        <table class="table datatable">
            <thead class="thead-dark">
            <tr>
                <th scope="col">Website</th>
                <th scope="col">Contact Email</th>
                <th scope="col">DA</th>
                <th scope="col">DR</th>
                <th scope="col">RD</th>
                <th scope="col">TR</th>
                <th scope="col">Price</th>
                <th scope="col">Sectors</th>
            </tr>
            </thead>
            <tbody>
            <?php while ($the_query->have_posts()) {
                $the_query->the_post();
                $id = get_the_ID(); ?>
                <tr>
                    <td>
                        <a data-type="iframe" href="<?php the_permalink(); ?>" target="_blank" data-fancybox><?php the_title(); ?></a>
                        <div style="display: none;">
                            <?php echo get_field('contact_name', $id); ?>
                            <?php echo get_field('comments', $id); ?>
                            <?php echo get_field('rating', $id); ?>
                            <?php echo get_field('origin_file', $id); ?>
                        </div>
                    </td>
                    <td><?php echo get_field('email', $id); ?></td>
                    <td><?php echo get_field('da', $id); ?></td>
                    <td><?php echo get_field('dr', $id); ?></td>
                    <td><?php echo get_field('rd', $id); ?></td>
                    <td><?php echo get_field('tr', $id); ?></td>
                    <td>
                        <div style="display: none;" id="price-<?php echo $id; ?>">
                            <?php
                            $currency = get_field('currency', $id);
                            if (!$currency) {
                                $currency = 'USD';
                            }
                            $finalePrice = '-';
                            $ogPrice = get_field('original_price', $id);
                            if ($ogPrice) {
                                echo '<p>Original Price: ' . $ogPrice . ' ' . $currency . '</p>';
                                $finalePrice = $ogPrice . ' ' . $currency;
                            }
                            $casino_price = get_field('casino_price', $id);
                            if ($casino_price) {
                                echo '<p>Casino Price: ' . $casino_price . ' ' . $currency . '</p>';
                            }
                            $cbd_price = get_field('cbd_price', $id);
                            if ($cbd_price) {
                                echo '<p>CBD Price: ' . $cbd_price . ' ' . $currency . '</p>';
                            }
                            $adult_price = get_field('adult_price', $id);
                            if ($adult_price) {
                                echo '<p>Adult Price: ' . $adult_price . ' ' . $currency . '</p>';
                            }
                            $link_placement_price = get_field('link_placement_price', $id);
                            if ($link_placement_price) {
                                echo '<p>Link Placement Price: ' . $link_placement_price . ' ' . $currency . '</p>';
                            }
                            $price = get_field('price', $id);
                            if ($price) {
                                echo '<p>Finale Price: ' . $price . ' ' . $currency . '</p>';
                                $finalePrice = $price . ' ' . $currency;
                            }
                            $discount_package = get_field('package__discount', $id);
                            if ($discount_package) {
                                echo '<p>Discount / Package: ' . $discount_package . '</p>';
                            }
                            $payment_method = get_field('payment_method', $id);
                            if ($payment_method) {
                                echo '<p>Payment Method: ' . $payment_method . '</p>';
                            }
                            ?>
                        </div>
                        <a href="price-<?php echo $id; ?>" data-fancybox data-src="#price-<?php echo $id; ?>"><?php echo $finalePrice; ?></a>
                    </td>
                    <td><?php echo implode(', ', wp_get_object_terms($id, 'sector', array('fields' => 'names'))); ?></td>
                </tr>
            <?php } ?>
            </tbody>
            <tfoot>
            <tr>
                <th scope="col">Website</th>
                <th scope="col">Contact Email</th>
                <th scope="col">DA</th>
                <th scope="col">DR</th>
                <th scope="col">RD</th>
                <th scope="col">TR</th>
                <th scope="col">Price</th>
                <th scope="col">Sectors</th>
            </tr>
            </tfoot>
        </table>
    </div>
<?php } ?>
<?php get_footer(); ?>
