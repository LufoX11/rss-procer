<div class="applet-header-weather">
    <ul data-role="listview" data-corners="false" data-inset="true">
        <li data-role="list-divider"><?= Utils::escape('Clima'); ?></li>
        <li><?= Utils::escape('Obteniendo...'); ?></li>
    </ul>
</div>

<script type="text/javascript">

    $("#<?= "page-{$headerMenuActive}"; ?>").on("pageshow", function() {
        var iHeaderWeather = HeaderWeather.getInstance({
            "source": "<?= $client->source; ?>",
            "deviceId": "<?= Utils::escape($client->deviceId); ?>",
            "deviceType": "<?= $client->deviceType; ?>",
            "deviceVersion": "<?= Utils::escape($client->deviceVersion); ?>"
        });

        iHeaderWeather.init();
    });

</script>
