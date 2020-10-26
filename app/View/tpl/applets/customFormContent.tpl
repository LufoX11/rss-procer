<div data-role="content">
    <div class="text">
        <p><?= Utils::escape($applet['description_long']); ?></p>
    </div>
    <ul id="customForm-form" data-role="listview" data-inset="true">
        <li data-role="fieldcontain" class="ui-hide-label">
<?php       foreach ($appletFields as $v) {
                switch ($v['type']) {

                    // Input type TEXT
                    case 'text': ?>
                        <div data-role="fieldcontain">
                            <label for="customForm-<?= $v['name']; ?>"><?= Utils::escape($v['title']); ?>:</label>
                            <input id="customForm-<?= $v['name']; ?>" type="text" class="customForm-data"
                                value="<?= Utils::escape($formData[$v['name']]); ?>"
                                data-title="<?= $v['title']; ?>"
                                data-type="<?= $v['type']; ?>"
                                data-required="<?= $v['required']; ?>"
                                placeholder="<?= Utils::escape($v['description']); ?>" />
                        </div>
<?php                   break;

                    // Input type EMAIL (TEXT with email syntax validation)
                    case 'email': ?>
                        <div data-role="fieldcontain">
                            <label for="customForm-<?= $v['name']; ?>"><?= Utils::escape($v['title']); ?>:</label>
                            <input id="customForm-<?= $v['name']; ?>" type="text" class="customForm-data"
                                value="<?= Utils::escape($formData[$v['name']]); ?>"
                                data-title="<?= $v['title']; ?>"
                                data-type="<?= $v['type']; ?>"
                                data-required="<?= $v['required']; ?>"
                                placeholder="<?= Utils::escape($v['description']); ?>" />
                        </div>
<?php                   break;

                    // Input type TEXTAREA
                    case 'textarea': ?>
                        <div data-role="fieldcontain">
                            <label for="customForm-<?= $v['name']; ?>"><?= Utils::escape($v['title']); ?>:</label>
                            <textarea id="customForm-<?= $v['name']; ?>" class="customForm-data"
                                data-title="<?= $v['title']; ?>"
                                data-type="<?= $v['type']; ?>"
                                data-required="<?= $v['required']; ?>"
                                placeholder="<?= Utils::escape($v['description']); ?>" class="full-size"
                                ><?= Utils::escape($formData[$v['name']]); ?></textarea>
                        </div>
<?php                   break;
                }
            } ?>
        </li>
        <li>
            <button id="customForm-submit" data-icon="check"><?= Utils::escape('Enviar'); ?></button>
        </li>
    </ul>
</div>

<script type="text/javascript">

    var iCustomForm = CustomForm.getInstance();

    // Triggers
    $('#customForm-submit').on("click", function() {
        iCustomForm.sendSupportContact();
    });

</script>
