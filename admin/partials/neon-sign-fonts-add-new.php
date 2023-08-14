<?php
$task = "add";
$font_id = 0;
$row = null;
if (isset($_REQUEST['task']) && isset($_REQUEST['font_id'])) {
    $task = $_REQUEST['task'];

    global $wpdb;

    $table_name = $wpdb->prefix . 'nsc_fonts';
    $font_id = $_REQUEST['font_id']; //
    $query = $wpdb->prepare("SELECT * FROM $table_name WHERE font_id = %s", $font_id);
    $row = $wpdb->get_row($query);

}

?>

<div class="container mt-3">
    <?php
    if ($task == "add") {
        echo "<h1>Fonts Manage - Add New</h1>";
    } else {
        echo "<h1>Fonts Manage - Update</h1>";
    }
    ?>
    <form id="fontUploadForm">
        <div class="form-group">
            <label for="font_name">Enter Font's Name</label>
            <input type="text" class="form-control" id="font_name" name="font_name"
                value="<?= ($row ? $row->font_name : "") ?>">
            <i>Find your Google fonts, <a href="https://fonts.google.com/" target="_blank">Click Here!</a></i>
        </div>
        <div class="form-group">
            <label for="font_short">Enter Font's Short Name</label>
            <input type="numbtexter" class="form-control" id="font_short" name="font_short"
                value="<?= ($row ? $row->font_short : "") ?>">
        </div>
        <div class="form-group">
            <label for="extra_price">Enter Font Extra Price</label>
            <input type="numbtexter" class="form-control" id="extra_price" name="extra_price"
                value="<?= ($row ? $row->extra_price : "") ?>">
        </div>
        <div class="form-group">
            <label for="font_url">Enter URL</label>
            <input type="text" class="form-control" id="font_url" name="font_url"
                value="<?= ($row ? $row->font_url : "") ?>">
        </div>
        <input type="hidden" name="task" id="task" value="<?= $task ?>">
        <input type="hidden" name="font_id" id="font_id" value="<?= ($row ? $row->font_id : "") ?>">
        <?php
        if ($task == "add") {
            echo '<button type="submit" class="btn btn-primary">ADD NEW</button>';
        } else {
            echo '<button type="submit" class="btn btn-warning">UPDATE</button>';
        }
        ?>

    </form>
</div>