<?php

/**
 * AJAX responser for contact actions.
 */

$Slim->map('/:source/:deviceId/:deviceType/:deviceVersion/ajax/contact/:action',
    $mdwDeviceGuessing,
    function ($source, $deviceId, $deviceType, $deviceVersion, $action) use ($Slim)
{
    Loader::mo(array('ContactMapper'), true);
    Loader::lib(array('Email'));

    $appCfg = $Slim->config('appCfg');

    // User session
    $user = Mo_UsersMapper::sessionUp($source, $deviceId, $deviceType, $deviceVersion);

    $Email = new Email(array(
        'whitelistDomains' => explode(',', $appCfg['main']['emails']['whitelistDomains']),
        'exclude' => explode(',', $appCfg['main']['emails']['exclude']),
        'blacklist' => explode(',', $appCfg['main']['emails']['blacklist'])));
    switch ($action) {
        case 'save':
            $name = $email = $description = '';
            extract($Slim->request()->post('data'));
            if ($name && $Email->validateEmail($email, false) && $description) {
                $Mo_ContactMapper = new Mo_ContactMapper($appCfg);
                $status = (strlen($description) > 10 && strlen($name) > 5 && $Email->validateEmail($email) ?
                    Mo_Contact::STATUS_UNREAD : Mo_Contact::STATUS_SUSPECT);
                $Mo_Contact = new Mo_Contact(array(
                    'name' => $name,
                    'email' => $email,
                    'reason' => Mo_Contact::REASON_ASK,
                    'description' => $description,
                    'service' => 'iMaat',
                    'deviceData' => "{$deviceId}|{$deviceType}|{$deviceVersion}",
                    'ip' => $_SERVER['REMOTE_ADDR'],
                    'userAgent' => $_SERVER['HTTP_USER_AGENT'],
                    'status' => $status
                ));

                if ($Mo_ContactMapper->save($Mo_Contact)) {
                    Ajax::setSuccess($Slim, array(
                        'data' => Utils::escape('Muchas gracias por escribirnos. Nos contactaremos en breve.')));
                } else {
                    Ajax::setError($Slim, array(
                        'data' => Utils::escape('Ocurrió un error al enviar.')));
                }
            } else {
                Ajax::setBadRequest($Slim, array(
                    'data' => Utils::escape('Faltan datos requeridos o se enviaron datos inválidos.')));
            }
            break;

        case 'sendSupportTicket':
            $fields = $Slim->request()->post('data');
            $applet = (isset($appCfg['applets']['customForm']) ? $appCfg['applets']['customForm'] : null);
            $messageFields = array();
            foreach ($fields as $k => $v) {
                list($name, $type, $required) = explode('|', $k);
                if ($required && !($v = trim($v))) {
                    Ajax::setBadRequest($Slim, array(
                        'data' => sprintf(Utils::escape('Por favor, completá el campo "%s".'),
                            "<strong>{$name}</strong>")));
                }

                // Validation rules
                switch ($type) {
                    case 'email':
                        if (!$Email->validateEmail($v, false)) {
                            Ajax::setBadRequest($Slim, array(
                                'data' => sprintf(Utils::escape('Por favor, verificá el campo "%s".'),
                                    "<strong>{$name}</strong>")));
                        }
                        $email = $v;
                        break;
                }
                $messageFields[] = '<strong>' . ucfirst($name) . '</strong>' . ": {$v}";
            }

            // Send email to service owner
            $emails = explode('|', $applet['emails']);
            $ticketId = round(microtime(true) * 10006);
            array_unshift($messageFields, "<strong>Ticket ID</strong>: {$ticketId}");
            foreach ($emails as $v) {
                $emailData = array(
                    'fromName' => $appCfg['main']['sys']['emails']['name'],
                    'fromEmail' => $appCfg['main']['sys']['emails']['email'],
                    'toEmail' => $v,
                    'subject' => str_replace('%TICKET_ID%', $ticketId, $applet['email_subject']),
                    'message' => sprintf(''
                        . '<h1>' . Utils::escape('Nueva consulta desde RSS Procer') . '</h1>%s',
                        implode('<br />', $messageFields)
                    )
                );
                if ($Email->send($emailData)) {

                    // Send email to user (only if field "email" is present)
                    if (isset($email)) {
                        $emailData = array(
                            'fromName' => $appCfg['main']['sys']['emails']['name'],
                            'fromEmail' => $appCfg['main']['sys']['emails']['email'],
                            'toEmail' => $email,
                            'subject' => str_replace('%TICKET_ID%', $ticketId, $applet['email_success_subject']),
                            'message' => str_replace('%TICKET_ID%', $ticketId, $applet['email_success_body'])
                        );
                        $Email->send($emailData);
                    }

                    Ajax::setSuccess($Slim, array(
                        'data' => Utils::escape('¡Muchas gracias por tu consulta! Nos contactaremos en breve.')));
                } else {
                    Ajax::setError($Slim, array(
                        'data' => Utils::escape('Ocurrió un error al enviar.')));
                }
            }
            break;

        case 'recommendApp':
            $name = $email = '';
            extract($Slim->request()->post('data'));
            if ($name && $Email->validateEmail($email, false)) {
                $data = array(
                    'fromName' => $appCfg['main']['sys']['emails']['name'],
                    'fromEmail' => $appCfg['main']['sys']['emails']['email'],
                    'toEmail' => $email,
                    'subject' => sprintf(Utils::escape('%s te recomendó una aplicación', false, true, false),
                        ucwords($name)),
                    'message' => sprintf(''
                        . '¡Hola!<br /><br />%s te recomendó que instales %s '
                        . 'y te actualices de forma gratuita e instantánea en tu mobile.<br /><br />'
                        . '<div style="text-align: center;">Descargá la aplicación accediendo desde '
                        . '<a href="%s">%s</a></div><br /><br /><br />'
                        . 'El equipo de %s.',
                        '<strong>' . ucwords($name) . '</strong>',
                        '<strong><span style="color: #BD0000;">RSS</span> Procer</strong>',
                        'http://downloads.imaat.com.ar',
                        'http://downloads.imaat.com.ar',
                        '<strong><span style="color: #BD0000;">i</span>Maat</strong>'
                    )
                );
                if ($Email->send($data)) {
                    Ajax::setSuccess($Slim, array(
                        'data' => Utils::escape('¡Muchas gracias por recomendarnos!')));
                } else {
                    Ajax::setError($Slim, array(
                        'data' => Utils::escape('Ocurrió un error al enviar.')));
                }
            } else {
                Ajax::setBadRequest($Slim, array(
                    'data' => Utils::escape('Faltan datos requeridos o se enviaron datos inválidos.')));
            }

            break;

        default:
            Ajax::setBadRequest($Slim, array(
                'data' => Utils::escape('Se requiere una acción.')));
    }
})->via('GET', 'POST');
