<?php

//define('MY_ROOT_PLUGIN_PATH', WP_PLUGIN_DIR . '/neon-sign-customize');

global $wpdb;
$table = $wpdb->prefix . "nsc_colors";
$result = $wpdb->get_results("SELECT * FROM $table");

$plugin_dir = plugin_dir_url(__FILE__);
// echo MY_ROOT_PLUGIN_PATH;

?>
<div class="container mt-3">

    <h3>Colors Manage</h3>
    <table class="table">
        <thead>
            <tr>
                <th scope="col">#</th>
                <th scope="col">Name</th>
                <th scope="col">Hex</th>
                <th scope="col">Extra Price</th>
                <th scope="col">Status</th>
                <th scope="col">Task</th>
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
                        <?= $data->color_name ?>
                    </td>
                    <td>
                        <?= $data->color_hex ?>
                    </td>
                    <td>
                        <?= '+' . $data->extra_price . ($data->extra_price_type == 0 ? "$" : "%") ?>
                    </td>
                    <td>
                        <?php
                        if ($data->status == 1) {
                            echo "on";
                        } else {
                            echo "off";
                        }
                        ?>
                    </td>
                    <td>
                        <button type="button" class="btn btn-danger nsc-delete-item" data-action="delete_color"
                            data-item-id="<?= $data->color_id ?>">DELETE</button>
                        <a
                            href="<?= admin_url('admin.php?page=neon-sign-customize&func=colors&param=add-new&task=update&color_id=' . $data->color_id) ?>">
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
    <a href="<?= admin_url('admin.php?page=neon-sign-customize&func=colors&param=add-new'); ?>">
        <button class="btn btn-primary">ADD NEW COLORS</button>
    </a>
</div>