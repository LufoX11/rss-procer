<div data-role="panel" id="panel-default" data-position="left" data-display="reveal">
    <h3 class="title"><?= Utils::escape($serviceData['title']); ?></h3>

    <!-- Applets -->
    <?php include "{$tplDirectory}/applets/headerWeather.tpl"; ?>

    <!-- Menu -->
    <ul id="panel-menu-main" data-role="listview" data-inset="true" data-corners="false">
        <li data-role="list-divider"><?= Utils::escape('Menú'); ?></li>
        <li><a href="<?= ($headerMenuActive === 'home' ? '#' : $Hp_View->getLink('home')); ?>">
            <img src="<?= $Hp_View->getImg('menu-home.png'); ?>" class="ui-li-icon" data-icon-name="home" />
            <?= Utils::escape(key($appearance['mainMenu'][0])); ?>
        </a></li>
<?php   if (current($appearance['mainMenu'][1])) { ?>
            <li><a href="<?= ($headerMenuActive === 'topics' ? '#' : $Hp_View->getLink('topics')); ?>">
                <img src="<?= $Hp_View->getImg('menu-topics.png'); ?>" class="ui-li-icon" data-icon-name="topics" />
                <?= Utils::escape(key($appearance['mainMenu'][1])); ?>
            </a></li>
<?php   } ?>
        <li><a href="<?= ($headerMenuActive === 'config' ? '#' : $Hp_View->getLink('config')); ?>">
            <img src="<?= $Hp_View->getImg('menu-config.png'); ?>" class="ui-li-icon" data-icon-name="config" />
            <?= Utils::escape(key($appearance['mainMenu'][2])); ?>
        </a></li>
        <li><a href="<?= ($headerMenuActive === 'more' ? '#' : $Hp_View->getLink('more')); ?>">
            <img src="<?= $Hp_View->getImg('menu-more.png'); ?>" class="ui-li-icon" data-icon-name="more" />
            <?= Utils::escape(key($appearance['mainMenu'][3])); ?>
        </a></li>
<?php   if ($exclusiveMode) { ?>
            <li><a href="<?= $Hp_View->getLink('guestLogin'); ?>" rel="external">
                <img src="<?= $Hp_View->getImg('menu-change.png'); ?>" class="ui-li-icon" data-icon-name="change" />
                <?= Utils::escape('Volver'); ?>
            </a></li>
<?php   } ?>
    </ul>
</div>
