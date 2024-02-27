<?php

//define('MY_ROOT_PLUGIN_PATH', WP_PLUGIN_DIR . '/neon-sign-customize');

global $wpdb;
$table = $wpdb->prefix . "nsc_backboard";
$result = $wpdb->get_results("SELECT * FROM $table");

$plugin_dir = plugin_dir_url(__FILE__);
// echo MY_ROOT_PLUGIN_PATH;

?>
<div class="container mt-3">

    <h3>Backboard Color Manage</h3>
    <table class="table">
        <thead>
            <tr>
                <th scope="col">#</th>
                <th scope="col" style="width:150px">Name</th>
                <th scope="col" style="width:300px">Description</th>
                <!-- <th scope="col">Image URL</th> -->
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
                        <?= $data->backboard_name ?>
                    </td>
                    <td>
                        <?= $data->backboard_des ?>
                    </td>
                    <!-- <td>
                        <img src=" <?= MY_ROOT_PLUGIN_PATH . "/public/partials/img/" . $data->backboard_img ?>" />
                        <?= $data->backboard_img ?>
                    </td> -->
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
                        <a
                            href="<?= admin_url('admin.php?page=neon-sign-customize&func=backboard&param=add-new&task=update&backboard_id=' . $data->backboard_id) ?>">
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
</div>