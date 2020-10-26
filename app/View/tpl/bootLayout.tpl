<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML Basic 1.1//EN"
    "http://www.w3.org/TR/xhtml-basic/xhtml-basic11.dtd">
<html>
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
    <meta name="format-detection" content="telephone=no" />
    <meta name="viewport" content="user-scalable=no, initial-scale=1, maximum-scale=1, minimum-scale=1, width=device-width, target-densitydpi=medium-dpi" />
    <meta name="description" content="<?= $serviceData['pageDescription']; ?>">
    <meta name="robots" content="noindex,nofollow">

    <title><?= $serviceData['pageTitle']; ?></title>
<?php
    if ($client->osBrand == Mo_Client::APPLE) { ?>
        <link rel="apple-touch-icon" sizes="57x57" type="image/png"
            href="<?= $Hp_View->getImg("/services/{$serviceData['favicon']}"); ?>">
<?php
    } else { ?>
        <link rel="icon" sizes="57x57" type="image/png"
            href="<?= $Hp_View->getImg("/services/{$serviceData['favicon']}"); ?>">
<?php
    } ?>

    <!-- CSS -->
    <link rel="stylesheet" href="<?= $Hp_View->getCss('boot.css'); ?>">
</head>
<body onload="setTimeout('init()', 3000);">

    <div class="css-customization">
        <style type="text/css">

<?php       // Avoid rounded corners for some old devices
            if ($avoidRoundedCorners) { ?>
                .event {
                    -moz-border-radius: 0 !important;
                    -webkit-border-radius: 0 !important;
                    border-radius: 0 !important;
                }
<?php       } ?>

        </style>
    </div>

    <?= $htmlContent; ?>

    <script type="text/javascript">

        function init() {

            location.replace('<?= $requestedUri; ?>');
        }

    </script>

</body>
</html>
