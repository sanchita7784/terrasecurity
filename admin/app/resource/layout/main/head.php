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
        
        <!-- plugin css -->
        <link href="assets/libs/choices.js/public/assets/styles/choices.min.css" rel="stylesheet" type="text/css" />

        <!-- preloader css -->
        <link rel="stylesheet" href="assets/css/preloader.min.css" type="text/css" />

        <!-- Bootstrap Css -->
        <link href="assets/css/bootstrap.min.css" id="bootstrap-style" rel="stylesheet" type="text/css" />
        <!-- Icons Css -->
        <link href="assets/css/icons.min.css" rel="stylesheet" type="text/css" />
        <!-- App Css-->
        <link href="assets/css/app.min.css" id="app-style" rel="stylesheet" type="text/css" />

        <link href="assets/libs/sweetalert2/sweetalert2.min.css" rel="stylesheet" type="text/css" />

        <script src="assets/libs/jquery/jquery.min.js"></script>

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