<?php

/**
 * This hook is part of Slim's default hooks.
 *
 * This hook is invoked before the Slim application is run and before output buffering is turned on.
 * This hook is invoked once during the Slim application lifecycle.
 *
 * @link http://docs.slimframework.com/#Default-Hooks
 */

$Slim->hook('slim.before', function () use ($Slim)
{
    ////
    // Application firewall to protect against multiple / malicious requests.
    ////

    Loader::lib(array('ImobileDetector', 'MemcachedDriver', 'Firewall'), true);

    // Apply rules only if it's not a mobile device (mobile devices change or share the IP every time)
    $iMobileDetector = new ImobileDetector();

    if (!$iMobileDetector->isMobile()) {
        $appCfg = $Slim->config('appCfg');
        $firewall = new Firewall(
            $appCfg['main']['firewall'],
            array(
                'cache' => new MemcachedDriver($appCfg['cache']['memcached']),
                'id' => $Slim->request()->getIp()));

        // Blacklist
        if ($firewall->isBlacklisted()) {
            Loader::vendor(array('Slim/Extras/LogWriter/TimestampLogFileWriter'), true);

            $log = $Slim->getLog();
            $log->setWriter(new TimestampLogFileWriter(array('path' => dirname(__FILE__) . '/../../logs/firewall/')));
            $log->error("Blacklist (IP): {$Slim->request()->getIp()}");

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
                            . Utils::escape('Este dispositivo o red se encuentra bloqueado en nuestro sistema. ')
                            . Utils::escape('Si creés que puede tratarse de un error, por favor contactanos.')
                    ),
                    403);
            }
            $Slim->stop();
        }

        // Whitelist
        if ($firewall->isWhitelisted()) {
            return;
        }

        // Flood
        if ($firewall->isFlooding()) {
            Loader::vendor(array('Slim/Extras/LogWriter/TimestampLogFileWriter'), true);

            $log = $Slim->getLog();
            $log->setWriter(new TimestampLogFileWriter(array('path' => dirname(__FILE__) . '/../../logs/firewall/')));
            $log->error("Flood (IP): {$Slim->request()->getIp()}");

            if ($Slim->request()->isAjax()) {
                $Slim->halt(403, 'flood');
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
                        'errorMessage' => Utils::escape('Flood') . ' (403).',
                        'errorMessageDescription' => ''
                            . 'Nuestro sistema detectó y bloqueó temporalmente este dispositivo debido a un '
                            . 'posible ataque recibido. '
                            . 'Si creés que puede tratarse de un error, por favor contactanos.'
                    ),
                    403);
                $Slim->stop();
            }
        }
    }
});
