<?php

use MAM\Plugin\Services\Admin\Orders;
use ParseCsv\Csv;

// init global data
global $mam_action, $mam_file,$importedOrders,
       $mam_duplicatedLines, $mam_errorLines, $mam_csv, $mam_new_sectors,
       $mam_new_order, $mam_existing_order, $mam_new_client, $mam_existing_client, $mam_new_agency, $mam_existing_agency;

// list of line numbers
$mam_duplicatedLines = array();
$mam_errorLines = array();
$mam_new_sectors = array();
$mam_new_order = array();
$mam_existing_order = array();
$mam_new_client = array();
$mam_existing_client = array();
$mam_new_agency = array();
$mam_existing_agency = array();

// Import the file OR Check only
$mam_action = get_field('action_orders', 'option');

// Csv file URL
$mam_file = get_field('upload_file_orders', 'option');

// mamdevsite auth
$mam_file = str_replace(site_url().'/', ABSPATH, $mam_file);

// check if the file exist then convert it to array
$mam_csv = array();
if (file_exists($mam_file)) {
    $newfile = 'import.csv';
    if (!copy($mam_file, $newfile)) {
        echo "failed to copy $mam_file...\n";
    }
    $csv   = new Csv($newfile);
    $mam_csv   = $csv->data;
} else {
    $mam_errorLines[] = ('Error: The uploaded file does not exist');
}

// check for errors in the CSV file
$res = array();
foreach ($mam_csv as $item){
    $res[] = array_map('trim', $item);
}
setLines($res);

// Import the file
if ($mam_action == 'Import the file') {
    $check = true;
    if (!empty($mam_duplicatedLines)) {
        $check = false;
    }
    if (!empty($mam_errorLines)) {
        $check = false;
    }
    if ($check) {
        $importedOrders = 1;
        foreach ($mam_csv as $orderData) {
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
                update_client($clientID, $agencyID, $orderData);
            } else {
                $clientID = wp_insert_post(array(
                    'post_title' => $orderData['Client Name'],
                    'post_type' => 'client',
                    'post_status' => 'publish',
                ));
                update_client($clientID, $agencyID, $orderData);
            }

            $orderID = post_exists($orderData['ID'], '', '', 'lborder');
            if ($orderID) {
                Orders::update_order($orderID, $clientID, $orderData);
            } else {
                $orderID = wp_insert_post(array(
                    'post_title' => $orderData['ID'],
                    'post_type' => 'lborder',
                    'post_status' => 'publish',
                ));
                Orders::update_order($orderID, $clientID, $orderData);
            }
            $importedOrders = $importedOrders + 1;
        }
    }
}

get_header(); ?>
<main id="content">
    <div class="container">
        <?php


        echo '<h1>Orders: ' . $mam_action . '</h1>';

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
                echo '<h3>Imported Orders: (' . ($importedOrders -1) . ')</h3>';
            }
        }

        ?>

    </div>
</main>

<?php


function setLines($lines)
{
    global $mam_duplicatedLines, $mam_errorLines, $mam_new_sectors,
           $mam_new_order, $mam_existing_order, $mam_new_client, $mam_existing_client,
           $mam_new_agency, $mam_existing_agency;

    if (!is_admin()) {
        require_once(ABSPATH . 'wp-admin/includes/post.php');
    }

    $count = 1;
    foreach ($lines as $line) {

        // $mam_new_order, $mam_existing_order
        if (post_exists($line['ID'], '', '', 'lborder')) {
            $mam_existing_order[] = $count . ': ' . $line['ID'];
        } else {
            $mam_new_order[] = $count . ': ' . $line['ID'];
        }

        // $mam_new_client, $mam_existing_client
        if (post_exists($line['Client Name'], '', '', 'client')) {
            $mam_existing_client[] = $count . ': ' . $line['Client Name'];
        } else {
            $mam_new_client[] = $count . ': ' . $line['Client Name'];
        }

        // $mam_new_agency and $mam_existing_agency
        if (post_exists($line['Agency'], '', '', 'agency')) {
            $mam_existing_agency[] = $count . ': ' . $line['Agency'];
        } else {
            $mam_new_agency[] = $count . ': ' . $line['Agency'];
        }

        // $mam_errorLines
        if (strlen($line['ID']) < 2) {
            $mam_errorLines[] = $count . ': Invalid ID';
        }
        if (strlen($line['Client Name']) < 2) {
            $mam_errorLines[] = $count . ': Invalid Client Name';
        }
        if (strlen($line['Client Website']) < 2) {
            $mam_errorLines[] = $count . ': Invalid client Website';
        }
        if (strlen($line['Agency']) < 2) {
            $mam_errorLines[] = $count . ': Invalid Agency name';
        }
        if (strlen($line['Anchor Text']) < 2) {
            $mam_errorLines[] = $count . ': Invalid Anchor Text';
        }
        if (strlen($line['Target URL']) < 2) {
            $mam_errorLines[] = $count . ': Invalid Target URL';
        }

        // $mam_duplicatedLines
        $_count = 1;
        foreach ($lines as $_line) {
            if ($line['ID'] == $_line['ID']) {
                if ($_count != $count) {
                    $mam_duplicatedLines[] = ($count + 1). ':' . ($_count + 1);
                }
            }

            $_count = $_count + 1;
        }

        // $mam_new_sectors
        if (isset($line['Sectors']) && $line['Sectors'] != '') {
            $sectors = explode(', ', $line['Sectors']);
            foreach ($sectors as $sector) {
                if (!term_exists($sector, 'sector')) {
                    if (!in_array($sector, $mam_new_sectors)) {
                        $mam_new_sectors[] = $count . ': ' . $sector;
                    }
                }
            }
        }
        $count = $count + 1;
    }
}

function update_client($clientID, $agencyID, $orderData)
{
    if (isset($agencyID)) {
        update_field('agency', $agencyID, $clientID);
    }
    if (isset($orderData['Client Website'])) {
        update_field('website', $orderData['Client Website'], $clientID);
    }
}

function url_exists($url)
{
    return curl_init($url) !== false;
}

?>

<?php get_footer(); ?>
