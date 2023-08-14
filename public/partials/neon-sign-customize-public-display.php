<link rel="preconnect" href="https://fonts.gstatic.com">
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/css/bootstrap.min.css"
    integrity="sha384-xOolHFLEh07PJGoPkLv1IbcEPTNtaed2xpHsD9ESMhqIYd0nLMwNLD69Npy4HI+N" crossorigin="anonymous">
<?php

defined('ABSPATH') || exit;

get_header(); // Get the header template

global $post;
$product = wc_get_product($post->ID);
$price_start = $product->get_meta('_price_start');
$price_per_char = $product->get_meta('_price_per_char');
$character_min = $product->get_meta('_character_min');
$character_max = $product->get_meta('_character_max');

// Get dynamic data from plugin table
global $wpdb;

// get fonts
$font_table = $wpdb->prefix . "nsc_fonts";
$result = $wpdb->get_results("SELECT * FROM $font_table");

?>

<?php
foreach ($result as $data) {
    ?>
    <link href="https://fonts.googleapis.com/css2?family=<?= $data->font_name ?>" rel="stylesheet">
    <?php
}
?>

<style>
    <?php
    foreach ($result as $data) {
        echo '
        .' . strtolower(str_replace(' ', '', $data->font_name)) . '-font {
            font-family: "' . $data->font_name . '", sans-serif;
            }
        ';
    }
    ?>
</style>

<div class="nsc-wrapper">
    <div class="nsc-body">
        <div class="nsc-main">
            <div id="nsc-text" class="nsc-text">
                <div class="nsc-backboard">
                    <p>HELLO NEON SIGN</p>
                </div>
                <div class="nsc-price-wrapper">
                    <div class="total-price" id="total-price" data-price="0.00"></div>
                    <div> VAT included</div>
                </div>
            </div>
        </div>
        <div class="nsc-side">
            <div class="nsc-top">
                <h4>CREATE YOUR NEON SIGN</h4>
            </div>
            <div class="nsc-middle">
                <label for="nsc-custom-text">Write your text</label> - <a href="#">Example</a><br />
                <input id="nsc-custom-text" type="text" placeholder="Neon Sign" value="Neon Sign" /><br />
                <div id="warning-message" style="color: red;"></div>
                <label for="nsc-custom-font">Choose your font</label> - <a href="#">Example</a><br />
                <select id="nsc-custom-font">
                    <option data-extra='0' value="">Choose your Fonts</option>
                    <?php
                    foreach ($result as $data) {
                        echo '<option  data-extra="' . $data->extra_price . '" value="' . strtolower(str_replace(' ', '', $data->font_short)) . '">' . $data->font_name . '</option>';
                    }
                    ?>

                </select><br />
                <label for="nsc-custom-color">Choose your Color</label> - <a href="#">Example</a><br />
                <select id="nsc-custom-color">
                    <option value="">Choose your Color</option>
                    <option value="Red">Red</option>
                    <option value="Pink">Pink</option>
                    <option value="Yellow">Yellow</option>
                    <option value="Purple">Purple</option>
                </select><br />
                <label for="nsc-custom-size">Choose your Size</label> - <a href="#">Example</a><br />
                <select id="nsc-custom-size">
                    <option data-price="0" value="0X">Choose your Size</option>
                    <option data-price="10" value="1X">1X</option>
                    <option data-price="20" value="2X">2X</option>
                    <option data-price="30" value="3X">3X</option>
                </select><br />
                <label for="nsc-custom-backboard">Choose your Backboard</label> - <a href="#">Example</a><br />
                <select id="nsc-custom-backboard">
                    <option value="Cut out shape">Cut out shape</option>
                    <option value="Board">Board</option>
                    <option value="Box">Box</option>
                    <option value="Raceway">Raceway</option>
                    <option value="Stand">Stand</option>
                </select><br />
                <label for="nsc-custom-backboard-color">Choose your Backboard Color</label> - <a
                    href="#">Example</a><br />
                <select id="nsc-custom-backboard-color">
                    <option value="Clear Acrylic">Clear Acrylic</option>
                    <option value="Gold">Gold</option>
                    <option value="Black">Black</option>
                </select><br />
                <label for="nsc-custom-backboard-material">Choose your Material</label> - <a href="#">Example</a><br />
                <select id="nsc-custom-backboard-material">
                    <option value="In door">In door</option>
                    <option value="Waterproof">Waterproof</option>
                </select><br />
                <label for="nsc-custom-jacket">Choose your Jacket</label> - <a href="#">Example</a><br />
                <select id="nsc-custom-jacket">
                    <option value="White">White</option>
                    <option value="Color">Color (+19$)</option>
                </select><br />
                <label for="nsc-custom-mounting">Choose your Mounting</label> - <a href="#">Example</a><br />
                <select id="nsc-custom-Mounting">
                    <option value="wall">Wall Mounting Kit</option>
                    <option value="hanging">Hanging Kit</option>
                </select><br />
                <label for="nsc-custom-remote-control">Remote Control</label> - <a href="#">Example</a><br />
                <select id="nsc-custom-remote-control">
                    <option value="yes">Yes</option>
                    <option value="no">No</option>
                </select><br />
                <label for="nsc-custom-plug-type">Plug Type</label> - <a href="#">Example</a><br />
                <select id="nsc-custom-plug-type">
                    <option value="USA/CAN">USA/CAN</option>
                    <option value="AU/NZ">AU/NZ</option>
                </select><br />
                <label for="nsc-custom-cable-color">Choose Cable Color</label> - <a href="#">Example</a><br />
                <select id="nsc-custom-cable-color">
                    <option value="White">White</option>
                    <option value="Black">Black</option>
                </select><br />
                <label for="nsc-custom-special">Any other special requests</label><br />
                <textarea id="nsc-custom-special"></textarea>
            </div>
            <div class="nsc-bottom">
                <div>

                    <?php

                    if ($product->product_type === 'neon_sign'): ?>
                        <button id="add-to-cart" class="btn btn-primary btn-lg"
                            data-product-id="<?php echo $product->get_id(); ?>">SAVE</button>
                        <button class="btn btn-primary btn-lg"
                            onClick="window.location.href = '<?= wc_get_cart_url() ?>'">CART</button>
                        <button class="btn btn-primary btn-lg"
                            onClick="window.location.href = '<?= wc_get_page_permalink('shop'); ?>'">SHOP</button>

                    <?php endif; ?>
                </div>
                <input type="hidden" name="price-start" id="price-start" value="<?= $price_start ?>" />
                <input type="hidden" name="price-per-char" id="price-per-char" value="<?= $price_per_char ?>" />
                <input type="hidden" name="character-min" id="character-min" value="<?= $character_min ?>" />
                <input type="hidden" name="character-max" id="character-max" value="<?= $character_max ?>" />

            </div>
        </div>
    </div>

