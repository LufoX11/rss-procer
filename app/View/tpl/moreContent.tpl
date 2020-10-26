<div data-role="content" class="content-more">
    <ul data-role="listview" <?= ($Hp_View->showMini() ? 'data-mini="true"' : ''); ?> data-inset="true">
        <li data-role="list-divider"><?= Utils::escape('Accesorios'); ?></li>
        <li><a href="<?= $Hp_View->getLink('weather'); ?>">
            <img src="<?= $Hp_View->getImg('more-weather.png'); ?>" />
            <h3><?= Utils::escape('Clima'); ?></h3>
            <p><?= Utils::escape('Informate sobre el clima actual.'); ?></p>
        </a></li>
<?php
            ////
            // User applets
            ////

            foreach ($userApplets as $k => $v) { ?>
                <li><a href="<?= $Hp_View->getLink("applets/{$k}"); ?>">
                    <img src="<?= $Hp_View->getImg($v['image']); ?>" />
                    <h3><?= Utils::escape($v['title']); ?></h3>
                    <p><?= Utils::escape($v['description']); ?></p>
                </a></li>
<?php       } ?>

        <li data-role="list-divider">
            <?= sprintf(Utils::escape('%sRSS%s Procer'), '<span class="rp-link">', '</span>'); ?>
        </li>
        <li><a href="<?= $Hp_View->getLink('recommend'); ?>">
            <img src="<?= $Hp_View->getImg('more-recommend.png'); ?>" />
            <h3><?= Utils::escape('Invitá a un amigo'); ?></h3>
            <p><?= Utils::escape('Invitá a un amigo a usar la aplicación.'); ?></p>
        </a></li>
        <li><a href="<?= $Hp_View->getLink('about'); ?>">
            <img src="<?= $Hp_View->getImg('more-about.png'); ?>" />
            <h3><?= Utils::escape('Sobre nosotros'); ?></h3>
            <p><?= Utils::escape('Contactanos y obtené ayuda.'); ?></p>
        <li><a href="<?= $Hp_View->getLink('/legal', null, array('base' => '/')); ?>" rel="external">
            <img src="<?= $Hp_View->getImg('legal.png'); ?>" />
            <h3><?= Utils::escape('Términos y Condiciones'); ?></h3>
            <p><?= Utils::escape('Política de datos y Términos y Condiciones de uso.'); ?></p>
        </a></li>
    </ul>
</div>
