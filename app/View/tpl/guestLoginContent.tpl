<div data-role="content" class="content-guestLogin">
    <form id="guestLogin-form" action="/" method="post" data-ajax="false">
        <input type="hidden" name="loginType" value="rssprocer" />
        <input type="hidden" name="deviceId" value="<?= Utils::escape($deviceId); ?>" />
        <input type="hidden" name="deviceType" value="<?= Utils::escape($deviceType); ?>" />
        <input type="hidden" name="deviceVersion" value="<?= Utils::escape($deviceVersion); ?>" />
        <input id="guestLogin-form-service" type="hidden" name="service" value="" />
    </form>
    <ul id="guestLogin-services" data-role="listview" data-inset="true" data-filter="true"
            data-filter-theme="a" data-divider-theme="a" class="ui-hide-label autoHeight"
            data-filter-placeholder="<?= Utils::escape('Buscar'); ?>">
<?php   foreach ($services as $t => $d) { ?>
            <li data-role="list-divider" <?= (!$Hp_View->showMini() ? 'class="li-normal"' : ''); ?>
                ><?= Utils::escape($t); ?></li>
<?php       foreach ($d as $k => $v) { ?>
                <li <?= (!$Hp_View->showMini() ? 'class="li-normal"' : ''); ?>>
                    <a href="#" data-service="<?= Utils::escape($v['id']); ?>">
                        <img src="<?= $v['icon']; ?>" class="ui-li-icon" />
                        <?= Utils::escape($v['title']); ?><br />
<?php                   if (isset($v['suggestedBy'])) { ?>
                            <p>
                                <?= Utils::escape('sugerido por '); ?>
                                <span class="rp-link"><?= $v['suggestedBy']; ?></span>
                            </p>
<?php                   } ?>
                    </a>
                </li>
<?php       }
        } ?>
    </ul>
    <div class="text">
        <p class="center">
            <span class="small-title"><?= Utils::escape('Desde tu Navegador'); ?></span><br />
            <span class="rp-link"><strong><?= $Hp_View->getLink('default', null,
                array('removeProtocol' => true)); ?></strong></span>
        </p>
    </div>
    <ul data-role="listview" data-inset="true"><li>
        <img src="<?= $Hp_View->getImg('themes://black/msg-info.png'); ?>" />
        <?= Utils::escape('Estamos agregando nuevos servicios. Si hay algún diario, revista o sitio informativo que te gustaría que agreguemos, escribinos a '); ?>
        <span class="rp-link"><a href="mailto:soporte@imaat.com.ar?subject=Pedido de Nuevo Canal" target="_blank"
            >soporte@imaat.com.ar</a></span>
        <?= Utils::escape('y te avisamos cuando esté listo.'); ?>
    </li></ul>
</div>

<script type="text/javascript">

    // Triggers
    $("#guestLogin-services li a").on("click", function () {

        $("#guestLogin-form-service").val($(this).attr("data-service"));
        $.mobile.showPageLoadingMsg();
        setTimeout(function () { $("#guestLogin-form").submit() }, 1000);
    });

</script>
