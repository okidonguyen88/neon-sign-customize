<?php

//define('MY_ROOT_PLUGIN_PATH', WP_PLUGIN_DIR . '/neon-sign-customize');

global $wpdb;
$table = $wpdb->prefix . "nsc_sizes";
$result = $wpdb->get_results("SELECT * FROM $table");

$plugin_dir = plugin_dir_url(__FILE__);
// echo MY_ROOT_PLUGIN_PATH;

?>
<div class="container mt-3">

    <h3>Sizes Manage</h3>
    <table class="table">
        <thead>
            <tr>
                <th scope="col">#</th>
                <th scope="col" style="width:120px">Title</th>
                <th scope="col" style="min-width:150px">Font Size (px)</th>
                <th scope="col">Volumetric</th>
                <th scope="col">Shipping Fee</th>
                <th scope="col">Length</th>
                <th scope="col">Max Width</th>
                <th scope="col">Max Height</th>
                <th scope="col">MIN</th>
                <th scope="col">MAX</th>
                <th scope="col" style="width:200px">Task</th>
            </tr>
        </thead>
        <tbody>
            <?php
            $count = 1;
            foreach ($result as $data) {
                ?>
                <tr>
                    <th scope="row">
                        <?= $count ?>
                    </th>
                    <td>
                        <?= $data->size_name ?>
                    </td>
                    <td>
                        <?= $data->size_des . " px" ?>
                    </td>

                    <td>
                        <?= $data->size_vol . "" ?>
                    </td>
                    <td>
                        <?= "$ " . $data->size_fee ?>
                    </td>
                    <td>
                        <?= $data->size_length . " cm" ?>
                    </td>
                    <td>
                        <?= $data->text_width . " cm" ?>
                    </td>
                    <td>
                        <?= $data->text_height . " cm" ?>
                    </td>
                    <td>
                        <?= $data->size_char_min ?>
                    </td>
                    <td>
                        <?= $data->size_char_max ?>
                    </td>

                    <td>
                        <button type="button" class="btn btn-danger nsc-delete-item" data-action="delete_size"
                            data-item-id="<?= $data->size_id ?>">DELETE</button>
                        <a
                            href="<?= admin_url('admin.php?page=neon-sign-customize&func=sizes&param=add-new&task=update&size_id=' . $data->size_id) ?>">
                            <button type="button" class="btn btn-warning">EDIT</button>
                        </a>
                    </td>
                </tr>
                <?php
                $count++;
            }
            ?>

        </tbody>
    </table>
    <a href="<?= admin_url('admin.php?page=neon-sign-customize&func=sizes&param=add-new'); ?>">
        <button class="btn btn-primary">ADD NEW SIZES</button>
    </a>
</div>