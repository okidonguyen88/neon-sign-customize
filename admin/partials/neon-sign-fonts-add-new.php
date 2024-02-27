<?php

global $wpdb;

// FONT
$task = "add";
$font_id = 0;
$font_data = null;

if (isset($_REQUEST['task']) && isset($_REQUEST['font_id'])) {
    $task = $_REQUEST['task'];
    $nsc_fonts = $wpdb->prefix . 'nsc_fonts';
    $font_id = $_REQUEST['font_id']; //
    $query = $wpdb->prepare("SELECT * FROM $nsc_fonts WHERE font_id = %d", $font_id);
    $font_data = $wpdb->get_row($query);
}

// Price
$price_data = null;
$price_name = "";

$nsc_prices = $wpdb->prefix . "nsc_prices";
$nsc_sizes = $wpdb->prefix . "nsc_sizes";
$nsc_font_price = $wpdb->prefix . "nsc_font_price";
$query = $wpdb->prepare("SELECT sub.*, CASE WHEN fp.price_id IS NOT NULL THEN 1 ELSE 0 END AS has_font_price
FROM (
    SELECT p.*, s.size_name
    FROM $nsc_prices p
    JOIN $nsc_sizes s ON p.size_id = s.size_id 
) AS sub
LEFT JOIN $nsc_font_price fp ON sub.price_id = fp.price_id AND fp.font_id = $font_id order by `price_name` = 'Very Complex Font', `price_name` = 'Complex Font', `price_name`='Simple Font';
");
// echo $query;
$price_data = $wpdb->get_results($query);

?>

<div class="container mt-3">
    <?php
    if ($task == "add") {
        echo "<h3>Add New Fonts</h3>";
    } else {
        echo "<h3>Update Fonts</h3>";
    }
    ?>
    <form id="neonAddFont" enctype="multipart/form-data">
        <div class="form-group">
            <strong><label for="font_name">Enter Font's Name</label></strong>
            <input type="text" class="form-control" id="font_name" name="font_name" maxlength="12"
                value="<?= ($font_data ? $font_data->font_name : "") ?>" required>
        </div>
        <div class="form-group">
            <strong><label for="font_url">Choose URL</label></strong>
            <input type="file" accept=".ttf" class="form-control" id="font_url" name="font_url"
                <?= ($task == "add" ? "required" : "") ?>>
        </div>
        <div class="form-group">
            <strong><label for="extra_price">Enter Extra Price</label></strong>
            <input type="number" step="0.01" class="form-control" id="extra_price" name="extra_price"
                value="<?= ($font_data ? $font_data->extra_price : "") ?>" required>
        </div>
        <div class="form-group">
            <strong><label for="extra_price_type">Select Extra Price Type</label></strong>
            <select class="form-control" id="extra_price_type" name="extra_price_type">
                <option value="0" <?= ($font_data && $font_data->extra_price_type === '0' ? 'selected' : '') ?>>Direct
                </option>
                <option value="1" <?= ($font_data && $font_data->extra_price_type === '1' ? 'selected' : '') ?>>Percent
                </option>
            </select>
        </div>
        <div class="form-group">
            <strong style="padding:0">Choose Price Tier</strong></br />
            <?php
            foreach ($price_data as $data) {
                if ($data->price_name != $price_name) {
                    echo '<strong style="padding:0">- ' . $data->price_name . '</strong></br/>';
                    $price_name = $data->price_name;
                }
                $checked = ($data->has_font_price == "1" ? "checked" : "");
                echo '<div style="padding-left:20px">';
                echo '<input type="checkbox" id="' . $data->price_id . '" class="nsc-checkbox ' . strtolower(str_replace(' ', '_', $data->price_name)) . '" name="price_tier[]" value="' . $data->price_id . '" ' . $checked . '>
                            <label for="' . $data->price_id . '"> <strong>' . $data->size_name . '</strong>';
                echo " <i>( Line 1 - Base: $" . $data->line_one_base . " , Letter: $" . $data->line_one_letter;
                echo " / Line 2 - Base: $" . $data->line_two_base . " , Letter: $" . $data->line_two_letter;
                echo " / Line 3 - Base: $" . $data->line_three_base . " , Letter: $" . $data->line_three_letter . " )</i></label></br/>";
                echo "</div>";
            }
            ?>
        </div>

        <input type="hidden" name="task" id="task" value="<?= $task ?>">
        <input type="hidden" name="font_id" id="font_id" value="<?= ($font_data ? $font_data->font_id : "") ?>">
        <input type="hidden" name="action" id="action" value="add_new_font">
        <input type="hidden" name="return" id="return" value="fonts">
        <?php
        if ($task == "add") {
            echo '<button type="submit" class="btn btn-primary">ADD NEW</button>';
        } else {
            echo '<button type="submit" class="btn btn-warning">UPDATE</button>';
        }
        ?>

    </form>
</div>