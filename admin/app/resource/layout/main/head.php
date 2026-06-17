<?php

require_once './app/include/Menu.php';

?>

<!doctype html>
<html lang="en">

    <head>

        <meta charset="utf-8" />
        <title><?= $title ?? "Terra Ventures" ?></title>
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <meta content="Premium Multipurpose Admin & Dashboard Template" name="description" />
        <meta content="Themesbrand" name="author" />
        <!-- App favicon -->
        <link rel="shortcut icon" href="assets/images/favicon.ico">
        
        <link rel="stylesheet"  type="text/css" href="assets/css/app.css"  />
        <link rel="stylesheet"  type="text/css" href="assets/css/bootstrap.min.css"  />
        <link rel="stylesheet"  type="text/css" href="assets/css/preloader.min.css"/>
        <link rel="stylesheet"  type="text/css" href="assets/css/icons.min.css" />

        <link rel="stylesheet"  type="text/css" href="assets/libs/select2/select2.min.css" />
        <link rel="stylesheet"  type="text/css" href="assets/libs/select2/select2-bootstrap.min.css"/>
        <link rel="stylesheet"  type="text/css" href="assets/libs/sweetalert2/sweetalert2.min.css" />
        <link rel="stylesheet"  type="text/css" href="assets/libs/Croppie-2.6.4/croppie.css" />
        <link rel="stylesheet"  type="text/css" href="assets/libs/fancybox/dist/jquery.fancybox.min.css" />

        <link rel="stylesheet" type="text/css"  href="assets/libs/bootstrap-datepicker/css/bootstrap-datepicker3.css" />
        <link rel="stylesheet" type="text/css"  href="assets/libs/bootstrap-timepicker/css/bootstrap-timepicker.min.css" />
        <link rel="stylesheet" type="text/css"  href="assets/libs/bootstrap-datetimepicker/css/bootstrap-datetimepicker.min.css" />

        <link rel="stylesheet"  type="text/css" href="public/css/backend/default.css?<?= SCRIPT_VERSION ?>" />


        <script src="assets/libs/jquery/jquery.min.js"></script>

        <link href="public/libs/loader/loader.css?<?= SCRIPT_VERSION ?>" rel="stylesheet" type="text/css" />
        <script src="public/libs/loader/loader.js?<?= SCRIPT_VERSION ?>" type="text/javascript" ></script>

        <style>
            .error{
                color: red;
            }
        </style>
    </head>

    <body>

        <div id="layout-wrapper">

            <?php require_once './app/resource/layout/main/header.php' ?>
            <?php require_once './app/resource/layout/main/sidebar.php' ?>
            
            <div class="main-content">
                <div class="page-content">

                <?php if (Session::hasFlash("success")): ?>
                <div class="alert alert-success" role="alert">
                    <?= Session::readFlash("success") ?>
                </div>
                <?php endif; ?>

                <?php if (Session::hasFlash("fail")): ?>
                <div class="alert alert-danger" role="alert">
                    <?= Session::readFlash("fail") ?>
                </div>
                <?php endif; ?>