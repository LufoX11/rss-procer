<?php

/**
 * This hook is part of Slim's default hooks.
 *
 * This hook is invoked before the current matching route is dispatched.
 * Usually this hook is invoked only once during the Slim application lifecycle;
 * however, this hook may be invoked multiple times if a matching route chooses to pass
 * to a subsequent matching route.
 *
 * @link http://docs.slimframework.com/#Default-Hooks
 */
$Slim->hook('slim.before.dispatch', function () use ($Slim) {

    ////
    // Blacklisted devices.
    ////

    Loader::lib(array('Firewall'), true);

    $appCfg = $Slim->config('appCfg');
    $params = $Slim->router()->getCurrentRoute()->getParams();
    if (!isset($params['deviceId'])) {
        return;
    }
    $firewall = new Firewall(
        $appCfg['main']['firewall'],
        array(
            'id' => ($params['deviceId'])));

    // Blacklist
    if ($firewall->isBlacklisted()) {
        Loader::vendor(array('Slim/Extras/LogWriter/TimestampLogFileWriter'), true);

        $log = $Slim->getLog();
        $log->setWriter(new TimestampLogFileWriter(array('path' => dirname(__FILE__) . '/../../logs/firewall/')));
        $log->error("Blacklist (DEVICE): {$Slim->request()->getIp()}");

        if ($Slim->request()->isAjax()) {
            $Slim->halt(403, 'blacklist');
        } else {
            View_Renderer::setPanel(null);
            View_Renderer::setHeader('errorHeader.tpl');
            View_Renderer::setLayout('errorLayout.tpl');
            $Slim->render(
                'errorContent.tpl',
                array(
                    'supportEmail' => $appCfg['main']['sys']['emails']['email'],
                    'boot' => $Slim->request()->get('boot'),
                    'errorCode' => 500,
                    'errorMessage' => Utils::escape('Blacklist') . ' (403).',
                    'errorMessageDescription' => ''
                        . Utils::escape('Este dispositivo se encuentra bloqueado en nuestro sistema. ')
                        . Utils::escape('Si creés que puede tratarse de un error, por favor contactanos.')
                ),
                403);
            $Slim->stop();
        }
    }
});
