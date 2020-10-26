<div data-role="content">
    <ul data-role="listview" data-inset="true"
            <?= ($Hp_View->showMini() ? 'data-mini="true"' : ''); ?> data-count-theme="a">
<?php   foreach ($topics as $k => $v) { ?>
            <li <?= (!$Hp_View->showMini() ? 'class="li-normal"' : ''); ?>>
                <a href="<?= $Hp_View->getLink("topic/{$k}"); ?>">
                    <img src="<?= $Hp_View->getImg($v->image); ?>" />
                    <h3><?= Utils::escape($v->title); ?></h3>
                    <p><?= Utils::escape($v->description); ?></p>
                    <span class="ui-li-count"><?= count($news[$k]); ?></span>
                </a>
            </li>
<?php   } ?>
    </ul>
</div>
