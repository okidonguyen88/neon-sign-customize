<?php
global $wpdb;

// Declare param
$task = "add";
$additional_id = 0;
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
$row = null;

if (isset($_REQUEST['task']) && isset($_REQUEST['additional_id']) && isset($_REQUEST['type'])) {

    // GET PARAMETER
    $task = sanitize_text_field($_REQUEST['task']);
    $additional_id = sanitize_text_field($_REQUEST['additional_id']);

    // GET DATA FOR UPDATE
    $table_name = $wpdb->prefix . 'nsc_additional';
    $query = $wpdb->prepare("SELECT * FROM $table_name WHERE additional_id = %s", $additional_id);
    $row = $wpdb->get_row($query);

}

?>

<div class="container mt-3">
    <?php
    if ($task == "add") {
        echo "<h3>" . $title . " Manage - Add New</h3>";
    } else {
        echo "<h3>" . $title . " Manage - Update</h3>";
    }
    ?>
    <form id="neonAddForm" enctype="multipart/form-data">
        <div class="form-group">
            <label for="additional_name">Enter
                <?= $title ?>'s Title
            </label>
            <input type="text" class="form-control" id="additional_name" name="additional_name"
                value="<?= ($row ? $row->additional_name : "") ?>" required>
        </div>
        <div class="form-group">
            <label for="additional_des">Enter
                <?= $title ?>'s Description
            </label>
            <input type="text" class="form-control" id="additional_des" name="additional_des"
                value="<?= ($row ? $row->additional_des : "") ?>" required>
        </div>
        <div class="form-group">
            <label for="additional_img">Choose Image</label>
            <input type="file" accept="image/*" class="form-control" id="additional_img" name="additional_img"
                <?= ($task == "add" ? "required" : "") ?>>
        </div>

        <div class="form-group" <?= ($type == "background" ? "style='display:none'" : "style='display:block'") ?>>
            <label for="extra_price">Enter color Extra Price</label>
            <input type="number" step="0.01" class="form-control" id="extra_price" name="extra_price"
                value="<?= ($row ? $row->extra_price : "0") ?>" required>
        </div>
        <div class="form-group" <?= ($type == "background" ? "style='display:none'" : "style='display:block'") ?>>
            <label for="extra_price_type">Select Extra Price Type</label>
            <select class="form-control" id="extra_price_type" name="extra_price_type">
                <option value="0" <?= ($row && $row->extra_price_type === '0' ? 'selected' : '') ?>>Direct</option>
                <option value="1" <?= ($row && $row->extra_price_type === '1' ? 'selected' : '') ?>>Percent</option>
            </select>
        </div>
        <input type="hidden" name="task" id="task" value="<?= $task ?>">
        <input type="hidden" name="additional_id" id="additional_id" value="<?= ($row ? $row->additional_id : "") ?>">
        <input type="hidden" name="action" id="action" value="add_new_additional">
        <input type="hidden" name="return" id="return" value="<?= $type ?>">
        <?php
        if ($task == "add") {
            echo '<button type="submit" class="btn btn-primary">ADD NEW</button>';
        } else {
            echo '<button type="submit" class="btn btn-warning">UPDATE</button>';
        }
        ?>

    </form>
</div>