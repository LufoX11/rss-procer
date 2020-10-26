<div data-role="content" class="content-error">
    <div id="common-message" class="center">
<?php   if (isset($errorMessageDescription)) { ?>
            <p><?= Utils::escape($errorMessageDescription); ?></p>
<?php   } else { ?>
            <p><?= Utils::escape('Si el error persiste, podés enviarnos un email '
                . 'para informarnos de la situación.'); ?></p>
<?php   } ?>
        <p><a href="mailto:<?= $supportEmail; ?>"><?= $supportEmail; ?></a></p>
    </div>
</div>
