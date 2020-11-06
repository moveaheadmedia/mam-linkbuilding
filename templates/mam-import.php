<?php get_header(); ?>
<main id="content">
    <div class="container">
        <?php

        global $mam_type, $mam_newLines, $mam_updatingLines, $mam_action, $mam_file,
               $mam_duplicatedLines, $mam_errorLines, $mam_csv, $mam_new_sectors,
               $mam_new_order, $mam_existing_order, $mam_new_client, $mam_existing_client, $mam_new_agency, $mam_existing_agency;

        // Import the file OR Check only
        $mam_action = get_field('action', 'option');
        // Csv file URL
        $mam_file = get_field('upload_file', 'option');

        // Resources or Orders
        $mam_type = 'Resources';
        // list of line numbers
        $mam_newLines = array();
        $mam_updatingLines = array();
        $mam_duplicatedLines = array();
        $mam_errorLines = array();
        $mam_new_sectors = array();
        $mam_new_order = array();
        $mam_existing_order = array();
        $mam_new_client = array();
        $mam_existing_client = array();
        $mam_new_agency = array();
        $mam_existing_agency = array();


        // check if the file exist then convert it to array
        if (url_exists($mam_file)) {
            $mam_csv = array_map('str_getcsv', file($mam_file));
        } else {
            die('Error: The uploaded file does not exist');
        }

        $mam_headers = array_shift($mam_csv);
        $mam_type = get_type($mam_headers);
        setLines($mam_csv);

        echo '<h1>File Type: ' . $mam_type . '</h1>';

        if (!empty($mam_new_order)) {
            echo '<h3>New Orders: (' . count($mam_new_order) . ')</h3>';
            echo '<div>' . implode('<br /> ', $mam_new_order) . '</div>';
        }

        if (!empty($mam_existing_order)) {
            echo '<h3>Existing Orders: (' . count($mam_existing_order) . ')</h3>';
            echo '<div>' . implode('<br /> ', $mam_existing_order) . '</div>';
        }

        if (!empty($mam_new_client)) {
            echo '<h3>New Clients: (' . count($mam_new_client) . ')</h3>';
            echo '<div>' . implode('<br /> ', $mam_new_client) . '</div>';
        }

        if (!empty($mam_existing_client)) {
            echo '<h3>Existing Clients: (' . count($mam_existing_client) . ')</h3>';
            echo '<div>' . implode('<br /> ', $mam_existing_client) . '</div>';
        }

        if (!empty($mam_new_agency)) {
            echo '<h3>New Agency: (' . count($mam_new_agency) . ')</h3>';
            echo '<div>' . implode('<br /> ', $mam_new_agency) . '</div>';
        }

        if (!empty($mam_existing_agency)) {
            echo '<h3>Existing Agency: (' . count($mam_existing_agency) . ')</h3>';
            echo '<div>' . implode('<br /> ', $mam_existing_agency) . '</div>';
        }

        if (!empty($mam_newLines)) {
            echo '<h3>New Resources: (' . count($mam_newLines) . ')</h3>';
            echo '<div>' . implode('<br /> ', $mam_newLines) . '</div>';
        }

        if (!empty($mam_updatingLines)) {
            echo '<h3>Existing Resources: (' . count($mam_updatingLines) . ')</h3>';
            echo '<div>' . implode('<br /> ', $mam_updatingLines) . '</div>';
        }

        if (!empty($mam_duplicatedLines)) {
            echo '<h3>Duplicated: (' . count($mam_duplicatedLines) . ')</h3>';
            echo '<div>' . implode('<br /> ', $mam_duplicatedLines) . '</div>';
        }

        if (!empty($mam_errorLines)) {
            echo '<h3>Errors: (' . count($mam_errorLines) . ')</h3>';
            echo '<div>' . implode('<br /> ', $mam_errorLines) . '</div>';
        }

        if (!empty($mam_new_sectors)) {
            echo '<h3>New Sectors: (' . count($mam_new_sectors) . ')</h3>';
            echo '<div>' . implode('<br /> ', $mam_new_sectors) . '</div>';
        }

        if ($mam_action == 'Import the file') {
            echo '<h1>Import the file</h1>';
            $check = true;
            if (!empty($mam_duplicatedLines)) {
                echo '<h2>Please fix the duplicated lines in the file before you import.</h2>';
                $check = false;
            }
            if (!empty($mam_errorLines)) {
                echo '<h2>Please fix the errors in the file before you import.</h2>';
                $check = false;
            }
            if ($check) {
                if ($mam_type == 'Resources') {
                    $count = 1;
                    foreach ($mam_csv as $row) {
                        $resourceData = array(
                            'URL' => $row[0],
                            'Email' => $row[1],
                            'Name' => $row[2],
                            'DA' => $row[3],
                            'DR' => $row[4],
                            'RD' => $row[5],
                            'TR' => $row[6],
                            'Currency' => $row[7],
                            'Original Price' => $row[8],
                            'Casino Price' => $row[9],
                            'CBD Price' => $row[10],
                            'Adult Price' => $row[11],
                            'Link Placement Price' => $row[12],
                            'Package / Discount' => $row[13],
                            'Finale Price' => $row[14],
                            'Payment Method' => $row[15],
                            'Comments' => $row[16],
                            'Secondary Email' => $row[17],
                            'Origin File' => $row[18],
                            'Rating' => $row[19],
                            'Sectors' => $row[20]
                        );
                        // $mam_updatingLines and $mam_newLines
                        $id = post_exists($resourceData['URL'], '', '', 'resources');
                        if ($id) {
                            update_resource($id, $resourceData);
                        } else {
                            $id = wp_insert_post(array(
                                'post_title' => $resourceData['URL'],
                                'post_type' => 'resources',
                                'post_status' => 'publish',
                            ));
                            update_resource($id, $resourceData);
                        }
                        $count = $count + 1;
                    }
                    echo '<h3>Imported Resources: (' . ($count-1) . ')</h3>';
                }
                if ($mam_type == 'Orders') {
                    $count = 1;
                    foreach ($mam_csv as $row) {
                        $orderData = array(
                            'ID' => $row[0],
                            'Client Name' => $row[1],
                            'Client Website' => $row[2],
                            'Agency' => $row[3],
                            'Anchor Text' => $row[4],
                            'Target URL' => $row[5],
                            'Resource URL' => $row[6],
                            'Resource Email' => $row[7],
                            'DA' => $row[8],
                            'RD' => $row[9],
                            'Notes' => $row[10],
                            'Sent To Writers' => $row[11],
                            'Currency' => $row[12],
                            'Price' => $row[13],
                            'Article sent to the site' => $row[14],
                            'Live Link Received' => $row[15],
                            'Live Link' => $row[16],
                            'Paid' => $row[17],
                            'USD Price' => $row[18],
                            'THB Price' => $row[19],
                            'Status' => $row[20],
                            'Sectors' => $row[21]
                        );
                        $agencyID = post_exists($orderData['Agency'], '', '', 'agency');
                        if (!$agencyID) {
                            $agencyID = wp_insert_post(array(
                                'post_title' => $orderData['Agency'],
                                'post_type' => 'agency',
                                'post_status' => 'publish',
                            ));
                        }

                        $clientID = post_exists($orderData['Client Name'], '', '', 'client');
                        if ($clientID) {
                            update_client($clientID, $agencyID);
                        } else {
                            $clientID = wp_insert_post(array(
                                'post_title' => $orderData['Agency'],
                                'post_type' => 'client',
                                'post_status' => 'publish',
                            ));
                            update_client($clientID, $agencyID);
                        }

                        $resourceID = post_exists($orderData['Resource URL'], '', '', 'resources');
                        if (!$resourceID) {
                            $resourceID = wp_insert_post(array(
                                'post_title' => $orderData['Resource URL'],
                                'post_type' => 'resources',
                                'post_status' => 'publish',
                            ));
                            update_resourceOrder($resourceID, $orderData);
                        }

                        $orderID = post_exists($orderData['ID'], '', '', 'order');
                        if ($orderID) {
                            update_order($orderID, $resourceID, $clientID, $orderData);
                        } else {
                            $resourceID = wp_insert_post(array(
                                'post_title' => $orderData['Resource URL'],
                                'post_type' => 'order',
                                'post_status' => 'publish',
                            ));
                            update_order($orderID, $resourceID, $clientID, $orderData);
                        }

                        $count = $count + 1;
                    }
                    echo '<h3>Imported Resources: (' . ($count-1). ')</h3>';
                }
            }
        }

        function update_order($orderID, $resourceID, $clientID, $orderData)
        {

            update_field('client', $clientID, $orderID);
            update_field('resource', $resourceID, $orderID);

            if (isset($orderData['Anchor Text'])) {
                update_field('anchor_text', $orderData['Anchor Text'], $orderID);
            }
            if (isset($orderData['Target URL'])) {
                update_field('target_url', $orderData['Target URL'], $orderID);
            }
            if (isset($orderData['Notes'])) {
                update_field('notes', $orderData['Notes'], $orderID);
            }
            if (isset($orderData['Sent To Writers'])) {
                update_field('sent_to_writers', $orderData['Sent To Writers'], $orderID);
            }
            if (isset($orderData['Currency'])) {
                update_field('currency', $orderData['Currency'], $orderID);
            }
            if (isset($orderData['Price'])) {
                update_field('price', $orderData['Price'], $orderID);
            }
            if (isset($orderData['DA'])) {
                update_field('da', $orderData['DA'], $orderID);
            }
            if (isset($orderData['RD'])) {
                update_field('rd', $orderData['RD'], $orderID);
            }
            if (isset($orderData['Article sent to the site'])) {
                update_field('articles_sent_to_the_sites', $orderData['Article sent to the site'], $orderID);
            }
            if (isset($orderData['Live Link Received'])) {
                update_field('live_link_received', $orderData['Live Link Received'], $orderID);
            }
            if (isset($orderData['Live Link'])) {
                update_field('live_link', $orderData['Live Link'], $orderID);
            }
            if (isset($orderData['Paid'])) {
                update_field('we_paid', $orderData['Paid'], $orderID);
            }
            if (isset($orderData['USD Price'])) {
                update_field('dollar_price', $orderData['USD Price'], $orderID);
            }
            if (isset($orderData['THB Price'])) {
                update_field('baht_price', $orderData['THB Price'], $orderID);
            }
            if (isset($orderData['Status'])) {
                update_field('status', $orderData['Status'], $orderID);
            }
            if (isset($orderData['Sectors'])) {
                $sectors = explode(', ', $orderData['Sectors']);
                wp_set_post_terms($orderID, $sectors, 'sector');
            }
        }

        function update_client($clientID, $agencyID)
        {
            if (isset($agencyID)) {
                update_field('agency', $agencyID, $clientID);
            }
        }

        function update_resourceOrder($resourceID, $orderData)
        {
            update_field('email', $orderData['Resource Email'], $resourceID);
            if (isset($data['DA'])) {
                update_field('da', $orderData['DA'], $resourceID);
            }
            if (isset($data['RD'])) {
                update_field('rd', $orderData['RD'], $resourceID);
            }
            if (isset($data['Original Price'])) {
                update_field('original_price', $orderData['Price'], $resourceID);
            }
        }

        function update_resource($id, $data)
        {
            update_field('email', $data['Email'], $id);
            if (isset($data['Name'])) {
                update_field('contact_name', $data['Name'], $id);
            }
            if (isset($data['DA'])) {
                update_field('da', $data['DA'], $id);
            }
            if (isset($data['DR'])) {
                update_field('dr', $data['DR'], $id);
            }
            if (isset($data['RD'])) {
                update_field('rd', $data['RD'], $id);
            }
            if (isset($data['TR'])) {
                update_field('tr', $data['TR'], $id);
            }
            if (isset($data['Currency'])) {
                update_field('currency', $data['Currency'], $id);
            }
            if (isset($data['Original Price'])) {
                update_field('original_price', $data['Original Price'], $id);
            }
            if (isset($data['Casino Price'])) {
                update_field('casino_price', $data['Casino Price'], $id);
            }
            if (isset($data['CBD Price'])) {
                update_field('cbd_price', $data['CBD Price'], $id);
            }
            if (isset($data['Adult Price'])) {
                update_field('adult_price', $data['Adult Price'], $id);
            }
            if (isset($data['Link Placement Price'])) {
                update_field('link_placement_price', $data['Link Placement Price'], $id);
            }
            if (isset($data['Package / Discount'])) {
                update_field('package__discount', $data['Package / Discount'], $id);
            }
            if (isset($data['Finale Price'])) {
                update_field('payment_method', $data['Finale Price'], $id);
            }
            if (isset($data['Comments'])) {
                update_field('comments', $data['Comments'], $id);
            }
            if (isset($data['Secondary Email'])) {
                update_field('secondary_email', $data['Secondary Email'], $id);
            }
            if (isset($data['Origin File'])) {
                update_field('origin_file', $data['Origin File'], $id);
            }
            if (isset($data['Rating'])) {
                update_field('rating', $data['Rating'], $id);
            }
            if (isset($data['Sectors'])) {
                $sectors = explode(', ', $data['Sectors']);
                wp_set_post_terms($id, $sectors, 'sector');
            }
        }

        function url_exists($url)
        {
            return curl_init($url) !== false;
        }

        function get_type($headers)
        {
            $resourceHeader = array('URL', 'Email', 'Name', 'DA', 'DR', 'RD', 'TR', 'Currency', 'Original Price', 'Casino Price', 'CBD Price', 'Adult Price',
                'Link Placement Price', 'Package / Discount', 'Finale Price', 'Payment Method', 'Comments', 'Secondary Email', 'Origin File', 'Rating', 'Sectors');
            if ($headers === $resourceHeader) {
                return 'Resources';
            }
            $ordersHeader = array('ID', 'Client Name', 'Client Website', 'Agency', 'Anchor Text', 'Target URL', 'Resource URL', 'Resource Email', 'DA', 'RD',
                'Notes', 'Sent To Writers', 'Currency', 'Price', 'Article sent to the site',
                'Live Link Received', 'Live Link', 'Paid', 'USD Price', 'THB Price', 'Status', 'Sectors');
            if ($headers === $ordersHeader) {
                return 'Orders';
            }
            die('Error: Please make sure the excel file matches the example and the fields are in the same order.');
        }

        function setLines($lines)
        {
            global $mam_type, $mam_newLines, $mam_updatingLines,
                   $mam_duplicatedLines, $mam_errorLines, $mam_new_sectors,
                   $mam_new_order, $mam_existing_order, $mam_new_client, $mam_existing_client, $mam_new_agency, $mam_existing_agency;

            if (!is_admin()) {
                require_once(ABSPATH . 'wp-admin/includes/post.php');
            }
            if ($mam_type == 'Resources') {
                $count = 1;
                foreach ($lines as $line) {
                    if (post_exists($line[0], '', '', 'resources')) {
                        $mam_updatingLines[] = $count . ': ' . $line[0];
                    } else {
                        $mam_newLines[] = $count . ': ' . $line[0];
                    }
                    // $mam_duplicatedLines
                    $_count = 1;
                    foreach ($lines as $_line) {
                        if ($line[0] == $_line[0]) {
                            if ($_count != $count) {
                                $mam_duplicatedLines[] = $count . ':' . $_count;
                            }
                        }

                        $_count = $_count + 1;
                    }

                    // $mam_errorLines
                    if (!filter_var(gethostbyname($line[0]), FILTER_VALIDATE_IP)) {
                        $mam_errorLines[] = $count . ': Invalid domain name';
                    }
                    if (!filter_var($line[1], FILTER_VALIDATE_EMAIL)) {
                        $mam_errorLines[] = $count . ': Invalid contact email';
                    }

                    // $mam_new_sectors
                    if (($line[20])) {
                        $sectors = explode(', ', $line[20]);
                        foreach ($sectors as $sector) {
                            if (!term_exists($sector, 'sector')) {
                                if (!in_array($sector, $mam_new_sectors)) {
                                    $mam_new_sectors[] = $sector;
                                }
                            }
                        }
                    }

                    $count = $count + 1;

                }
            }


            if ($mam_type == 'Orders') {
                $count = 1;
                foreach ($lines as $line) {

                    // $mam_new_order, $mam_existing_order
                    if (post_exists($line[0], '', '', 'order')) {
                        $mam_existing_order[] = $count . ': ' . $line[0];
                    } else {
                        $mam_new_order[] = $count . ': ' . $line[0];
                    }

                    // $mam_new_client, $mam_existing_client
                    if (post_exists($line[1], '', '', 'client')) {
                        $mam_existing_client[] = $count . ': ' . $line[1];
                    } else {
                        $mam_new_client[] = $count . ': ' . $line[1];
                    }

                    // $mam_new_agency and $mam_existing_agency
                    if (post_exists($line[3], '', '', 'agency')) {
                        $mam_existing_agency[] = $count . ': ' . $line[3];
                    } else {
                        $mam_new_agency[] = $count . ': ' . $line[3];
                    }

                    // $mam_updatingLines and $mam_newLines
                    if (post_exists($line[6], '', '', 'resources')) {
                        $mam_updatingLines[] = $count . ': ' . $line[6];
                    } else {
                        $mam_newLines[] = $count . ': ' . $line[6];
                    }

                    // $mam_errorLines
                    if (strlen($line[0]) < 2) {
                        $mam_errorLines[] = $count . ': Invalid order ID';
                    }
                    if (strlen($line[1]) < 2) {
                        $mam_errorLines[] = $count . ': Invalid client name';
                    }

                    // $mam_errorLines
                    if (!filter_var(gethostbyname($line[6]), FILTER_VALIDATE_IP)) {
                        $mam_errorLines[] = $count . ': Invalid domain name';
                    }
                    if (!filter_var($line[7], FILTER_VALIDATE_EMAIL)) {
                        $mam_errorLines[] = $count . ': Invalid contact email';
                    }

                    // $mam_duplicatedLines
                    $_count = 1;
                    foreach ($lines as $_line) {
                        if ($line[0] == $_line[0]) {
                            if ($_count != $count) {
                                $mam_duplicatedLines[] = $count . ':' . $_count;
                            }
                        }

                        $_count = $_count + 1;
                    }

                    // $mam_new_sectors
                    if (($line[21])) {
                        $sectors = explode(', ', $line[21]);
                        foreach ($sectors as $sector) {
                            if (!term_exists($sector, 'sector')) {
                                if (!in_array($sector, $mam_new_sectors)) {
                                    $mam_new_sectors[] = $sector;
                                }
                            }
                        }
                    }

                }
            }
        }

        ?>

    </div>
</main>
<?php get_footer(); ?>
