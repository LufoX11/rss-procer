<style type="text/css">

    .app {
        background: url("<?= str_replace('%THEME_COLOR%', $themeColor, $serviceData['loaderImg']); ?>") no-repeat center top;
        padding: 102px 0 0 0;
        margin: -66px 0 0 -160px;
    }

</style>
<div class="app">
    <div id="deviceready" class="blink">
        <p class="event listening">Iniciando</p>
    </div>
    <iframe id="preloaded-uri" src="<?= Utils::escape($iframeUri); ?>" ></iframe>
</div>
