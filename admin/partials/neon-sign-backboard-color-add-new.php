<?php
$task = "add";
$backboard_color_id = 0;
$row = null;
if (isset($_REQUEST['task']) && isset($_REQUEST['backboard_color_id'])) {
    $task = $_REQUEST['task'];

    global $wpdb;

    $table_name = $wpdb->prefix . 'nsc_backboard_color';
    $backboard_color_id = $_REQUEST['backboard_color_id']; //
    $query = $wpdb->prepare("SELECT * FROM $table_name WHERE backboard_color_id = %s", $backboard_color_id);
    $row = $wpdb->get_row($query);

}

?>

<div class="container mt-3">
    <?php
    if ($task == "add") {
        echo "<h3>Backboard Color Manage - Add New</h3>";
    } else {
        echo "<h3>Backboard Color Manage - Update</h3>";
    }
    ?>
    <form id="neonAddForm" enctype="multipart/form-data">
        <div class="form-group">
            <label for="backboard_color_name">Enter backboard_color's Name</label>
            <input type="text" class="form-control" id="backboard_color_name" name="backboard_color_name"
                value="<?= ($row ? $row->backboard_color_name : "") ?>" required>
        </div>
        <div class="form-group">
            <label for="backboard_color_hex">Enter backboard_color's Hex</label>
            <input type="color" class="form-control" id="backboard_color_hex" name="backboard_color_hex"
                value="<?= ($row ? $row->backboard_color_hex : "") ?>" required>
        </div>
        <div class="form-group">
            <label for="backboard_color_img">Choose Image</label>
            <input type="file" accept="image/*" class="form-control" id="backboard_color_img" name="backboard_color_img"
                <?= ($task == "add" ? "required" : "") ?>>
        </div>
        <div class="form-group">
            <label for="extra_price">Enter backboard_color Extra Price</label>
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
        <input type="hidden" name="backboard_color_id" id="backboard_color_id"
            value="<?= ($row ? $row->backboard_color_id : "") ?>">
        <input type="hidden" name="action" id="action" value="add_new_backboard_color">
        <input type="hidden" name="return" id="return" value="backboard_color">
        <?php
        if ($task == "add") {
            echo '<button type="submit" class="btn btn-primary">ADD NEW</button>';
        } else {
            echo '<button type="submit" class="btn btn-warning">UPDATE</button>';
        }
        ?>

    </form>
</div>