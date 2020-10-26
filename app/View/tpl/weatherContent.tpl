<div data-role="content">
<?php
    if ($weather) { ?>

        <!-- Grid -->
        <ul id="weather-content" data-role="listview" data-divider-theme="a" data-inset="true"
                <?= ($Hp_View->showMini() ? 'data-mini="true"' : ''); ?>>
<?php       $currentDay = time();
            foreach ($weather->forecast as $v) {
                $day = ($currentDay == time() ? Utils::escape('Hoy') : ucwords(strftime('%A', $currentDay))); ?>
                <li data-role="list-divider"><?= $day; ?></li>
                <li <?= (!$Hp_View->showMini() ? 'class="li-small"' : ''); ?>>
                    <img src="<?= $Hp_View->getImg($v['icon']); ?>" />
                    <h3><?= $v['description']; ?></h3>
<?php               if ($currentDay == time() && $weather->current['temperature']) { ?>
                        <p>
                            <?= Utils::escape('Temp'); ?>: <strong><?= $weather->current['temperature']; ?>&deg;
                                </strong> |
                            <?= Utils::escape('Hum'); ?>: <strong><?= $weather->current['humidity']; ?>%
                                </strong>
                        </p>
<?php               } ?>
                    <p>
                        <?= Utils::escape('Máx'); ?>: <strong><?= $v['high']; ?>&deg;</strong> |
                        <?= Utils::escape('Mín'); ?>: <strong><?= $v['low']; ?>&deg;</strong>
                    </p>
                </li>
<?php           $currentDay += 86400;
            } ?>
        </ul>

        <!-- Choose location -->
        <div data-role="fieldcontain" class="weather-select-location inline-content">
            <select id="weather-location" name="weatherLocation">
        <?php   foreach ($weatherLocations as $k => $v) { ?>
                    <option value="<?= $k; ?>" <?= ($userLocation == $k ? 'selected="selected"' : ''); ?>
                        ><?= $v[0]; ?></option>
        <?php   } ?>
            </select>
        </div>
<?php
    } else { ?>
        <h3><?= Utils::escape('Servicio no disponible'); ?></h3>
        <p><?= Utils::escape('El servicio se encuentra momentáneamente interrumpido. Por favor, revisá más tarde.'); ?></p>
<?php
    } ?>
</div>

<script type="text/javascript">

    var iWeather = Weather.getInstance({
        "source": "<?= $client->source; ?>",
        "deviceId": "<?= Utils::escape($client->deviceId); ?>",
        "deviceType": "<?= $client->deviceType; ?>",
        "deviceVersion": "<?= Utils::escape($client->deviceVersion); ?>"
    });

</script>
