<?php

/**
 * Production mode.
 */

$Slim->configureMode('production', function () use ($Slim)
{
    if (class_exists('View_Renderer')) {
        View_Renderer::setDebugMode(false);
    }

    // Exceptions
    $Slim->error(function (Exception $e) use ($Slim)
    {
        Loader::vendor(array('Slim/Extras/LogWriter/TimestampLogFileWriter'), true);
        Loader::lib(array('Email'), true);

        $Slim->config('debug', false);
        $appCfg = $Slim->config('appCfg');

        // Save log
        $log = $Slim->getLog();
        $log->setWriter(new TimestampLogFileWriter(array('path' => dirname(__FILE__) . '/../../logs/')));
        $log->error($e);

        $fileName = $e->getFile();
        $receivers = explode('|', $appCfg['main']['alerts']['site']['toEmail']);

        // Send an alert email
        $Email = new Email();
        foreach ($receivers as $v) {
            $Email->send(array(
                'fromName' => $appCfg['main']['alerts']['site']['fromName'],
                'fromEmail' => $appCfg['main']['alerts']['site']['fromEmail'],
                'toEmail' => $v,
                'subject' => "RSSPROCER_SITE_ALERT: Uncaught Exception in: {$fileName}",
                'message' => $e
            ));
        }

        View_Renderer::setPanel(null);
        View_Renderer::setHeader('errorHeader.tpl');
        View_Renderer::setLayout('errorLayout.tpl');
        $Slim->render(
            'errorContent.tpl',
            array(
                'supportEmail' => $appCfg['main']['sys']['emails']['email'],
                'boot' => $Slim->request()->get('boot'),
                'errorCode' => 500,
                'errorMessage' => Utils::escape('Error interno') . ' (500).'
            ),
            200);
    });

    // Page not found
    $Slim->notFound(function () use ($Slim)
    {
        Loader::vendor(array('Slim/Extras/LogWriter/TimestampLogFileWriter'), true);

        $Slim->config('debug', false);
        $appCfg = $Slim->config('appCfg');
        View_Renderer::setPanel(null);
        View_Renderer::setHeader('errorHeader.tpl');
        View_Renderer::setLayout('errorLayout.tpl');
        $Slim->render(
            'errorContent.tpl',
            array(
                'supportEmail' => $appCfg['main']['sys']['emails']['email'],
                'boot' => $Slim->request()->get('boot'),
                'errorCode' => 404,
                'errorMessage' => Utils::escape('Página no disponible') . ' (404).'
            ),
            200);
    });
});
