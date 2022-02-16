<?php

get_header();


use MAM\Plugin\Services\Admin\Resources;
use MAM\Plugin\Services\Admin\ImportResources;

function proccess_csv()
{
    // warnings
    $warnings = array();
    // Csv file URL
    $mam_file = get_field('upload_file_resources', 'option');

    // mamdevsite auth
    $mam_file = str_replace(site_url() . '/', ABSPATH, $mam_file);

    // check file exist
    if (!file_exists($mam_file)) {
        return '<h1 class="text text-danger">Error: The uploaded file does not exist.</h1>';
    }
    // init data
    $csv = ImportResources::init_import_csv($mam_file);

    // if error show the error
    if (is_string($csv)) {
        return '<h1 class="text text-danger">Error: ' . $csv . '</h1>';
    }

    $titles = ImportResources::init_import_data_titles($csv->titles);
    $warnings = array_merge($warnings, ImportResources::init_titles_warnings($csv->titles));
    $data = ImportResources::init_import_data($csv->data);
    $sectors = get_terms('sector');

    ob_start();
    ?>
    <div class="import-table-item"><h1>Importing Resources</h1></div>
    <?php
    if (!empty($warnings)) {
        echo '<h1 class="text text-danger">Please Check The Warnings Below:</h1>';
        foreach ($warnings as $warning) {
            echo '<p  style="padding-left:15px;" class="text text-danger"> ' . $warning . '</p>';
        }
    }
    ?>
    <h2 class="existing-h1"><i class="fas fa-sort-down"></i> Existing Resources</h2>
    <div class="existing-content">
        <div class="counter" style="padding-left:15px;"><input type="submit" class="btn btn-primary import-existing-all" value="Import All"> <span class="import-all-completed">0</span> / <span class="import-all-total">0</span></div>
        <?php

        foreach ($data as $row) {
            $_resourcePostID = Resources::get_resource($row['website']);
            ?>
            <?php if ($_resourcePostID) { ?>
                <div class="import-table-item" data-name="<?php echo $row['website']; ?>">
                    <h4><?php echo $row['website']; ?></h4>
                    <form>
                        <table class="dataTable">
                            <thead>
                            <tr>
                                <th></th>
                                <?php
                                foreach ($titles as $title) {
                                    ?>
                                    <th><?php echo strtoupper($title); ?></th>
                                    <?php
                                }
                                ?></tr>
                            </thead>
                            <tbody>
                            <tr class="existing">
                                <td><b>Existing</b></td>
                                <?php
                                foreach ($titles as $title) {
                                    if ($title == 'website') {
                                        $status = '';
                                        if(Resources::get_status($_resourcePostID) == 'Do Not Use'){
                                            $status = '<span class="badge badge-danger">Do Not Use</span>';
                                        }
                                        ?>
                                        <th>
                                            <a data-type="iframe" href="<?php echo get_permalink($_resourcePostID); ?>" target="_blank" data-fancybox="">
                                                <?php echo $status; ?> <?php echo Resources::get_existing_column_text_value($title, $_resourcePostID); ?>
                                            </a>
                                        </th>
                                        <?php
                                    } else {
                                        ?>
                                        <td><?php echo Resources::get_existing_column_text_value($title, $_resourcePostID); ?></td>
                                        <?php
                                    }
                                }
                                ?>
                            </tr>
                            <tr class="new">
                                <td><b>CSV</b></td>
                                <?php
                                foreach ($titles as $title) {
                                    ?>
                                    <td><?php echo $row[$title]; ?></td>
                                    <?php
                                }
                                ?>
                            </tr>
                            <tr class="import">
                                <td><b>Import</b></td>
                                <?php
                                foreach ($titles as $title) {
                                    $finale_value = $row[$title];
                                    if(!$row[$title]){
                                        $finale_value = Resources::get_existing_column_text_value($title, $_resourcePostID);
                                    }
                                    if ($title == 'sectors') {
                                        $_sectors = explode(', ', $finale_value);
                                        ?>

                                        <td class="sector">
                                        <select name="sectors[]" title="Select sectors" data-live-search="true" multiple="multiple"
                                                class="form-control selectpicker" id="sectors">
                                            <?php foreach ($sectors as $sector) { ?>
                                                <option value="<?php echo $sector->name; ?>" <?php if (in_array($sector->name, $_sectors)) { ?> selected <?php } ?>><?php echo $sector->name; ?></option>
                                            <?php } ?>
                                        </select>
                                        </td>
                                    <?php } else { ?>
                                        <td><input type="text" name="<?php echo Resources::get_field_name_by_column_name($title); ?>" value="<?php echo $finale_value; ?>"/></td>
                                    <?php } ?>
                                    <?php
                                }
                                ?>
                            </tr>
                            </tbody>
                        </table>
                        <input type="hidden" name="resourcePostID" value="<?php echo $_resourcePostID; ?>">
                        <input type="hidden" name="action" value="import_existing_resource">
                        <input type="button" class="btn btn-primary import-existing-resource import" value="Import">
                        <input type="button" class="btn btn-primary cancel" data-target="<?php echo $row['website']; ?>" value="Cancel">
                        <input type="button" class="btn btn-primary undo" data-target="<?php echo $row['website']; ?>" value="Undo">
                    </form>
                </div>
            <?php }
        } ?>
    </div>
    <h2 class="new-h1"><i class="fas fa-sort-down"></i> New Resources</h2>
    <div class="new-content">
        <div class="import-table-item">
            <div class="counter" style="padding-left:15px;">
                <input type="submit" class="btn btn-primary import-new-all" value="Import All">
                <span class="import-all-new-completed">0</span> / <span class="import-all-new-total">0</span>
            </div>

            <table>
                <thead>
                <tr>
                    <th>Action</th>
                    <?php
                    foreach ($titles as $title) {
                        ?>
                        <th><?php echo strtoupper($title); ?></th>
                        <?php
                    }
                    ?></tr>
                </thead>
                <tbody>
                </tbody>
            </table>
                <?php
                foreach ($data as $row) {
                    $_resourcePostID = Resources::get_resource($row['website']);
                    ?>
                    <?php if (!$_resourcePostID) { ?>
                        <form>
                            <table>
                            <tr class="import" data-name="<?php echo $row['website']; ?>">
                                <td>
                                    <input type="hidden" name="action" value="import_new_resource">
                                    <input type="button" class="btn btn-primary import import-new-resource" value="Import">
                                    <input type="button" class="btn btn-primary cancel" data-target="<?php echo $row['website']; ?>" value="Cancel">
                                    <input type="button" class="btn btn-primary undo" data-target="<?php echo $row['website']; ?>" value="Undo">
                                </td>
                                <?php
                                foreach ($titles as $title) {
                                    $finale_value = $row[$title];
                                    ?>
                                    <?php
                                    if ($title == 'sectors') {
                                        $_sectors = explode(', ', $finale_value);
                                        ?>

                                        <td class="sector">
                                            <select name="sectors[]" title="Select sectors" data-live-search="true" multiple="multiple"
                                                    class="form-control selectpicker" id="sectors">
                                                <?php foreach ($sectors as $sector) { ?>
                                                    <option value="<?php echo $sector->name; ?>" <?php if (in_array($sector->name, $_sectors)) { ?> selected <?php } ?>><?php echo $sector->name; ?></option>
                                                <?php } ?>
                                            </select>
                                        </td>
                                    <?php } else { ?>
                                        <td><input type="text" name="<?php echo Resources::get_field_name_by_column_name($title); ?>" value="<?php echo $finale_value; ?>"/></td>
                                    <?php } ?>
                                    <?php
                                }
                                ?>
                            </tr>
                            </table>
                        </form>
                    <?php }
                } ?>
        </div>
    </div>
    <?php
    return ob_get_clean();
}


echo proccess_csv();
?>
<?php
get_footer();
?>