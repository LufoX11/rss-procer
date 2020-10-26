<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML Basic 1.1//EN"
    "http://www.w3.org/TR/xhtml-basic/xhtml-basic11.dtd">
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="user-scalable=no, initial-scale=1, maximum-scale=1, minimum-scale=1, width=device-width, target-densitydpi=medium-dpi" />
    <meta name="description" content="RSS Procer - Error page.">
    <meta name="robots" content="noindex,nofollow">

    <title>:: Error - RSS Procer::</title>
    <link rel="icon" sizes="57x57" type="image/png" href="<?= $Hp_View->getImg('themes://favicon.png'); ?>">

    <!-- JS -->
    <script type="text/javascript" src="<?= $Hp_View->getJs('jquery-1.9.1.min.js'); ?>"></script>
    <script type="text/javascript" src="<?= $Hp_View->getJs('jquery.mobile-1.3.1.min.js'); ?>"></script>
    <script type="text/javascript" src="<?= $Hp_View->getJs('Main.js'); ?>"></script>

    <!-- CSS -->
    <link rel="stylesheet" href="<?= $Hp_View->getCss("themes/default.min.css"); ?>">
    <link rel="stylesheet" href="<?= $Hp_View->getCss('jquery.mobile.structure-1.3.1.min.css'); ?>">
    <link rel="stylesheet" href="<?= $Hp_View->getCss('custom.css'); ?>">
    <link rel="stylesheet" href="<?= $Hp_View->getCss('error.css'); ?>">
<?php
    if ($Hp_View->showMini()) { ?>
        <link rel="stylesheet" href="<?= $Hp_View->getCss('smartphones.css'); ?>">
<?php
    } else { ?>
        <link rel="stylesheet" href="<?= $Hp_View->getCss('tablets.css'); ?>">
<?php
    } ?>
    <link rel="stylesheet" href="<?= $Hp_View->getCss("themes/common-black.css"); ?>">
</head>
<body>
    <div data-role="page" id="page-error" data-theme="a" data-dom-cache="<?= ($jqmCache ? 'true' : 'false'); ?>">

        <div class="css-customization">
            <style type="text/css">

<?php           // Avoid rounded corners for some old devices
                if ($avoidRoundedCorners) { ?>
                    .ui-corner-all, .ui-btn-corner-all,
                    .ui-icon, .ui-icon-searchfield:after,
                    .ui-listview>.ui-li.ui-first-child,
                    .ui-listview .ui-btn.ui-first-child>.ui-li>.ui-btn-text>.ui-link-inherit {
                        -moz-border-radius: 0 !important;
                        -webkit-border-radius: 0 !important;
                        border-radius: 0 !important;
                    }
<?php           } ?>

            </style>
        </div>

        <!-- Header -->
        <?= $htmlHeader; ?>

        <!-- Content -->
        <div class="page-messages" style="display: none;"></div>
        <?= $htmlContent; ?>

        <!-- Footer -->
        <?= $htmlFooter; ?>
    </div>

<?php
    if (!$boot) { ?>
        <!-- JS Initialization -->
        <script type="text/javascript">

            var iMain = Main.getInstance({
                "appVersion": "<?= $appVersion; ?>",
                "themes": <?= json_encode(Mo_UserSetting::$themes); ?>,
                "themeColor": "<?= $themeColor; ?>",
                "paths": {
                    "img": "<?= $paths['static']['img']; ?>",
                    "css": "<?= $paths['static']['css']; ?>"
                }
            });

        </script>
<?php
    } ?>
</body>
</html>
