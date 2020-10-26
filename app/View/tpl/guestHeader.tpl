<div data-role="header">
    <ul data-role="listview" <?= ($Hp_View->showMini() ? 'data-mini="true"' : ''); ?>><li>
        <img src="<?= $Hp_View->getImg('themes://rssprocer-thumb.png'); ?>" />
        <h3><?= sprintf(Utils::escape('¡Bienvenido a %sRSS%s Procer!'), '<span class="rp-link">', '</span>'); ?></h3>
        <p><em><?= Utils::escape('TU lector de noticias MOBILE.');; ?></em></p>
    </li></ul>
</div>
