<div data-role="content">
    <div class="text">
        <p><?= sprintf(Utils::escape('Si te gusta %s, podés recomendarle la aplicación a un amigo.'),
            '<strong><span class="rp-link">RSS</span> Procer</strong>'); ?></p>
    </div>
    <ul id="recommend-form" data-role="listview" data-inset="true">
        <li data-role="fieldcontain" class="ui-hide-label">
            <div data-role="fieldcontain">
                <label for="recommend-name"><?= Utils::escape('Tu nombre'); ?>:</label>
                <input id="recommend-name" type="text"
                    placeholder="<?= Utils::escape('Tu nombre'); ?>" />
            </div>
            <div data-role="fieldcontain">
                <label for="recommend-email"><?= Utils::escape('Email de tu amigo'); ?>:</label>
                <input id="recommend-email" type="text"
                    placeholder="<?= Utils::escape('Email de tu amigo'); ?>" />
            </div>
        </li>
        <li>
            <button id="recommend-submit" data-icon="check"><?= Utils::escape('Recomendar'); ?></button>
        </li>
    </ul>
</div>

<script type="text/javascript">

    // Triggers
    $('#recommend-submit').on("click", function() {
        iMain.recommendApp();
    });

</script>
