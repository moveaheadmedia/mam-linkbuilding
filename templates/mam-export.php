<?php

if (isset($_GET['export'])) {
    $csvArray = array();
    if ($_GET['export'] == 'resources') {
        /**
         * @var $the_query WP_Query
         */
        $the_query = apply_filters('mam-resources-filtered-posts', $_GET['export']);
        if($the_query->have_posts()){
            $csvArray[] = array('URL', 'Email', 'Name', 'DA', 'DR', 'RD', 'TR', 'Currency', 'Original Price', 'Casino Price', 'CBD Price', 'Adult Price', 'Link Placement Price', 'Package / Discount', 'Finale Price', 'Payment Method', 'Comments', 'Secondary Email', 'Origin File', 'Rating', 'Sectors');
            while ($the_query->have_posts()){
                $the_query->the_post();
                $item = array();
                $id = get_the_ID();
                $item['URL'] = get_the_title();
                $item['Email'] = get_field('email', $id);
                $item['Name'] = get_field('contact_name', $id);
                $item['DA'] = get_field('da', $id);
                $item['DR'] = get_field('dr', $id);
                $item['RD'] = get_field('rd', $id);
                $item['TR'] = get_field('tr', $id);
                $item['Currency'] = get_field('currency', $id);
                $item['Original Price'] = get_field('original_price', $id);
                $item['Casino Price'] = get_field('casino_price', $id);
                $item['CBD Price'] = get_field('cbd_price', $id);
                $item['Adult Price'] = get_field('adult_price', $id);
                $item['Link Placement Price'] = get_field('link_placement_price', $id);
                $item['Package / Discount'] = get_field('package__discount', $id);
                $item['Finale Price'] = get_field('price', $id);
                $item['Payment Method'] = get_field('payment_method', $id);
                $item['Comments'] = get_field('comments', $id);
                $item['Secondary Email'] = get_field('secondary_email', $id);
                $item['Origin File'] = get_field('origin_file', $id);
                $item['Rating'] = get_field('rating', $id);
                $item['Sectors'] = implode(', ', wp_get_object_terms( $id, 'sector', array( 'fields' => 'names' ) ));
                $csvArray[] = $item;
            }
            $output = fopen("php://output",'w') or die("Can't open php://output");
            header("Content-Type:application/csv");
            header("Content-Disposition:attachment;filename=resources.csv");
            foreach($csvArray as $resource) {
                fputcsv($output, $resource);
            }
            fclose($output) or die("Can't close php://output");
        }else{
            echo 'No resources found';
        }
    }
    if ($_GET['export'] == 'orders') {
        $the_query = apply_filters('mam-orders-filtered-posts', $_GET['export']);
        if($the_query->have_posts()){
            $csvArray[] = array('ID', 'Client Name', 'Client Website', 'Agency', 'Anchor Text', 'Target URL', 'Resource URL', 'Resource Email', 'DA', 'RD', 'Notes', 'Sent To Writers', 'Currency', 'Price', 'Article sent to the site', 'Live Link Received', 'Live Link', 'Paid', 'USD Price', 'THB Price', 'Status', 'Sectors');
            while ($the_query->have_posts()){
                $the_query->the_post();
                $item = array();
                $id = get_the_ID();
                $resource = get_field('resource', $id);
                $client = get_field('client', $id);
                $agency = get_field('agency', $client);
                $item['ID'] = get_the_title();
                $item['Client Name'] = get_the_title($client);
                $item['Client Website'] = get_field('website', $client);
                $item['Agency'] = get_the_title($agency);
                $item['Anchor Text'] = get_field('anchor_text', $id);
                $item['Target URL'] = get_field('target_url', $id);
                $item['Resource URL'] = get_the_title($resource);
                $item['Resource Email'] = get_field('email', $resource);
                $item['DA'] = get_field('da', $id);
                $item['RD'] = get_field('rd', $id);
                $item['Notes'] = get_field('notes', $id);
                $item['Sent To Writers'] = get_field('sent_to_writers', $id);
                $item['Currency'] = get_field('currency', $id);
                $item['Price'] = get_field('price', $id);
                $item['Article sent to the site'] = get_field('articles_sent_to_the_sites', $id);
                $item['Live Link Received'] = get_field('live_link_received', $id);
                $item['Live Link'] = get_field('live_link', $id);
                $item['Paid'] = get_field('we_paid', $id);
                $item['USD Price'] = get_field('dollar_price', $id);
                $item['THB Price'] = get_field('baht_price', $id);
                $item['Status'] = get_field('status', $id);
                $item['Sectors'] = implode(', ', wp_get_object_terms( $id, 'sector', array( 'fields' => 'names' ) ));
                $csvArray[] = $item;
            }
            $output = fopen("php://output",'w') or die("Can't open php://output");
            header("Content-Type:application/csv");
            header("Content-Disposition:attachment;filename=orders.csv");
            foreach($csvArray as $order) {
                fputcsv($output, $order);
            }
            fclose($output) or die("Can't close php://output");
        }
    }
}