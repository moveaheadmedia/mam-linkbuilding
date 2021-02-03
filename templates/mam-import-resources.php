<?php

use ParseCsv\Csv;

get_header();

global $mam_newLines, $mam_updatingLines,
       $mam_duplicatedLines, $mam_errorLines, $mam_new_sectors;


// list of line numbers
$mam_newLines = array();
$mam_updatingLines = array();
$mam_duplicatedLines = array();
$mam_errorLines = array();
$mam_new_sectors = array();

// Import the file OR Check only
$mam_action = get_field('action_resources', 'option');

// Csv file URL
$mam_file = get_field('upload_file_resources', 'option');

// mamdevsite auth
//$mam_file = str_replace('https://mamdevsite.com/', 'https://moveahead:mam@mamdev@mamdevsite.com/', $mam_file);
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
    // check for errors
    $check = true;
    if (!empty($mam_duplicatedLines)) {
        $check = false;
    }
    if (!empty($mam_errorLines)) {
        $check = false;
    }
    $importedResources = 0;
    foreach ($mam_csv as $row) {
        $resourceData = $row;
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
        $importedResources = $importedResources + 1;
    }
}

?>
<main id="content">
    <div class="container">
        <?php

        echo '<h1>Resources: ' . $mam_action . '</h1>';

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

            echo '<h3>Imported Resources: (' . $importedResources . ')</h3>';
        }
        ?>
    </div>
</main>
<?php


function update_resource($id, $data)
{
    update_field('email', $data['Email'], $id);
    if (isset($data['IP Address'])) {
        update_field('ip_address', $data['IP Address'], $id);
    }
    if (!isset($data['IP Address']) || $data['IP Address'] == '') {
        update_field('ip_address', gethostbyname($data['URL']), $id);
    }
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
    if (isset($data['PA'])) {
        update_field('pa', $data['PA'], $id);
    }
    if (isset($data['TF'])) {
        update_field('tf', $data['TF'], $id);
    }
    if (isset($data['CF'])) {
        update_field('cf', $data['CF'], $id);
    }
    if (isset($data['Organic Keywords'])) {
        update_field('organic_keywords', $data['Organic Keywords'], $id);
    }
    if (isset($data['Currency']) && $data['Currency'] != '') {
        update_field('currency', $data['Currency'], $id);
    }else{
        update_field('currency', 'USD', $id);
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
        update_field('price', $data['Finale Price'], $id);
    }
    if (isset($data['Payment Method'])) {
        update_field('payment_method', $data['Payment Method'], $id);
    }
    if (isset($data['Notes'])) {
        update_field('notes', $data['Notes'], $id);
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
    if (isset($data['Status']) && $data['Status'] != '') {
        update_field('status', $data['Status'], $id);
    }else{
        update_field('status', 'Raw', $id);
    }
    if (isset($data['Metrics Update Date'])) {
        update_field('metrics_update_date', $data['Metrics Update Date'], $id);
    }
    if (isset($data['Niche'])) {
        update_field('niche', $data['Niche'], $id);
    }
    if (isset($data['Country'])) {
        update_field('country', $data['Country'], $id);
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

function setLines($lines)
{
    global $mam_newLines, $mam_updatingLines,
           $mam_duplicatedLines, $mam_errorLines, $mam_new_sectors;

    if (!is_admin()) {
        require_once(ABSPATH . 'wp-admin/includes/post.php');
    }
    $count = 2;
    foreach ($lines as $line) {
        if(!$line['URL']){
            continue;
        }
        if (post_exists($line['URL'], '', '', 'resources')) {
            $mam_updatingLines[] = $count . ': ' . $line['URL'];
        } else {
            $mam_newLines[] = $count . ': ' . $line['URL'];
        }
        // $mam_duplicatedLines
        $_count = 2;
        foreach ($lines as $_line) {
            if ($line['URL'] == $_line['URL']) {
                if ($_count != $count) {
                    $mam_duplicatedLines[] = $count . ': ' . $line['URL'] . ' - ' . $_count . ': ' . $_line['URL'];
                }
            }
            $_count = $_count + 1;
        }

        // $mam_new_sectors
        if (isset($line['Sectors'])) {
            $sectors = explode(', ', $line['Sectors']);
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
?>
<?php get_footer(); ?>
