<?php
$task = "add";
$size_id = 0;
$row = null;
if (isset($_REQUEST['task']) && isset($_REQUEST['size_id'])) {
    $task = $_REQUEST['task'];

    global $wpdb;

    $table_name = $wpdb->prefix . 'nsc_sizes';
    $size_id = $_REQUEST['size_id']; //
    $query = $wpdb->prepare("SELECT * FROM $table_name WHERE size_id = %s", $size_id);
    $row = $wpdb->get_row($query);

}



?>

<div class="container mt-3">
    <?php
    if ($task == "add") {
        echo "<h3>Sizes Manage - Add New</h3>";
    } else {
        echo "<h3>Sizes Manage - Update</h3>";
    }
    ?>
    <form id="neonAddForm" enctype="multipart/form-data">
        <div class="form-group">
            <strong><label for="size_name">Size's Title</label></strong>
            <input type="text" class="form-control" id="size_name" name="size_name"
                value="<?= ($row ? $row->size_name : "") ?>" required>
        </div>
        <div class="row">
            <div class="form-group col-4">
                <strong><label for="size_des">Font size (px) <a
                            href="https://www.unitconverters.net/typography/pixel-x-to-centimeter.htm" target="_blank">
                            -
                            Reference</a></label></strong>
                <input type="number" step="1" class="form-control" id="size_des" name="size_des"
                    value="<?= ($row ? $row->size_des : "") ?>" required>
            </div>
            <div class="form-group col-4">
                <strong><label for="size_char_min">Minimum Character</label></strong>
                <input type="number" step="1" class="form-control" id="size_char_min" name="size_char_min"
                    value="<?= ($row ? $row->size_char_min : "") ?>" required>
            </div>
            <div class="form-group col-4">
                <strong><label for="size_char_max">Maximum Character</label></strong>
                <input type="number" step="1" class="form-control" id="size_char_max" name="size_char_max"
                    value="<?= ($row ? $row->size_char_max : "") ?>" required>
            </div>
        </div>
        <div class="row">
            <div class="form-group col-4">
                <strong><label for="size_vol">Volumetric Number</label></strong>
                <input type="number" step="0.1" class="form-control" id="size_vol" name="size_vol"
                    value="<?= ($row ? $row->size_vol : "") ?>" required>
            </div>
            <div class="form-group col-4">
                <strong><label for="size_fee">Shipping Fee ($)</label></strong>
                <input type="number" step="0.1" class="form-control" id="size_fee" name="size_fee"
                    value="<?= ($row ? $row->size_fee : "") ?>" required>
            </div>
            <div class="form-group col-4">
                <strong><label for="size_length">Shipping Length (Centimeter)</label></strong>
                <input type="number" step="0.1" class="form-control" id="size_length" name="size_length"
                    value="<?= ($row ? $row->size_length : "") ?>" required>
            </div>
        </div>
        <div class="row">
            <div class="form-group col-6">
                <strong><label for="text_width">Maximum Width of Text (Centimeter)</label></strong>
                <input type="number" step="1" class="form-control" id="text_width" name="text_width"
                    value="<?= ($row ? $row->text_width : "") ?>" required>
            </div>
            <div class="form-group col-6">
                <strong><label for="text_height">Maximum Height of Text (Centimeter)</label></strong>
                <input type="number" step="1" class="form-control" id="text_height" name="text_height"
                    value="<?= ($row ? $row->text_height : "") ?>" required>
            </div>
        </div>
        <input type="hidden" name="task" id="task" value="<?= $task ?>">
        <input type="hidden" name="size_id" id="size_id" value="<?= ($row ? $row->size_id : "") ?>">
        <input type="hidden" name="action" id="action" value="add_new_size">
        <input type="hidden" name="return" id="return" value="sizes">
        <?php
        if ($task == "add") {
            echo '<button type="submit" class="btn btn-primary">ADD NEW</button>';
        } else {
            echo '<button type="submit" class="btn btn-warning">UPDATE</button>';
        }
        ?>

    </form>
</div>