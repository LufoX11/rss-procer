<div data-role="header">
    <ul data-role="listview" <?= ($Hp_View->showMini() ? 'data-mini="true"' : ''); ?>><li>
        <img src="<?= $Hp_View->getImg('themes://rssprocer-thumb.png'); ?>" />
        <h3><?= Utils::escape('Ocurrió un error'); ?></h3>
        <p><em><?= (isset($errorMessage) ? $errorMessage : ''); ?></em></p>
    </li></ul>
</div>
