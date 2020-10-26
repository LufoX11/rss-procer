<div data-role="content">
<?php
    if ($Hp_View->isMobile() || $debugMode) { ?>
        <div class="text">
            <p><strong><span class="rp-link">RSS</span> Procer</strong>,
                <?= Utils::escape('es el lector de noticias que forma parte de la familia de productos '
                    . 'para dispositivos móviles de'); ?> <strong><span class="rp-link">i</span>Maat</strong>.</p>
            <p><?= Utils::escape('Si querés ponerte en contacto con nosotros, ya sea para reportar un problema '
                . 'en la aplicación, para sugerirnos una funcionalidad o por cualquier otro motivo, por favor '
                . 'completá el formulario a continuación y nos pondremos en contacto lo antes posible. '
                . '¡Muchas gracias por elegirnos!'); ?></p>
        </div>
        <ul id="about-form" data-role="listview" data-inset="true">
            <li class="ui-hide-label">
                <div data-role="fieldcontain">
                    <label for="about-name"><?= Utils::escape('Nombre'); ?>:</label>
                    <input id="about-name" type="text"
                        placeholder="<?= Utils::escape('Nombre'); ?>" />
                </div>
                <div data-role="fieldcontain">
                    <label for="about-email"><?= Utils::escape('Email'); ?>:</label>
                    <input id="about-email" type="text"
                        placeholder="<?= Utils::escape('Email'); ?>" />
                </div>
                <div data-role="fieldcontain">
                    <label for="about-description"><?= Utils::escape('Consulta'); ?>:</label>
                    <textarea id="about-description"
                        placeholder="<?= Utils::escape('Consulta'); ?>"
                        class="full-size"></textarea>
                </div>
            </li>
            <li>
                <button id="about-submit" data-icon="check"><?= Utils::escape('Enviar'); ?></button>
            </li>
        </ul>
<?php
    } else { ?>
        <div class="text">
            <p><strong><span class="rp-link">RSS</span> Procer</strong>,
                <?= Utils::escape('es el lector de noticias que forma parte de la familia de productos '
                    . 'para dispositivos móviles de'); ?> <strong><span class="rp-link">i</span>Maat</strong>.</p>
            <p><?= sprintf(Utils::escape('Si querés ponerte en contacto con nosotros, ya sea para reportar un problema '
                . 'en la aplicación, para sugerirnos una funcionalidad o por cualquier otro motivo, por favor '
                . 'envianos un email a %s y nos pondremos en contacto lo antes posible. '
                . '¡Muchas gracias por elegirnos!'), "<a href=\"mailto:{$supportEmail}\">{$supportEmail}</a>"); ?></p>
        </div>
<?php
    } ?>
</div>

<script type="text/javascript">

    // Triggers
    $('#about-submit').on("click", function() {
        iMain.saveContact();
    });

</script>
