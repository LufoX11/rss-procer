<div data-role="header">
    <a href="#panel-default" data-icon="bars" data-iconpos="notext"></a>
<?php
    if (isset($serviceData['forceTitle']) && (bool) $serviceData['forceTitle']) { ?>
        <h1><?= Utils::escape($serviceData['title']); ?></h1>
<?php
    } else { ?>
        <h1><span class="rp-link">RSS</span> Procer</h1>
<?php
    } ?>
    <a href="<?= $Hp_View->getLink('home'); ?>" data-icon="home" data-iconpos="notext"></a>
</div>
