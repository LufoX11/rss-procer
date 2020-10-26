<div data-role="content" class="content-config">
    <ul data-role="listview" data-inset="true" data-divider-theme="a"
        <?= ($Hp_View->showMini() ? 'data-mini="true"' : ''); ?>>

        <!-- Font size -->
        <li data-role="list-divider"><?= Utils::escape('Tamaño del texto'); ?></li>
        <li data-role="fieldcontain">
            <img id="config-textsize-icon" src="<?= $Hp_View->getImg('zoom.png'); ?>" />
            <fieldset id="config-textsize" data-role="controlgroup" data-type="horizontal">
                <input type="radio" name="config-text" id="config-text-b" value="0"
                    <?= ($defaultTextSize === 0 ? 'checked="checked"' : ''); ?> />
                <label for="config-text-b"><span class="font-2">A</span></label>
                <input type="radio" name="config-text" id="config-text-c" value="2"
                    <?= ($defaultTextSize === 2 ? 'checked="checked"' : ''); ?> />
                <label for="config-text-c"><span class="font-3">A</span></label>
<?php           if (!$Hp_View->showMini()) { ?>
                    <input type="radio" name="config-text" id="config-text-d" value="4"
                        <?= ($defaultTextSize === 4 ? 'checked="checked"' : ''); ?> />
                    <label for="config-text-d"><span class="font-4">A</span></label>
                    <input type="radio" name="config-text" id="config-text-e" value="6"
                        <?= ($defaultTextSize === 6 ? 'checked="checked"' : ''); ?> />
                    <label for="config-text-e"><span class="font-5">A</span></label>
<?php           } ?>
            </fieldset>
        </li>

        <!-- Theme -->
<?php   if ($canShowTheme) { ?>
            <li data-role="list-divider"><?= Utils::escape('Tema'); ?></li>
            <li data-role="fieldcontain">
                <img id="config-theme-icon" src="<?= $Hp_View->getImg('palette.png'); ?>" />
                <select id="config-theme" data-inline="true">
                    <optgroup label="<?= Utils::escape('Con base luminosa'); ?>">
<?php                   foreach ($themes['black'] as $v) { ?>
                            <option value="<?= $v; ?>" <?= ($defaultTheme == $v ? 'selected="selected"' : ''); ?>
                                ><?= ucwords($v); ?></option>
<?php                   } ?>
                    </optgroup>
                    <optgroup label="<?= Utils::escape('Con base oscura'); ?>">
<?php                   foreach ($themes['white'] as $v) { ?>
                            <option value="<?= $v; ?>" <?= ($defaultTheme == $v ? 'selected="selected"' : ''); ?>
                                ><?= ucwords($v); ?></option>
<?php                   } ?>
                    </optgroup>
                </select>
            </li>
<?php   } ?>

        <!-- App info -->
        <li data-role="list-divider"><?= Utils::escape('Información'); ?></li>
        <li data-role="fieldcontain" class="config-update">
            <img id="config-info-icon" src="<?= $Hp_View->getImg('info.png'); ?>" />

            <p class="title" style="margin-top: 0;"><?= Utils::escape('Aplicación'); ?></p>
            <p><?= Utils::escape('Versión actual'); ?>: <strong><?= $appVersion; ?></strong></p>

            <p class="title"><?= Utils::escape('Navegación'); ?></p>
            <p><?= Utils::escape('Tipo'); ?>:
                <strong><?= ($guestMode ? Utils::escape('Web') : Utils::escape('Mobile')); ?></strong></p>

            <p class="title"><?= Utils::escape('Dispositivo'); ?></p>
            <p><?= Utils::escape('Fabricante'); ?>: <strong><?= $appInfo['deviceBrand']; ?></strong></p>
            <p><?= Utils::escape('Tipo'); ?>: <strong><?= $appInfo['deviceCategory']; ?></strong></p>
            <p><?= Utils::escape('Modelo'); ?>: <strong><?= $appInfo['deviceTypeName']; ?></strong></p>
            <p><?= Utils::escape('Versión'); ?>: <strong><?= $appInfo['deviceVersion']; ?></strong></p>
        </li>

    </ul>
</div>

<script type="text/javascript">

    // Triggers
    $('#config-textsize input[type="radio"]').on("change", function() {
        iMain.textResize($(this).val());
    });
    $('#config-theme').on("change", function() {
        iMain.changeTheme($(this).val());
    });

</script>
