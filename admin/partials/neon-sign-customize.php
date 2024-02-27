<?php

function getAdminUrl($func, $param = '', $type = '')
{
    $adminURL = "admin.php?page=neon-sign-customize";
    $func = sanitize_text_field($func);
    $param = sanitize_text_field($param);
    if ($func != '') {
        $adminURL = $adminURL . '&func=' . $func;
        if ($param != '') {
            $adminURL = $adminURL . '&param=' . $param;
        }
        if ($type != '') {
            $adminURL = $adminURL . '&type=' . $type;
        }
    }
    return $adminURL;
}
?>
<div class="container mt-3">
    <div id="toastContainer" style="position: fixed; top: 40px; right: 40px; z-index: 9999"></div>
    <nav class="navbar navbar-expand-lg  navbar-dark bg-dark">
        <a class="navbar-brand" href="<?= getAdminUrl('manual') ?>">Neon Sign Customize</a>
        <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarNavDropdown"
            aria-controls="navbarNavDropdown" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="navbarNavDropdown">
            <ul class="navbar-nav">
                <li class="nav-item dropdown">
                    <a class="nav-link dropdown-toggle" href="#" id="navbarDropdownMenuLink" data-toggle="dropdown"
                        aria-haspopup="true" aria-expanded="false">
                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor"
                            class="bi bi-tools" viewBox="0 0 16 16">
                            <path
                                d="M1 0 0 1l2.2 3.081a1 1 0 0 0 .815.419h.07a1 1 0 0 1 .708.293l2.675 2.675-2.617 2.654A3.003 3.003 0 0 0 0 13a3 3 0 1 0 5.878-.851l2.654-2.617.968.968-.305.914a1 1 0 0 0 .242 1.023l3.27 3.27a.997.997 0 0 0 1.414 0l1.586-1.586a.997.997 0 0 0 0-1.414l-3.27-3.27a1 1 0 0 0-1.023-.242L10.5 9.5l-.96-.96 2.68-2.643A3.005 3.005 0 0 0 16 3c0-.269-.035-.53-.102-.777l-2.14 2.141L12 4l-.364-1.757L13.777.102a3 3 0 0 0-3.675 3.68L7.462 6.46 4.793 3.793a1 1 0 0 1-.293-.707v-.071a1 1 0 0 0-.419-.814L1 0Zm9.646 10.646a.5.5 0 0 1 .708 0l2.914 2.915a.5.5 0 0 1-.707.707l-2.915-2.914a.5.5 0 0 1 0-.708ZM3 11l.471.242.529.026.287.445.445.287.026.529L5 13l-.242.471-.026.529-.445.287-.287.445-.529.026L3 15l-.471-.242L2 14.732l-.287-.445L1.268 14l-.026-.529L1 13l.242-.471.026-.529.445-.287.287-.445.529-.026L3 11Z" />
                        </svg>
                        Required Options
                    </a>
                    <div class="dropdown-menu" aria-labelledby="navbarDropdownMenuLink">
                        <a class="dropdown-item" href="<?= getAdminUrl('fonts', 'manage') ?>">Fonts</a>
                        <a class="dropdown-item" href="<?= getAdminUrl('colors', 'manage') ?>">Colors</a>
                        <a class="dropdown-item" href="<?= getAdminUrl('sizes', 'manage') ?>">Sizes</a>
                        <a class="dropdown-item" href="<?= getAdminUrl('prices', 'manage') ?>">Pricing</a>
                    </div>
                </li>
                <li class="nav-item dropdown">
                    <a class="nav-link dropdown-toggle" href="#" id="navbarDropdownMenuLink" data-toggle="dropdown"
                        aria-haspopup="true" aria-expanded="false">
                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor"
                            class="bi bi-plus-circle" viewBox="0 0 16 16">
                            <path d="M8 15A7 7 0 1 1 8 1a7 7 0 0 1 0 14zm0 1A8 8 0 1 0 8 0a8 8 0 0 0 0 16z" />
                            <path
                                d="M8 4a.5.5 0 0 1 .5.5v3h3a.5.5 0 0 1 0 1h-3v3a.5.5 0 0 1-1 0v-3h-3a.5.5 0 0 1 0-1h3v-3A.5.5 0 0 1 8 4z" />
                        </svg>
                        Additional Options
                    </a>
                    <div class="dropdown-menu" aria-labelledby="navbarDropdownMenuLink">
                        <a class="dropdown-item"
                            href="<?= getAdminUrl('additional', 'manage', 'background') ?>">Background</a>
                        <a class="dropdown-item" href="<?= getAdminUrl('backboard', 'manage') ?>">Backboard</a>
                        <a class="dropdown-item" href="<?= getAdminUrl('backboard_color', 'manage') ?>">Backboard
                            Colors</a>
                        <a class="dropdown-item"
                            href="<?= getAdminUrl('additional', 'manage', 'material') ?>">Materials</a>
                        <a class="dropdown-item"
                            href="<?= getAdminUrl('additional', 'manage', 'jacket', ) ?>">Jacket</a>
                        <a class="dropdown-item"
                            href="<?= getAdminUrl('additional', 'manage', 'mounting') ?>">Mounting</a>
                        <a class="dropdown-item"
                            href="<?= getAdminUrl('additional', 'manage', 'remote_control') ?>">Remote
                            Control</a>
                        <a class="dropdown-item" href="<?= getAdminUrl('additional', 'manage', 'plug_type') ?>">Plug
                            Type</a>
                        <a class="dropdown-item" href="<?= getAdminUrl('additional', 'manage', 'cable_color') ?>">Cable
                            Color</a>
                    </div>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="<?= getAdminUrl('settings') ?>">
                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor"
                            class="bi bi-wrench-adjustable" viewBox="0 0 16 16">
                            <path d="M16 4.5a4.492 4.492 0 0 1-1.703 3.526L13 5l2.959-1.11c.027.2.041.403.041.61Z" />
                            <path
                                d="M11.5 9c.653 0 1.273-.139 1.833-.39L12 5.5 11 3l3.826-1.53A4.5 4.5 0 0 0 7.29 6.092l-6.116 5.096a2.583 2.583 0 1 0 3.638 3.638L9.908 8.71A4.49 4.49 0 0 0 11.5 9Zm-1.292-4.361-.596.893.809-.27a.25.25 0 0 1 .287.377l-.596.893.809-.27.158.475-1.5.5a.25.25 0 0 1-.287-.376l.596-.893-.809.27a.25.25 0 0 1-.287-.377l.596-.893-.809.27-.158-.475 1.5-.5a.25.25 0 0 1 .287.376ZM3 14a1 1 0 1 1 0-2 1 1 0 0 1 0 2Z" />
                        </svg>
                        Settings
                    </a>
                </li>
            </ul>
        </div>
    </nav>
    <?php
    if (isset($_GET['func'])) {
        $func = sanitize_text_field($_GET['func']);
        if ($func == 'manual') {
            include_once(MY_AWESOME_PLUGIN_PATH . '/partials/neon-sign-manual.php');
        } else if ($func == 'fonts') {
            if (isset($_GET['param']) && $_GET['param'] == 'add-new') {
                include_once(MY_AWESOME_PLUGIN_PATH . '/partials/neon-sign-fonts-add-new.php');
            } else {
                include_once(MY_AWESOME_PLUGIN_PATH . '/partials/neon-sign-fonts-manage.php');
            }
        } else if ($func == 'colors') {
            if (isset($_GET['param']) && $_GET['param'] == 'add-new') {
                include_once(MY_AWESOME_PLUGIN_PATH . '/partials/neon-sign-colors-add-new.php');
            } else {
                include_once(MY_AWESOME_PLUGIN_PATH . '/partials/neon-sign-colors-manage.php');
            }
        } else if ($func == 'sizes') {
            if (isset($_GET['param']) && $_GET['param'] == 'add-new') {
                include_once(MY_AWESOME_PLUGIN_PATH . '/partials/neon-sign-sizes-add-new.php');
            } else {
                include_once(MY_AWESOME_PLUGIN_PATH . '/partials/neon-sign-sizes-manage.php');
            }
        } else if ($func == 'prices') {
            if (isset($_GET['param']) && $_GET['param'] == 'add-new') {
                include_once(MY_AWESOME_PLUGIN_PATH . '/partials/neon-sign-prices-add-new.php');
            } else {
                include_once(MY_AWESOME_PLUGIN_PATH . '/partials/neon-sign-prices-manage.php');
            }
        } else if ($func == 'backboard') {
            if (isset($_GET['param']) && $_GET['param'] == 'add-new') {
                include_once(MY_AWESOME_PLUGIN_PATH . '/partials/neon-sign-backboard-add-new.php');
            } else {
                include_once(MY_AWESOME_PLUGIN_PATH . '/partials/neon-sign-backboard-manage.php');
            }
        } else if ($func == 'backboard_color') {
            if (isset($_GET['param']) && $_GET['param'] == 'add-new') {
                include_once(MY_AWESOME_PLUGIN_PATH . '/partials/neon-sign-backboard-color-add-new.php');
            } else {
                include_once(MY_AWESOME_PLUGIN_PATH . '/partials/neon-sign-backboard-color-manage.php');
            }
        } else if ($func == 'additional') {
            if (isset($_GET['param']) && $_GET['param'] == 'add-new') {
                include_once(MY_AWESOME_PLUGIN_PATH . '/partials/neon-sign-additional-add-new.php');
            } else {
                include_once(MY_AWESOME_PLUGIN_PATH . '/partials/neon-sign-additional-manage.php');
            }
        } else if ($func == 'settings') {
            include_once(MY_AWESOME_PLUGIN_PATH . '/partials/neon-sign-settings.php');
        } else {
            include_once(MY_AWESOME_PLUGIN_PATH . '/partials/neon-sign-manual.php');
        }
    } else {
        include_once(MY_AWESOME_PLUGIN_PATH . '/partials/neon-sign-manual.php');
    }

    ?>
</div>