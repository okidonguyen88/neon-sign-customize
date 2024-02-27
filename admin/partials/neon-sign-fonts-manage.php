<?php
global $wpdb;
$table = $wpdb->prefix . "nsc_fonts";
$result = $wpdb->get_results("SELECT * FROM $table");

?>
<div class="container mt-3">
    <h3>Fonts Manage</h3>
    <table class="table">
        <thead>
            <tr>
                <th scope="col">#</th>
                <th scope="col">Name</th>
                <th scope="col">Font URL</th>
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
                    <?= $data->font_url ?>
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
                    <button type="button" class="btn btn-danger nsc-delete-item" data-action="delete_font"
                        data-item-id="<?= $data->font_id ?>">DELETE</button>
                    <a
                        href="<?= admin_url('admin.php?page=neon-sign-customize&func=fonts&param=add-new&task=update&font_id=' . $data->font_id) ?>">
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
    <a href="<?= admin_url('admin.php?page=neon-sign-customize&func=fonts&param=add-new'); ?>">
        <button class="btn btn-primary">ADD NEW FONTS</button>
    </a>
</div>