<?php

/**
 * Renders the application interface for Legal.
 */

$Slim->get('/legal',
    $mdwDeviceGuessing,
    $mdwCache(),
    function () use ($Slim)
{
    // Render options
    View_Renderer::setPanel(null);
    View_Renderer::setHeader('guestHeader.tpl');
    View_Renderer::setLayout('guestLayout.tpl');

    $Slim->render('guestLegalContent.tpl');
});
