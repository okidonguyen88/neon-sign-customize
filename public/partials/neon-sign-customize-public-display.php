<link rel="preconnect" href="https://fonts.gstatic.com">
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/css/bootstrap.min.css"
    integrity="sha384-xOolHFLEh07PJGoPkLv1IbcEPTNtaed2xpHsD9ESMhqIYd0nLMwNLD69Npy4HI+N" crossorigin="anonymous">
<?php
session_start();
if (!isset($_SESSION['neon-size'])) {
    $_SESSION['neon-size'] = array();
}

defined('ABSPATH') || exit;

global $product;
$product_id = get_the_ID(); // Assuming you have the product's ID
$product = wc_get_product($product_id);
$product_description = $product->get_description();

get_header();
// do_action( 'woocommerce_before_main_content' );
do_action('woocommerce_before_single_product');


global $post;
$product = wc_get_product($post->ID);

// Get dynamic data from plugin table
global $wpdb;
$plugin_dir = plugin_dir_url(__FILE__);

// Settings
$nsc_tbl_settings = $wpdb->prefix . "nsc_settings";
$nsc_settings_results = $wpdb->get_results("SELECT * FROM $nsc_tbl_settings");
$nsc_settings = array();
foreach ($nsc_settings_results as $result) {
    $nsc_settings[$result->setting_name] = $result->status;
}
// print_r($nsc_settings);

// get fonts
$nsc_fonts = $wpdb->prefix . "nsc_fonts";
$nsc_fonts_results = $wpdb->get_results("SELECT * FROM $nsc_fonts");

