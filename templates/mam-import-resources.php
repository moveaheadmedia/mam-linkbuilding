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
    $item = array_change_key_case($item, CASE_LOWER);
    $sectors = array();
    $count = 100;
    for($i = 1; $i <= $count; $i++){
        if(isset($item['sector'.$i])){
            $sectors[] = $item['sector'.$i];
        }else{
            break;
        }
    }
    $item['sectors'] = implode(', ', $sectors);
    $res[] = array_map('trim', $item);
}

setLines($res);

// Import the file
if ($mam_action == 'Import the file') {
    // check for errors
    $check = true;
    if (!empty($mam_errorLines)) {
        $check = false;
    }

    $importedResources = 0;
    if($check){
        foreach ($res as $row) {
            $resourceData = $row;
            // $mam_updatingLines and $mam_newLines
            $id = post_exists($resourceData['url'], '', '', 'resources');
            if ($id) {
                update_resource($id, $resourceData);
            } else {
                $id = wp_insert_post(array(
                    'post_title' => $resourceData['url'],
                    'post_type' => 'resources',
                    'post_status' => 'publish',
                ));
                update_resource($id, $resourceData);
            }
            $importedResources = $importedResources + 1;
        }
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
            if($check){
                echo '<h3>Imported Resources: (' . $importedResources . ')</h3>';
            }
        }
        ?>
    </div>
</main>
<?php


function update_resource($id, $data)
{
    if (isset($data['email'])) {
        update_field('email', $data['email'], $id);
    }
    if (isset($data['ip address'])) {
        update_field('ip_address', $data['ip address'], $id);
    }
    if (!isset($data['ip address']) || $data['ip address'] == '') {
        update_field('ip_address', gethostbyname($data['url']), $id);
    }
    if (isset($data['name'])) {
        update_field('contact_name', $data['name'], $id);
    }
    if (isset($data['da']) && $data['da'] != '') {
        update_field('da', $data['da'], $id);
    }else{
        update_field('da', '0', $id);
    }
    if (isset($data['dr']) && $data['dr'] != '') {
        update_field('dr', $data['dr'], $id);
    }else{
        update_field('dr', '0', $id);
    }
    if (isset($data['rd'])  && $data['rd'] != '') {
        update_field('rd', $data['rd'], $id);
    }else{
        update_field('rd', '0', $id);
    }
    if (isset($data['tr'])  && $data['tr'] != '') {
        update_field('tr', $data['tr'], $id);
    }else{
        update_field('tr', '0', $id);
    }
    if (isset($data['pa']) &&  $data['pa'] != '') {
        update_field('pa', $data['pa'], $id);
    }else{
        update_field('pa', '0', $id);
    }
    if (isset($data['tf']) &&  $data['tf'] != '') {
        update_field('tf', $data['tf'], $id);
    }else{
        update_field('tf', '0', $id);
    }
    if (isset($data['cf']) &&  $data['cf'] != '') {
        update_field('cf', $data['cf'], $id);
    }else{
        update_field('cf', '0', $id);
    }
    if (isset($data['organic keywords']) &&  $data['organic keywords'] != '') {
        update_field('organic_keywords', $data['organic keywords'], $id);
    }else{
        update_field('organic_keywords', '0', $id);
    }
    if (isset($data['currency']) && $data['currency'] != '') {
        update_field('currency', $data['currency'], $id);
    }else{
        update_field('currency', 'USD', $id);
    }
    if (isset($data['original price']) &&  $data['original price'] != '') {
        update_field('original_price', $data['original price'], $id);
    }else{
        update_field('original_price', '0', $id);
    }
    if (isset($data['casino price'])) {
        update_field('casino_price', $data['casino price'], $id);
    }
    if (isset($data['cbd price'])) {
        update_field('cbd_price', $data['cbd price'], $id);
    }
    if (isset($data['adult price'])) {
        update_field('adult_price', $data['adult price'], $id);
    }
    if (isset($data['link placement price'])) {
        update_field('link_placement_price', $data['link placement price'], $id);
    }
    if (isset($data['package / discount'])) {
        update_field('package__discount', $data['package / discount'], $id);
    }
    if (isset($data['finale price']) && $data['finale price'] != '') {
        update_field('price', $data['finale price'], $id);
    }else{
        update_field('price', '0', $id);
    }
    if (isset($data['payment method'])) {
        update_field('payment_method', $data['payment method'], $id);
    }
    if (isset($data['notes'])) {
        update_field('notes', $data['notes'], $id);
    }
    if (isset($data['secondary email'])) {
        update_field('secondary_email', $data['secondary email'], $id);
    }
    if (isset($data['origin file'])) {
        update_field('origin_file', $data['origin file'], $id);
    }
    if (isset($data['rating'])) {
        update_field('rating', $data['rating'], $id);
    }
    if (isset($data['status']) && $data['status'] != '') {
        update_field('status', $data['status'], $id);
    }else{
        update_field('status', 'Raw', $id);
    }
    if (isset($data['metrics update date'])) {
        update_field('metrics_update_date', $data['metrics update date'], $id);
    }
    if (isset($data['niche'])) {
        update_field('niche', $data['niche'], $id);
    }
    if (isset($data['country'])) {
        update_field('country', $data['country'], $id);
    }
    if (isset($data['new remarks'])) {
        update_field('new_remarks', $data['new remarks'], $id);
    }
    if (isset($data['social media'])) {
        update_field('social_media', $data['social media'], $id);
    }
    if (isset($data['other info'])) {
        update_field('other_info', $data['other info'], $id);
    }
    if (isset($data['contact / email'])) {
        update_field('contact__email', $data['contact / email'], $id);
    }
    if (isset($data['sectors'])) {
        $sectors = explode(', ', $data['sectors']);
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
           $mam_duplicatedLines, $mam_errorLines;

    if (!is_admin()) {
        require_once(ABSPATH . 'wp-admin/includes/post.php');
    }
    $count = 2;

    foreach ($lines as $line) {
        if(!$line['url']){
            continue;
        }
        if (post_exists($line['url'], '', '', 'resources')) {
            $mam_updatingLines[] = $count . ': ' . $line['url'];
        } else {
            $mam_newLines[] = $count . ': ' . $line['url'];
        }
        // $mam_duplicatedLines
        $_count = 2;
        foreach ($lines as $_line) {
            if ($line['url'] == $_line['url']) {
                if ($_count != $count) {
                    $mam_duplicatedLines[] = $count . ': ' . $line['url'] . ' - ' . $_count . ': ' . $_line['url'];
                }
            }
            $_count = $_count + 1;
        }

        // $mam_new_sectors
        if (isset($line['sectors'])) {
            $sectors = explode(', ', $line['sectors']);
            foreach ($sectors as $sector) {
                if($sector == ',' || $sector == ''){
                    continue;
                }
                if (!term_exists($sector, 'sector')) {
                    if (!in_array($sector, $mam_errorLines)) {
                        $mam_errorLines[] = 'Sector Does not Exist: '.$sector;
                    }
                }
            }
        }
        $count = $count + 1;
    }
}
?>
<?php get_footer(); ?>
