<?php
global $wpdb;
$table = $wpdb->prefix . "nsc_additional";
$result = null;
$type = sanitize_text_field($_REQUEST['type']);
$title = ucfirst($type);
if ($type == "cable_color") {
    $title = "Cable Color";
}
if ($type == "remote_control") {
    $title = "Remote Control";
}
if ($type == "backboard_color") {
    $title = "Backboard Color";
}
if ($type == "plug_type") {
    $title = "Plug Type";
}
if (isset($type)) {
    $result = $wpdb->get_results("SELECT * FROM $table WHERE `additional_type`='" . $type . "'");
    ?>
<div class="container mt-3">
    <h3>
        <?= $title ?> Manage
    </h3>
    <table class="table">
        <thead>
            <tr>
                <th scope="col">#</th>
                <th scope="col">Title</th>
                <th scope="col" style="width:250px">Description</th>
                <th scope="col">Image</th>
                <th scope="col" <?= ($type == "background" ? "style='display:none'" : "style='display:block'") ?>>Extra
                    Price</th>
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
                    <?= $data->additional_name ?>
                </td>
                <td>
                    <?= $data->additional_des ?>
                </td>
                <td>
                    <?= $data->additional_img ?>
                </td>
                <td <?= ($type == "background" ? "style='display:none'" : "style='display:block'") ?>>
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
                    <button type="button" class="btn btn-danger nsc-delete-item" data-action="delete_additional"
                        data-item-id="<?= $data->additional_id ?>">DELETE</button>
                    <a
                        href="<?= admin_url('admin.php?page=neon-sign-customize&func=additional&type=' . $type . '&param=add-new&task=update&additional_id=' . $data->additional_id) ?>">
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
    <a href="<?= admin_url('admin.php?page=neon-sign-customize&func=additional&type=' . $type . '&param=add-new'); ?>">
        <button class="btn btn-primary">ADD NEW
            <?= strtoupper($title) ?>
        </button>
    </a>
</div>
<?php
}


?>