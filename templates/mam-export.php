<?php

if (isset($_GET['export'])) {
    $csvArray = array();
    if ($_GET['export'] == 'resources') {
        /**
         * @var $the_query WP_Query
         */
        $the_query = apply_filters('mam-resources-filtered-posts', $_GET['export']);
        if($the_query->have_posts()){
            $csvArray[] = array(
                'URL', 'IP Address', 'Email', 'Name', 'DA', 'DR', 'RD', 'TR', 'PA', 'TF', 'CF',
                'Organic Keywords', 'Country', 'Currency', 'Original Price', 'Casino Price', 'CBD Price',
                'Adult Price', 'Link Placement Price', 'Package / Discount', 'Finale Price',
                'Payment Method', 'Notes', 'Secondary Email', 'Origin File', 'Rating', 'Status', 'Metrics Update Date', 'Sectors', 'Niche');
            while ($the_query->have_posts()){
                $the_query->the_post();
                $item = array();
                $id = get_the_ID();
                $item['URL'] = get_the_title();
                $item['IP Address'] = get_field('ip_address', $id);
                $item['Email'] = get_field('email', $id);
                $item['Name'] = get_field('contact_name', $id);
                $item['DA'] = get_field('da', $id);
                $item['DR'] = get_field('dr', $id);
                $item['RD'] = get_field('rd', $id);
                $item['TR'] = get_field('tr', $id);
                $item['PA'] = get_field('pa', $id);
                $item['TF'] = get_field('tf', $id);
                $item['CF'] = get_field('cf', $id);
                $item['Organic Keywords'] = get_field('organic_keywords', $id);
                $item['Country'] = get_field('country', $id);
                $item['Currency'] = get_field('currency', $id);
                $item['Original Price'] = get_field('original_price', $id);
                $item['Casino Price'] = get_field('casino_price', $id);
                $item['CBD Price'] = get_field('cbd_price', $id);
                $item['Adult Price'] = get_field('adult_price', $id);
                $item['Link Placement Price'] = get_field('link_placement_price', $id);
                $item['Package / Discount'] = get_field('package__discount', $id);
                $item['Finale Price'] = get_field('price', $id);
                $item['Payment Method'] = get_field('payment_method', $id);
                $item['Notes'] = get_field('notes', $id);
                $item['Secondary Email'] = get_field('secondary_email', $id);
                $item['Origin File'] = get_field('origin_file', $id);
                $item['Rating'] = get_field('rating', $id);
                $item['Status'] = get_field('status', $id);
                $item['Metrics Update Date'] = get_field('metrics_update_date', $id);
                $item['Sectors'] = implode(', ', wp_get_object_terms( $id, 'sector', array( 'fields' => 'names' ) ));
                $item['Niche'] = get_field('niche', $id);
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
            $csvArray[] = array('ID', "Client Name", "Client Website", "Agency", "Anchor Text", "Target URL", "Niche", "Sent To Writers",
                "Article sent to the site", "Live Link Received", "Live Link", "Date Paid", "USD Price", "THB Price", "Status",
                "Start Date", "Complete Date", "Sectors", "Resource URL", "IP Address", "Email", "Name","DA", "DR", "RD", "TR", "PA", "TF", "CF",
                "Organic Keywords", "Currency", "Original Price", "Casino Price", "CBD Price", "Adult Price", "Link Placement Price",
                "Package / Discount", "Finale Price", "Payment Method","Notes", "Secondary Email", "Origin File", "Rating",
                "Metrics Update Date");
            while ($the_query->have_posts()){
                $the_query->the_post();
                $id = get_the_ID();
                $client = get_field('client', $id);
                $agency = get_the_title(get_field('agency', $client));

                $orderData = array(
                    "ID" => get_the_title($id),
                    "Client Name" => get_the_title($client),
                    "Client Website" => get_field('website', $client),
                    "Agency" => $agency,
                    "Anchor Text Type" => get_field('anchor_text_type', $id),
                    "Anchor Text" => get_field('anchor_text', $id),
                    "Target URL" => get_field('target_url', $id),
                    "Niche" => get_field('niche', $id),
                    "Sent To Writers" => get_field('sent_to_writers', $id),
                    "Article sent to the site" => get_field('articles_sent_to_the_sites', $id),
                    "Live Link Received" => get_field('live_link_received', $id),
                    "Live Link" => get_field('live_link', $id),
                    "Date Paid" => get_field('date_paid', $id),
                    "USD Price" => get_field('usd_price', $id),
                    "THB Price" => get_field('thb_price', $id),
                    "Status" => get_field('status', $id),
                    "Start Date" => get_field('start_date', $id),
                    "Complete Date" => get_field('complete_date', $id),
                    "Sectors" => implode(', ', wp_get_object_terms($id, 'sector', array('fields' => 'names'))),
                    "Resource URL" => get_field('resource_url', $id),
                    "IP Address" => get_field('ip_address', $id),
                    "Email" => get_field('email', $id),
                    "Name" => get_field('contact_name', $id),
                    "DA" => get_field('da', $id),
                    "DR" => get_field('dr', $id),
                    "RD" => get_field('rd', $id),
                    "TR" => get_field('tr', $id),
                    "PA" => get_field('pa', $id),
                    "TF" => get_field('tf', $id),
                    "CF" => get_field('cf', $id),
                    "Organic Keywords" => get_field('organic_keywords', $id),
                    "Country" => get_field('country', $id),
                    "Currency" => get_field('currency', $id),
                    "Original Price" => get_field('original_price', $id),
                    "Casino Price" => get_field('casino_price', $id),
                    "CBD Price" => get_field('cbd_price', $id),
                    "Adult Price" => get_field('adult_price', $id),
                    "Link Placement Price" => get_field('link_placement_price', $id),
                    "Package / Discount" => get_field('package__discount', $id),
                    "Finale Price" => get_field('price', $id),
                    "Payment Method" => get_field('payment_method', $id),
                    "Notes" => get_field('notes', $id),
                    "Secondary Email" => get_field('secondary_email', $id),
                    "Origin File" => get_field('origin_file', $id),
                    "Rating" => get_field('rating', $id),
                    "Metrics Update Date" => get_field('metrics_update_date', $id)
                );
                $csvArray[] = $orderData;
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