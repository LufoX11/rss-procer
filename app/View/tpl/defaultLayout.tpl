<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML Basic 1.1//EN"
    "http://www.w3.org/TR/xhtml-basic/xhtml-basic11.dtd">
<html>
<head>
    <meta charset="utf-8">
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

    <!-- JS -->
    <script type="text/javascript" src="<?= $Hp_View->getJs('jquery-1.9.1.min.js'); ?>"></script>
    <script type="text/javascript" src="<?= $Hp_View->getJs('jquery.mobile-1.3.1.min.js'); ?>"></script>
    <script type="text/javascript" src="<?= $Hp_View->getJs('Utils.js'); ?>"></script>
    <script type="text/javascript" src="<?= $Hp_View->getJs('Storage.js'); ?>"></script>
    <script type="text/javascript" src="<?= $Hp_View->getJs('Main.js'); ?>"></script>
    <script type="text/javascript" src="<?= $Hp_View->getJs('Tracking.js'); ?>"></script>
    <script type="text/javascript" src="<?= $Hp_View->getJs('Weather.js'); ?>"></script>
    <script type="text/javascript" src="<?= $Hp_View->getJs('News.js'); ?>"></script>
    <script type="text/javascript" src="<?= $Hp_View->getJs('applets/headerWeather.js'); ?>"></script>
    <script type="text/javascript" src="<?= $Hp_View->getJs('applets/customForm.js'); ?>"></script>

    <!-- CSS -->
    <link rel="stylesheet" class="link-theme-file" href="<?= $Hp_View->getCss("themes/{$theme}.min.css"); ?>">
    <link rel="stylesheet" href="<?= $Hp_View->getCss('jquery.mobile.structure-1.3.1.min.css'); ?>">
    <link rel="stylesheet" href="<?= $Hp_View->getCss('custom.css'); ?>">
<?php
    if ($Hp_View->showMini()) { ?>
        <link rel="stylesheet" href="<?= $Hp_View->getCss('smartphones.css'); ?>">
<?php
    } else { ?>
        <link rel="stylesheet" href="<?= $Hp_View->getCss('tablets.css'); ?>">
<?php
    } ?>
    <link rel="stylesheet" href="<?= $Hp_View->getCss('error.css'); ?>">
    <link rel="stylesheet" class="link-theme-common-file"
        href="<?= $Hp_View->getCss("themes/common-{$themeColor}.css"); ?>">
</head>
<body>
    <div data-role="page" id="<?= "page-{$headerMenuActive}"; ?>" data-theme="a"
        data-dom-cache="<?= ($jqmCache ? 'true' : 'false'); ?>" class="<?= $theme; ?> main-page">

        <div class="css-customization">
            <style type="text/css">

<?php           // Inline styles for text size customization
                foreach ($textSizes as $k => $v) { ?>
                    <?= $k; ?> {
                        font-size: <?= $v; ?>px;
                    }
<?php           } ?>

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

        <script type="text/javascript">

            // Required objects initialization
            Utils.init();
            var iStorage = Storage.getInstance();

        </script>

        <!-- Panel -->
        <?= $htmlPanel; ?>

        <!-- Header -->
        <?= $htmlHeader; ?>

        <!-- Content -->
        <?= $htmlContent; ?>

        <!-- Footer -->
        <?= $htmlFooter; ?>
    </div>

    <!-- JS Initialization -->
    <script type="text/javascript">

        var iMain = Main.getInstance({
            "source": "<?= $client->source; ?>",
            "deviceId": "<?= Utils::escape($client->deviceId); ?>",
            "deviceType": "<?= $client->deviceType; ?>",
            "deviceVersion": "<?= Utils::escape($client->deviceVersion); ?>",
            "showMini": <?= ($Hp_View->showMini() ? 'true' : 'false'); ?>,
            "appVersion": "<?= $appVersion; ?>",
            "themes": <?= json_encode(Mo_UserSetting::$themes); ?>,
            "themeColor": "<?= $themeColor; ?>",
            "paths": {
                "img": "<?= $paths['static']['img']; ?>",
                "css": "<?= $paths['static']['css']; ?>"
            }
        });
        var iTracking = new Tracking(
            "<?= $client->source; ?>",
            "<?=  Utils::escape($client->deviceId); ?>",
            "<?= $client->deviceType; ?>",
            "<?= Utils::escape($client->deviceVersion); ?>",
            "<?= $appVersion; ?>");
        iTracking.baseUrl = "<?= "{$paths['site']['default']}/tracking"; ?>";

        $(document).ready(function() {

            // Clear cache (for refreshing news)
            iMain.clearCacheTimer(<?= $serviceData['newsCacheTimer']; ?>);

            // Check for application updates
            iMain.checkForUpdates();
<?php
            // Pages preload
            if ($preloadPages) { ?>
                setTimeout(function () {

                        iMain.preloadPages(["topics", "weather", "more", "config"]);
<?php                   if (isset($newsForPreload) && $newsForPreload) { ?>
                            iMain.preloadPages([<?= $newsForPreload; ?>]);
<?php                   } ?>
                    }, 1500);
<?php       } ?>
        });

    </script>
</body>
</html>
