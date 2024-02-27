<?php
global $wpdb;
$plugin_dir = plugin_dir_url(__FILE__);
$task = "add";
$backboard_id = 0;
$row = null;
if (isset($_REQUEST['task']) && isset($_REQUEST['backboard_id'])) {
    $task = $_REQUEST['task'];
    $table_name = $wpdb->prefix . 'nsc_backboard';
    $backboard_id = $_REQUEST['backboard_id']; //
    $query = $wpdb->prepare("SELECT * FROM $table_name WHERE backboard_id = %s", $backboard_id);
    $row = $wpdb->get_row($query);
}

// Price
$backboard_color_data = null;
$backboard_color_name = "";

$nsc_backboard_color = $wpdb->prefix . "nsc_backboard_color";
$nsc_backboard_color_link = $wpdb->prefix . "nsc_backboard_color_link";
$query = $wpdb->prepare('
SELECT
    sub.*,
    CASE WHEN bl.backboard_color_id IS NOT NULL THEN 1 ELSE 0
END AS has_backboard_color
FROM
    (
    SELECT
        *
    FROM
        `' . $nsc_backboard_color . '` bc
) AS sub
LEFT JOIN `' . $nsc_backboard_color_link . '` bl ON
    bl.backboard_color_id = sub.backboard_color_id and bl.backboard_id = ' . $backboard_id . '
');
// echo $query;
$backboard_color_data = $wpdb->get_results($query);

?>

<div class="container mt-3">
    <?php
    if ($task == "add") {
        echo "<h3>Backboard Manage - Add New</h3>";
    } else {
        echo "<h3>Backboard Manage - Update</h3>";
    }
    ?>
    <form id="neonAddForm" enctype="multipart/form-data">
        <div class="form-group">
            <strong><label for="backboard_name">Enter backboard's Name</label></strong>
            <input type="text" class="form-control" id="backboard_name" name="backboard_name"
                value="<?= ($row ? $row->backboard_name : "") ?>" required>
        </div>
        <div class="form-group">
            <strong><label for="backboard_des">Enter backboard's Description</label></strong>
            <input type="text" class="form-control" id="backboard_des" name="backboard_des"
                value="<?= ($row ? $row->backboard_des : "") ?>" required>
        </div>
        <div class="form-group">
            <strong><label for="backboard_img">Choose Image</label></strong>
            <input type="file" accept="image/*" class="form-control" id="backboard_img" name="backboard_img"
                <?= ($task == "add" ? "required" : "") ?>>
        </div>
        <div class="form-group">
            <div class="row">
                <div class="col-md-6">
                    <strong><label for="extra_price">Enter backboard Extra Price</label></strong>
                    <input type="number" step="0.01" class="form-control" id="extra_price" name="extra_price"
                        value="<?= ($row ? $row->extra_price : "") ?>" required>
                </div>
                <div class="col-md-6">
                    <strong><label for="extra_price_type">Select Extra Price Type</label></strong>
                    <select class="form-control" id="extra_price_type" name="extra_price_type">
                        <option value="0" <?= ($row && $row->extra_price_type === '0' ? 'selected' : '') ?>>Direct</option>
                        <option value="1" <?= ($row && $row->extra_price_type === '1' ? 'selected' : '') ?>>Percent
                        </option>
                    </select>
                </div>
            </div>

        </div>
        <div class="form-group">
            <strong><label for="extra_price_type">Choose Backboard Color</label></br></strong>
            <div class="d-flex text-center p-2">
                <?php
                foreach ($backboard_color_data as $data) {
                    $checked = ($data->has_backboard_color == "1" ? "checked" : "");
                    echo '<div>';
                    echo '<span style="padding-right:15px"><input type="checkbox" id="' . $data->backboard_color_id . '" name="backboard_colors[]" value="' . $data->backboard_color_id . '" ' . $checked . '>
                            <label for="' . $data->backboard_color_id . '">  ' . $data->backboard_color_name . '  </label></span>';
                    echo '<br/>
                 <div           
                 for="' . $data->backboard_color_id . '"                     
                    class="nsc-box m-1 rounded bg-white"  
                    style="                              
                    background-image: url(' . str_replace('admin', 'public/', $plugin_dir) . 'backboard_color_img/' . $data->backboard_color_img . ');
                    background-size:cover;
                    background-repeat: no-repeat;
                    background-position: center;
                    width: 100px;
                    height: 80px;
                        ">                                   
                </div></div>
                ';
                }
                ?>
            </div>
        </div>
        <input type="hidden" name="task" id="task" value="<?= $task ?>">
        <input type="hidden" name="backboard_id" id="backboard_id" value="<?= ($row ? $row->backboard_id : "") ?>">
        <input type="hidden" name="action" id="action" value="add_new_backboard">
        <input type="hidden" name="return" id="return" value="backboard">
        <?php
        if ($task == "add") {
            echo '<button type="submit" class="btn btn-primary">ADD NEW</button>';
        } else {
            echo '<button type="submit" class="btn btn-warning">UPDATE</button>';
        }
        ?>

    </form>
</div>