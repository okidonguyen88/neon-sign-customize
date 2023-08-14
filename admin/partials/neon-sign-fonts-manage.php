<?php
global $wpdb;
$table = $wpdb->prefix . "nsc_fonts";
$result = $wpdb->get_results("SELECT * FROM $table");

?>
<div class="container mt-5">

    <h1>Quản lý Fonts - Danh sách Fonts</h1>
    <table class="table">
        <thead>
            <tr>
                <th scope="col">#</th>
                <th scope="col">Name</th>
                <th scope="col">Short Name</th>
                <th scope="col">URL</th>
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
                        <?= $data->font_name ?>
                    </td>
                    <td>
                        <?= $data->font_short ?>
                    </td>
                    <td>
                        <?= $data->font_url ?>
                    </td>
                    <td>
                        <?= $data->extra_price ?>
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
                        <button type="button" class="btn btn-danger delete-item"
                            data-item-id="<?= $data->font_id ?>">DELETE</button>
                        <a href="<?= admin_url('admin.php?page=neon-new-fonts&task=update&font_id=' . $data->font_id) ?>">
                            <button type="button" class="btn btn-warning">UPDATE</button>
                        </a>
                    </td>
                </tr>
                <?php
                $count++;
            }
            ?>

        </tbody>
    </table>
    <a href="<?= admin_url('admin.php?page=neon-new-fonts'); ?>">
        <button class="btn btn-primary">ADD NEW FONTS</button>
    </a>
</div>