</div>


<script>

    jQuery(document).ready(function ($) {

        var price_start = parseInt($('#price-start').val());
        var price_per_char = parseInt($('#price-per-char').val());
        var character_min = parseInt($('#character-min').val());
        var character_max = parseInt($('#character-max').val());

        // When the page loads, set the initial text for the neon sign
        function updateNeon() {
            updateNeonText();
            updateNeonFont();
            updateNeonColor();
            updateTotalPrice();
        }

        updateNeon();

        // Attach an event listener to the "nsc-custom-text" input field
        $("#nsc-custom-text").on("input", function () {
            // When the input value changes, call the function to update the neon sign
            updateNeon();

            // Check if the entered text length is outside the allowed range (3 to 10 characters)
            var enteredText = $(this).val();
            if (enteredText.length < character_min || enteredText.length > character_max) {
                $("#warning-message").text("Text must be between " + character_min + " and " + character_max + " characters.");
                $(this).val(enteredText.slice(0, character_max));
            } else {
                $("#warning-message").text(""); // Clear the warning message if within the allowed range
            }
        });

        function updateTotalPrice() {

            var totalPrice = price_start;
            // Calculate Text
            var enteredText = $("#nsc-custom-text").val();
            var nonBlankCharacterCount = parseInt(enteredText.replace(/\s/g, "").length);
            totalPrice = totalPrice + (nonBlankCharacterCount * price_per_char);

            // Calculate Font
            var font_extra_price = parseFloat($('#nsc-custom-font').find(':selected').data('extra'));
            totalPrice = totalPrice + font_extra_price;

            // Calculate Size
            var size_price = parseFloat($('#nsc-custom-size').find(':selected').data('price'));
            // alert(size_price);
            totalPrice = totalPrice + size_price;

            $("#total-price").html('<div>$ ' + totalPrice.toFixed(2) + '</div>');
            $("#total-price").data("price", totalPrice);
        }

        function updateNeonText() {
            // Get the value from the "nsc-custom-text" input field
            var newText = $("#nsc-custom-text").val();

            // Update the content of the "nsc-text" div to display the new text
            $("#nsc-text p").text(newText);
        }

        // Attach an event listener to the "nsc-custom-font" dropdown
        $("#nsc-custom-font").on("change", function () {
            // When the selected font changes, call the function to update the neon sign font
            updateNeon();
        });

        function updateNeonFont() {
            // Get the selected font from the "nsc-custom-font" dropdown
            var selectedFont = $("#nsc-custom-font").val();

            // Remove any existing font classes from the "nsc-text" div
            $("#nsc-text p").removeClass();

            // Add the appropriate font class to the "nsc-text" div based on the selected font
            switch (selectedFont) {
                <?php
                foreach ($result as $data) {
                    echo '
                            case "' . strtolower(str_replace(' ', '', $data->font_short)) . '":
                        $("#nsc-text p").addClass("' . strtolower(str_replace(' ', '', $data->font_name)) . '-font");
                        break;
                        ';
                }
                ?>
                default:
            break;
        }

        $("#nsc-custom-size").on("change", function () {
            // When the selected font changes, call the function to update the neon sign font
            updateNeon();
        });
    }

        // Attach an event listener to the "nsc-custom-color" dropdown
        $("#nsc-custom-color").on("change", function () {
        // When the selected color changes, call the function to update the neon sign color
        updateNeon();
    });

    function updateNeonColor() {
        // Get the selected color from the "nsc-custom-color" dropdown
        var selectedColor = $("#nsc-custom-color").val();

        // Remove any existing color classes from the "nsc-text" div
        $("#nsc-text p").removeClass(
            "neon-red-text neon-pink-text neon-yellow-text neon-purple-text"
        );

        // Add the appropriate color class to the "nsc-text" div based on the selected color
        switch (selectedColor) {
            case "Red":
                $("#nsc-text p").addClass("neon-red-text");
                break;
            case "Pink":
                $("#nsc-text p").addClass("neon-pink-text");
                break;
            case "Yellow":
                $("#nsc-text p").addClass("neon-yellow-text");
                break;
            case "Purple":
                $("#nsc-text p").addClass("neon-purple-text");
                break;
            default:
                // Default color class (you can set a default color here)
                $("#nsc-text p").addClass("neon-default-text");
                break;
        }


    }

    $('#add-to-cart').on('click', function (e) {
        e.preventDefault();

        var customPrice = $('#total-price').data('price');
        var customText = $('#nsc-custom-text').val();
        var customFont = $('#nsc-custom-font').val();
        var customColor = $('#nsc-custom-color').val();
        var customSize = $('#nsc-custom-size').val();
        var customBackboard = $('#nsc-custom-backboard').val();
        var customBackboardColor = $('#nsc-custom-backboard-color').val();
        var customBackboardMaterial = $('#nsc-custom-backboard-material').val();
        var customJacket = $('#nsc-custom-jacket').val();
        var customRemoteControl = $('#nsc-custom-remote-control').val();
        var customCableColor = $('#nsc-custom-cable-color').val();
        var customSpecial = $('#nsc-custom-special').val();
        var productID = $(this).data("product-id");

        // alert(productID);

        $.ajax({
            type: 'POST',
            url: myAjax.ajaxurl,
            data: {
                action: 'update_custom_data_in_cart',
                product_id: productID,
                custom_price: customPrice,
                custom_text: customText,
                custom_font: customFont,
                custom_color: customColor,
                custom_size: customSize,
                custom_backboard: customBackboard,
                custom_backboard_color: customBackboardColor,
                custom_backboard_material: customBackboardMaterial,
                custom_jacket: customJacket,
                custom_remote_control: customRemoteControl,
                custom_cable_color: customCableColor,
                custom_special: customSpecial,
            },
            success: function (response) {
                alert(response);
                // Update UI or refresh the cart contents as needed
            }
        });
    });
   
});


</script>

<script src="https://cdn.jsdelivr.net/npm/jquery@3.5.1/dist/jquery.slim.min.js"
    integrity="sha384-DfXdz2htPH0lsSSs5nCTpuj/zy4C+OGpamoFVy38MVBnE+IbbVYUew+OrCXaRkfj"
    crossorigin="anonymous"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/js/bootstrap.bundle.min.js"
    integrity="sha384-Fy6S3B9q64WdZWQUiU+q4/2Lc9npb8tCaSX9FK7E8HnRr0Jz8D6OP9dO5Vg3Q9ct"
    crossorigin="anonymous"></script>