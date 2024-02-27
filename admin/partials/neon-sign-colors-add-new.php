<?php
$task = "add";
$color_id = 0;
$row = null;
if (isset($_REQUEST['task']) && isset($_REQUEST['color_id'])) {
    $task = $_REQUEST['task'];

    global $wpdb;

    $table_name = $wpdb->prefix . 'nsc_colors';
    $color_id = $_REQUEST['color_id']; //
    $query = $wpdb->prepare("SELECT * FROM $table_name WHERE color_id = %s", $color_id);
    $row = $wpdb->get_row($query);

}

?>

<div class="container mt-3">
    <?php
    if ($task == "add") {
        echo "<h3>Colors Manage - Add New</h3>";
    } else {
        echo "<h3>Colors Manage - Update</h3>";
    }
    ?>
    <form id="neonAddForm" enctype="multipart/form-data">
        <div class="form-group">
            <label for="color_name">Enter color's Name</label>
            <input type="text" class="form-control" id="color_name" name="color_name"
                value="<?= ($row ? $row->color_name : "") ?>" required>
        </div>
        <div class="form-group">
            <label for="color_hex">Enter color's Hex</label>
            <input type="color" class="form-control" id="color_hex" name="color_hex"
                value="<?= ($row ? $row->color_hex : "") ?>" required>
        </div>
        <div class="form-group">
            <label for="is_rbg">Is RBG Color Type</label>
            <select class="form-control" id="is_rbg" name="is_rbg">
                <option value="0" <?= ($row && $row->is_rbg === '0' ? 'selected' : '') ?>>False</option>
                <option value="1" <?= ($row && $row->is_rbg === '1' ? 'selected' : '') ?>>True</option>
            </select>
        </div>
        <div class="form-group">
            <label for="extra_price">Enter color Extra Price</label>
            <input type="number" step="0.01" class="form-control" id="extra_price" name="extra_price"
                value="<?= ($row ? $row->extra_price : "") ?>" required>
        </div>
        <div class="form-group">
            <label for="extra_price_type">Select Extra Price Type</label>
            <select class="form-control" id="extra_price_type" name="extra_price_type">
                <option value="0" <?= ($row && $row->extra_price_type === '0' ? 'selected' : '') ?>>Direct</option>
                <option value="1" <?= ($row && $row->extra_price_type === '1' ? 'selected' : '') ?>>Percent</option>
            </select>
        </div>
        <input type="hidden" name="task" id="task" value="<?= $task ?>">
        <input type="hidden" name="color_id" id="color_id" value="<?= ($row ? $row->color_id : "") ?>">
        <input type="hidden" name="action" id="action" value="add_new_color">
        <input type="hidden" name="return" id="return" value="colors">
        <?php
        if ($task == "add") {
            echo '<button type="submit" class="btn btn-primary">ADD NEW</button>';
        } else {
            echo '<button type="submit" class="btn btn-warning">UPDATE</button>';
        }
        ?>

    </form>
</div>