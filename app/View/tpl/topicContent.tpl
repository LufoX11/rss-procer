<div data-role="content" class="content-topic">
    <ul data-role="listview" <?= ($Hp_View->showMini() ? 'data-mini="true"' : ''); ?> data-inset="true"
            data-filter="true" data-filter-theme="a" data-filter-placeholder="<?= Utils::escape('Buscar'); ?>">
<?php   foreach ($news as $v) {
            $alreadyRead = in_array($v->checksum, $readNews); ?>
            <li <?= (!$Hp_View->showMini() ? 'class="li-normal"' : ''); ?>><a data-id="<?= $v->checksum; ?>"
                href="<?= $Hp_View->getLink("news/{$v->checksum}"); ?>"
                class="list-news-a <?= $alreadyRead ? 'readnews' : '' ?>">
<?php           if ($v->image) { ?>
                    <img src="<?= $v->getMdImage(); ?>" />
<?php           } ?>
                <h3>
                    <img src="<?= $Hp_View->getImg($topics[$v->channels_id]->getImageIcon()); ?>" />
                    <?= Utils::escape($v->title); ?>
                </h3>
<?php           if ($v->summary) { ?>
                    <p><?= Utils::escape($v->summary, true); ?></p>
<?php           } ?>
<?php           if (!$Hp_View->showMini() && $v->datetime) { ?>
                    <p class="ui-li-aside">
                        <?= $topics[$v->channels_id]->title; ?> -
                        <strong><?= strftime('%H:%M', strtotime($v->datetime)); ?></strong> Hs.</p>
<?php           } ?>
            </a></li>
<?php   } ?>
    </ul>
</div>

<script type="text/javascript">

    var iNews = News.getInstance({
        "source": "<?= $client->source; ?>",
        "deviceId": "<?= Utils::escape($client->deviceId); ?>",
        "deviceType": "<?= $client->deviceType; ?>",
        "deviceVersion": "<?= Utils::escape($client->deviceVersion); ?>"
    });
<?php
    // Pages preload
    if ($preloadPages && isset($newsForPreload) && $newsForPreload) { ?>
        if (typeof iMain != "undefined") {
            iMain.preloadPages([<?= $newsForPreload; ?>]);
        }
<?php
    } ?>

</script>
