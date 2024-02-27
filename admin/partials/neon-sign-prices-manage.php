<?php

//define('MY_ROOT_PLUGIN_PATH', WP_PLUGIN_DIR . '/neon-sign-customize');

global $wpdb;
$nsc_prices = $wpdb->prefix . "nsc_prices";
$nsc_sizes = $wpdb->prefix . "nsc_sizes";
$result = $wpdb->get_results("SELECT p.*, s.`size_name` FROM $nsc_prices as p, $nsc_sizes as s WHERE p.`size_id` = s.`size_id` order by `price_name` = 'Very Complex Font', `price_name` = 'Complex Font', `price_name`='Simple Font'");


$plugin_dir = plugin_dir_url(__FILE__);
// echo MY_ROOT_PLUGIN_PATH;

?>
<div class="container mt-3">

    <h3>Prices Manage</h3>
    <table class="table">
        <thead>
            <tr>
                <th scope="col">#</th>
                <th scope="col" style="width:200px">Price Tier</th>
                <th scope="col">Size's Name</th>
                <th scope="col">Line</th>
                <th scope="col">Status</th>
                <th scope="col" style="width:300px">Task</th>
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
                        <?= $data->price_name ?>
                    </td>
                    <td>
                        <?= $data->size_name ?>
                    </td>
                    <td>
                        <?php
                        echo " - Base: $" . $data->line_one_base . " , Letter: $" . $data->line_one_letter . "<br/>";
                        echo " - Base: $" . $data->line_two_base . " , Letter: $" . $data->line_two_letter . "<br/>";
                        echo " - Base: $" . $data->line_three_base . " , Letter: $" . $data->line_three_letter;
                        ?>
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
                        <button type="button" class="btn btn-danger nsc-delete-item" data-action="delete_price"
                            data-item-id="<?= $data->price_id ?>">DELETE</button>
                        <a
                            href="<?= admin_url('admin.php?page=neon-sign-customize&func=prices&param=add-new&task=update&price_id=' . $data->price_id) ?>">
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
    <a href="<?= admin_url('admin.php?page=neon-sign-customize&func=prices&param=add-new'); ?>">
        <button class="btn btn-primary">ADD NEW PRICES</button>
    </a>
</div>