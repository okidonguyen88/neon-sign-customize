<?php

global $wpdb;
$nsc_settings = $wpdb->prefix . "nsc_settings";
$result = $wpdb->get_results("SELECT * FROM $nsc_settings");

?>
<div class="container mt-3">

    <h3>Settings</h3>
    <table class="table">
        <thead>
            <tr>
                <th scope="col">#</th>
                <th scope="col" style="width:200px">Value</th>
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
                        <?= $data->setting_name ?>
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
                        <button type="button"
                            class="btn btn-<?= $data->status == 1 ? "success" : "danger" ?> nsc-change-item"
                            data-action="change_setting" data-item-id="<?= $data->setting_id ?>">CHANGE</button>
                    </td>
                </tr>
                <?php
                $count++;
            }
            ?>

        </tbody>
    </table>
</div>