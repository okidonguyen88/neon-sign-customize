<?php
global $wpdb;
$task = "add";
$price_id = 0;
$row = null;
$nsc_prices = "";
if (isset($_REQUEST['task']) && isset($_REQUEST['price_id'])) {
    $task = $_REQUEST['task'];
    $nsc_prices = $wpdb->prefix . 'nsc_prices';
    $price_id = $_REQUEST['price_id']; //
    $query = $wpdb->prepare("SELECT * FROM $nsc_prices WHERE price_id = %s", $price_id);
    $row = $wpdb->get_row($query);

}

// GET SIZE
$nsc_sizes = $wpdb->prefix . 'nsc_sizes';
$nsc_prices = $wpdb->prefix . 'nsc_prices';
$nsc_sizes_data = $wpdb->get_results("SELECT * FROM $nsc_sizes ");

// CHECK  disabled
$disabled = ($task != "add" ? "disabled" : "");

?>

<div class="container mt-3">
    <?php
    if ($task == "add") {
        echo "<h3>Prices Manage - Add New</h3>";
    } else {
        echo "<h3>Prices Manage - Update</h3>";
    }
    ?>
    <form id="neonAddForm">
        <div class="form-group">
            <div class="row">
                <div class="col-md-6">
                    <label for="price_name">Choose Price Tier</label>

                    <select class="form-control" id="price_name" name="price_name">
                        <?php
                        if ($task == "add") {
                            ?>
                            <option value="Simple Font">Simple Font</option>
                            <option value="Complex Font">Complex Font</option>
                            <option value="Very Complex Font">Very Complex Font</option>
                            <?php
                        } else {
                            ?>
                            <option value="Simple Font" <?= ($row && $row->price_name === 'Simple Font' ? 'selected' : 'disabled') ?>>
                                Simple
                                Font</option>
                            <option value="Complex Font" <?= ($row && $row->price_name === 'Complex Font' ? 'selected' : 'disabled') ?>>Complex
                                Font</option>
                            <option value="Very Complex Font" <?= ($row && $row->price_name === 'Very Complex Font' ? 'selected' : 'disabled') ?>>Very Complex Font</option>
                            <?php
                        }

                        ?>
                    </select>
                </div>
                <div class="col-md-6">
                    <label for="size_id">Choose Size</label>
                    <select class="form-control" id="size_id" name="size_id" required>
                        <?php
                        foreach ($nsc_sizes_data as $data) {
                            if ($task == "add") {
                                echo "<option value='" . $data->size_id . "'  >" . $data->size_name . "</option>";
                            } else {
                                echo "<option value='" . $data->size_id . "' " . ($row->size_id == $data->size_id ? "selected" : "disabled") . " >" . $data->size_name . "</option>";
                            }
                        }
                        ?>
                    </select>
                </div>
            </div>
        </div>
        <div class="form-group">
            <div class="row">
                <div class="col-md-6">
                    <label for="line_one_base">Line 1 - Base Price</label>
                    <input type="number" step="1" class="form-control" id="line_one_base" name="line_one_base"
                        value="<?= ($row ? $row->line_one_base : "0") ?>" required>
                </div>
                <div class="col-md-6">
                    <label for="line_one_letter">Line 1 - Letter Price</label>
                    <input type="number" step="1" class="form-control" id="line_one_letter" name="line_one_letter"
                        value="<?= ($row ? $row->line_one_letter : "0") ?>" required>
                </div>
            </div>
        </div>
        <div class="form-group">
            <div class="row">
                <div class="col-md-6">
                    <label for="line_two_base">Line 2 - Base Price</label>
                    <input type="number" step="1" class="form-control" id="line_two_base" name="line_two_base"
                        value="<?= ($row ? $row->line_two_base : "0") ?>" required>
                </div>
                <div class="col-md-6">
                    <label for="line_two_letter">Line 2 - Letter Price</label>
                    <input type="number" step="1" class="form-control" id="line_two_letter" name="line_two_letter"
                        value="<?= ($row ? $row->line_two_letter : "0") ?>" required>
                </div>
            </div>
        </div>
        <div class="form-group">
            <div class="row">
                <div class="col-md-6">
                    <label for="line_three_base">Line 3 - Base Price</label>
                    <input type="number" step="1" class="form-control" id="line_three_base" name="line_three_base"
                        value="<?= ($row ? $row->line_three_base : "0") ?>" required>
                </div>
                <div class="col-md-6">
                    <label for="line_three_letter">Line 3 - Letter Price</label>
                    <input type="number" step="1" class="form-control" id="line_three_letter" name="line_three_letter"
                        value="<?= ($row ? $row->line_three_letter : "0") ?>" required>
                </div>
            </div>
        </div>

        <input type="hidden" name="task" id="task" value="<?= $task ?>">
        <input type="hidden" name="price_id" id="price_id" value="<?= ($row ? $row->price_id : "0") ?>">
        <input type="hidden" name="action" id="action" value="add_new_price">
        <input type="hidden" name="return" id="return" value="prices">
        <?php
        if ($task == "add") {
            echo '<button type="submit" class="btn btn-primary">ADD NEW</button>';
        } else {
            echo '<button type="submit" class="btn btn-warning">UPDATE</button>';
        }
        ?>

    </form>
</div>