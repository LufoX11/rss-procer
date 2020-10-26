<div data-role="content" class="content-news">
<?php
    if ($news) { ?>
        <div id="page-news-newsContainer">
            <h1>
                <img src="<?= $Hp_View->getImg($topic->getImageIcon()); ?>" />
                <?= Utils::escape($news->title); ?>
            </h1>
<?php
            if ($news->image) { ?>
                <img src="<?= $news->getBgImage(); ?>" class="picture-frame" />
<?php
            } ?>
            <div class="text">
                <h5><?= ucfirst(strftime("%A, %e de %B de %Y - %H:%M Hs.", strtotime($news->datetime))); ?><br /></h5>
                <?= Utils::escape($news->description, false, false, false); ?>

<?php           // Sharer applet
                include 'applets/sharerToolbar.tpl'; ?>

                <h5 class="center">
                    <a href="<?= $news->shortLink; ?>" data-role="button" data-icon="forward" rel="external"
                        data-inline="true" data-mini="true" target="_blank"
                        ><?= Utils::escape($serviceData['copyright']); ?></a>
                </h5>
            </div>
        </div>
<?php
    } ?>
</div>
