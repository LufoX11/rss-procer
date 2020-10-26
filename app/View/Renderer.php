<?php

/**
 * Renderizes a template.
 * This object should be only implemented by Slim.
 *
 * ---
 * Use:
 *
 * $View_Renderer = new View_Renderer(<options>);
 *
 * ---
 * Options:
 *
 * 'layout': The layout to use in the template. This is the main file container.
 * 'header': The header to use in the layout.
 * 'footer': The footer to use in the layout.
 * 'panel': The panel to use in the layout.
 * 'appCfg': Application configuration.
 * 'exclusiveMode': Whether the app is in exclusive mode (for a client like AAAVyT) or not.
 * 'debugMode': Whether the app is running in development or production mode (development = debug).
 */

class View_Renderer extends Slim\View
{
    /**
     * @var array Renderer options.
     */
    protected static $_options = array(
        'layout' => 'defaultLayout.tpl',
        'header' => 'defaultHeader.tpl',
        'footer' => 'defaultFooter.tpl',
        'panel' => 'defaultPanel.tpl',
        'exclusiveMode' => false,
        'debugMode' => true
    );

    /**
     * Builds a new Renderer.
     *
     * @param $options array Initialization options.
     * @return void.
     */
    public function __construct(array $options)
    {
        $domain = $options['domain'] = (isset($options['domain']) ? $options['domain'] : 'public1');
        $uriParts = array_values(array_filter(explode('/', current(explode('?', $_SERVER['REQUEST_URI'])))));
        $theme = (isset($options['appCfg']['service']['theme']) ? $options['appCfg']['service']['theme'] : 'default');
        switch ($domain) {
            case 'public1': default:
                $Appearance = new Hp_Appearance($options['appCfg']['appearance']);
                self::$_options['appearance'] = $Appearance->make();
                self::$_options['headerMenuActive'] = (isset($uriParts[4]) ? $uriParts[4] : 'boot');
                break;

            case 'guest':
                self::$_options['headerMenuActive'] = (isset($uriParts[0]) ? $uriParts[0] : 'guestLogin');
                break;
        }
        self::$_options['Hp_View'] = new Hp_View(array('Path', 'Browser'), $options['appCfg']);
        self::$_options['uri'] = $options['appCfg']['paths']['web']['site']['base'];
        self::$_options['paths'] = $options['appCfg']['paths']['web'];
        self::$_options['fsPaths'] = $options['appCfg']['paths']['fs'];
        self::$_options['appVersion'] = $options['appCfg']['front']['versioning']['application'];
        self::$_options['theme'] = (Mo_UsersMapper::isThemeAvailable($theme) ? $theme : 'default');
        self::$_options['preloadPages'] = $options['appCfg']['main']['framework']['preloadPages'];
        self::$_options['exclusiveMode'] =
            (isset($options['exclusiveMode']) ? $options['exclusiveMode'] : self::$_options['exclusiveMode']);
        self::$_options['debugMode'] =
            (isset($options['debugMode']) ? $options['debugMode'] : self::$_options['debugMode']);
        self::$_options = $options + self::$_options;
    }

    /**
     * Renders the template.
     *
     * @param $template string The template name to use.
     * @return string HTML page content.
     */
    public function render($template)
    {
        Loader::mo(array('Client'), true);

        // Data for the template
        extract($this->data);
        $Hp_View = self::$_options['Hp_View'];
        $paths = self::$_options['paths'];
        $fsPaths = self::$_options['fsPaths'];
        $uri = self::$_options['uri'];
        $appVersion = self::$_options['appVersion'];
        $headerMenuActive = self::$_options['headerMenuActive'];
        $jqmCache = (isset($jqmCache) ? $jqmCache : true);
        $tplDirectory = $this->getTemplatesDirectory();
        $themeColor = 'black';
        $theme = self::$_options['theme'];
        $preloadPages = (Hp_Browser::isBlackberry() ? false : self::$_options['preloadPages']);
        $guestMode = (!isset($client) || substr($client->deviceId, 0, 6) == 'guest-');
        $isTablet = Hp_Browser::isTablet();
        $exclusiveMode = self::$_options['exclusiveMode'];
        $debugMode = self::$_options['debugMode'];
        $avoidRoundedCorners = false;
        if (isset($client)) {
            $avoidRoundedCorners =
                ($client->deviceType == ImobileDetector::DEVICE_TYPE_ANDROID && $client->deviceVersion < 3);
        }

        // Adds extra helpers to the View object
        if (isset($viewHelpers) && is_array($viewHelpers)) {
            foreach ($viewHelpers as $v) {
                $Hp_View->addHelper($v);
            }
        }

        if (self::$_options['domain'] == 'public1') {
            $appearance = self::$_options['appearance'];

            if (isset($user)) {

                // Text size from user settings
                $textSizes = array();
                $textSize = $user->getSetting(Mo_UserSetting::TEXT_SIZE);
                foreach (Mo_UserSetting::$textSizeSelectors as $k => $v) {
                    $textSizes[$k] = $v + (int) $textSize;
                }

                // Theme from user settings
                if (($userTheme = $user->getSetting(Mo_UserSetting::THEME)) !== null
                    && Mo_UsersMapper::isThemeAvailable($userTheme))
                {
                    $theme = $userTheme;
                }
            } else {
                $textSizes = Mo_UserSetting::$textSizeSelectors;
                $theme = 'default';
            }

            // Sets the icons set to use
            foreach (Mo_UserSetting::$themes as $k => $v) {
                if (in_array($theme, $v)) {
                    $themeColor = $k;
                    $Hp_View->setImagesSubdir("themes/{$k}");
                    break;
                }
            }
        }

        // Gets Panel
        $htmlPanel = '';
        if (self::$_options['panel']) {
            ob_start();
            require "{$tplDirectory}/" . self::$_options['panel'];
            $htmlPanel = ob_get_clean();
        }

        // Gets header
        $htmlHeader = '';
        if (self::$_options['header']) {
            ob_start();
            require "{$tplDirectory}/" . self::$_options['header'];
            $htmlHeader = ob_get_clean();
        }

        // Gets content
        ob_start();
        require "{$tplDirectory}/{$template}";
        $htmlContent = ob_get_clean();

        // Gets footer
        $htmlFooter = '';
        if (self::$_options['footer']) {
            ob_start();
            require "{$tplDirectory}/" . self::$_options['footer'];
            $htmlFooter = ob_get_clean();
        }

        // Gets full layout
        ob_start();
        require "{$tplDirectory}/" . self::$_options['layout'];
        $res = ob_get_clean();

        return $res;
    }

    ////
    // Setters from now on.
    ////

    public static function setPanel($name)
    {
        self::$_options['panel'] = $name;
    }

    public static function setLayout($name)
    {
        self::$_options['layout'] = $name;
    }

    public static function setHeader($name)
    {
        self::$_options['header'] = $name;
    }

    public static function setFooter($name)
    {
        self::$_options['footer'] = $name;
    }

    public static function setDomain($name)
    {
        self::$_options['domain'] = $name;
    }

    public static function setExclusiveMode($mode)
    {
        self::$_options['exclusiveMode'] = (bool) $mode;
    }

    public static function setDebugMode($mode)
    {
        self::$_options['debugMode'] = (bool) $mode;
    }
}