// get font price
$nsc_font_price = $wpdb->prefix . "nsc_font_price";
$nsc_prices = $wpdb->prefix . "nsc_prices";
$nsc_sizes = $wpdb->prefix . "nsc_sizes";
$nsc_fpz_results = $wpdb->get_results('
    SELECT
        fp.font_id,
        sub.*
    FROM
        `' . $nsc_font_price . '` fp
    LEFT JOIN(
        SELECT p.*,
            s.`size_name`,
            s.`size_des`,
            s.`size_length`,
            s.`size_vol`,
            s.`size_fee`,
            s.`text_width`,
            s.`text_height`,
            s.`text_line`,
            s.`size_char_min`,
            s.`size_char_max`
        FROM
            `' . $nsc_prices . '` p,
            `' . $nsc_sizes . '` s
        WHERE
            p.size_id = s.size_id
    ) AS sub
    ON
        fp.price_id = sub.price_id
');



// get color
$nsc_colors = $wpdb->prefix . "nsc_colors";
$nsc_colors_results = $wpdb->get_results("SELECT * FROM $nsc_colors");

// get backboard
$nsc_backboard = $wpdb->prefix . "nsc_backboard";
$nsc_backboard_results = $wpdb->get_results("SELECT * FROM $nsc_backboard");

// get backboard color
$nsc_backboard_color = $wpdb->prefix . "nsc_backboard_color";
$nsc_backboard_color_link = $wpdb->prefix . "nsc_backboard_color_link";
$nsc_backboard_color_results = $wpdb->get_results('
    SELECT bc.*, bl.backboard_id FROM ' . $nsc_backboard_color_link . ' bl 
        LEFT JOIN ' . $nsc_backboard_color . ' bc
        ON bc.backboard_color_id = bl.backboard_color_id
');

// get additional
$nsc_additional = $wpdb->prefix . "nsc_additional";
$nsc_additional_results = $wpdb->get_results("SELECT * FROM $nsc_additional");

// get background
$nsc_background = $wpdb->prefix . "nsc_additional";
$nsc_background_results = $wpdb->get_results("SELECT * FROM $nsc_background WHERE `additional_type` = 'background'");
?>


<style>
<?php foreach ($nsc_fonts_results as $data) {
    echo '
@font-face {
        font-family: ' . strtolower(str_replace('', '', $data->font_name)) . ';
        src: url(' . $plugin_dir . '/font_url/' . $data->font_url . ');
    }

    ';
echo '
.' . strtolower(str_replace('', '', $data->font_name)) . '-font {
        font-family: ' . strtolower(str_replace('', '', $data->font_name)) . '
    }

    ';

}


?>
</style>
<script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.16.0/umd/popper.min.js" crossorigin="anonymous">
</script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/js/bootstrap.bundle.min.js"
    integrity="sha384-Fy6S3B9q64WdZWQUiU+q4/2Lc9npb8tCaSX9FK7E8HnRr0Jz8D6OP9dO5Vg3Q9ct" crossorigin="anonymous">
</script>

<div class="modal fade p-0 m-0" id="imageModal" tabindex="-1" role="dialog" aria-labelledby="imageModalLabel"
    aria-hidden="true" style="z-index: 9999">
    <div class="modal-dialog modal-dialog-centered modal-md">
        <div class="modal-content w-75" style="margin: 0 auto">
            <div class="modal-body">
                <img src="" class="img-fluid" id="modalImage" alt="Large Image">
            </div>
        </div>
    </div>
</div>


<div class="nsc-wrapper container-fluid" style="max-width:1200px">
    <div id="toastContainer" style="position: fixed; top: 40px; right: 40px; z-index: 9999;"></div>
    <div class="text-center p-2">
        <h3 class="pb-0" style="color:var(--primary-text-color)"><?= $product->get_title(); ?></h3>
        <p><?= $product->get_short_description(); ?>
        </p>
        <br />
    </div>
    <div class="nsc-body">
        <div id="mainModal" class="row">
            <div class="nsc-main col-lg-7 vw-100">
                <img id="nsc-main-background" class="nsc-img"
                    src="<?= $plugin_dir ?>additional_img/background_default.jpg" />
                <div id="nsc-live-choose" style="display: block;">
                    <div id="nsc-neon-live" class="nsc-neon-live">
                        <div class="nsc-backboard">
                            <p>HELLO NEON SIGN</p>
                            <div id="nsc-backboard-height">10cm</div>
                            <div id="nsc-backboard-width">20cm</div>
                        </div>
                        <div class="nsc-price-wrapper">
                            <a id="detail-price" class="cursor-pointer text-decoration-none text-white">
                                <div class="total-price" id="total-price" data-price="0.00"></div>
                            </a>
                        </div>
                        <div class="nsc-toggle-wrapper d-none d-md-flex justify-content-between" style="height:68px;">
                            <div class="nsc-toggle-on cursor-pointer">ON</div>
                            <div class="nsc-toggle-off cursor-pointer">OFF</div>
                        </div>
                        <div class="nsc-toggle-mobile-wrapper d-flex d-md-none">
                            <div id="nsc-toggle-mobile-light" class="nsc-toggle-mobile cursor-pointer">
                                <div class="nsc-toggle-button" id="nsc-toggle-light-open">
                                    <svg width="20" height="20" viewBox="0 0 24 24" fill="none"
                                        xmlns="http://www.w3.org/2000/svg">
                                        <path
                                            d="M12 17C14.7614 17 17 14.7614 17 12C17 9.23858 14.7614 7 12 7C9.23858 7 7 9.23858 7 12C7 14.7614 9.23858 17 12 17Z"
                                            stroke="currentcolor" stroke-width="2" stroke-linecap="round"
                                            stroke-linejoin="round"></path>
                                        <path d="M12 1V3" stroke="currentColor" stroke-width="2" stroke-linecap="round"
                                            stroke-linejoin="round"></path>
                                        <path d="M12 21V23" stroke="currentColor" stroke-width="2"
                                            stroke-linecap="round" stroke-linejoin="round"></path>
                                        <path d="M4.22 4.22L5.64 5.64" stroke="currentcolor" stroke-width="2"
                                            stroke-linecap="round" stroke-linejoin="round"></path>
                                        <path d="M18.36 18.36L19.78 19.78" stroke="currentcolor" stroke-width="2"
                                            stroke-linecap="round" stroke-linejoin="round"></path>
                                        <path d="M1 12H3" stroke="currentcolor" stroke-width="2" stroke-linecap="round"
                                            stroke-linejoin="round"></path>
                                        <path d="M21 12H23" stroke="currentcolor" stroke-width="2"
                                            stroke-linecap="round" stroke-linejoin="round"></path>
                                        <path d="M4.22 19.78L5.64 18.36" stroke="currentcolor" stroke-width="2"
                                            stroke-linecap="round" stroke-linejoin="round"></path>
                                        <path d="M18.36 5.64L19.78 4.22" stroke="currentcolor" stroke-width="2"
                                            stroke-linecap="round" stroke-linejoin="round"></path>
                                    </svg>
                                </div>
                                <div class="nsc-toggle-button" id="nsc-toggle-light-close" style="display:none; ">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor"
                                        class="bi bi-x" viewBox="0 0 16 16">
                                        <path
                                            d="M4.646 4.646a.5.5 0 0 1 .708 0L8 7.293l2.646-2.647a.5.5 0 0 1 .708.708L8.707 8l2.647 2.646a.5.5 0 0 1-.708.708L8 8.707l-2.646 2.647a.5.5 0 0 1-.708-.708L7.293 8 4.646 5.354a.5.5 0 0 1 0-.708z" />
                                    </svg>
                                </div>
                                <div class="nsc-toggle-wrapper" id="nsc-toggle-light-button"
                                    style="display:none; position: absolute">
                                    <div class="nsc-toggle-on">ON
                                    </div>
                                    <div class="nsc-toggle-off">OFF</div>
                                </div>
                            </div>
                            <div id="nsc-toggle-mobile-background" class="nsc-toggle-mobile cursor-pointer">
                                <div class="nsc-toggle-button" id="nsc-toggle-background-open">
                                    <svg width="20" height="20" viewBox="0 0 18 18" fill="none"
                                        xmlns="http://www.w3.org/2000/svg">
                                        <path fill-rule="evenodd" clip-rule="evenodd"
                                            d="M5.38851 17.3333H12.6107C15.4359 17.3333 17.3334 15.3516 17.3334 12.4027V5.59727C17.3334 2.64843 15.4359 0.666672 12.6115 0.666672H5.38851C2.5641 0.666672 0.666687 2.64843 0.666687 5.59727V12.4027C0.666687 15.3516 2.5641 17.3333 5.38851 17.3333ZM6.0824 8.16667C4.93353 8.16667 4.00002 7.2319 4.00002 6.08334C4.00002 4.93478 4.93353 4.00001 6.0824 4.00001C7.23043 4.00001 8.16478 4.93478 8.16478 6.08334C8.16478 7.2319 7.23043 8.16667 6.0824 8.16667ZM15.5174 11.445C15.7964 12.1605 15.6514 13.0205 15.3531 13.7291C14.9995 14.5719 14.3224 15.21 13.4693 15.4887C13.0906 15.6125 12.6934 15.6667 12.297 15.6667H5.27389C4.57501 15.6667 3.95658 15.499 3.44961 15.1868C3.13201 14.9907 3.07587 14.5384 3.31134 14.2451C3.70519 13.7549 4.09401 13.263 4.48619 12.7668C5.23367 11.8173 5.73729 11.5421 6.29706 11.7838C6.52416 11.8836 6.75209 12.0332 6.98672 12.1914C7.61186 12.6163 8.48084 13.2002 9.62552 12.5664C10.4089 12.1277 10.8632 11.3751 11.2588 10.7198L11.2655 10.7088C11.2935 10.6628 11.3213 10.6169 11.349 10.571L11.349 10.571C11.482 10.351 11.6131 10.1339 11.7615 9.93396C11.9476 9.6837 12.6372 8.90111 13.5305 9.45838C14.0995 9.80926 14.578 10.284 15.09 10.7922C15.2852 10.9866 15.4243 11.2076 15.5174 11.445Z"
                                            fill="currentColor"></path>
                                    </svg>
                                </div>
                                <div class="nsc-toggle-button" id="nsc-toggle-background-close" style="display:none; ">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor"
                                        class="bi bi-x" viewBox="0 0 16 16">
                                        <path
                                            d="M4.646 4.646a.5.5 0 0 1 .708 0L8 7.293l2.646-2.647a.5.5 0 0 1 .708.708L8.707 8l2.647 2.646a.5.5 0 0 1-.708.708L8 8.707l-2.646 2.647a.5.5 0 0 1-.708-.708L7.293 8 4.646 5.354a.5.5 0 0 1 0-.708z" />
                                    </svg>
                                </div>
                                <div class="nsc-toggle-wrapper" id="nsc-toggle-background-button"
                                    style="display:none; position: absolute">
                                    <?php
                                    foreach ($nsc_background_results as $data) {
                                        ?>
                                    <div id="background-'<?= $data->additional_id ?>'"
                                        class="nsc-box mt-1 mr-1 rounded bg-white background-click"
                                        data-image=" <?= $plugin_dir ?>additional_img/<?= $data->additional_img ?>">
                                        <img class="nsc-img"
                                            src="<?= $plugin_dir ?>additional_img/<?= $data->additional_img ?>" />
                                    </div>
                                    <?php
                                    }
                                    ?>
                                </div>
                            </div>
                        </div>
                        <div class="nsc-background-wrapper d-none d-md-flex">
                            <?php
                            foreach ($nsc_background_results as $data) {
                                ?>
                            <div id="background-'<?= $data->additional_id ?>'"
                                class="nsc-box mt-1 mr-1 rounded bg-white background-click"
                                data-image=" <?= $plugin_dir ?>additional_img/<?= $data->additional_img ?>">
                                <img class="nsc-img "
                                    src="<?= $plugin_dir ?>additional_img/<?= $data->additional_img ?>" />
                            </div>
                            <?php
                            }
                            ?>
                        </div>

                    </div>
                </div>

            </div>
            <div class="nsc-side col-lg-5 p-0">
                <div id="nsc-side-choose" style="display:flex">
                    <div class="nsc-middle">
                        <div class="nsc-top pt-3 pb-3">
                            <h4 style="font-weight: 800; color:var(--primary-text-color)">CUSTOMIZE NEON SIGN</h4>
                            <div class="d-flex d-lg-none mt-3 justify-content-between" style="border: 1px solid
                                rgb(221, 221, 221);">
                                <div class="d-flex">
                                    <div id="nsc-mobile-control-toggle" class="nsc-mobile-control-toggle cursor-pointer"
                                        style="padding:10px">
                                        <svg fill="none" height="24" viewBox="0 0 24 24" width="24"
                                            xmlns="http://www.w3.org/2000/svg">
                                            <path d="M4 6H20M4 12H20M4 18H20" stroke="currentColor"
                                                stroke-linecap="round" stroke-linejoin="round" stroke-width="2"></path>
                                        </svg>
                                    </div>
                                    <div id="nsc-mobile-control-label" style="padding:10px"></div>
                                </div>
                                <div class="d-flex">
                                    <div id="nsc-mobile-control-current" style="padding:10px">1/8</div>
                                    <div class="nsc-mobile-control-button nsc-mobile-control-pre cursor-pointer">
                                        <svg width="15" height="14" viewBox="0 0 15 14" fill="none"
                                            xmlns="http://www.w3.org/2000/svg">
                                            <path
                                                d="M8.15625 13.8125L8.78125 13.2188C8.9375 13.0625 8.9375 12.8125 8.78125 12.6875L3.9375 7.8125H14.625C14.8125 7.8125 15 7.65625 15 7.4375V6.5625C15 6.375 14.8125 6.1875 14.625 6.1875H3.9375L8.78125 1.34375C8.9375 1.21875 8.9375 0.96875 8.78125 0.8125L8.15625 0.21875C8.03125 0.0625 7.78125 0.0625 7.625 0.21875L1.09375 6.75C0.9375 6.90625 0.9375 7.125 1.09375 7.28125L7.625 13.8125C7.78125 13.9688 8.03125 13.9688 8.15625 13.8125Z"
                                                fill="currentColor"></path>
                                        </svg>
                                    </div>
                                    <div class="nsc-mobile-control-button nsc-mobile-control-next cursor-pointer">
                                        <svg width="14" height="14" viewBox="0 0 14 14" fill="none"
                                            xmlns="http://www.w3.org/2000/svg">
                                            <path
                                                d="M6.8125 0.21875L6.1875 0.8125C6.03125 0.96875 6.03125 1.21875 6.1875 1.34375L11.0312 6.1875H0.375C0.15625 6.1875 0 6.375 0 6.5625V7.4375C0 7.65625 0.15625 7.8125 0.375 7.8125H11.0312L6.1875 12.6875C6.03125 12.8125 6.03125 13.0625 6.1875 13.2188L6.8125 13.8125C6.9375 13.9688 7.1875 13.9688 7.34375 13.8125L13.875 7.28125C14.0312 7.125 14.0312 6.90625 13.875 6.75L7.34375 0.21875C7.1875 0.0625 6.9375 0.0625 6.8125 0.21875Z"
                                                fill="currentColor"></path>
                                        </svg>
                                    </div>
                                </div>
                            </div>
                            <nav id="nsc-nav-tab-wrapper" class="p-2" style="display: none">
                                <div class="nav nav-tabs" id="nsc-nav-tab">
                                    <div class="nsc-nav-link active" id="nav-text-tab" data-toggle="pill" data-index="0"
                                        data-label="Text" data-target="#nav-text" role="tab" aria-controls="nav-text"
                                        aria-selected="true">
                                        <div>Text</div>
                                        <div class="p-2">
                                            <svg width="70" height="70" viewBox="0 0 70 70" fill="none"
                                                xmlns="http://www.w3.org/2000/svg">
                                                <path
                                                    d="M21.6667 21.6667C20.2859 21.6667 19.1667 22.786 19.1667 24.1667C19.1667 25.5474 20.2859 26.6667 21.6667 26.6667H27.5V44.1667C27.5 45.5474 28.6193 46.6667 30 46.6667C31.3807 46.6667 32.5 45.5474 32.5 44.1667V26.6667H38.3333C39.714 26.6667 40.8333 25.5474 40.8333 24.1667C40.8333 22.786 39.714 21.6667 38.3333 21.6667H21.6667ZM50.8333 32.5C50.8333 31.1193 49.714 30 48.3333 30H38.3333C36.9526 30 35.8333 31.1193 35.8333 32.5C35.8333 33.8807 36.9526 35 38.3333 35H40.8333V44.1667C40.8333 45.5474 41.9526 46.6667 43.3333 46.6667C44.714 46.6667 45.8333 45.5474 45.8333 44.1667V35H48.3333C49.714 35 50.8333 33.8807 50.8333 32.5Z"
                                                    fill="var(--primary-text-color)"></path>
                                                <rect x="0.5" y="0.5" width="69" height="69" rx="34.5" stroke="#DDDDDD">
                                                </rect>
                                            </svg>
                                        </div>
                                    </div>
                                    <div class="nsc-nav-link" id="nav-font-tab" data-toggle="pill" data-index="1"
                                        data-label="Font" data-target="#nav-font" role="tab" aria-controls="nav-font"
                                        aria-selected="false">
                                        <div>Font</div>
                                        <div class="p-2">
                                            <svg width="70" height="70" viewBox="0 0 70 70" fill="none"
                                                xmlns="http://www.w3.org/2000/svg">
                                                <path
                                                    d="M47.017 20C45.9792 20 45.1379 20.8413 45.1379 21.8791C45.1379 22.3774 44.9345 22.8554 44.5723 23.2078C44.2102 23.5601 43.719 23.7581 43.2069 23.7581H27.7586C26.2222 23.7581 24.7487 24.352 23.6623 25.4092C22.5759 26.4664 21.9655 27.9002 21.9655 29.3953C21.9655 30.4331 22.8068 31.2744 23.8446 31.2744H23.9485C24.9863 31.2744 25.8276 30.4331 25.8276 29.3953C25.8276 28.8969 26.031 28.419 26.3932 28.0666C26.7553 27.7142 27.2465 27.5162 27.7586 27.5162H33.769L31.1959 35.0325H26.7411C25.7034 35.0325 24.8621 35.8738 24.8621 36.9115C24.8621 37.9493 25.7034 38.7906 26.7411 38.7906H29.9069L27.6572 45.3673C27.5352 45.669 27.31 45.9206 27.0196 46.0795C26.7293 46.2385 26.3914 46.2952 26.0631 46.24C25.7347 46.1848 25.436 46.0212 25.2172 45.7766C24.9985 45.5321 24.873 45.2216 24.8621 44.8976V44.4798C24.8621 43.4133 23.9975 42.5487 22.931 42.5487C21.8646 42.5487 21 43.4133 21 44.4798V44.8976C21.0157 46.1121 21.4705 47.2824 22.2847 48.203C23.0988 49.1237 24.2202 49.7359 25.452 49.9321C26.6838 50.1283 27.9473 49.8961 29.0206 49.2761C30.0939 48.6561 30.9085 47.688 31.3214 46.5417L33.9766 38.7906H38.4313C39.4691 38.7906 40.3103 37.9493 40.3103 36.9115C40.3103 35.8738 39.4691 35.0325 38.4313 35.0325H35.2655L37.8386 27.5162H43.2069C44.7433 27.5162 46.2168 26.9223 47.3032 25.8652C48.3897 24.808 49 23.3741 49 21.8791C49 20.8413 48.1587 20 47.1209 20H47.017Z"
                                                    fill="var(--primary-text-color)"></path>
                                                <rect x="0.5" y="0.5" width="69" height="69" rx="34.5" stroke="#DDDDDD">
                                                </rect>
                                            </svg>
                                        </div>
                                    </div>
                                    <div class="nsc-nav-link" id="nav-color-tab" data-toggle="pill" data-index="2"
                                        data-label="Color" data-target="#nav-color" role="tab" aria-controls="nav-color"
                                        aria-selected="false">
                                        <div>Color</div>
                                        <div class="p-2">
                                            <svg width="70" height="70" viewBox="0 0 70 70" fill="none"
                                                xmlns="http://www.w3.org/2000/svg">
                                                <path
                                                    d="M35 18.75C35 18.4185 34.8683 18.1005 34.6339 17.8661C34.3995 17.6317 34.0815 17.5 33.75 17.5C33.4185 17.5 33.1005 17.6317 32.8661 17.8661C32.6317 18.1005 32.5 18.4185 32.5 18.75V20.845C31.87 21.005 31.275 21.3325 30.7825 21.825L18.3425 34.265C17.6496 34.957 17.255 35.8926 17.2428 36.8718C17.2307 37.8509 17.602 38.796 18.2775 39.505L27.3275 49.005C27.6723 49.3674 28.0861 49.6571 28.5446 49.8571C29.003 50.0571 29.4969 50.1634 29.997 50.1697C30.4972 50.176 30.9935 50.0822 31.4569 49.8937C31.9202 49.7053 32.3412 49.4261 32.695 49.0725L45.365 36.4025C45.7135 36.0542 45.9899 35.6407 46.1785 35.1856C46.3671 34.7305 46.4642 34.2427 46.4642 33.75C46.4642 33.2573 46.3671 32.7695 46.1785 32.3144C45.9899 31.8593 45.7135 31.4458 45.365 31.0975L36.085 21.825C35.76 21.505 35.3925 21.25 35 21.0725V18.75ZM43.2325 35H21.1425L32.5 23.6425V26.25C32.5 26.5815 32.6317 26.8995 32.8661 27.1339C33.1005 27.3683 33.4185 27.5 33.75 27.5C34.0815 27.5 34.3995 27.3683 34.6339 27.1339C34.8683 26.8995 35 26.5815 35 26.25V24.275L43.5975 32.8675C43.8318 33.1019 43.9635 33.4198 43.9635 33.7513C43.9635 34.0827 43.8318 34.4006 43.5975 34.635L43.2325 35Z"
                                                    fill="var(--primary-text-color)"></path>
                                                <path
                                                    d="M47.3025 40.555C47.1711 40.4052 47.0092 40.2852 46.8277 40.2029C46.6462 40.1207 46.4493 40.0782 46.25 40.0782C46.0507 40.0782 45.8538 40.1207 45.6723 40.2029C45.4907 40.2852 45.3289 40.4052 45.1975 40.555L42.3225 43.8425C39.365 47.2175 41.7625 52.5 46.25 52.5C50.7375 52.5 53.1325 47.2175 50.18 43.8425L47.305 40.555H47.3025Z"
                                                    fill="var(--primary-text-color)"></path>
                                                <rect x="0.5" y="0.5" width="69" height="69" rx="34.5" stroke="#DDDDDD">
                                                </rect>
                                            </svg>
                                        </div>
                                    </div>
                                    <div class="nsc-nav-link" id="nav-size-tab" data-toggle="pill" data-index="3"
                                        data-label="Size" data-target="#nav-size" role="tab" aria-controls="nav-size"
                                        aria-selected="false">
                                        <div>Size</div>
                                        <div class="p-2">
                                            <svg width="70" height="70" viewBox="0 0 70 70" fill="none"
                                                xmlns="http://www.w3.org/2000/svg">
                                                <path
                                                    d="M40.6666 51.6667H29.3333C29.202 51.6667 29.072 51.6408 28.9506 51.5905C28.8293 51.5403 28.7191 51.4666 28.6262 51.3738C28.5333 51.2809 28.4597 51.1707 28.4094 51.0494C28.3592 50.928 28.3333 50.798 28.3333 50.6667V19.3333C28.3333 19.0681 28.4387 18.8138 28.6262 18.6262C28.8137 18.4387 29.0681 18.3333 29.3333 18.3333H40.6666C40.9319 18.3333 41.1862 18.4387 41.3738 18.6262C41.5613 18.8138 41.6666 19.0681 41.6666 19.3333V50.6667C41.6666 50.9319 41.5613 51.1862 41.3738 51.3738C41.1862 51.5613 40.9319 51.6667 40.6666 51.6667V51.6667Z"
                                                    stroke="var(--primary-text-color)" stroke-width="3"
                                                    stroke-linecap="round" stroke-linejoin="round"></path>
                                                <path d="M41.6667 43.3333H36.6667" stroke="var(--primary-text-color)"
                                                    stroke-width="3" stroke-linecap="round" stroke-linejoin="round">
                                                </path>
                                                <path d="M41.6667 26.6667H36.6667" stroke="var(--primary-text-color)"
                                                    stroke-width="3" stroke-linecap="round" stroke-linejoin="round">
                                                </path>
                                                <path
                                                    d="M53.3333 35L50 31.6667M36.6667 35H53.3333H36.6667ZM53.3333 35L50 38.3333L53.3333 35Z"
                                                    stroke="var(--primary-text-color)" stroke-width="3"
                                                    stroke-linecap="round" stroke-linejoin="round"></path>
                                                <path
                                                    d="M16.6667 35H28.3333M16.6667 35L20 31.6667L16.6667 35ZM16.6667 35L20 38.3333L16.6667 35Z"
                                                    stroke="var(--primary-text-color)" stroke-width="3"
                                                    stroke-linecap="round" stroke-linejoin="round"></path>
                                                <rect x="0.5" y="0.5" width="69" height="69" rx="34.5" stroke="#DDDDDD">
                                                </rect>
                                            </svg>
                                        </div>
                                    </div>
                                    <div class="nsc-nav-link" id="nav-backboard-tab" data-toggle="pill" data-index="4"
                                        data-label="Backboard" data-target="#nav-backboard" role="tab"
                                        aria-controls="nav-backboard" aria-selected="false">
                                        <div>Backboard</div>
                                        <div class="p-2">
                                            <svg width="70" height="70" viewBox="0 0 70 70" fill="none"
                                                xmlns="http://www.w3.org/2000/svg">
                                                <path
                                                    d="M47.017 20C45.9792 20 45.1379 20.8413 45.1379 21.8791C45.1379 22.3774 44.9345 22.8554 44.5723 23.2078C44.2102 23.5601 43.719 23.7581 43.2069 23.7581H27.7586C26.2222 23.7581 24.7487 24.352 23.6623 25.4092C22.5759 26.4664 21.9655 27.9002 21.9655 29.3953C21.9655 30.4331 22.8068 31.2744 23.8446 31.2744H23.9485C24.9863 31.2744 25.8276 30.4331 25.8276 29.3953C25.8276 28.8969 26.031 28.419 26.3932 28.0666C26.7553 27.7142 27.2465 27.5162 27.7586 27.5162H33.769L31.1959 35.0325H26.7411C25.7034 35.0325 24.8621 35.8738 24.8621 36.9115C24.8621 37.9493 25.7034 38.7906 26.7411 38.7906H29.9069L27.6572 45.3673C27.5352 45.669 27.31 45.9206 27.0196 46.0795C26.7293 46.2385 26.3914 46.2952 26.0631 46.24C25.7347 46.1848 25.436 46.0212 25.2172 45.7766C24.9985 45.5321 24.873 45.2216 24.8621 44.8976V44.4798C24.8621 43.4133 23.9975 42.5487 22.931 42.5487C21.8646 42.5487 21 43.4133 21 44.4798V44.8976C21.0157 46.1121 21.4705 47.2824 22.2847 48.203C23.0988 49.1237 24.2202 49.7359 25.452 49.9321C26.6838 50.1283 27.9473 49.8961 29.0206 49.2761C30.0939 48.6561 30.9085 47.688 31.3214 46.5417L33.9766 38.7906H38.4313C39.4691 38.7906 40.3103 37.9493 40.3103 36.9115C40.3103 35.8738 39.4691 35.0325 38.4313 35.0325H35.2655L37.8386 27.5162H43.2069C44.7433 27.5162 46.2168 26.9223 47.3032 25.8652C48.3897 24.808 49 23.3741 49 21.8791C49 20.8413 48.1587 20 47.1209 20H47.017Z"
                                                    fill="var(--primary-text-color)"></path>
                                                <rect x="0.5" y="0.5" width="69" height="69" rx="34.5" stroke="#DDDDDD">
                                                </rect>
                                            </svg>
                                        </div>
                                    </div>
                                    <div class="nsc-nav-link" id="nav-material-tab" data-toggle="pill" data-index="5"
                                        data-label="Material" data-target="#nav-material" role="tab"
                                        aria-controls="nav-material" aria-selected="false">
                                        <div>Material</div>
                                        <div class="p-2">
                                            <svg width="70" height="70" viewBox="0 0 70 70" fill="none"
                                                xmlns="http://www.w3.org/2000/svg">
                                                <path fill-rule="evenodd" clip-rule="evenodd"
                                                    d="M51.805 19.6604L28.284 10.5555C25.0074 9.28715 21.479 11.7048 21.479 15.2183V17.1259C21.4925 17.1308 21.5059 17.1359 21.5193 17.141L24.479 18.2868V15.2183C24.479 13.8129 25.8904 12.8459 27.201 13.3532L50.722 22.4581C51.4923 22.7563 52 23.4973 52 24.3232V51.9938C52 53.3992 50.5886 54.3663 49.278 53.859L46.3182 52.7133V55.7817C46.3182 55.8311 46.3165 55.8799 46.3131 55.9282L48.195 56.6567C51.4716 57.925 55 55.5073 55 51.9938V24.3232C55 22.2583 53.7306 20.4058 51.805 19.6604Z"
                                                    fill="var(--primary-text-color)"></path>
                                                <ellipse cx="23.5" cy="24" rx="1.5" ry="2"
                                                    fill="var(--primary-text-color)"></ellipse>
                                                <path fill-rule="evenodd" clip-rule="evenodd"
                                                    d="M46.1232 23.4482L22.6023 14.3433C19.3257 13.075 15.7973 15.4927 15.7973 19.0062V39.4083C16.2479 38.6893 16.7811 38.0175 17.3908 37.4079C17.8293 36.9693 18.3 36.5703 18.7973 36.2132V19.0062C18.7973 17.6008 20.2086 16.6337 21.5193 17.141L45.0402 26.2459C45.8105 26.5441 46.3182 27.2851 46.3182 28.1111V55.7817C46.3182 57.1871 44.9069 58.1541 43.5963 57.6468L33.2815 53.654C32.3937 54.5034 31.3821 55.1957 30.29 55.7129L42.5133 60.4445C45.7898 61.7128 49.3182 59.2952 49.3182 55.7817V28.1111C49.3182 26.0461 48.0489 24.1936 46.1232 23.4482Z"
                                                    fill="var(--primary-text-color)"></path>
                                                <path fill-rule="evenodd" clip-rule="evenodd"
                                                    d="M25.426 36.1457C22.9602 36.1457 20.5953 37.1252 18.8517 38.8688C17.1081 40.6124 16.1286 42.9772 16.1286 45.4431C16.1286 47.9089 17.1081 50.2737 18.8517 52.0173C20.5953 53.7609 22.9602 54.7405 25.426 54.7405C27.8918 54.7405 30.2567 53.7609 32.0003 52.0173C33.7439 50.2737 34.7234 47.9089 34.7234 45.4431C34.7234 42.9772 33.7439 40.6124 32.0003 38.8688C30.2567 37.1252 27.8918 36.1457 25.426 36.1457ZM25.426 51.6413V39.2448C26.24 39.2448 27.046 39.4051 27.798 39.7166C28.55 40.0281 29.2333 40.4846 29.8088 41.0602C30.3844 41.6358 30.841 42.3191 31.1524 43.0711C31.4639 43.8231 31.6243 44.6291 31.6243 45.443C31.6243 46.257 31.4639 47.063 31.1524 47.815C30.841 48.567 30.3844 49.2503 29.8088 49.8259C29.2333 50.4014 28.55 50.858 27.798 51.1695C27.046 51.481 26.24 51.6413 25.426 51.6413Z"
                                                    fill="var(--primary-text-color)"></path>
                                                <ellipse cx="41.5" cy="30" rx="1.5" ry="2"
                                                    fill="var(--primary-text-color)"></ellipse>
                                                <ellipse cx="41.5" cy="51" rx="1.5" ry="2"
                                                    fill="var(--primary-text-color)"></ellipse>
                                                <rect x="0.5" y="0.5" width="69" height="69" rx="34.5" stroke="#DDDDDD">
                                                </rect>
                                            </svg>
                                        </div>
                                    </div>
                                    <div class="nsc-nav-link" id="nav-jacket-tab" data-toggle="pill" data-index="6"
                                        data-label="Jacket" data-target="#nav-jacket" role="tab"
                                        aria-controls="nav-jacket" aria-selected="false">
                                        <div>Jacket</div>
                                        <div class="p-2">
                                            <svg width="70" height="70" viewBox="0 0 70 70" fill="none"
                                                xmlns="http://www.w3.org/2000/svg">
                                                <path fill-rule="evenodd" clip-rule="evenodd"
                                                    d="M53.3833 25.9548C53.3963 25.7444 53.369 25.3417 53.1413 24.6588C52.6971 23.326 51.6572 21.6141 50.0215 19.9785C48.3859 18.3428 46.674 17.3029 45.3412 16.8587C44.6583 16.631 44.2556 16.6037 44.0452 16.6166C44.0322 16.827 44.0596 17.2297 44.2872 17.9127C44.7315 19.2454 45.7714 20.9573 47.407 22.593C49.0427 24.2286 50.7546 25.2685 52.0873 25.7128C52.7703 25.9404 53.173 25.9678 53.3833 25.9548ZM45.2857 24.7143C49.0728 28.5014 53.6779 30.0364 55.5714 28.1429C57.465 26.2493 55.9299 21.6442 52.1428 17.8571C48.3558 14.07 43.7507 12.535 41.8571 14.4286C39.9636 16.3221 41.4986 20.9272 45.2857 24.7143Z"
                                                    fill="var(--primary-text-color)"></path>
                                                <path
                                                    d="M44.7861 36.7861C42.2816 39.7816 37.0958 39.0146 33.9642 35.883C30.8326 32.7514 27.9733 32.9733 24.5 33.5"
                                                    stroke="var(--primary-text-color)" stroke-width="3"></path>
                                                <path fill-rule="evenodd" clip-rule="evenodd"
                                                    d="M55.5714 28.1428C54.5963 29.118 52.902 29.1839 50.9706 28.5011L28.5011 50.9706C29.1839 52.9019 29.1181 54.5962 28.1429 55.5714L55.5715 28.1429L55.5714 28.1428ZM14.4363 41.8495C15.4126 40.8816 17.1031 40.8179 19.0295 41.4989L41.4989 19.0295C40.8183 17.104 40.8816 15.4142 41.8481 14.4376L14.4363 41.8495Z"
                                                    fill="var(--primary-text-color)"></path>
                                                <path fill-rule="evenodd" clip-rule="evenodd"
                                                    d="M25.9548 53.3834C25.9678 53.173 25.9404 52.7703 25.7128 52.0873C25.2685 50.7546 24.2286 49.0427 22.593 47.407C20.9573 45.7714 19.2454 44.7315 17.9127 44.2872C17.2297 44.0596 16.827 44.0322 16.6167 44.0452C16.6037 44.2556 16.631 44.6583 16.8587 45.3412C17.3029 46.674 18.3428 48.3859 19.9785 50.0215C21.6141 51.6572 23.326 52.6971 24.6588 53.1413C25.3417 53.369 25.7444 53.3963 25.9548 53.3834ZM17.8571 52.1429C21.6442 55.93 26.2493 57.465 28.1429 55.5714C30.0364 53.6779 28.5014 49.0728 24.7143 45.2857C20.9272 41.4986 16.3221 39.9636 14.4286 41.8571C12.535 43.7507 14.0701 48.3558 17.8571 52.1429Z"
                                                    fill="var(--primary-text-color)"></path>
                                                <rect x="0.5" y="0.5" width="69" height="69" rx="34.5" stroke="#DDDDDD">
                                                </rect>
                                            </svg>
                                        </div>
                                    </div>
                                    <div class="nsc-nav-link" id="nav-mounting-tab" data-toggle="pill" data-index="7"
                                        data-label="Extras" data-target="#nav-mounting" role="tab"
                                        aria-controls="nav-mounting" aria-selected="false">
                                        <div>Extras</div>
                                        <div class="p-2">
                                            <svg width="70" height="70" viewBox="0 0 70 70" fill="none"
                                                xmlns="http://www.w3.org/2000/svg">
                                                <path
                                                    d="M45.5 23H53M17 23H39.5M45.5 47H53M17 47H39.5M29.5 35H53M17 35H24.5"
                                                    stroke="var(--primary-text-color)" stroke-width="3"
                                                    stroke-linecap="round" stroke-linejoin="round"></path>
                                                <path
                                                    d="M42.5 26C44.1569 26 45.5 24.6569 45.5 23C45.5 21.3431 44.1569 20 42.5 20C40.8431 20 39.5 21.3431 39.5 23C39.5 24.6569 40.8431 26 42.5 26Z"
                                                    stroke="var(--primary-text-color)" stroke-width="3"
                                                    stroke-linecap="round" stroke-linejoin="round"></path>
                                                <path
                                                    d="M27 38C28.6569 38 30 36.6569 30 35C30 33.3431 28.6569 32 27 32C25.3431 32 24 33.3431 24 35C24 36.6569 25.3431 38 27 38Z"
                                                    fill="var(--primary-text-color)" stroke="var(--primary-text-color)"
                                                    stroke-width="3" stroke-linecap="round" stroke-linejoin="round">
                                                </path>
                                                <path
                                                    d="M42.5 50C44.1569 50 45.5 48.6569 45.5 47C45.5 45.3431 44.1569 44 42.5 44C40.8431 44 39.5 45.3431 39.5 47C39.5 48.6569 40.8431 50 42.5 50Z"
                                                    stroke="var(--primary-text-color)" stroke-width="3"
                                                    stroke-linecap="round" stroke-linejoin="round"></path>
                                                <rect x="0.5" y="0.5" width="69" height="69" rx="34.5" stroke="#DDDDDD">
                                                </rect>
                                            </svg>
                                        </div>
                                    </div>
                                    <!-- <div class="nsc-nav-link" id="nav-remote-control-tab" data-toggle="pill"
                                        data-index="8" data-label="Remote Control" data-target="#nav-remote-control"
                                        role="tab" aria-controls="nav-remote-control" aria-selected="false">
                                        <div>Remote Control</div>
                                        <div class="p-2">
                                            <svg xmlns="http://www.w3.org/2000/svg" width="70" height="70"
                                                fill="currentColor" class="bi bi-dpad" viewBox="0 0 70 70">
                                                <path stroke="var(--primary-text-color)"
                                                    d="m7.788 2.34-.799 1.278A.25.25 0 0 0 7.201 4h1.598a.25.25 0 0 0 .212-.382l-.799-1.279a.25.25 0 0 0-.424 0Zm0 11.32-.799-1.277A.25.25 0 0 1 7.201 12h1.598a.25.25 0 0 1 .212.383l-.799 1.278a.25.25 0 0 1-.424 0ZM3.617 9.01 2.34 8.213a.25.25 0 0 1 0-.424l1.278-.799A.25.25 0 0 1 4 7.201V8.8a.25.25 0 0 1-.383.212Zm10.043-.798-1.277.799A.25.25 0 0 1 12 8.799V7.2a.25.25 0 0 1 .383-.212l1.278.799a.25.25 0 0 1 0 .424Z" />
                                                <path stroke="var(--primary-text-color)"
                                                    d="M6.5 0A1.5 1.5 0 0 0 5 1.5v3a.5.5 0 0 1-.5.5h-3A1.5 1.5 0 0 0 0 6.5v3A1.5 1.5 0 0 0 1.5 11h3a.5.5 0 0 1 .5.5v3A1.5 1.5 0 0 0 6.5 16h3a1.5 1.5 0 0 0 1.5-1.5v-3a.5.5 0 0 1 .5-.5h3A1.5 1.5 0 0 0 16 9.5v-3A1.5 1.5 0 0 0 14.5 5h-3a.5.5 0 0 1-.5-.5v-3A1.5 1.5 0 0 0 9.5 0h-3ZM6 1.5a.5.5 0 0 1 .5-.5h3a.5.5 0 0 1 .5.5v3A1.5 1.5 0 0 0 11.5 6h3a.5.5 0 0 1 .5.5v3a.5.5 0 0 1-.5.5h-3a1.5 1.5 0 0 0-1.5 1.5v3a.5.5 0 0 1-.5.5h-3a.5.5 0 0 1-.5-.5v-3A1.5 1.5 0 0 0 4.5 10h-3a.5.5 0 0 1-.5-.5v-3a.5.5 0 0 1 .5-.5h3A1.5 1.5 0 0 0 6 4.5v-3Z" />
                                            </svg>
                                        </div>
                                    </div>
                                    <div class="nsc-nav-link" id="nav-plug-type-tab" data-toggle="pill" data-index="9"
                                        data-label="Plug Type" data-target="#nav-plug-type" role="tab"
                                        aria-controls="nav-plug-type" aria-selected="false">
                                        <div>Plug Type</div>
                                        <div class="p-2">
                                            <svg width="70" height="70" viewBox="0 0 70 70" fill="none"
                                                xmlns="http://www.w3.org/2000/svg">
                                                <path
                                                    d="M47.017 20C45.9792 20 45.1379 20.8413 45.1379 21.8791C45.1379 22.3774 44.9345 22.8554 44.5723 23.2078C44.2102 23.5601 43.719 23.7581 43.2069 23.7581H27.7586C26.2222 23.7581 24.7487 24.352 23.6623 25.4092C22.5759 26.4664 21.9655 27.9002 21.9655 29.3953C21.9655 30.4331 22.8068 31.2744 23.8446 31.2744H23.9485C24.9863 31.2744 25.8276 30.4331 25.8276 29.3953C25.8276 28.8969 26.031 28.419 26.3932 28.0666C26.7553 27.7142 27.2465 27.5162 27.7586 27.5162H33.769L31.1959 35.0325H26.7411C25.7034 35.0325 24.8621 35.8738 24.8621 36.9115C24.8621 37.9493 25.7034 38.7906 26.7411 38.7906H29.9069L27.6572 45.3673C27.5352 45.669 27.31 45.9206 27.0196 46.0795C26.7293 46.2385 26.3914 46.2952 26.0631 46.24C25.7347 46.1848 25.436 46.0212 25.2172 45.7766C24.9985 45.5321 24.873 45.2216 24.8621 44.8976V44.4798C24.8621 43.4133 23.9975 42.5487 22.931 42.5487C21.8646 42.5487 21 43.4133 21 44.4798V44.8976C21.0157 46.1121 21.4705 47.2824 22.2847 48.203C23.0988 49.1237 24.2202 49.7359 25.452 49.9321C26.6838 50.1283 27.9473 49.8961 29.0206 49.2761C30.0939 48.6561 30.9085 47.688 31.3214 46.5417L33.9766 38.7906H38.4313C39.4691 38.7906 40.3103 37.9493 40.3103 36.9115C40.3103 35.8738 39.4691 35.0325 38.4313 35.0325H35.2655L37.8386 27.5162H43.2069C44.7433 27.5162 46.2168 26.9223 47.3032 25.8652C48.3897 24.808 49 23.3741 49 21.8791C49 20.8413 48.1587 20 47.1209 20H47.017Z"
                                                    fill="var(--primary-text-color)"></path>
                                                <rect x="0.5" y="0.5" width="69" height="69" rx="34.5" stroke="#DDDDDD">
                                                </rect>
                                            </svg>
                                        </div>
                                    </div>
                                    <div class="nsc-nav-link" id="nav-cable-color-tab" data-toggle="pill"
                                        data-index="10" data-label="Cable Color" data-target="#nav-cable-color"
                                        role="tab" aria-controls="nav-cable-color" aria-selected="false">
                                        <div>Cable Color</div>
                                        <div class="p-2">
                                            <svg width="70" height="70" viewBox="0 0 70 70" fill="none"
                                                xmlns="http://www.w3.org/2000/svg">
                                                <path
                                                    d="M47.017 20C45.9792 20 45.1379 20.8413 45.1379 21.8791C45.1379 22.3774 44.9345 22.8554 44.5723 23.2078C44.2102 23.5601 43.719 23.7581 43.2069 23.7581H27.7586C26.2222 23.7581 24.7487 24.352 23.6623 25.4092C22.5759 26.4664 21.9655 27.9002 21.9655 29.3953C21.9655 30.4331 22.8068 31.2744 23.8446 31.2744H23.9485C24.9863 31.2744 25.8276 30.4331 25.8276 29.3953C25.8276 28.8969 26.031 28.419 26.3932 28.0666C26.7553 27.7142 27.2465 27.5162 27.7586 27.5162H33.769L31.1959 35.0325H26.7411C25.7034 35.0325 24.8621 35.8738 24.8621 36.9115C24.8621 37.9493 25.7034 38.7906 26.7411 38.7906H29.9069L27.6572 45.3673C27.5352 45.669 27.31 45.9206 27.0196 46.0795C26.7293 46.2385 26.3914 46.2952 26.0631 46.24C25.7347 46.1848 25.436 46.0212 25.2172 45.7766C24.9985 45.5321 24.873 45.2216 24.8621 44.8976V44.4798C24.8621 43.4133 23.9975 42.5487 22.931 42.5487C21.8646 42.5487 21 43.4133 21 44.4798V44.8976C21.0157 46.1121 21.4705 47.2824 22.2847 48.203C23.0988 49.1237 24.2202 49.7359 25.452 49.9321C26.6838 50.1283 27.9473 49.8961 29.0206 49.2761C30.0939 48.6561 30.9085 47.688 31.3214 46.5417L33.9766 38.7906H38.4313C39.4691 38.7906 40.3103 37.9493 40.3103 36.9115C40.3103 35.8738 39.4691 35.0325 38.4313 35.0325H35.2655L37.8386 27.5162H43.2069C44.7433 27.5162 46.2168 26.9223 47.3032 25.8652C48.3897 24.808 49 23.3741 49 21.8791C49 20.8413 48.1587 20 47.1209 20H47.017Z"
                                                    fill="var(--primary-text-color)"></path>
                                                <rect x="0.5" y="0.5" width="69" height="69" rx="34.5" stroke="#DDDDDD">
                                                </rect>
                                            </svg>
                                        </div>
                                    </div>
                                    <div class="nsc-nav-link" id="nav-request-tab" data-toggle="pill" data-index="11"
                                        data-label="Special Request" data-target="#nav-request" role="tab"
                                        aria-controls="nav-request" aria-selected="false">
                                        <div>Special Request</div>
                                        <div class="p-2">
                                            <svg width="70" height="70" viewBox="0 0 70 70" fill="none"
                                                xmlns="http://www.w3.org/2000/svg">
                                                <path
                                                    d="M47.017 20C45.9792 20 45.1379 20.8413 45.1379 21.8791C45.1379 22.3774 44.9345 22.8554 44.5723 23.2078C44.2102 23.5601 43.719 23.7581 43.2069 23.7581H27.7586C26.2222 23.7581 24.7487 24.352 23.6623 25.4092C22.5759 26.4664 21.9655 27.9002 21.9655 29.3953C21.9655 30.4331 22.8068 31.2744 23.8446 31.2744H23.9485C24.9863 31.2744 25.8276 30.4331 25.8276 29.3953C25.8276 28.8969 26.031 28.419 26.3932 28.0666C26.7553 27.7142 27.2465 27.5162 27.7586 27.5162H33.769L31.1959 35.0325H26.7411C25.7034 35.0325 24.8621 35.8738 24.8621 36.9115C24.8621 37.9493 25.7034 38.7906 26.7411 38.7906H29.9069L27.6572 45.3673C27.5352 45.669 27.31 45.9206 27.0196 46.0795C26.7293 46.2385 26.3914 46.2952 26.0631 46.24C25.7347 46.1848 25.436 46.0212 25.2172 45.7766C24.9985 45.5321 24.873 45.2216 24.8621 44.8976V44.4798C24.8621 43.4133 23.9975 42.5487 22.931 42.5487C21.8646 42.5487 21 43.4133 21 44.4798V44.8976C21.0157 46.1121 21.4705 47.2824 22.2847 48.203C23.0988 49.1237 24.2202 49.7359 25.452 49.9321C26.6838 50.1283 27.9473 49.8961 29.0206 49.2761C30.0939 48.6561 30.9085 47.688 31.3214 46.5417L33.9766 38.7906H38.4313C39.4691 38.7906 40.3103 37.9493 40.3103 36.9115C40.3103 35.8738 39.4691 35.0325 38.4313 35.0325H35.2655L37.8386 27.5162H43.2069C44.7433 27.5162 46.2168 26.9223 47.3032 25.8652C48.3897 24.808 49 23.3741 49 21.8791C49 20.8413 48.1587 20 47.1209 20H47.017Z"
                                                    fill="var(--primary-text-color)"></path>
                                                <rect x="0.5" y="0.5" width="69" height="69" rx="34.5" stroke="#DDDDDD">
                                                </rect>
                                            </svg>
                                        </div>
                                    </div> -->
                                    <div class="nsc-nav-link" id="nav-review-tab" data-toggle="pill" data-index="8"
                                        data-label="Review" data-target="#nav-review" role="tab"
                                        aria-controls="nav-review" aria-selected="false">
                                        <div>Review</div>
                                        <div class="p-2">
                                            <svg width="70" height="70" viewBox="0 0 70 70" fill="none"
                                                xmlns="http://www.w3.org/2000/svg">
                                                <path fill-rule="evenodd" clip-rule="evenodd"
                                                    d="M52.1519 19.4984L28.2756 10.256C24.9991 8.98762 21.4707 11.4053 21.4707 14.9188V16.9032L24.4707 18.0645V14.9188C24.4707 13.5134 25.882 12.5463 27.1927 13.0537L51.0689 22.2961C51.8392 22.5943 52.3469 23.3353 52.3469 24.1612V52.2445C52.3469 53.6499 50.9356 54.6169 49.625 54.1096L46.5919 52.9355V56.0812C46.5919 56.1049 46.5914 56.1285 46.5907 56.1519L48.542 56.9073C51.8185 58.1756 55.3469 55.758 55.3469 52.2445V24.1612C55.3469 22.0963 54.0776 20.2438 52.1519 19.4984Z"
                                                    fill="var(--primary-text-color)"></path>
                                                <path fill-rule="evenodd" clip-rule="evenodd"
                                                    d="M22.5205 14.0927L46.3968 23.3351C48.3225 24.0806 49.5919 25.9331 49.5919 27.998V56.0812C49.5919 59.5947 46.0634 62.0124 42.7869 60.744L18.9106 51.5016C16.9849 50.7562 15.7156 48.9037 15.7156 46.8388V18.7555C15.7156 15.2421 19.244 12.8244 22.5205 14.0927ZM21.4376 16.8904L45.3138 26.1328C46.0841 26.431 46.5919 27.172 46.5919 27.998V56.0812C46.5919 57.4866 45.1805 58.4537 43.8699 57.9463L19.9936 48.7039C19.2233 48.4057 18.7156 47.6647 18.7156 46.8388V18.7555C18.7156 17.3501 20.1269 16.3831 21.4376 16.8904Z"
                                                    fill="var(--primary-text-color)"></path>
                                                <circle cx="23.5" cy="23.5" r="1.5" fill="var(--primary-text-color)">
                                                </circle>
                                                <ellipse cx="23.5" cy="45" rx="1.5" ry="2"
                                                    fill="var(--primary-text-color)"></ellipse>
                                                <circle cx="42" cy="30" r="2" fill="var(--primary-text-color)">
                                                </circle>
                                                <circle cx="42" cy="51" r="2" fill="var(--primary-text-color)">
                                                </circle>
                                                <rect x="0.5" y="0.5" width="69" height="69" rx="34.5" stroke="#DDDDDD">
                                                </rect>
                                            </svg>
                                        </div>
                                    </div>
                                </div>

                            </nav>
                        </div>
                        <div class="tab-content" id="nav-tabContent">
                            <div class="tab-pane fade show active" id="nav-text" role="tabpanel"
                                aria-labelledby="nav-text-tab">
                                <!-- <br/> -->
                                <label for="nsc-text"><strong>Text</strong></label>
                                <textarea class="w-100 pr-5" id="nsc-text" data-lines="1" data-line-one=""
                                    data-line-two="" data-line-three=""
                                    placeholder="Neon Sign"><?= isset($_SESSION['neon-size']['text']) ? $_SESSION['neon-size']['text'] : "Neon Sign" ?></textarea>
                                <i>
                                    <div id="warning-message" style="color: red;"></div>
                                    <div id="warning-message-width" style="color: red;"></div>
                                    <div id="warning-message-height" style="color: red;"></div>
                                </i>
                            </div>
                            <div class="tab-pane fade" id="nav-font" role="tabpanel" aria-labelledby="nav-font-tab">
                                <!-- <br/> -->
                                <?php
                                // if ($nsc_settings['font'] == 1) {} ?>
                                <div class="d-flex justify-content-between mt-2">
                                    <label style="padding: 0; margin: 0">
                                        <strong>Font</strong><br />
                                    </label>
                                </div>
                                <div>
                                    <div class="d-flex justify-content-start">
                                        <div class="mr-2"
                                            style="width: 50%; border: solid 1.5px var(--primary-text-color); position: relative;">
                                            <div id="nsc-font-toggle" class="p-2">
                                            </div>
                                            <div id="nsc-font-toggle-hide"
                                                style="position: absolute; right:0; top:0; padding:15px 10px; color:var(--primary-text-color); pointer-events: none; ">
                                                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16"
                                                    fill="currentColor" class="bi bi-chevron-down" viewBox="0 0 16 16">
                                                    <path fill-rule="evenodd"
                                                        d="M1.646 4.646a.5.5 0 0 1 .708 0L8 10.293l5.646-5.647a.5.5 0 0 1 .708.708l-6 6a.5.5 0 0 1-.708 0l-6-6a.5.5 0 0 1 0-.708z" />
                                                </svg>
                                            </div>
                                            <div id="nsc-font-toggle-show"
                                                style="position: absolute; right:0; top:0; padding:15px 10px; color:var(--primary-text-color); pointer-events: none; display: none; ">
                                                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16"
                                                    fill="currentColor" class="bi bi-chevron-up" viewBox="0 0 16 16">
                                                    <path fill-rule="evenodd"
                                                        d="M7.646 4.646a.5.5 0 0 1 .708 0l6 6a.5.5 0 0 1-.708.708L8 5.707l-5.646 5.647a.5.5 0 0 1-.708-.708l6-6z" />
                                                </svg>
                                            </div>
                                        </div>
                                        <div id="nsc-font-left" class="btn nsc-box mr-2 font-align"
                                            style="padding:12px">
                                            <svg width="24" height="23" viewBox="0 0 24 23" fill="none"
                                                xmlns="http://www.w3.org/2000/svg">
                                                <path
                                                    d="M15.0362 0.501566H0.972786C0.714267 0.501566 0.559155 0.708382 0.559155 0.915197V1.74246C0.559155 2.00098 0.714267 2.15609 0.972786 2.15609H15.0362C15.243 2.15609 15.4498 2.00098 15.4498 1.74246V0.915197C15.4498 0.708382 15.243 0.501566 15.0362 0.501566ZM23.3088 20.3558H0.972786C0.714267 20.3558 0.559155 20.5626 0.559155 20.7694V21.5967C0.559155 21.8552 0.714267 22.0103 0.972786 22.0103H23.3088C23.5156 22.0103 23.7224 21.8552 23.7224 21.5967V20.7694C23.7224 20.5626 23.5156 20.3558 23.3088 20.3558ZM15.0362 13.7377H0.972786C0.714267 13.7377 0.559155 13.9445 0.559155 14.1514V14.9786C0.559155 15.2371 0.714267 15.3923 0.972786 15.3923H15.0362C15.243 15.3923 15.4498 15.2371 15.4498 14.9786V14.1514C15.4498 13.9445 15.243 13.7377 15.0362 13.7377ZM23.3088 7.11965H0.972786C0.714267 7.11965 0.559155 7.32647 0.559155 7.53328V8.36054C0.559155 8.61906 0.714267 8.77417 0.972786 8.77417H23.3088C23.5156 8.77417 23.7224 8.61906 23.7224 8.36054V7.53328C23.7224 7.32647 23.5156 7.11965 23.3088 7.11965Z"
                                                    fill="currentColor"></path>
                                            </svg>
                                        </div>
                                        <div id="nsc-font-center" class="btn nsc-box mr-2 font-align"
                                            style="padding:12px">
                                            <svg width="24" height="23" viewBox="0 0 24 23" fill="none"
                                                xmlns="http://www.w3.org/2000/svg">
                                                <path
                                                    d="M17.9166 0.604201H5.50771C5.24919 0.604201 5.09408 0.811016 5.09408 1.01783V1.84509C5.09408 2.10361 5.24919 2.25872 5.50771 2.25872H17.9166C18.1234 2.25872 18.3302 2.10361 18.3302 1.84509V1.01783C18.3302 0.811016 18.1234 0.604201 17.9166 0.604201ZM22.8802 20.4585H0.544143C0.285625 20.4585 0.130513 20.6653 0.130513 20.8721V21.6993C0.130513 21.9579 0.285625 22.113 0.544143 22.113H22.8802C23.087 22.113 23.2938 21.9579 23.2938 21.6993V20.8721C23.2938 20.6653 23.087 20.4585 22.8802 20.4585ZM17.9166 13.8404H5.50771C5.24919 13.8404 5.09408 14.0472 5.09408 14.254V15.0813C5.09408 15.3398 5.24919 15.4949 5.50771 15.4949H17.9166C18.1234 15.4949 18.3302 15.3398 18.3302 15.0813V14.254C18.3302 14.0472 18.1234 13.8404 17.9166 13.8404ZM22.8802 7.22228H0.544143C0.285625 7.22228 0.130513 7.4291 0.130513 7.63592V8.46318C0.130513 8.72169 0.285625 8.87681 0.544143 8.87681H22.8802C23.087 8.87681 23.2938 8.72169 23.2938 8.46318V7.63592C23.2938 7.4291 23.087 7.22228 22.8802 7.22228Z"
                                                    fill="currentColor"></path>
                                            </svg>
                                        </div>
                                        <div id="nsc-font-right" class="btn nsc-box mr-2 font-align"
                                            style="padding:12px">
                                            <svg width="25" height="23" viewBox="0 0 25 23" fill="none"
                                                xmlns="http://www.w3.org/2000/svg">
                                                <path
                                                    d="M23.7405 0.604201H9.6771C9.41858 0.604201 9.26347 0.811016 9.26347 1.01783V1.84509C9.26347 2.10361 9.41858 2.25872 9.6771 2.25872H23.7405C23.9473 2.25872 24.1542 2.10361 24.1542 1.84509V1.01783C24.1542 0.811016 23.9473 0.604201 23.7405 0.604201ZM23.7405 20.4585H1.4045C1.14598 20.4585 0.990865 20.6653 0.990865 20.8721V21.6993C0.990865 21.9579 1.14598 22.113 1.4045 22.113H23.7405C23.9473 22.113 24.1542 21.9579 24.1542 21.6993V20.8721C24.1542 20.6653 23.9473 20.4585 23.7405 20.4585ZM23.7405 13.8404H9.6771C9.41858 13.8404 9.26347 14.0472 9.26347 14.254V15.0813C9.26347 15.3398 9.41858 15.4949 9.6771 15.4949H23.7405C23.9473 15.4949 24.1542 15.3398 24.1542 15.0813V14.254C24.1542 14.0472 23.9473 13.8404 23.7405 13.8404ZM23.7405 7.22228H1.4045C1.14598 7.22228 0.990865 7.4291 0.990865 7.63592V8.46318C0.990865 8.72169 1.14598 8.87681 1.4045 8.87681H23.7405C23.9473 8.87681 24.1542 8.72169 24.1542 8.46318V7.63592C24.1542 7.4291 23.9473 7.22228 23.7405 7.22228Z"
                                                    fill="currentColor"></path>
                                            </svg>
                                        </div>
                                    </div>
                                    <div id="nsc-font-list">
                                        <div class="row pt-1 m-0">
                                            <?php
                                            $fistTime = true;
                                            foreach ($nsc_fonts_results as $data) {
                                                if ($fistTime) {
                                                    if (isset($_SESSION['neon-size']['font'])) {
                                                        echo '<input type="hidden" id="nsc-font" 
                                        data-id="' . $_SESSION['neon-size']['font']['id'] . '" 
                                        data-name="' . $_SESSION['neon-size']['font']['name'] . '"     
                                        data-extra-price="' . $_SESSION['neon-size']['font']['extraPrice'] . '" 
                                        data-price-type="' . $_SESSION['neon-size']['font']['priceType'] . '"                                     
                                        data-toggle="' . $_SESSION['neon-size']['font']['toggle'] . '"  
                                        data-align="' . $_SESSION['neon-size']['font']['align'] . '"  
                                        data-font-url="' . $_SESSION['neon-size']['font']['fontUrl'] . '" />';
                                                    } else {
                                                        $_SESSION['neon-size']['font'] = array(
                                                            'id' => $data->font_id,
                                                            'name' => $data->font_name,
                                                            'extraPrice' => $data->extra_price,
                                                            'priceType' => $data->extra_price_type,
                                                            'toggle' => "0",
                                                            'align' => 'left',
                                                            'fontUrl' => $plugin_dir . 'font_url/' . $data->font_url
                                                        );
                                                        echo '<input type="hidden" 
                                            id="nsc-font" 
                                            data-id="' . $data->font_id . '" 
                                            data-name="' . $data->font_name . '" 
                                            data-extra-price="' . $data->extra_price . '" 
                                            data-price-type="' . $data->extra_price_type . '"
                                            data-toggle="0" 
                                            data-align="left" 
                                            data-font-url="' . $plugin_dir . 'font_url/' . $data->font_url . '" />';
                                                    }
                                                    $fistTime = false;
                                                }
                                                echo '
                             <div class="col-md-4 p-0">                       
                            <div
                                id="font-' . $data->font_id . '" 
                                class="nsc-box mt-1 mr-1 rounded bg-white font-click text-center" 
                                data-id="' . $data->font_id . '" 
                                data-name="' . $data->font_name . '" 
                                data-extra-price="' . $data->extra_price . '" 
                                data-price-type="' . $data->extra_price_type . '"
                                data-font-url="' . $plugin_dir . 'font_url/' . $data->font_url . '" style="                                                                   
                                font-family: ' . strtolower(str_replace(' ', '', $data->font_name)) . ';
                                line-height:55px;
                                width: 97%;
                                height: 60px;
                                font-size: 1em;               
                                    ">' . $data->font_name . ' 
                           </div>
                           </div>
                            ';
                                            }
                                            ?>

                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="tab-pane fade" id="nav-color" role="tabpanel" aria-labelledby="nav-color-tab">
                                <!-- <br /> -->
                                <?php
                                // if ($nsc_settings['color'] == 1) {} ?>
                                <div class="d-flex justify-content-between mt-2">
                                    <label for="nsc-color" style="padding: 0; margin: 0"><strong>Color</strong><span
                                            id="nsc-color-selected"></span></label>
                                </div>
                                <div class="d-flex">
                                    <?php
                                    $fistTime = true;
                                    foreach ($nsc_colors_results as $data) {
                                        if ($fistTime) {
                                            if (isset($_SESSION['neon-size']['color'])) {
                                                echo '<input type="hidden" id="nsc-color" 
                                        data-id="' . $_SESSION['neon-size']['color']['id'] . '" 
                                        data-name="' . $_SESSION['neon-size']['color']['name'] . '" 
                                        data-hex="' . $_SESSION['neon-size']['color']['hex'] . '" 
                                        data-rbg="' . $_SESSION['neon-size']['color']['rbg'] . '" 
                                        data-toggle="' . $_SESSION['neon-size']['color']['toggle'] . '" 
                                        data-extra-price="' . $_SESSION['neon-size']['color']['extraPrice'] . '" 
                                        data-price-type="' . $_SESSION['neon-size']['color']['priceType'] . '" 
                                        />';
                                            } else {
                                                $_SESSION['neon-size']['color'] = array(
                                                    'id' => $data->color_id,
                                                    'name' => $data->color_name,
                                                    'hex' => $data->color_hex,
                                                    'rbg' => $data->is_rbg,
                                                    'toggle' => 'on',
                                                    'extraPrice' => $data->extra_price,
                                                    'priceType' => $data->extra_price_type
                                                );
                                                echo '<input type="hidden" 
                                            id="nsc-color" 
                                            data-id="' . $data->color_id . '" 
                                            data-name="' . $data->color_name . '" 
                                            data-hex="' . $data->color_hex . '" 
                                            data-rbg="' . $data->is_rbg . '" 
                                            data-toggle="on" 
                                            data-extra-price="' . $data->extra_price . '" 
                                            data-price-type="' . $data->extra_price_type . '"  />';
                                            }
                                            $fistTime = false;
                                        }

                                        $colorStyle = "";
                                        if ($data->is_rbg == "0") {
                                            $colorStyle = "background-image: linear-gradient(140deg, #ffffff5e 5%, " . $data->color_hex . " 50%, #ffffff5e 95%);";
                                        } else {

                                            $colorStyle = "background-image: url(" . $plugin_dir . "color_img/is_rpg.png); background-size: contain;";
                                        }
                                        echo '
                           <div class="text-center">
                            <div
                                id="colors-' . $data->color_id . '" 
                                class="nsc-box mt-1 mr-1 rounded bg-white colors-click" 
                                data-id="' . $data->color_id . '" 
                                data-name="' . $data->color_name . '" 
                                data-hex="' . $data->color_hex . '" 
                                data-rbg="' . $data->is_rbg . '" 
                                data-toggle="on" 
                                data-extra-price="' . $data->extra_price . '" 
                                data-price-type="' . $data->extra_price_type . '" 
                                style="         
                                ' . $colorStyle . '
                                background-clip:content-box;
                                width: 70px;
                                height: 70px;
                                    ">                                   
                            </div>
                           </div>
                            ';
                                    }
                                    ?>
                                </div>
                            </div>
                            <div class="tab-pane fade" id="nav-size" role="tabpanel" aria-labelledby="nav-size-tab">
                                <!-- <br /> -->

                                <?php
                                // if ($nsc_settings['size'] == 1) {} ?>
                                <div id="size-title">
                                    <div class="d-flex justify-content-between mt-2">
                                        <strong><label for="nsc-size" style="padding: 0; margin: 0">Size <span
                                                    id="size-name"></span></label></strong>
                                    </div>
                                </div>
                                <div>
                                    <div class="row d-grid">
                                        <?php
                                        $fistTime = true;
                                        foreach ($nsc_fpz_results as $data) {
                                            if ($fistTime) {
                                                if (isset($_SESSION['neon-size']['size'])) {
                                                    echo '<input type="hidden" id="nsc-size" 
                                        data-id="' . $_SESSION['neon-size']['size']['id'] . '" 
                                        data-name="' . $_SESSION['neon-size']['size']['name'] . '" 
                                        data-font-id="' . $_SESSION['neon-size']['size']['fontId'] . '"
                                        data-font-size="' . $_SESSION['neon-size']['size']['fontSize'] . '"
                                        data-line-one="' . $_SESSION['neon-size']['size']['lineOne'] . '" 
                                        data-line-two="' . $_SESSION['neon-size']['size']['lineTwo'] . '" 
                                        data-line-three="' . $_SESSION['neon-size']['size']['lineThree'] . '" 
                                        data-size-length="' . $_SESSION['neon-size']['size']['sizeLength'] . '" 
                                        data-size-vol="' . $_SESSION['neon-size']['size']['sizeVol'] . '" 
                                        data-size-fee="' . $_SESSION['neon-size']['size']['sizeFee'] . '" 
                                        data-text-width="' . $_SESSION['neon-size']['size']['textWidth'] . '" 
                                        data-text-height="' . $_SESSION['neon-size']['size']['textHeight'] . '" 
                                        data-text-line="' . $_SESSION['neon-size']['size']['TextLine'] . '" 
                                        data-size-char-min="' . $_SESSION['neon-size']['size']['sizeCharMin'] . '" 
                                        data-size-char-max="' . $_SESSION['neon-size']['size']['sizeCharMax'] . '" />';
                                                } else {
                                                    $_SESSION['neon-size']['size'] = array(
                                                        'id' => $data->size_id,
                                                        'name' => $data->size_name,
                                                        'fontId' => $data->font_id,
                                                        'fontSize' => $data->size_des,
                                                        'lineOne' => $data->line_one_base . '@' . $data->line_one_letter,
                                                        'lineTwo' => $data->line_two_base . '@' . $data->line_two_letter,
                                                        'lineThree' => $data->line_three_base . '@' . $data->line_three_letter,
                                                        'sizeLength' => $data->size_length,
                                                        'sizeVol' => $data->size_vol,
                                                        'sizeFee' => $data->size_fee,
                                                        'textWidth' => $data->text_width,
                                                        'textHeight' => $data->text_height,
                                                        'TextLine' => $data->text_line,
                                                        'sizeCharMin' => $data->size_char_min,
                                                        'sizeCharMax' => $data->size_char_max
                                                    );
                                                    echo '<input type="hidden" 
                                            id="nsc-size" 
                                            data-id="' . $data->size_id . '" 
                                            data-name="' . $data->size_name . '" 
                                            data-font-id="' . $data->font_id . '"
                                            data-font-size="' . $data->size_des . '"
                                            data-line-one="' . $data->line_one_base . '@' . $data->line_one_letter . '" 
                                            data-line-two="' . $data->line_two_base . '@' . $data->line_two_letter . '" 
                                            data-line-three="' . $data->line_three_base . '@' . $data->line_three_letter . '" 
                                            data-size-length="' . $data->size_length . '" 
                                            data-size-vol="' . $data->size_vol . '" 
                                            data-size-fee="' . $data->size_fee . '" 
                                            data-text-width="' . $data->text_width . '" 
                                            data-text-height="' . $data->text_height . '" 
                                            data-text-line="' . $data->text_line . '" 
                                            data-size-char-min="' . $data->size_char_min . '" 
                                            data-size-char-max="' . $data->size_char_max . '"/>';
                                                }
                                                $fistTime = false;
                                            }

                                            echo ' 
                           <div class="col-4 p-0 size-click-wrapper size-group-' . $data->font_id . '-wrapper">                            
                                <div
                                    id="size-' . $data->size_id . $data->font_id . '" 
                                    class="nsc-box mt-1 mr-1 rounded bg-white size-click text-right size-group-' . $data->font_id . '" 
                                        data-id="' . $data->size_id . '" 
                                        data-name="' . $data->size_name . '" 
                                        data-font-id="' . $data->font_id . '"
                                        data-font-size="' . $data->size_des . '"
                                        data-line-one="' . $data->line_one_base . '@' . $data->line_one_letter . '" 
                                        data-line-two="' . $data->line_two_base . '@' . $data->line_two_letter . '" 
                                        data-line-three="' . $data->line_three_base . '@' . $data->line_three_letter . '" 
                                        data-size-length="' . $data->size_length . '" 
                                        data-size-vol="' . $data->size_vol . '" 
                                        data-size-fee="' . $data->size_fee . '" 
                                        data-text-width="' . $data->text_width . '" 
                                        data-text-height="' . $data->text_height . '" 
                                        data-text-line="' . $data->text_line . '" 
                                        data-size-char-min="' . $data->size_char_min . '" 
                                        data-size-char-max="' . $data->size_char_max . '"
                                        style="padding: 5px; font-size: 1.2rem;"> ' . $data->size_name . ' 
                                        <div class="child-size" style="font-size: 0.75rem; line-height:1.5"></div>
                                </div>
                           </div>
                            ';

                                        }
                                        ?>
                                    </div>
                                </div>
                            </div>
                            <div class="tab-pane fade" id="nav-backboard" role="tabpanel"
                                aria-labelledby="nav-backboard-tab">
                                <!-- <br /> -->

                                <?php
                                if ($nsc_settings['backboard'] == 1) {
                                    ?>
                                <div class="d-flex justify-content-between mt-2">
                                    <label for="nsc-backboard" style="padding: 0; margin: 0">
                                        <strong>Backboard</strong><br />
                                    </label>
                                </div>
                                <small><i>The backboard of a neon sign is a flat surface that serves as a base for
                                        mounting the
                                        neon
                                        tubes and electrical components</i></small>
                                <div>
                                    <div class="row">
                                        <?php
                                            $fistTime = true;
                                            foreach ($nsc_backboard_results as $data) {
                                                if ($fistTime) {
                                                    if (isset($_SESSION['neon-size']['backboard'])) {
                                                        echo '<input type="hidden" id="nsc-backboard" 
                                        data-id="' . $_SESSION['neon-size']['backboard']['id'] . '" 
                                        data-name="' . $_SESSION['neon-size']['backboard']['name'] . '" 
                                        data-size="' . $_SESSION['neon-size']['backboard']['size'] . '" 
                                        data-extra-price="' . $_SESSION['neon-size']['backboard']['extraPrice'] . '" 
                                        data-price-type="' . $_SESSION['neon-size']['backboard']['priceType'] . '" 
                                        />';
                                                    } else {
                                                        $_SESSION['neon-size']['backboard'] = array(
                                                            'id' => $data->backboard_id,
                                                            'name' => $data->backboard_name,
                                                            'size' => '',
                                                            'extraPrice' => $data->extra_price,
                                                            'priceType' => $data->extra_price_type
                                                        );
                                                        echo '<input type="hidden" 
                                            id="nsc-backboard" 
                                            data-id="' . $data->backboard_id . '" 
                                            data-name="' . $data->backboard_name . '" 
                                            data-size="" 
                                            data-extra-price="' . $data->extra_price . '" 
                                            data-price-type="' . $data->extra_price_type . '" 
                                            />';
                                                    }
                                                    $fistTime = false;
                                                }
                                                echo '
                             <div class="col-md-12 p-0">                       
                            <div
                                id="backboard-' . $data->backboard_id . '" 
                                class="nsc-box mt-1 mr-1 rounded bg-white backboard-click text-left " 
                                data-id="' . $data->backboard_id . '" 
                                data-name="' . $data->backboard_name . '" 
                                data-size="" 
                                data-extra-price="' . $data->extra_price . '" 
                                data-price-type="' . $data->extra_price_type . '" 
                                data-image="' . $plugin_dir . 'backboard_img/' . $data->backboard_img . '" style="                                                                   
                                background-size:cover;
                                background-repeat: no-repeat;
                                background-position: center;
                                position:relative;
                                width: 100%;
                                height: 100px;
                                font-size:1rem; 
                                padding: 10px;                       
                                    ">   <strong>' . $data->backboard_name . '  </strong><br/>                              
                                    <i>' . $data->backboard_des . '  </i>                            
                                    <a class="backboard-example text-decoration-none cursor-pointer position-absolute" 
                                    data-image="' . $plugin_dir . 'backboard_img/' . $data->backboard_img . '"
                                    data-id="nsc-backboard"
                                    style="top:15; right:15"
                                    >Example</a>
                           </div>
                           
                           </div>
                            ';
                                            }
                                            ?>

                                    </div>
                                </div>
                                <?php } ?>
                                <!-- <br /> -->
                                <?php
                                if ($nsc_settings['backboard_color'] == 1) {
                                    ?>
                                <div id="backboard-color-title">
                                    <div class="d-flex justify-content-between mt-2">
                                        <strong><label for="nsc-backboard-color"
                                                style="padding: 0; margin: 0">Blackboard
                                                Color <span id="backboard-color-name"></span></label></strong>
                                        <a class="text-decoration-none cursor-pointer image-example"
                                            data-id="nsc-backboard-color">Example</a>
                                    </div>
                                </div>
                                <div class="d-flex p-0">
                                    <?php
                                        $fistTime = true;
                                        foreach ($nsc_backboard_color_results as $data) {
                                            if ($fistTime) {
                                                if (isset($_SESSION['neon-size']['backboard_color'])) {
                                                    echo '<input type="hidden" id="nsc-backboard-color" 
                                        data-id="' . $_SESSION['neon-size']['backboard_color']['id'] . '" 
                                        data-parent="' . $_SESSION['neon-size']['backboard_color']['parent'] . '"
                                        data-name="' . $_SESSION['neon-size']['backboard_color']['name'] . '" 
                                        data-hex="' . $_SESSION['neon-size']['backboard_color']['hex'] . '" 
                                        data-extra-price="' . $_SESSION['neon-size']['backboard_color']['extraPrice'] . '" 
                                        data-price-type="' . $_SESSION['neon-size']['backboard_color']['priceType'] . '" 
                                        data-image="' . $_SESSION['neon-size']['backboard_color']['image'] . '" />';
                                                } else {
                                                    $_SESSION['neon-size']['backboard_color'] = array(
                                                        'id' => $data->backboard_color_id,
                                                        'parent' => $data->backboard_id,
                                                        'name' => $data->backboard_color_name,
                                                        'hex' => $data->backboard_color_hex,
                                                        'extraPrice' => $data->extra_price,
                                                        'priceType' => $data->extra_price_type,
                                                        'image' => $plugin_dir . 'backboard_color_img/' . $data->backboard_color_img
                                                    );
                                                    echo '<input type="hidden" 
                                            id="nsc-backboard-color" 
                                            data-id="' . $data->backboard_color_id . '" 
                                            data-parent="' . $data->backboard_id . '" 
                                            data-name="' . $data->backboard_color_name . '" 
                                            data-hex="' . $data->backboard_color_hex . '" 
                                            data-extra-price="' . $data->extra_price . '" 
                                            data-price-type="' . $data->extra_price_type . '" 
                                            data-image="' . $plugin_dir . 'backboard_color_img/' . $data->backboard_color_img . '" />';
                                                }
                                                $fistTime = false;
                                            }
                                            echo '
                           <div class="text-center">                            
                            <div
                                id="backboard-color-' . $data->backboard_color_id . $data->backboard_id . '" 
                                class="nsc-box mt-1 mr-1 rounded bg-white backboard-color-click backboard-group-' . $data->backboard_id . '" 
                                data-id="' . $data->backboard_color_id . '" 
                                data-name="' . $data->backboard_color_name . '" 
                                data-parent="' . $data->backboard_id . '" 
                                data-hex="' . $data->backboard_color_hex . '" 
                                data-extra-price="' . $data->extra_price . '" 
                                data-price-type="' . $data->extra_price_type . '" 
                                data-image="' . $plugin_dir . 'backboard_color_img/' . $data->backboard_color_img . '" style="  
                                position: relative;                                                                 
                                width: 100px;
                                height: 80px;
                                    ">        
                                    <img class="nsc-img" src="' . $plugin_dir . 'backboard_color_img/' . $data->backboard_color_img . '"/>
                            </div>
                           </div>
                            ';

                                        }
                                        ?>
                                </div>
                                <?php } ?>
                            </div>
                            <div class="tab-pane fade" id="nav-material" role="tabpanel"
                                aria-labelledby="nav-material-tab">
                                <!-- <br /> -->

                                <?php
                                if ($nsc_settings['material'] == 1) {
                                    ?>
                                <div class="d-flex justify-content-between mt-2">
                                    <label style="padding: 0; margin: 0">
                                        <strong>Material</strong><br />
                                    </label>
                                    <a class="text-decoration-none cursor-pointer image-example"
                                        data-id="nsc-material">Example</a>
                                </div>
                                <small><i>Choose Neon Sign Material</i></small>
                                <div>
                                    <div class="row">
                                        <?php
                                            $fistTime = true;
                                            foreach ($nsc_additional_results as $data) {
                                                if ($data->additional_type == 'material') {
                                                    if ($fistTime) {
                                                        if (isset($_SESSION['neon-size']['material'])) {
                                                            echo '<input type="hidden" id="nsc-material" 
                                        data-id="' . $_SESSION['neon-size']['material']['id'] . '" 
                                        data-name="' . $_SESSION['neon-size']['material']['name'] . '" 
                                        data-extra-price="' . $_SESSION['neon-size']['material']['extraPrice'] . '" 
                                        data-price-type="' . $_SESSION['neon-size']['material']['priceType'] . '" 
                                        data-image="' . $_SESSION['neon-size']['material']['image'] . '" />';
                                                        } else {
                                                            $_SESSION['neon-size']['material'] = array(
                                                                'id' => $data->additional_id,
                                                                'name' => $data->additional_name,
                                                                'extraPrice' => $data->extra_price,
                                                                'priceType' => $data->extra_price_type,
                                                                'image' => $plugin_dir . 'additional_img/' . $data->additional_img
                                                            );
                                                            echo '<input type="hidden" 
                                            id="nsc-material" 
                                            data-id="' . $data->additional_id . '" 
                                            data-name="' . $data->additional_name . '" 
                                            data-extra-price="' . $data->extra_price . '" 
                                            data-price-type="' . $data->extra_price_type . '" 
                                            data-image="' . $plugin_dir . 'additional_img/' . $data->additional_img . '" />';
                                                        }
                                                        $fistTime = false;
                                                    }
                                                    echo '
                             <div class="col-md-12 p-0">                       
                            <div
                                id="material-' . $data->additional_id . '" 
                                class="nsc-box mt-1 mr-1 rounded bg-white material-click text-left " 
                                data-id="' . $data->additional_id . '" 
                                data-name="' . $data->additional_name . '" 
                                data-extra-price="' . $data->extra_price . '" 
                                data-price-type="' . $data->extra_price_type . '" 
                                data-image="' . $plugin_dir . 'additional_img/' . $data->additional_img . '" style="                                                                   
                                background-size:cover;
                                background-repeat: no-repeat;
                                background-position: center;
                                width: 100%;
                                height: 100px;
                                font-size:1rem; 
                                padding: 10px;                       
                                    ">   <strong>' . $data->additional_name . '  </strong><br/>                              
                                    <i>' . $data->additional_des . '  </i>
                            
                           </div></div>
                            ';
                                                }
                                            }
                                            ?>

                                    </div>
                                </div>
                                <?php } ?>
                            </div>
                            <div class="tab-pane fade" id="nav-jacket" role="tabpanel" aria-labelledby="nav-jacket-tab">
                                <!-- <br /> -->
                                <?php
                                if ($nsc_settings['jacket'] == 1) {
                                    ?>
                                <div class="d-flex justify-content-between mt-2">
                                    <label for="nsc-jacket" style="padding: 0; margin: 0">
                                        <strong>Jacket</strong><br />
                                    </label>
                                    <a class="text-decoration-none cursor-pointer image-example"
                                        data-id="nsc-jacket">Example</a>
                                </div>
                                <small><i>Choose the tube color when sign is turned off</i></small>
                                <div>
                                    <div class="row">
                                        <?php
                                            $fistTime = true;
                                            foreach ($nsc_additional_results as $data) {
                                                if ($data->additional_type == 'jacket') {
                                                    if ($fistTime) {
                                                        if (isset($_SESSION['neon-size']['jacket'])) {
                                                            echo '<input type="hidden" id="nsc-jacket" 
                                        data-id="' . $_SESSION['neon-size']['jacket']['id'] . '" 
                                        data-name="' . $_SESSION['neon-size']['jacket']['name'] . '" 
                                        data-extra-price="' . $_SESSION['neon-size']['jacket']['extraPrice'] . '" 
                                        data-price-type="' . $_SESSION['neon-size']['jacket']['priceType'] . '" 
                                        data-image="' . $_SESSION['neon-size']['jacket']['image'] . '" />';
                                                        } else {
                                                            $_SESSION['neon-size']['jacket'] = array(
                                                                'id' => $data->additional_id,
                                                                'name' => $data->additional_name,
                                                                'extraPrice' => $data->extra_price,
                                                                'priceType' => $data->extra_price_type,
                                                                'image' => $plugin_dir . 'additional_img/' . $data->additional_img
                                                            );
                                                            echo '<input type="hidden" 
                                            id="nsc-jacket" 
                                            data-id="' . $data->additional_id . '" 
                                            data-name="' . $data->additional_name . '" 
                                            data-extra-price="' . $data->extra_price . '" 
                                            data-price-type="' . $data->extra_price_type . '" 
                                            data-image="' . $plugin_dir . 'additional_img/' . $data->additional_img . '" />';
                                                        }
                                                        $fistTime = false;
                                                    }
                                                    echo '
                             <div class="col-md-12 p-0">                       
                            <div
                                id="jacket-' . $data->additional_id . '" 
                                class="nsc-box mt-1 mr-1 rounded bg-white jacket-click text-left " 
                                data-id="' . $data->additional_id . '" 
                                data-name="' . $data->additional_name . '" 
                                data-extra-price="' . $data->extra_price . '" 
                                data-price-type="' . $data->extra_price_type . '" 
                                data-image="' . $plugin_dir . 'additional_img/' . $data->additional_img . '" style="                                                                   
                                background-size:cover;
                                background-repeat: no-repeat;
                                background-position: center;
                                width: 100%;
                                height: 100px;
                                font-size:1rem; 
                                padding: 10px;                       
                                    ">   <strong>' . $data->additional_name . '  </strong><br/>                              
                                    <i>' . $data->additional_des . '  </i>
                            
                           </div></div>
                            ';
                                                }
                                            }
                                            ?>
                                    </div>
                                </div>
                                <?php } ?>
                            </div>
                            <div class="tab-pane fade" id="nav-mounting" role="tabpanel"
                                aria-labelledby="nav-mounting-tab">
                                <!-- <br /> -->
                                <?php
                                if ($nsc_settings['mounting'] == 1) {
                                    ?>
                                <div class="d-flex justify-content-between mt-2">
                                    <label for="nsc-mounting" style="padding: 0; margin: 0">
                                        <strong>Mounting</strong><br />
                                    </label>
                                    <a class="text-decoration-none cursor-pointer image-example"
                                        data-id="nsc-mounting">Example</a>
                                </div>
                                <small><i></i></small>
                                <div>
                                    <div class="row">
                                        <?php
                                            $fistTime = true;
                                            foreach ($nsc_additional_results as $data) {
                                                if ($data->additional_type == 'mounting') {
                                                    if ($fistTime) {
                                                        if (isset($_SESSION['neon-size']['mounting'])) {
                                                            echo '<input type="hidden" id="nsc-mounting" 
                                        data-id="' . $_SESSION['neon-size']['mounting']['id'] . '" 
                                        data-name="' . $_SESSION['neon-size']['mounting']['name'] . '" 
                                        data-extra-price="' . $_SESSION['neon-size']['mounting']['extraPrice'] . '" 
                                        data-price-type="' . $_SESSION['neon-size']['mounting']['priceType'] . '" 
                                        data-image="' . $_SESSION['neon-size']['mounting']['image'] . '" />';
                                                        } else {
                                                            $_SESSION['neon-size']['mounting'] = array(
                                                                'id' => $data->additional_id,
                                                                'name' => $data->additional_name,
                                                                'extraPrice' => $data->extra_price,
                                                                'priceType' => $data->extra_price_type,
                                                                'image' => $plugin_dir . 'additional_img/' . $data->additional_img
                                                            );
                                                            echo '<input type="hidden" 
                                            id="nsc-mounting" 
                                            data-id="' . $data->additional_id . '" 
                                            data-name="' . $data->additional_name . '" 
                                            data-extra-price="' . $data->extra_price . '" 
                                            data-price-type="' . $data->extra_price_type . '" 
                                            data-image="' . $plugin_dir . 'additional_img/' . $data->additional_img . '" />';
                                                        }
                                                        $fistTime = false;
                                                    }
                                                    echo '
                             <div class="col-md-12 p-0">                       
                            <div
                                id="mounting-' . $data->additional_id . '" 
                                class="nsc-box mt-1 mr-1 rounded bg-white mounting-click text-left " 
                                data-id="' . $data->additional_id . '" 
                                data-name="' . $data->additional_name . '" 
                                data-extra-price="' . $data->extra_price . '" 
                                data-price-type="' . $data->extra_price_type . '" 
                                data-image="' . $plugin_dir . 'additional_img/' . $data->additional_img . '" style="                                                                   
                                background-size:cover;
                                background-repeat: no-repeat;
                                background-position: center;
                                width: 100%;
                                height: 100px;
                                font-size:1rem; 
                                padding: 10px;                       
                                    ">   <strong>' . $data->additional_name . '  </strong><br/>                              
                                    <i>' . $data->additional_des . '  </i>
                            
                           </div></div>
                            ';
                                                }
                                            }
                                            ?>

                                    </div>
                                </div>
                                <?php } ?>
                                <!-- <br /> -->
                                <?php
                                if ($nsc_settings['remote_control'] == 1) {
                                    ?>
                                <div class="d-flex justify-content-between mt-2">
                                    <label for="nsc-remote-control" style="padding: 0; margin: 0">
                                        <strong>Remote Control</strong><br />
                                    </label>
                                    <a class="text-decoration-none cursor-pointer image-example"
                                        data-id="nsc-remote-control">Example</a>
                                </div>
                                <small><i>A remote control is included free with every sign</i></small>
                                <div>
                                    <div class="row">
                                        <?php
                                            $fistTime = true;
                                            foreach ($nsc_additional_results as $data) {
                                                if ($data->additional_type == 'remote_control') {
                                                    if ($fistTime) {
                                                        if (isset($_SESSION['neon-size']['remote_control'])) {
                                                            echo '<input type="hidden" id="nsc-remote-control" 
                                        data-id="' . $_SESSION['neon-size']['remote_control']['id'] . '" 
                                        data-name="' . $_SESSION['neon-size']['remote_control']['name'] . '" 
                                        data-extra-price="' . $_SESSION['neon-size']['remote_control']['extraPrice'] . '" 
                                        data-price-type="' . $_SESSION['neon-size']['remote_control']['priceType'] . '" 
                                        data-image="' . $_SESSION['neon-size']['remote_control']['image'] . '" />';
                                                        } else {
                                                            $_SESSION['neon-size']['remote_control'] = array(
                                                                'id' => $data->additional_id,
                                                                'name' => $data->additional_name,
                                                                'extraPrice' => $data->extra_price,
                                                                'priceType' => $data->extra_price_type,
                                                                'image' => $plugin_dir . 'additional_img/' . $data->additional_img
                                                            );
                                                            echo '<input type="hidden" 
                                            id="nsc-remote-control" 
                                            data-id="' . $data->additional_id . '" 
                                            data-name="' . $data->additional_name . '" 
                                            data-extra-price="' . $data->extra_price . '" 
                                            data-price-type="' . $data->extra_price_type . '" 
                                            data-image="' . $plugin_dir . 'additional_img/' . $data->additional_img . '" />';
                                                        }
                                                        $fistTime = false;
                                                    }
                                                    echo '
                             <div class="col-md-6 p-0">                       
                            <div
                                id="remote-control-' . $data->additional_id . '" 
                                class="nsc-box mt-1 mr-1 rounded bg-white remote-control-click text-center " 
                                data-id="' . $data->additional_id . '" 
                                data-name="' . $data->additional_name . '" 
                                data-extra-price="' . $data->extra_price . '" 
                                data-price-type="' . $data->extra_price_type . '" 
                                data-image="' . $plugin_dir . 'additional_img/' . $data->additional_img . '" style="                                                                   
                                background-size:cover;
                                background-repeat: no-repeat;
                                background-position: center;                                
                                margin-right: 2px;
                                height: 60px;
                                font-size:1.2rem;
                                line-height: 55px;                                
                                    ">   ' . $data->additional_name . '                                
                            
                           </div></div>
                            ';
                                                }
                                            }
                                            ?>

                                    </div>
                                </div>
                                <?php } ?>
                                <!-- <br /> -->
                                <?php
                                if ($nsc_settings['plug_type'] == 1) {
                                    ?>
                                <div class="d-flex justify-content-between mt-2">
                                    <label for="nsc-plug-type" style="padding: 0; margin: 0">
                                        <strong>Plug Type</strong><br />
                                    </label>
                                    <a class="text-decoration-none cursor-pointer image-example"
                                        data-id="nsc-plug-type">Example</a>
                                </div>
                                <small><i>Choose the right plug for your country</i></small>
                                <div>
                                    <?php
                                        $fistTime = true;
                                        $hidden_plug_type = "";
                                        echo "<select id='nsc-select-plug-type' style='width:100%;font-size:1.5rem; padding-right: 5px'>  ";
                                        foreach ($nsc_additional_results as $data) {
                                            if ($data->additional_type == 'plug_type') {
                                                if ($fistTime) {
                                                    if (isset($_SESSION['neon-size']['plug_type'])) {
                                                        $hidden_plug_type = '<input type="hidden" id="nsc-plug-type" 
data-id="' . $_SESSION['neon-size']['plug_type']['id'] . '" 
                                        data-name="' . $_SESSION['neon-size']['plug_type']['name'] . '" 
                                        data-extra-price="' . $_SESSION['neon-size']['plug_type']['extraPrice'] . '" 
                                        data-price-type="' . $_SESSION['neon-size']['plug_type']['priceType'] . '" 
                                        data-image="' . $_SESSION['neon-size']['plug_type']['image'] . '" />';
                                                    } else {
                                                        $_SESSION['neon-size']['plug_type'] = array(
                                                            'id' => $data->additional_id,
                                                            'name' => $data->additional_name,
                                                            'extraPrice' => $data->extra_price,
                                                            'priceType' => $data->extra_price_type,
                                                            'image' => $plugin_dir . 'additional_img/' . $data->additional_img
                                                        );
                                                        $hidden_plug_type = '<input type="hidden" 
                                            id="nsc-plug-type" 
                                            data-id="' . $data->additional_id . '" 
                                            data-name="' . $data->additional_name . '" 
                                            data-extra-price="' . $data->extra_price . '" 
                                            data-price-type="' . $data->extra_price_type . '" 
                                            data-image="' . $plugin_dir . 'additional_img/' . $data->additional_img . '" />';
                                                    }
                                                    $fistTime = false;
                                                }
                                                echo '
                            <option
                                id="plug-type-' . $data->additional_id . '"                                 
                                data-id="' . $data->additional_id . '" 
                                data-name="' . $data->additional_name . '" 
                                data-extra-price="' . $data->extra_price . '" 
                                data-price-type="' . $data->extra_price_type . '" 
                                data-image="' . $plugin_dir . 'additional_img/' . $data->additional_img . '" style="                                                                           
                                    "> ' . $data->additional_name . '                                  
                            </option>

                            ';
                                            }
                                        }
                                        echo "</select>";
                                        echo $hidden_plug_type;
                                        ?>

                                </div>
                                <?php } ?>
                                <!-- <br /> -->
                                <?php
                                if ($nsc_settings['cable_color'] == 1) {
                                    ?>
                                <div class="d-flex justify-content-between mt-2">
                                    <label for="nsc-cable-color" style="padding: 0; margin: 0"><strong>Cable
                                            Color</strong></label>
                                    <a class="text-decoration-none cursor-pointer image-example"
                                        data-id="nsc-cable-color">Example</a>
                                </div>
                                <div class="d-flex">
                                    <?php
                                        $fistTime = true;
                                        foreach ($nsc_additional_results as $data) {
                                            if ($data->additional_type == 'cable_color') {
                                                if ($fistTime) {
                                                    if (isset($_SESSION['neon-size']['cable_color'])) {
                                                        echo '<input type="hidden" id="nsc-cable-color" 
                                        data-id="' . $_SESSION['neon-size']['cable_color']['id'] . '" 
                                        data-name="' . $_SESSION['neon-size']['cable_color']['name'] . '" 
                                        data-extra-price="' . $_SESSION['neon-size']['cable_color']['extraPrice'] . '" 
                                        data-price-type="' . $_SESSION['neon-size']['cable_color']['priceType'] . '" 
                                        data-image="' . $_SESSION['neon-size']['cable_color']['image'] . '" />';
                                                    } else {
                                                        $_SESSION['neon-size']['cable_color'] = array(
                                                            'id' => $data->additional_id,
                                                            'name' => $data->additional_name,
                                                            'extraPrice' => $data->extra_price,
                                                            'priceType' => $data->extra_price_type,
                                                            'image' => $plugin_dir . 'additional_img/' . $data->additional_img
                                                        );
                                                        echo '<input type="hidden" 
                                            id="nsc-cable-color" 
                                            data-id="' . $data->additional_id . '" 
                                            data-name="' . $data->additional_name . '" 
                                            data-extra-price="' . $data->extra_price . '" 
                                            data-price-type="' . $data->extra_price_type . '" 
                                            data-image="' . $plugin_dir . 'additional_img/' . $data->additional_img . '" />';
                                                    }
                                                    $fistTime = false;
                                                }
                                                echo '
                           <div class="text-center">
                            <div class="cable-label" id="cable-label-' . $data->additional_id . '">' . $data->additional_name . '</div>
                            <div
                                id="cable-color-' . $data->additional_id . '" 
                                class="nsc-box mt-1 mr-1 rounded bg-white cable-color-click" 
                                data-id="' . $data->additional_id . '" 
                                data-name="' . $data->additional_name . '" 
                                data-extra-price="' . $data->extra_price . '" 
                                data-price-type="' . $data->extra_price_type . '" 
                                data-image="' . $plugin_dir . 'additional_img/' . $data->additional_img . '" style="
                                position: relative;
                                width: 100px;
                                height: 80px;
                                
                                    ">       
                                    <img class="nsc-img" src="' . $plugin_dir . 'additional_img/' . $data->additional_img . '"/>
                            </div>
                           </div>
                            ';
                                            }
                                        }
                                        ?>
                                </div>
                                <?php } ?>
                                <!-- <br /> -->
                                <?php
                                if ($nsc_settings['special_requests'] == 1) {
                                    ?>
                                <label for="nsc-special"><strong>Any other special
                                        requests</strong><br />
                                    <small>
                                        <i>Place in special requests and we will contact you before
                                            production</i></small></label>
                                <br />
                                <textarea class="w-100"
                                    id="nsc-special"><?= isset($_SESSION['neon-size']['special']) ? $_SESSION['neon-size']['special'] : "" ?></textarea>
                                <br /><br />
                                <?php
                                }
                                ?>
                            </div>

                            <!-- temp -->
                            <div class="tab-pane fade" id="nav-remote-control" role="tabpanel"
                                aria-labelledby="nav-remote-control-tab">
                            </div>
                            <div class="tab-pane fade" id="nav-plug-type" role="tabpanel"
                                aria-labelledby="nav-plug-type-tab">
                            </div>
                            <div class="tab-pane fade" id="nav-cable-color" role="tabpanel"
                                aria-labelledby="nav-cable-color-tab">
                            </div>
                            <div class="tab-pane fade" id="nav-request" role="tabpanel"
                                aria-labelledby="nav-request-tab">
                            </div>
                        </div>
                    </div>
                    <div class="nsc-bottom mt-2">
                        <div class="tab-pane fade" id="nav-review" role="tabpanel" aria-labelledby="nav-review-tab">
                            <div><strong>Total:</strong><span class="total-price" id="total-price-2"></span></div>
                            <?php

                            if ($product->product_type === 'neon_sign'): ?>
                            <button id="session-save" class="nsc-button btn btn-lg btn-block">FINISH & REVIEW</button>

                            <?php endif; ?>
                        </div>
                        <div class="d-flex justify-content-between d-lg-none mt-3">
                            <div class="nsc-mobile-control-button nsc-mobile-control-pre cursor-pointer"
                                style="width: 150px;">
                                <svg width="15" height="14" viewBox="0 0 15 14" fill="none"
                                    xmlns="http://www.w3.org/2000/svg">
                                    <path
                                        d="M8.15625 13.8125L8.78125 13.2188C8.9375 13.0625 8.9375 12.8125 8.78125 12.6875L3.9375 7.8125H14.625C14.8125 7.8125 15 7.65625 15 7.4375V6.5625C15 6.375 14.8125 6.1875 14.625 6.1875H3.9375L8.78125 1.34375C8.9375 1.21875 8.9375 0.96875 8.78125 0.8125L8.15625 0.21875C8.03125 0.0625 7.78125 0.0625 7.625 0.21875L1.09375 6.75C0.9375 6.90625 0.9375 7.125 1.09375 7.28125L7.625 13.8125C7.78125 13.9688 8.03125 13.9688 8.15625 13.8125Z"
                                        fill="currentColor"></path>
                                </svg>
                            </div>
                            <div class="nsc-mobile-control-button nsc-mobile-control-next cursor-pointer"
                                style="width: 150px;">
                                <svg width="14" height="14" viewBox="0 0 14 14" fill="none"
                                    xmlns="http://www.w3.org/2000/svg">
                                    <path
                                        d="M6.8125 0.21875L6.1875 0.8125C6.03125 0.96875 6.03125 1.21875 6.1875 1.34375L11.0312 6.1875H0.375C0.15625 6.1875 0 6.375 0 6.5625V7.4375C0 7.65625 0.15625 7.8125 0.375 7.8125H11.0312L6.1875 12.6875C6.03125 12.8125 6.03125 13.0625 6.1875 13.2188L6.8125 13.8125C6.9375 13.9688 7.1875 13.9688 7.34375 13.8125L13.875 7.28125C14.0312 7.125 14.0312 6.90625 13.875 6.75L7.34375 0.21875C7.1875 0.0625 6.9375 0.0625 6.8125 0.21875Z"
                                        fill="currentColor"></path>
                                </svg>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div id="reviewModal" style="overflow-y: auto; overflow-x: hidden; display: none;">
            <div id="reviewModalContent" class="row m-0">
                <div id="nsc-live-result" class="col-lg-7 p-0">
                    <div id="carouselControls" class="carousel slide" data-ride="carousel">
                        <div class="carousel-inner">
                            <?php
                            $background_active = "active";
                            foreach ($nsc_background_results as $data) {
                                ?>
                            <div class="carousel-item <?= $background_active ?> h-100" style="position: relative;">
                                <img class="nsc-img"
                                    src="<?= $plugin_dir ?>additional_img/<?= $data->additional_img ?>" />
                                <div class="nsc-neon-live">
                                    <div class="nsc-backboard">
                                        <p></p>
                                    </div>
                                </div>
                            </div>
                            <?php
                                $background_active = "";
                            }
                            ?>
                        </div>
                        <button class="carousel-control-prev bg-transparent h-100" type="button"
                            data-target="#carouselControls" data-slide="prev">
                            <span class="carousel-control-prev-icon" aria-hidden="true"></span>
                            <span class="sr-only">Previous</span>
                        </button>
                        <button class="carousel-control-next bg-transparent h-100" type="button"
                            data-target="#carouselControls" data-slide="next">
                            <span class="carousel-control-next-icon" aria-hidden="true"></span>
                            <span class="sr-only">Next</span>
                        </button>
                    </div>

                </div>
                <div id="nsc-side-result" class="col-lg-5">
                    <div class="d-block d-lg-none w-100" style="border-bottom: solid 1px #eee;">
                        <div class="result-fee rounded p-1 m-3"
                            style="background-color:antiquewhite; font-size: 1.3rem; color:var(--primary-text-color)">
                            <div class="text-center"
                                style="<?= ($nsc_settings['show_shipping_fee'] == 1 ? "display:block" : "display:none") ?>">
                                <strong>Shipping Fee: </strong><span class="nsc-shipping-fee"></span>
                            </div>
                            <div class="text-center"><strong>Total Price: </strong><span id="nsc-price-result"
                                    class="total-price"></span>
                            </div>
                        </div>
                        <div class="d-flex justify-content-around m-3">
                            <?php

                            if ($product->product_type === 'neon_sign'): ?>

                            <button class="add-to-cart nsc-button btn btn-lg btn-block m-1"
                                data-product-id="<?php echo $product->get_id(); ?>">BUY NOW</button>
                            <button class="edit-neon nsc-button btn btn-lg btn-block m-1">EDIT</button>

                            <?php endif; ?>
                        </div>
                    </div>

                    <div id="result-middle" style="font-size:1rem; margin:0px 20px">
                        <div class="text-center p-2"
                            style="color:var(--primary-text-color); font-size: 1.5rem; font-weight: bold; ">
                            <p>YOUR DETAILS:</p>
                        </div>
                        <div class="row m-0">
                            <div class="col-12"><strong>Text: </strong><span id="nsc-text-result"></span></div>
                            <div class="col-12"><strong>Size: (Width x Height):</strong><br /><span
                                    id="nsc-size-result"></span>
                            </div>
                            <div class="col-6"><strong>Font: </strong><span id="nsc-font-result"></span></div>
                            <div class="col-6"><strong>Color: </strong><span id="nsc-color-result"></span></div>
                            <div class="col-6"><strong>Backboard: </strong><span id="nsc-backboard-result"></span>
                            </div>
                            <div class="col-6"><strong>Backboard-color: </strong><span
                                    id="nsc-backboard-color-result"></span>
                            </div>
                            <div class="col-6"><strong>Jacket: </strong><span id="nsc-jacket-result"></span></div>
                            <div class="col-6"><strong>Material: </strong><span id="nsc-material-result"></span>
                            </div>
                            <div class="col-6"><strong>Mounting: </strong><span id="nsc-mounting-result"></span>
                            </div>
                            <div class="col-6"><strong>Remote-control: </strong><span
                                    id="nsc-remote-control-result"></span>
                            </div>
                            <div class="col-6"><strong>Plug-type: </strong><span id="nsc-plug-type-result"></span>
                            </div>
                            <div class="col-6"><strong>Cable-color: </strong><span id="nsc-cable-color-result"></span>
                            </div>
                            <div class="col-12"><strong>Requests: </strong><span id="nsc-special-result"></span>
                            </div>
                        </div>
                        <div class="result-fee d-none d-lg-block rounded p-3 m-3"
                            style="background-color:antiquewhite; font-size: 1.3rem; color:var(--primary-text-color)">
                            <div class="text-center"
                                style="<?= ($nsc_settings['show_shipping_fee'] == 1 ? "display:block" : "display:none") ?>">
                                <strong>Shipping Fee: </strong><span class="nsc-shipping-fee"></span>
                            </div>
                            <div class="text-center"><strong>Total Price: </strong><span id="nsc-price-result"
                                    class="total-price"></span>
                            </div>
                        </div>
                        <div class="text-center bg-info p-3 m-3 text-white d-none">
                            <?php
                            echo $product->get_meta('_nsc_custom_field_one');
                            ?>
                        </div>
                        <div class="text-center p-3 m-3  d-none">
                            <?php
                            echo $product->get_meta('_nsc_custom_field_two');
                            ?>
                        </div>
                    </div>
                    <div id="result-bottom" style="border-top: solid 1px #eee">
                        <div class="d-none d-lg-flex justify-content-around">
                            <?php

                            if ($product->product_type === 'neon_sign'): ?>

                            <button class="add-to-cart nsc-button btn btn-lg btn-block m-1"
                                data-product-id="<?php echo $product->get_id(); ?>">BUY NOW</button>
                            <button class="edit-neon nsc-button btn btn-lg btn-block m-1">EDIT</button>

                            <?php endif; ?>
                        </div>
                    </div>

                </div>
            </div>
        </div>
    </div>


    <?php
    // echo '<div class="product-description">' . wp_kses_post($product_description) . '</div>';
    ?>

    <?php do_action('woocommerce_after_single_product_summary'); ?>
    <?php do_action('woocommerce_after_single_product'); ?>




    <script>
    (function($) {
        "use strict";

        $(document).ready(function() {

            let intervalId = null;
            let heightAnimate = null;
            let widthAnimate = null;
            let priceAnimate = null;
            let navTabIndex = 0;
            let navTab = [
                "#nav-text-tab",
                "#nav-font-tab",
                "#nav-color-tab",
                "#nav-size-tab",
                "#nav-backboard-tab",
                "#nav-material-tab",
                "#nav-jacket-tab",
                "#nav-mounting-tab",
                // "#nav-remote-control-tab",
                // "#nav-plug-type-tab",
                // "#nav-cable-color-tab",
                // "#nav-request-tab"
                "#nav-review-tab",
            ];

            function updateNavTab() {
                $("#nsc-mobile-control-current").text((navTabIndex + 1) + "/" +
                    navTab.length);
                $("#nsc-mobile-control-label").text($(navTab[navTabIndex]).data("label"));
                $(".nsc-nav-link").removeClass("active");
                $(".tab-pane").removeClass("show active");

                $(navTab[navTabIndex]).addClass("show active");
                let targetTab = $(navTab[navTabIndex]).data("target");
                $(targetTab).addClass("show active");
            }

            $(".nsc-mobile-control-pre").click(function() {
                navTabIndex = (navTabIndex > 0) ? navTabIndex - 1 : 0;
                updateNavTab();
            });

            $(".nsc-mobile-control-next").click(function() {
                navTabIndex = (navTabIndex < (navTab.length - 1)) ? navTabIndex + 1 : (navTab
                    .length - 1);
                updateNavTab();
            });

            $("#nsc-mobile-control-toggle").click(function() {
                let navTabWrapper = $("#nsc-nav-tab-wrapper");
                navTabWrapper.css("display") == "block" ? navTabWrapper.css("display", "none") :
                    navTabWrapper.css("display", "block");
            });

            $(".nsc-nav-link").click(function() {
                $("#nsc-nav-tab-wrapper").css("display", "none");
                // alert($(this).data("index"));
                navTabIndex = parseInt($(this).data("index"));
                updateNavTab();
            });



            // When the page loads, set the initial text for the neon sign
            function updateNeon() {
                updateNeonText();
                updateNeonFont();
                updateFontSize();
                caculateNeonSize();
                updateNeonColor();
                updateToggleLight();
                updateBackboard();
                updateBackboardColor();
                updateMaterial();
                updateJacket();
                updateMounting();
                updateRemoteControl();
                updatePlugType();
                updateCableColor();
                updateNavTab();
                updateTotalPrice();
            }

            updateNeon();

            $("#nsc-text").on("input", function() {
                updateNeonText();
            });

            $("#nsc-text").on("keydown", function(event) {
                let enteredText = $(this).val();

                let textWidth = $("#nsc-size").data("text-width");
                let textHeight = $("#nsc-size").data("text-height");
                let textLine = $("#nsc-size").data("text-line");
                let lines = enteredText.split("\n");

                // alert(lines);
                if (lines.length >= parseInt(textLine) && event.key === "Enter") {
                    showToast("Cannot input more than " + textLine + " Lines", "danger");
                    event.preventDefault();
                }
                updateNeon();
            });

            function updateNeonText() {
                // Get the value from the "nsc-neon-live" input field
                let windowSizeWidthChoose = parseFloat($("#nsc-live-choose").width()) / parseFloat(5.5);
                let windowSizeWidthResult = parseFloat($("#nsc-live-result").width()) / parseFloat(5.5);
                let windowSizeWidth = parseFloat(windowSizeWidthChoose) + parseFloat(windowSizeWidthResult);
                let newText = $("#nsc-text").val().replace(/\n/g, '<br/>');
                // zoom
                let lineSort = newText.split("<br/>");
                lineSort.sort((a, b) => b.length - a.length);
                let inputLength = lineSort[0].length;
                let fontSize = (windowSizeWidth) - (inputLength * parseFloat(2));

                fontSize < 42 ? fontSize = 42 : fontSize = fontSize;
                fontSize > 90 ? fontSize = 90 : fontSize = fontSize;

                // console.log(fontSize + " --- " + windowSizeWidth);


                // Clear data
                $("#nsc-text").data("line-one", "");
                $("#nsc-text").data("line-two", "");
                $("#nsc-text").data("line-three", "");

                let lines = newText.split("<br/>");
                $("#nsc-text").data("lines", lines.length);
                $("#nsc-text").data("line-one", lines[0]);
                $("#nsc-text").data("line-two", lines[1]);
                $("#nsc-text").data("line-three", lines[2]);

                // showToast($("#nsc-text").data("line-three"), "success");

                // Update the content of the "nsc-neon-live" div to display the new text
                $(".nsc-neon-live p").html(newText);
                // $(".nsc-neon-live p").css("font-size", fontSize + "px");
                $(".nsc-neon-live p").css("font-size", (fontSize - 25) + "px");

            }

            function updateTotalPrice() {

                let totalPrice = 0;
                let shippingFree = 0;
                let totalLineOnePrice = 0;
                let totalLineTwoPrice = 0;
                let totalLineThreePrice = 0;
                let totalFontPrice = 0;
                let totalColorPrice = 0;
                let totalBackboardPrice = 0;
                let totalBackboardColorPrice = 0;
                let totalMaterialPrice = 0;
                let totalJacketPrice = 0;
                let totalMountingPrice = 0;
                let totalRemoteControlPrice = 0;
                let totalPlugTypePrice = 0;
                let totalCableColorPrice = 0;
                // get line 1
                let textLineOne = $("#nsc-text").data('line-one').replace(/\s/g, '');
                let lineOnePrice = $("#nsc-size").data("lineOne").split("@");
                totalLineOnePrice = (textLineOne.length > 0 ? (parseFloat(lineOnePrice[0]) + (
                    parseInt(
                        textLineOne.length) * parseFloat(lineOnePrice[1]))) : 0);
                if (!isNaN(totalLineOnePrice)) {
                    totalPrice = parseFloat(totalPrice) + parseFloat(totalLineOnePrice);
                }

                // get line 2
                let textLineTwo = $("#nsc-text").data('line-two').replace(/\s/g, '');
                let lineTwoPrice = $("#nsc-size").data("lineTwo").split("@");
                totalLineTwoPrice = (textLineTwo.length > 0 ? (parseFloat(lineTwoPrice[0]) + (
                    parseInt(
                        textLineTwo.length) * parseFloat(lineTwoPrice[1]))) : 0);
                if (!isNaN(totalLineTwoPrice)) {
                    totalPrice = parseFloat(totalPrice) + parseFloat(totalLineTwoPrice);
                }

                // get line 3
                let textLineThree = $("#nsc-text").data('line-three').replace(/\s/g, '');
                let lineThreePrice = $("#nsc-size").data("lineThree").split("@");
                totalLineThreePrice = (textLineThree.length > 0 ? (parseFloat(lineThreePrice[0]) + (
                    parseInt(
                        textLineThree.length) * parseFloat(lineThreePrice[1]))) : 0);
                if (!isNaN(totalLineThreePrice)) {
                    totalPrice = parseFloat(totalPrice) + parseFloat(lineThreePrice);
                }

                // get font extra price
                let fontPrice = parseFloat($("#nsc-font").data("extra-price"));
                if (!isNaN(fontPrice)) {
                    let fontPriceType = $("#nsc-font").data("price-type");
                    totalFontPrice = (fontPriceType == "0" ? fontPrice : parseFloat((totalPrice *
                        fontPrice) / 100))
                    totalPrice = parseFloat(totalPrice) + parseFloat(totalFontPrice);
                }


                // get color extra price
                let colorPrice = parseFloat($("#nsc-color").data("extra-price"));
                if (!isNaN(colorPrice)) {
                    let colorPriceType = $("#nsc-color").data("price-type");
                    totalColorPrice = (colorPriceType == "0" ? colorPrice : parseFloat((totalPrice *
                        colorPrice) / 100))
                    totalPrice = parseFloat(totalPrice) + parseFloat(totalColorPrice);
                }

                // get backboard extra price
                let backboardPrice = parseFloat($("#nsc-backboard").data("extra-price"));
                if (!isNaN(backboardPrice)) {
                    let backboardPriceType = $("#nsc-backboard").data("price-type");
                    totalBackboardPrice = (backboardPriceType == "0" ? backboardPrice : parseFloat((
                        totalPrice *
                        backboardPrice) / 100))
                    totalPrice = parseFloat(totalPrice) + parseFloat(totalBackboardPrice);
                }

                // get backboardColor extra price
                let backboardColorPrice = parseFloat($("#nsc-backboard-color").data("extra-price"));
                if (!isNaN(backboardColorPrice)) {
                    let backboardColorPriceType = $("#nsc-backboard-color").data("price-type");
                    totalBackboardColorPrice = (backboardColorPriceType == "0" ?
                        backboardColorPrice :
                        parseFloat((totalPrice * backboardColorPrice) / 100))
                    totalPrice = parseFloat(totalPrice) + parseFloat(totalBackboardColorPrice);
                }

                // get material extra price
                let materialPrice = parseFloat($("#nsc-material").data("extra-price"));
                if (!isNaN(materialPrice)) {
                    let materialPriceType = $("#nsc-material").data("price-type");
                    totalMaterialPrice = (materialPriceType == "0" ? materialPrice : parseFloat((
                        totalPrice *
                        materialPrice) / 100))
                    totalPrice = parseFloat(totalPrice) + parseFloat(totalMaterialPrice);
                }

                // get jacket extra price
                let jacketPrice = parseFloat($("#nsc-jacket").data("extra-price"));
                if (!isNaN(jacketPrice)) {
                    let jacketPriceType = $("#nsc-jacket").data("price-type");
                    totalJacketPrice = (jacketPriceType == "0" ? jacketPrice : parseFloat((
                        totalPrice *
                        jacketPrice) / 100))
                    totalPrice = parseFloat(totalPrice) + parseFloat(totalJacketPrice);
                }

                // get mounting extra price
                let mountingPrice = parseFloat($("#nsc-mounting").data("extra-price"));
                if (!isNaN(mountingPrice)) {
                    let mountingPriceType = $("#nsc-mounting").data("price-type");
                    totalMountingPrice = (mountingPriceType == "0" ? mountingPrice : parseFloat((
                        totalPrice *
                        mountingPrice) / 100))
                    totalPrice = parseFloat(totalPrice) + parseFloat(totalMountingPrice);
                }

                // get remoteControl extra price
                let remoteControlPrice = parseFloat($("#nsc-remote-control").data("extra-price"));
                if (!isNaN(remoteControlPrice)) {
                    let remoteControlPriceType = $("#nsc-remote-control").data("price-type");
                    totalRemoteControlPrice = (remoteControlPriceType == "0" ? remoteControlPrice :
                        parseFloat((
                            totalPrice * remoteControlPrice) / 100));
                    totalPrice = parseFloat(totalPrice) + parseFloat(totalRemoteControlPrice);
                }

                // get plugType extra price                
                let plugTypePrice = parseFloat($("#nsc-plug-type").data("extra-price"))
                if (!isNaN(plugTypePrice)) {
                    let plugTypePriceType = $("#nsc-plug-type").data("price-type");
                    totalPlugTypePrice = (plugTypePriceType == "0" ? plugTypePrice : parseFloat((
                        totalPrice *
                        plugTypePrice) / 100));
                    totalPrice = parseFloat(totalPrice) + parseFloat(totalPlugTypePrice);
                }

                // get cableColor extra price
                let cableColorPrice = parseFloat($("#nsc-cable-color").data("extra-price"));
                if (!isNaN(cableColorPrice)) {
                    let cableColorPriceType = $("#nsc-cable-color").data("price-type");
                    totalCableColorPrice = (cableColorPriceType == "0" ? cableColorPrice :
                        parseFloat((
                            totalPrice * cableColorPrice) / 100));
                    totalPrice = parseFloat(totalPrice) + parseFloat(totalCableColorPrice);
                }

                let sizeCurrent = textSizeHelper();
                let sizeLength = $("#nsc-size").data("size-length");
                let sizeVol = $("#nsc-size").data("size-vol");
                let sizeFee = $("#nsc-size").data("size-fee");
                shippingFree = ((parseFloat(sizeCurrent.heightCm) * parseFloat(sizeCurrent
                            .widthCm) *
                        parseFloat(sizeLength)) /
                    parseFloat(sizeVol)) * parseFloat(sizeFee);
                $(".nsc-shipping-fee").html("$ " + shippingFree.toFixed(2));
                totalPrice = totalPrice + shippingFree;

                // Set Total Price
                if ($("#nsc-text").val().replace(/\s/g, '').length <= 0) {
                    totalPrice = 0;
                }

                $("#total-price").data("price", totalPrice);

                if (priceAnimate != null) {
                    priceAnimate.stop();
                }

                priceAnimate = $(".total-price").animate({
                    Counter: totalPrice
                }, {
                    duration: 300, // Animation duration in milliseconds
                    step: function(now, fx) {
                        if (fx.prop === "Counter") {
                            $(".total-price").text("$ " + now.toFixed(2));
                        }
                    }
                });

            }

            // UPDATE FONT
            $(".background-click").click(function() {
                $("#nsc-main-background").attr("src", $(this).data("image"));
                $(".background-click").removeClass("selected");
                $(this).addClass("selected");
            });

            // UPDATE FONT
            $(".font-click").click(function() {
                // hidden value
                $("#nsc-font").data("id", $(this).data("id"));
                $("#nsc-font").data("name", $(this).data("name"));
                $("#nsc-font").data("font-url", $(this).data("font-url"));
                $("#nsc-font").data("extra-price", $(this).data("extra-price"));
                $("#nsc-font").data("price-type", $(this).data("price-type"));

                // Changge style
                updateNeon();
            });

            function updateNeonFont() {
                // font click
                $(".nsc-neon-live p").css("font-family", $("#nsc-font").data("name").replace(" ",
                        "")
                    .toLowerCase());
                $(".font-click").removeClass("nsc-selected");
                $("#font-" + $("#nsc-font").data("id")).addClass("nsc-selected");
                $("#nsc-font-toggle").text($("#nsc-font").data("name"));

                // toggle click
                let toggle = $("#nsc-font").data("toggle");
                if (toggle == "1") {
                    $("#nsc-font-toggle").addClass("nsc-selected");
                    $("#nsc-font-list").css("display", "block");
                    $("#nsc-font-toggle-show").css("display", "block");
                    $("#nsc-font-toggle-hide").css("display", "none");
                } else {
                    $("#nsc-font-toggle").removeClass("nsc-selected");
                    $("#nsc-font-list").css("display", "none");
                    $("#nsc-font-toggle-show").css("display", "none");
                    $("#nsc-font-toggle-hide").css("display", "block");
                }

                $(".font-align").removeClass("nsc-selected");
                $("#nsc-font-" + $("#nsc-font").data("align")).addClass("nsc-selected");
            }


            $("#nsc-font-toggle").click(function() {

                let toggle = $("#nsc-font").data("toggle");
                if (toggle == "0") {
                    $("#nsc-font").data("toggle", 1);
                }
                if (toggle == "1") {
                    $("#nsc-font").data("toggle", 0);
                }

                // Changge style
                updateNeon();
            });

            // align left
            $("#nsc-font-left").click(function() {
                $("#nsc-font").data("align", "left");
                $(".nsc-neon-live").css("text-align", "left");
                updateNeon();
            })
            // align center
            $("#nsc-font-center").click(function() {
                $("#nsc-font").data("align", "center");
                $(".nsc-neon-live").css("text-align", "center")
                updateNeon();
            })
            // align right
            $("#nsc-font-right").click(function() {
                $("#nsc-font").data("align", "right");
                $(".nsc-neon-live").css("text-align", "right")
                updateNeon();
            })


            // UPDATE SIZE
            $(".size-click").click(function() {
                // hidden value
                $("#nsc-size").data("id", $(this).data("id"));
                $("#nsc-size").data("name", $(this).data("name"));
                $("#nsc-size").data("font-id", $(this).data("font-id"));
                $("#nsc-size").data("font-size", $(this).data("font-size"));
                $("#nsc-size").data("line-one", $(this).data("line-one"));
                $("#nsc-size").data("line-two", $(this).data("line-two"));
                $("#nsc-size").data("line-three", $(this).data("line-three"));
                $("#nsc-size").data("text-width", $(this).data("text-width"));
                $("#nsc-size").data("text-height", $(this).data("text-height"));
                $("#nsc-size").data("text-line", $(this).data("text-line"));
                $("#nsc-size").data("size-char-min", $(this).data("size-char-min"));
                $("#nsc-size").data("size-char-max", $(this).data("size-char-max"));

                // Changge style
                backboardEffect();
                updateNeon();
            });

            function updateFontSize() {

                // selected
                $(".size-click-wrapper").css("display", "none");
                $(".size-group-" + $("#nsc-font").data("id") + "-wrapper").css("display", "block");
                if ($(".size-group-" + $("#nsc-font").data("id")).length > 0) {
                    $("#size-title").css("display", "block");
                    let firstElement = $(".size-group-" + $("#nsc-font").data("id")).first();
                    if (firstElement.data("font-id") != $("#nsc-size").data("font-id")) {
                        $("#nsc-size").data("id", $(firstElement).data("id"));
                        $("#nsc-size").data("name", $(firstElement).data("name"));
                        $("#nsc-size").data("font-id", $(firstElement).data("font-id"));
                        $("#nsc-size").data("font-size", $(firstElement).data("font-size"));
                        $("#nsc-size").data("line-one", $(firstElement).data("line-one"));
                        $("#nsc-size").data("line-two", $(firstElement).data("line-two"));
                        $("#nsc-size").data("line-three", $(firstElement).data("line-three"));
                        $("#nsc-size").data("text-width", $(firstElement).data("text-width"));
                        $("#nsc-size").data("text-height", $(firstElement).data("text-height"));
                        $("#nsc-size").data("text-line", $(firstElement).data("text-line"));
                        $("#nsc-size").data("size-char-min", $(firstElement).data("size-char-min"));
                        $("#nsc-size").data("size-char-max", $(firstElement).data("size-char-max"));
                    }
                } else {
                    $("#size-title").css("display", "none");
                    $("#nsc-size").data("id", "0");
                    $("#nsc-size").data("name", "");
                    $("#nsc-size").data("font-id", "0");
                    $("#nsc-size").data("font-size", "0");
                    $("#nsc-size").data("line-one", "0@0");
                    $("#nsc-size").data("line-two", "0@0");
                    $("#nsc-size").data("line-three", "0@0");
                    $("#nsc-size").data("text-width", "0");
                    $("#nsc-size").data("text-height", "0");
                    $("#nsc-size").data("text-line", "0");
                    $("#nsc-size").data("size-char-min", "0");
                    $("#nsc-size").data("size-char-max", "0");
                }

                // selected
                $(".size-click").removeClass("nsc-selected");
                $("#size-" + $("#nsc-size").data("id") + $("#nsc-font").data("id")).addClass(
                    "nsc-selected");

                // show name                
                if ($("#nsc-size").data("font-id") == $("#nsc-font").data("id")) {
                    $("#size-name").html(" - " + $("#nsc-size").data("name"));
                }

                //align
                $(".nsc-neon-live").css("text-align", $("#nsc-font").data("align"));

            }

            function caculateNeonSize() {
                const text = $("#nsc-text");
                const sizeCurrent = textSizeHelper();

                $(".nsc-backboard").data("size", sizeCurrent.widthCm.toFixed(2) + "cm x " +
                    sizeCurrent.heightCm.toFixed(2) + "cm / " +
                    sizeCurrent.widthInch.toFixed(2) + "in x " +
                    sizeCurrent.heightInch.toFixed(2) + "in");

                if (heightAnimate != null) {
                    heightAnimate.stop();
                }

                if (widthAnimate != null) {
                    widthAnimate.stop();
                }

                heightAnimate = $("#nsc-backboard-height").animate({
                    num1: sizeCurrent.heightCm,
                    num2: sizeCurrent.heightInch
                }, {
                    duration: 300, // Animation duration in milliseconds
                    step: function(now, fx) {
                        if (fx.prop === "num1") {
                            var cmText = now.toFixed(2) + "cm";
                            var inchText = (sizeCurrent.heightInch * now / sizeCurrent
                                    .heightCm)
                                .toFixed(2) + "in";
                            $("#nsc-backboard-height").text(cmText + " \ " + inchText);
                        }
                    }
                });

                widthAnimate = $("#nsc-backboard-width").animate({
                    num1: sizeCurrent.widthCm,
                    num2: sizeCurrent.widthInch
                }, {
                    duration: 300, // Animation duration in milliseconds
                    step: function(now, fx) {
                        if (fx.prop === "num1") {
                            var cmText = now.toFixed(2) + "cm";
                            var inchText = (sizeCurrent.widthInch * now / sizeCurrent
                                    .widthCm)
                                .toFixed(2) + "in";
                            $("#nsc-backboard-width").text(cmText + " / " + inchText);
                        }
                    }
                });

                // $("#nsc-backboard-height").text(sizeCurrent.heightCm.toFixed(2) + "cm \ " + sizeCurrent
                //     .heightInch.toFixed(2) + "in");
                // $("#nsc-backboard-width").text(sizeCurrent.widthCm.toFixed(2) + "cm / " + sizeCurrent
                //     .widthInch.toFixed(2) + "in");


                $(".size-click").each(function() {
                    //let genSize = textSizeHelper();
                    $(this).find(".child-size").text(
                        sizeCurrent.widthCm.toFixed(2) + "cm x " +
                        sizeCurrent.heightCm.toFixed(2) + "cm / " +
                        sizeCurrent.widthInch.toFixed(2) + "in x " +
                        sizeCurrent.heightInch.toFixed(2) + "in"
                    );
                });


                // Check length
                let checkButton = true;
                let textWidth = parseFloat($("#nsc-size").data("text-width"));
                let textHeight = parseFloat($("#nsc-size").data("text-height"));
                let textLength = parseInt(text.val().replace(/\s/g, '').length);

                let charMin = parseInt($("#nsc-size").data("size-char-min"));
                let charMax = parseInt($("#nsc-size").data("size-char-max"));

                if (textLength < charMin || textLength > charMax) {
                    $("#warning-message").text("Text must be between " + charMin + " and " +
                        charMax +
                        " characters.");
                    checkButton = false;
                } else {
                    $("#warning-message").text("");
                }

                if (sizeCurrent.widthCm > textWidth) {
                    $("#warning-message-width").text("Maximum width is " + textWidth + " cm");
                    checkButton = false;
                } else {
                    $("#warning-message-width").text("");
                }

                if (sizeCurrent.heightCm > textHeight) {
                    $("#warning-message-height").text("Maximum height is " + textHeight + " cm");
                    checkButton = false;
                } else {
                    $("#warning-message-height").text("");
                }

                if (checkButton) {
                    $("#session-save").prop('disabled', '');
                } else {
                    $("#session-save").prop('disabled', 'disabled')
                }
            }

            function textSizeHelper() {

                const textContainer = $('.nsc-neon-live p');
                const computedStyle = window.getComputedStyle(textContainer[0]);
                const fontFamily = computedStyle.fontFamily;

                const text = $("#nsc-text");
                const textSize = parseFloat($("#nsc-size").data("font-size"));

                const lineOne = getTextDimensions(text.data("line-one"), textSize, fontFamily);
                const lineTwo = getTextDimensions(text.data("line-two"), textSize, fontFamily);
                const lineThree = getTextDimensions(text.data("line-three"), textSize, fontFamily);

                const widthCm = Math.max(parseFloat(lineOne.widthCm), parseFloat(lineTwo.widthCm),
                    parseFloat(
                        lineThree.widthCm));
                const widthInch = Math.max(parseFloat(lineOne.widthInch), parseFloat(lineTwo
                        .widthInch),
                    parseFloat(lineThree.widthInch));

                let heightCm = 0;
                let heightInch = 0;
                if (text.data("line-one").length > 0) {
                    heightCm = heightCm + parseFloat(lineOne.heightCm);
                    heightInch = heightInch + parseFloat(lineOne.heightInch);
                }
                if (text.data("line-two").length > 0) {
                    heightCm = heightCm + parseFloat(lineTwo.heightCm);
                    heightInch = heightInch + parseFloat(lineTwo.heightInch);
                }
                if (text.data("line-three").length > 0) {
                    heightCm = heightCm + parseFloat(lineThree.heightCm);
                    heightInch = heightInch + parseFloat(lineThree.heightInch);
                }

                return {
                    widthCm,
                    heightCm,
                    widthInch,
                    heightInch
                };
            }

            function getTextDimensions(text, fontSize, fontFamily) {
                const canvas = document.createElement('canvas');
                const context = canvas.getContext('2d');
                context.font = `${fontSize}px ${fontFamily}`;

                const textMetrics = context.measureText(text);

                const widthPx = textMetrics.width;
                const heightPx = fontSize; // Assuming the height is roughly equal to the font size

                const widthCm = ((widthPx / window.devicePixelRatio) * 2.54 / 96).toFixed(2);
                const heightCm = ((heightPx / window.devicePixelRatio) * 2.54 / 96).toFixed(2);

                const widthInch = (widthCm / 2.54).toFixed(2);
                const heightInch = (heightCm / 2.54).toFixed(2);

                return {
                    widthPx,
                    heightPx,
                    widthCm,
                    heightCm,
                    widthInch,
                    heightInch
                };
            }


            // UPDATE COLOR
            $(".colors-click").click(function() {
                // hidden value
                $("#nsc-color").data("id", $(this).data("id"));
                $("#nsc-color").data("name", $(this).data("name"));
                $("#nsc-color").data("hex", $(this).data("hex"));
                $("#nsc-color").data("rbg", $(this).data("rbg"));
                $("#nsc-color").data("extra-price", $(this).data("extra-price"));
                $("#nsc-color").data("price-type", $(this).data("price-type"));

                // Changge style
                updateNeon();
            });


            function updateToggleLight() {
                if ($("#nsc-color").data("toggle") == "on") {
                    $(".nsc-toggle-on").css("background-color", "var(--primary-text-color)");
                    $(".nsc-toggle-off").css("background-color", "#28303D");
                } else {
                    $(".nsc-toggle-off").css("background-color", "var(--primary-text-color)");
                    $(".nsc-toggle-on").css("background-color", "#28303D");
                }

            }


            function updateNeonColor() {

                $(".colors-click").removeClass("nsc-selected");
                $("#colors-" + $("#nsc-color").data("id")).addClass("nsc-selected");
                let hex = $("#nsc-color").data("hex");
                let rbg = $("#nsc-color").data("rbg");
                let toggle = $("#nsc-color").data("toggle");
                if (toggle == "on") {
                    if (rbg == "0") {
                        $(".nsc-neon-live p").css("text-shadow", hex + " 0px 0px 5px," + hex +
                            " 0px 0px 10px, " + hex + " 0px 0px 20px," + hex +
                            " 0px 0px 30px, " +
                            hex +
                            " 0px 0px 40px, " + hex + " 0px 0px 55px," + hex + " 0px 0px 75px");

                        if (intervalId) {
                            clearInterval(intervalId);
                            intervalId = null;
                        }
                    } else {
                        function getRandomColor() {
                            let letters = '0123456789ABCDEF';
                            let color = '#';
                            for (let i = 0; i < 6; i++) {
                                color += letters[Math.floor(Math.random() * 16)];
                            }
                            return color;
                        }

                        // Update shadow color with a random color and animate it
                        function updateShadowColor() {
                            let randomColor = getRandomColor();
                            $(".nsc-neon-live p").css("text-shadow", randomColor + " 0px 0px 5px," +
                                randomColor +
                                " 0px 0px 10px, " + randomColor + " 0px 0px 20px," +
                                randomColor +
                                " 0px 0px 30px, " + randomColor +
                                " 0px 0px 40px, " + randomColor + " 0px 0px 55px," +
                                randomColor +
                                " 0px 0px 75px");


                        }

                        // Initially set the shadow color and start the animation loop
                        updateShadowColor();

                        // Schedule the animation to run every 2 seconds
                        clearInterval(intervalId);
                        intervalId = setInterval(updateShadowColor, 1000);
                    }
                } else {
                    clearInterval(intervalId);
                    $(".nsc-neon-live p").css("text-shadow", "none");
                }


                $("#nsc-color-selected").html(" - " + $("#nsc-color").data("name"));
            }

            // UPDATE backboard
            $(".backboard-click").click(function() {
                // hidden value
                $("#nsc-backboard").data("id", $(this).data("id"));
                $("#nsc-backboard").data("name", $(this).data("name"));
                $("#nsc-backboard").data("extra-price", $(this).data("extra-price"));
                $("#nsc-backboard").data("price-type", $(this).data("price-type"));

                // Changge style
                updateNeon();
            });

            function updateBackboard() {
                $(".backboard-click").removeClass("nsc-selected");
                $("#backboard-" + $("#nsc-backboard").data("id")).addClass("nsc-selected");
                updateBackboardColor();
            }

            // UPDATE BACKBOARD COLOR
            $(".backboard-color-click").click(function() {
                // hidden value
                $("#nsc-backboard-color").data("id", $(this).data("id"));
                $("#nsc-backboard-color").data("name", $(this).data("name"));
                $("#nsc-backboard-color").data("parent", $(this).data("parent"));
                $("#nsc-backboard-color").data("hex", $(this).data("hex"));
                $("#nsc-backboard-color").data("extra-price", $(this).data("extra-price"));
                $("#nsc-backboard-color").data("price-type", $(this).data("price-type"));
                $("#nsc-backboard-color").data("image", $(this).data("image"));

                // Changge style
                updateNeon();
            });

            function updateBackboardColor() {

                // selected
                $(".backboard-color-click").css("display", "none");
                $(".backboard-group-" + $("#nsc-backboard").data("id")).css("display", "block");
                if ($(".backboard-group-" + $("#nsc-backboard").data("id")).length > 0) {
                    $("#backboard-color-title").css("display", "block");
                    let firstElement = $(".backboard-group-" + $("#nsc-backboard").data("id"))
                        .first();
                    if (firstElement.data("parent") != $("#nsc-backboard-color").data("parent")) {
                        $("#nsc-backboard-color").data("id", firstElement.data("id"));
                        $("#nsc-backboard-color").data("name", firstElement.data("name"));
                        $("#nsc-backboard-color").data("parent", firstElement.data("parent"));
                        $("#nsc-backboard-color").data("hex", firstElement.data("hex"));
                        $("#nsc-backboard-color").data("extra-price", firstElement.data(
                            "extra-price"));
                        $("#nsc-backboard-color").data("price-type", firstElement.data(
                            "price-type"));
                        $("#nsc-backboard-color").data("image", firstElement.data("image"));
                    }
                } else {
                    $("#backboard-color-title").css("display", "none");
                    $("#nsc-backboard-color").data("id", '0');
                    $("#nsc-backboard-color").data("name", "");
                    $("#nsc-backboard-color").data("parent", "0");
                    $("#nsc-backboard-color").data("hex", "");
                    $("#nsc-backboard-color").data("extra-price", "0");
                    $("#nsc-backboard-color").data("price-type", "0");
                    $("#nsc-backboard-color").data("image", "/");
                }

                // selected
                $(".backboard-color-click").removeClass("nsc-selected");
                $("#backboard-color-" + $("#nsc-backboard-color").data("id") + $("#nsc-backboard")
                        .data(
                            "id"))
                    .addClass("nsc-selected");

                // show name                
                if ($("#nsc-backboard-color").data("parent") == $("#nsc-backboard").data("id")) {
                    $("#backboard-color-name").html(" - " + $("#nsc-backboard-color").data("name"));
                }
            }

            // UPDATE MATERIAL
            $(".material-click").click(function() {
                // hidden value
                $("#nsc-material").data("id", $(this).data("id"));
                $("#nsc-material").data("name", $(this).data("name"));
                $("#nsc-material").data("extra-price", $(this).data("extra-price"));
                $("#nsc-material").data("price-type", $(this).data("price-type"));
                $("#nsc-material").data("image", $(this).data("image"));

                // Changge style
                updateNeon();
            });

            function updateMaterial() {
                $(".material-click").removeClass("nsc-selected");
                $("#material-" + $("#nsc-material").data("id")).addClass("nsc-selected");
            }

            // UPDATE JACKET
            $(".jacket-click").click(function() {
                // hidden value
                $("#nsc-jacket").data("id", $(this).data("id"));
                $("#nsc-jacket").data("name", $(this).data("name"));
                $("#nsc-jacket").data("extra-price", $(this).data("extra-price"));
                $("#nsc-jacket").data("price-type", $(this).data("price-type"));
                $("#nsc-jacket").data("image", $(this).data("image"));

                // Changge style
                updateNeon();
            });

            function updateJacket() {
                $(".jacket-click").removeClass("nsc-selected");
                $("#jacket-" + $("#nsc-jacket").data("id")).addClass("nsc-selected");
            }

            // UPDATE MOUNTING
            $(".mounting-click").click(function() {
                // hidden value
                $("#nsc-mounting").data("id", $(this).data("id"));
                $("#nsc-mounting").data("name", $(this).data("name"));
                $("#nsc-mounting").data("extra-price", $(this).data("extra-price"));
                $("#nsc-mounting").data("price-type", $(this).data("price-type"));
                $("#nsc-mounting").data("image", $(this).data("image"));

                // Changge style
                updateNeon();
            });

            function updateMounting() {
                $(".mounting-click").removeClass("nsc-selected");
                $("#mounting-" + $("#nsc-mounting").data("id")).addClass("nsc-selected");
            }

            // UPDATE REMOTE CONTROL
            $(".remote-control-click").click(function() {
                // hidden value
                $("#nsc-remote-control").data("id", $(this).data("id"));
                $("#nsc-remote-control").data("name", $(this).data("name"));
                $("#nsc-remote-control").data("extra-price", $(this).data("extra-price"));
                $("#nsc-remote-control").data("price-type", $(this).data("price-type"));
                $("#nsc-remote-control").data("image", $(this).data("image"));

                // Changge style
                updateNeon();
            });

            function updateRemoteControl() {
                $(".remote-control-click").removeClass("nsc-selected");
                $("#remote-control-" + $("#nsc-remote-control").data("id")).addClass(
                    "nsc-selected");
            }

            // UPDATE Plug TYPE
            $("#nsc-select-plug-type").on("change", function() {
                // hidden value
                $("#nsc-plug-type").data("id", $(this).find(':selected').data("id"));
                $("#nsc-plug-type").data("name", $(this).find(':selected').data("name"));
                $("#nsc-plug-type").data("extra-price", $(this).find(':selected').data(
                    "extra-price"));
                $("#nsc-plug-type").data("price-type", $(this).find(':selected').data(
                    "price-type"));
                $("#nsc-plug-type").data("image", $(this).find(':selected').data("image"));

                // Changge style
                updateNeon();
            });

            function updatePlugType() {
                $("#plug-type-" + $("#nsc-plug-type").data("id")).attr("selected", "selected");
            }

            // UPDATE CABLE COLOR
            $(".cable-color-click").click(function() {
                // hidden value
                $("#nsc-cable-color").data("id", $(this).data("id"));
                $("#nsc-cable-color").data("name", $(this).data("name"));
                $("#nsc-cable-color").data("extra-price", $(this).data("extra-price"));
                $("#nsc-cable-color").data("price-type", $(this).data("price-type"));
                $("#nsc-cable-color").data("image", $(this).data("image"));

                // Changge style
                updateNeon();
            });

            function updateCableColor() {
                $(".cable-color-click").removeClass("nsc-selected");
                $("#cable-color-" + $("#nsc-cable-color").data("id")).addClass("nsc-selected");

                $(".cable-label").removeClass("label-selected");
                $("#cable-label-" + $("#nsc-cable-color").data("id")).addClass("label-selected");
            }


            $('.add-to-cart').on('click', function(e) {
                e.preventDefault();

                let customText = ($("#nsc-text").data("line-one").length > 0 ? " Line 1: " + $(
                            "#nsc-text")
                        .data(
                            "line-one") : "") +
                    ($("#nsc-text").data("line-two").length > 0 ? "<br/> / Line 2: " + $(
                        "#nsc-text").data(
                        "line-two") : "") +
                    ($("#nsc-text").data("line-three").length > 0 ? "<br/> / Line 3: " + $(
                            "#nsc-text")
                        .data(
                            "line-three") : "") + " / Align:" + $(
                        "#nsc-font").data(
                        "align");
                let customPrice = $('#total-price').data('price');
                let customFont = $("#nsc-font").data('name');
                let customColor = $("#nsc-color").data('name');
                let customSize = $(".nsc-backboard").data("size");
                let customBackboard = $("#nsc-backboard").data('name');
                let customBackboardColor = $("#nsc-backboard-color").data('name');
                let customMaterial = $("#nsc-material").data('name');
                let customJacket = $("#nsc-jacket").data('name');
                let customMounting = $("#nsc-mounting").data('name');
                let customRemoteControl = $("#nsc-remote-control").data('name');
                let customPlugType = $("#nsc-plug-type").data('name');
                let customCableColor = $("#nsc-cable-color").data('name');
                let customSpecial = $("#nsc-special").val();
                let productID = $(this).data("product-id");

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
                        custom_material: customMaterial,
                        custom_jacket: customJacket,
                        custom_mounting: customMounting,
                        custom_remote_control: customRemoteControl,
                        custom_plug_type: customPlugType,
                        custom_cable_color: customCableColor,
                        custom_special: customSpecial,
                    },
                    success: function(response) {
                        if (response == "success") {
                            window.location.href = '<?= wc_get_checkout_url() ?>';
                        }
                    }
                });
            });

            $("#session-save").on("click", function(e) {
                e.preventDefault();

                let customPrice = $('#total-price').data('price');
                $("#nsc-price-result").html("$ " + customPrice.toFixed(2));

                let customText = $("#nsc-text").val();
                $("#nsc-text-result").html("<div>" +
                    ($("#nsc-text").data("line-one").length > 0 ? " - Line 1: " + $(
                            "#nsc-text")
                        .data(
                            "line-one") : "") +
                    ($("#nsc-text").data("line-two").length > 0 ? "<br/>- Line 2: " + $(
                        "#nsc-text").data(
                        "line-two") : "") +
                    ($("#nsc-text").data("line-three").length > 0 ? "<br/>- Line 3: " + $(
                            "#nsc-text")
                        .data(
                            "line-three") : "") +
                    "</div>" +
                    "<strong>Align:</strong> " + $(
                        "#nsc-font").data(
                        "align"));

                let customFont = {
                    id: $("#nsc-font").data('id'),
                    name: $("#nsc-font").data('name'),
                    toggle: $("#nsc-font").data('toggle'),
                    align: $("#nsc-font").data('align'),
                    fontUrl: $("#nsc-font").data('font-url'),
                    extraPrice: $("#nsc-font").data('extra-price'),
                    priceType: $("#nsc-font").data('price-type'),
                }
                $("#nsc-font-result").html(customFont.name);

                let customSize = {
                    id: $("#nsc-size").data('id'),
                    name: $("#nsc-size").data('name'),
                    fontId: $("#nsc-size").data('font-id'),
                    fontSize: $("#nsc-size").data('font-size'),
                    lineOne: $("#nsc-size").data('line-one'),
                    lineTwo: $("#nsc-size").data('line-two'),
                    lineThree: $("#nsc-size").data('line-three'),
                    sizeLength: $("#nsc-size").data('size-length'),
                    sizeVol: $("#nsc-size").data('size-vol'),
                    sizeFee: $("#nsc-size").data('size-fee'),
                    textWidth: $("#nsc-size").data('text-width'),
                    textHeight: $("#nsc-size").data('text-height'),
                    TextLine: $("#nsc-size").data('text-line'),
                    sizeCharMin: $("#nsc-size").data('size-char-min'),
                    sizeCharMax: $("#nsc-size").data('size-char-max'),
                }
                $("#nsc-size-result").html($(".nsc-backboard").data("size"));

                let customColor = {
                    id: $("#nsc-color").data('id'),
                    name: $("#nsc-color").data('name'),
                    hex: $("#nsc-color").data('hex'),
                    rbg: $("#nsc-color").data('rbg'),
                    toggle: $("#nsc-color").data('toggle'),
                    extraPrice: $("#nsc-color").data('extra-price'),
                    priceType: $("#nsc-color").data('price-type'),
                }
                $("#nsc-color-result").html(customColor.name);

                let customBackboard = {
                    id: $("#nsc-backboard").data('id'),
                    name: $("#nsc-backboard").data('name'),
                    size: $("#nsc-backboard").data('size'),
                    extraPrice: $("#nsc-backboard").data('extra-price'),
                    priceType: $("#nsc-backboard").data('price-type'),
                    image: $("#nsc-backboard").data('image'),
                }
                $("#nsc-backboard-result").html(customBackboard.name);

                let customBackboardColor = {
                    id: $("#nsc-backboard-color").data('id'),
                    parent: $("#nsc-backboard-color").data('parent'),
                    name: $("#nsc-backboard-color").data('name'),
                    hex: $("#nsc-backboard-color").data('hex'),
                    extraPrice: $("#nsc-backboard-color").data('extra-price'),
                    priceType: $("#nsc-backboard-color").data('price-type'),
                    image: $("#nsc-backboard-color").data('image'),
                }
                $("#nsc-backboard-color-result").html(customBackboardColor.name);

                let customMaterial = {
                    id: $("#nsc-material").data('id'),
                    name: $("#nsc-material").data('name'),
                    extraPrice: $("#nsc-material").data('extra-price'),
                    priceType: $("#nsc-material").data('price-type'),
                    image: $("#nsc-material").data('image'),
                }

                $("#nsc-material-result").html(customMaterial.name);

                let customJacket = {
                    id: $("#nsc-jacket").data('id'),
                    name: $("#nsc-jacket").data('name'),
                    extraPrice: $("#nsc-jacket").data('extra-price'),
                    priceType: $("#nsc-jacket").data('price-type'),
                    image: $("#nsc-jacket").data('image'),
                }
                $("#nsc-jacket-result").html(customJacket.name);

                let customMounting = {
                    id: $("#nsc-mounting").data('id'),
                    name: $("#nsc-mounting").data('name'),
                    extraPrice: $("#nsc-mounting").data('extra-price'),
                    priceType: $("#nsc-mounting").data('price-type'),
                    image: $("#nsc-mounting").data('image'),
                }
                $("#nsc-mounting-result").html(customMounting.name);

                let customRemoteControl = {
                    id: $("#nsc-remote-control").data('id'),
                    name: $("#nsc-remote-control").data('name'),
                    extraPrice: $("#nsc-remote-control").data('extra-price'),
                    priceType: $("#nsc-remote-control").data('price-type'),
                    image: $("#nsc-remote-control").data('image'),
                }
                $("#nsc-remote-control-result").html(customRemoteControl.name);

                let customPlugType = {
                    id: $("#nsc-plug-type").data('id'),
                    name: $("#nsc-plug-type").data('name'),
                    extraPrice: $("#nsc-plug-type").data('extra-price'),
                    priceType: $("#nsc-plug-type").data('price-type'),
                    image: $("#nsc-plug-type").data('image'),
                }
                $("#nsc-plug-type-result").html(customPlugType.name);

                let customCableColor = {
                    id: $("#nsc-cable-color").data('id'),
                    name: $("#nsc-cable-color").data('name'),
                    extraPrice: $("#nsc-cable-color").data('extra-price'),
                    priceType: $("#nsc-cable-color").data('price-type'),
                    image: $("#nsc-cable-color").data('image'),
                }
                $("#nsc-cable-color-result").html(customCableColor.name);

                let specialRequest = $("#nsc-special").val();
                $("#nsc-special-result").html(specialRequest);

                // alert(customCableColor);

                $.ajax({
                    type: "POST",
                    url: myAjax.ajaxurl,
                    data: {
                        action: "session_save",
                        custom_price: customPrice,
                        custom_text: customText,
                        custom_font: customFont,
                        custom_size: customSize,
                        custom_color: customColor,
                        custom_backboard: customBackboard,
                        custom_backboard_color: customBackboardColor,
                        custom_material: customMaterial,
                        custom_jacket: customJacket,
                        custom_mounting: customMounting,
                        custom_remote_control: customRemoteControl,
                        custom_plug_type: customPlugType,
                        custom_cable_color: customCableColor,
                        custom_special: specialRequest,
                    },
                    success: function(response) {
                        let data = JSON.parse(response);

                        if (data.message == "success") {
                            $("#mainModal").css("display", "none");
                            $("#reviewModal").css("display", "block");
                        }

                    },
                });
            });

            $(".edit-neon").click(function() {
                editNeon()
            });

            $(".image-example").click(function() {
                let id = $(this).data("id");
                $("#modalImage").attr("src", $("#" + id).data("image"));
                $("#imageModal").modal("show");
            });

            $(".backboard-example").click(function() {
                $("#modalImage").attr("src", $(this).data("image"));
                $("#imageModal").modal("show");
            });

            $("#nsc-toggle-light-open").click(function() {
                // open light
                $("#nsc-toggle-light-open").css("display", "none");
                $("#nsc-toggle-light-close").css("display", "block");
                $("#nsc-toggle-light-button").css("display", "flex");

                //close background
                $("#nsc-toggle-background-open").css("display", "block");
                $("#nsc-toggle-background-close").css("display", "none");
                $("#nsc-toggle-background-button").css("display", "none");
            });

            $("#nsc-toggle-light-close").click(function() {
                $("#nsc-toggle-light-open").css("display", "block");
                $("#nsc-toggle-light-close").css("display", "none");
                $("#nsc-toggle-light-button").css("display", "none");
            });

            $("#nsc-toggle-background-open").click(function() {
                // open background
                $("#nsc-toggle-background-open").css("display", "none");
                $("#nsc-toggle-background-close").css("display", "block");
                $("#nsc-toggle-background-button").css("display", "flex");

                //close light
                $("#nsc-toggle-light-open").css("display", "block");
                $("#nsc-toggle-light-close").css("display", "none");
                $("#nsc-toggle-light-button").css("display", "none");
            });

            $("#nsc-toggle-background-close").click(function() {
                $("#nsc-toggle-background-open").css("display", "block");
                $("#nsc-toggle-background-close").css("display", "none");
                $("#nsc-toggle-background-button").css("display", "none");
            });


            $(".nsc-toggle-on").click(function() {
                $("#nsc-color").data("toggle", "on");
                updateNeon();
            });

            $(".nsc-toggle-off").click(function() {
                $("#nsc-color").data("toggle", "off");
                updateNeon();
            });

            function editNeon() {
                $("#reviewModal").css("display", "none");
                $("#mainModal").css("display", "flex");

            }

            function backboardEffect() {
                $(".nsc-backboard").css("transform", "scale(1.1)");

                // Return 1 after scaling
                setTimeout(function() {
                    $(".nsc-backboard").css("transform", "scale(1)");
                }, 300);
            }

            function showToast(message, type) {
                const toastContainer = $("#toastContainer");
                const toast = $(
                        '<div class="toast" style="z-index:9999" role="alert" aria-live="assertive" aria-atomic="true">'
                    )
                    .addClass(`bg-${type} text-white`)
                    .attr("data-bs-delay", "10000")
                    .append($('<div class="toast-body">').text(message));

                toastContainer.append(toast);

                const bsToast = new bootstrap.Toast(toast[0]);
                bsToast.show();
            }

            function updateTabVisibility() {
                updateNeon();
                if ($(window).width() >= 992) { // Large screens (lg)

                    // $('.tab-content.tab-pane').css("display", "block");
                    $('.tab-pane').removeClass("fade");
                    $('.tab-pane').removeClass("nsc-mobile");
                    $('.tab-pane').addClass("nsc-desktop");
                } else { // Medium screens (md) and below
                    // $('.tab-content.tab-pane').hide();
                    $('.tab-pane').addClass("nsc-mobile");
                    $('.tab-pane').removeClass("nsc-desktop");
                }
            }

            // Call the function on page load and window resize
            updateTabVisibility();
            $(window).resize(updateTabVisibility);
        });
    })(jQuery);
    </script>
    <script src="https://cdn.jsdelivr.net/npm/jquery@3.5.1/dist/jquery.slim.min.js"
        integrity="sha384-DfXdz2htPH0lsSSs5nCTpuj/zy4C+OGpamoFVy38MVBnE+IbbVYUew+OrCXaRkfj" crossorigin="anonymous">
    </script>
    <?php
    do_action('woocommerce_after_main_content');
    get_footer();
    ?>