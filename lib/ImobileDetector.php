<?php

/**
 * This library contains functions to retrieve data from the HTTP headers
 * and determine if it's a mobile device and the mobile type.
 *
 * ---
 * Example of use:
 *
 * require 'ImobileDetector.php';
 * $iMobileDetector = new ImobileDetector();
 * echo 'Browser is mobile: ' . $iMobileDetector->isMobile();
 *
 * ---
 * Based on data / projects from:
 *
 * https://github.com/serbanghita/Mobile-Detect
 * http://detectmobilebrowsers.com/
 * http://www.zytrax.com/tech/web/mobile_ids.html
 *
 *
 * MIT License
 * ===========
 *
 * Permission is hereby granted, free of charge, to any person obtaining
 * a copy of this software and associated documentation files (the
 * "Software"), to deal in the Software without restriction, including
 * without limitation the rights to use, copy, modify, merge, publish,
 * distribute, sublicense, and/or sell copies of the Software, and to
 * permit persons to whom the Software is furnished to do so, subject to
 * the following conditions:
 *
 * The above copyright notice and this permission notice shall be included
 * in all copies or substantial portions of the Software.
 *
 * THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS
 * OR IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF
 * MERCHANTABILITY, FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT.
 * IN NO EVENT SHALL THE AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY
 * CLAIM, DAMAGES OR OTHER LIABILITY, WHETHER IN AN ACTION OF CONTRACT,
 * TORT OR OTHERWISE, ARISING FROM, OUT OF OR IN CONNECTION WITH THE
 * SOFTWARE OR THE USE OR OTHER DEALINGS IN THE SOFTWARE.
 *
 * ---
 * @author Luciano Fantuzzi (luciano.fantuzzi@gmail.com)
 * @license http://www.opensource.org/licenses/mit-license.php  MIT License
 * @link https://github.com/shamank/iMobileDetector
 * @version 1.00.0000
 */

class ImobileDetector
{
    /**
     * @const Device types.
     */
    const DEVICE_TYPE_IPAD = 'ia';
    const DEVICE_TYPE_IPHONE = 'ih';
    const DEVICE_TYPE_IPOD = 'io';
    const DEVICE_TYPE_ANDROID = 'aa';
    const DEVICE_TYPE_ANDROID_TABLET = 'at';
    const DEVICE_TYPE_BLACKBERRY = 'bb';
    const DEVICE_TYPE_PLAYBOOK = 'pb';
    const DEVICE_TYPE_GENERIC = 'gn';

    /**
     * @var string HTTP user agent.
     */
    protected $httpUserAgent;

    /**
     * @var string HTTP http headers.
     */
    protected $httpHeaders;

    /**
     * Builds a new object.
     *
     * @param $httpUserAgent string HTTP user agent.
     * @param $httpHeaders array HTTP headers. They can be found as $_SERVER['HTTP_*'].
     * @return void.
     */
    public function __construct($httpUserAgent = null, array $httpHeaders = null)
    {
        if (isset($_SERVER['HTTP_USER_AGENT'])) {
            $this->setHttpUserAgent($_SERVER['HTTP_USER_AGENT']);
        }
        if ($httpUserAgent) {
            $this->setHttpUserAgent($httpUserAgent);
        }
        if (isset($_SERVER)) {
            $this->setHttpHeaders($_SERVER);
        }
        if ($httpHeaders) {
            $this->setHttpHeaders($httpHeaders);
        }
    }

    /**
     * Tells whether the browser is mobile or not.
     *
     * @return boolean TRUE if mobile; FALSE otherwise.
     */
    public function isMobile()
    {
        $rule1 = '/(bb\d+|meego).+mobile|android|avantgo|bada\/|blackberry|blazer|compal|elaine|fennec|hiptop|iemobile'
            . '|ip(hone|od|ad)|iris|kindle|lge |maemo|midp|mmp|netfront|opera m(ob|in)i|palm( os)?|phone|p(ixi|re)\/'
            . '|plucker|pocket|psp|series(4|6)0|symbian|treo|up\.(browser|link)|vodafone|wap|windows (ce|phone)'
            . '|xda|xiino|tablet|playbook|mobile/s';
        $rule2 = '/1207|6310|6590|3gso|4thp|50[1-6]i|770s|802s|a wa|abac|ac(er|oo|s\-)|ai(ko|rn)|al(av|ca|co)|amoi|an'
            . '(ex|ny|yw)|aptu|ar(ch|go)|as(te|us)|attw|au(di|\-m|r |s )|avan|be(ck|ll|nq)|bi(lb|rd)|bl(ac|az)|br(e|v)'
            . 'w|bumb|bw\-(n|u)|c55\/|capi|ccwa|cdm\-|cell|chtm|cldc|cmd\-|co(mp|nd)|craw|da(it|ll|ng)|dbte|dc\-s|devi'
            . '|dica|dmob|do(c|p)o|ds(12|\-d)|el(49|ai)|em(l2|ul)|er(ic|k0)|esl8|ez([4-7]0|os|wa|ze)|fetc|fly(\-|_)|g1'
            . 'u|g560|gene|gf\-5|g\-mo|go(\.w|od)|gr(ad|un)|haie|hcit|hd\-(m|p|t)|hei\-|hi(pt|ta)|hp( i|ip)|hs\-c|ht(c'
            . '(\-| |_|a|g|p|s|t)|tp)|hu(aw|tc)|i\-(20|go|ma)|i230|iac( |\-|\/)|ibro|idea|ig01|ikom|im1k|inno|ipaq'
            . '|iris|ja(t|v)a|jbro|jemu|jigs|kddi|keji|kgt( |\/)|klon|kpt |kwc\-|kyo(c|k)|le(no|xi)|lg( g|\/(k|l|u)|50'
            . '|54|\-[a-w])|libw|lynx|m1\-w|m3ga|m50\/|ma(te|ui|xo)|mc(01|21|ca)|m\-cr|me(rc|ri)|mi(o8|oa|ts)|mmef|mo'
            . '(01|02|bi|de|do|t(\-| |o|v)|zz)|mt(50|p1|v )|mwbp|mywa|n10[0-2]|n20[2-3]|n30(0|2)|n50(0|2|5)|n7(0(0|1)'
            . '|10)|ne((c|m)\-|on|tf|wf|wg|wt)|nok(6|i)|nzph|o2im|op(ti|wv)|oran|owg1|p800|pan(a|d|t)|pdxg|pg(13|\-'
            . '([1-8]|c))|phil|pire|pl(ay|uc)|pn\-2|po(ck|rt|se)|prox|psio|pt\-g|qa\-a|qc(07|12|21|32|60|\-[2-7]|i\-)|'
            . 'qtek|r380|r600|raks|rim9|ro(ve|zo)|s55\/|sa(ge|ma|mm|ms|ny|va)|sc(01|h\-|oo|p\-)|sdk\/|se(c(\-|0|1)|47|'
            . 'mc|nd|ri)|sgh\-|shar|sie(\-|m)|sk\-0|sl(45|id)|sm(al|ar|b3|it|t5)|so(ft|ny)|sp(01|h\-|v\-|v )|sy(01|mb)'
            . '|t2(18|50)|t6(00|10|18)|ta(gt|lk)|tcl\-|tdg\-|tel(i|m)|tim\-|t\-mo|to(pl|sh)|ts(70|m\-|m3|m5)|tx\-9|up'
            . '(\.b|g1|si)|utst|v400|v750|veri|vi(rg|te)|vk(40|5[0-3]|\-v)|vm40|voda|vulc|vx(52|53|60|61|70|80|81|83'
            . '|85|98)|w3c(\-| )|webc|whit|wi(g |nc|nw)|wmlb|wonu|x700|yas\-|your|zeto|zte\-/';

        // Basic and fast guessing by checking for HTTP headers
        $res = ($this->fetchHttpHeaders('HTTP_ACCEPT') !== null &&
                (strpos($this->fetchHttpHeaders('HTTP_ACCEPT'), 'application/x-obml2d') !== false
                || strpos($this->fetchHttpHeaders('HTTP_ACCEPT'), 'application/vnd.rim.html') !== false
                || strpos($this->fetchHttpHeaders('HTTP_ACCEPT'), 'text/vnd.wap.wml') !== false
                || strpos($this->fetchHttpHeaders('HTTP_ACCEPT'), 'application/vnd.wap.xhtml+xml') !== false)
            || $this->fetchHttpHeaders('HTTP_X_WAP_PROFILE') !== null
            || $this->fetchHttpHeaders('HTTP_X_WAP_CLIENTID') !== null
            || $this->fetchHttpHeaders('HTTP_WAP_CONNECTION') !== null
            || $this->fetchHttpHeaders('HTTP_PROFILE') !== null
            || $this->fetchHttpHeaders('HTTP_X_OPERAMINI_PHONE_UA') !== null
            || $this->fetchHttpHeaders('HTTP_X_NOKIA_IPADDRESS') !== null
            || $this->fetchHttpHeaders('HTTP_X_NOKIA_GATEWAY_ID') !== null
            || $this->fetchHttpHeaders('HTTP_X_ORANGE_ID') !== null
            || $this->fetchHttpHeaders('HTTP_X_VODAFONE_3GPDPCONTEXT') !== null
            || $this->fetchHttpHeaders('HTTP_X_HUAWEI_USERID') !== null
            || $this->fetchHttpHeaders('HTTP_UA_OS') !== null
            || $this->fetchHttpHeaders('HTTP_X_MOBILE_GATEWAY') !== null
            || $this->fetchHttpHeaders('HTTP_X_ATT_DEVICEID') !== null
            || $this->fetchHttpHeaders('HTTP_UA_CPU') == 'ARM');

        // Try to guess evaluating the user agent header
        if (!$res) {
            $httpUserAgent = $this->getHttpUserAgent();
            $res = (preg_match($rule1, $httpUserAgent)
                || preg_match($rule2, substr($httpUserAgent, 0, 4)));
        }

        return $res;
    }

    /**
     * Checks if the device is tablet.
     *
     * @return boolean TRUE if tablet; FALSE otherwise.
     */
    public function isTablet()
    {
        $tabletDevices = array(
            'generic' => 'tablet(?!.*pc)|viewpad7|lg-v909|mid7015|bntv250a|logicpd zoom2|\ba7eb\b|catnova8|a1_07'
                . '|ct704|ct1002|\bm721\b|hp-tablet',
            'ipad' => 'ipad',
            'samsung' => 'samsung.*tablet|galaxy.*tab|gt-p(\d+){4}|s[c|g|p]h-[i|p|t](\d+){3}',
            'android' => '^.*android((?!mobile).)*$',
            'motorola' => 'xoom|sholest|mz[5|6](\d+){2}',
            'sony' => 'sony tablet',
            'htc' => 'htc flyer|htc jetstream|htc-p715a|htc evo view 4g|pg41200',
            'kindle' => 'kindle|silk.*accelerated',
            'asus' => 'transformer|tf101',
            'rim' => 'playbook|rim tablet',
            'arnova' => 'an10g2|an7bg3|an7fg3|an8g3|an8cg3|an7g3|an9g3|an7dg3|an7dg3st|an7dg3childpad|an10bg3|'
                . 'an10bg3dt',
            'ainol' => 'novo7'
        );

        $res = false;
        foreach ($tabletDevices as $v) {
            $v = strtolower($v);
            if ($res = (bool) preg_match("/{$v}/s", $this->httpUserAgent)) {
                break;
            }
        }

        return $res;
    }

    /**
     * Gets the type of the device.
     * Check "self::DEVICE_TYPE_*" for the returning values types.
     *
     * @return string Device type.
     */
    public function getDeviceType()
    {
        $res = self::DEVICE_TYPE_GENERIC;
        $httpUserAgent = $this->getHttpUserAgent();
        $isTablet = $this->isTablet();

        // Blackberry / Playbook
        if (preg_match('/blackberry|maui wap browser|playbook|bb10/s', $httpUserAgent)) {
            $res = ($this->isTablet() ? self::DEVICE_TYPE_PLAYBOOK : self::DEVICE_TYPE_BLACKBERRY);
        }

        // Android (Smartphone / Tablet)
        else if (strpos($httpUserAgent, 'android') !== false) {
            $res = (strpos($httpUserAgent, 'mobile') !== false ?
                self::DEVICE_TYPE_ANDROID : self::DEVICE_TYPE_ANDROID_TABLET);
        }

        // iPad
        else if (strpos($httpUserAgent, 'ipad')) {
             $res = self::DEVICE_TYPE_IPAD;
        }

        // iPod
        else if (strpos($httpUserAgent, 'ipod')) {
             $res = self::DEVICE_TYPE_IPOD;
        }

        // iPhone
        else if (strpos($httpUserAgent, 'iphone')) {
             $res = self::DEVICE_TYPE_IPHONE;
        }

        return $res;
    }

    ////
    // Setters and Getters from now on.
    ////

    /**
     * Sets the HTTP user agent.
     *
     * @param $httpUserAgent string HTTP user agent.
     * @return self.
     */
    public function setHttpUserAgent($httpUserAgent)
    {
        $this->httpUserAgent = strtolower($httpUserAgent);

        return $this;
    }

    /**
     * Sets the HTTP headers.
     *
     * @param $httpHeaders array HTTP headers.
     * @return self.
     */
    public function setHttpHeaders(array $httpHeaders)
    {
        $this->httpHeaders = array();
        foreach ($httpHeaders as $k => $v) {
            if (substr($k, 0, 5) == 'HTTP_') {
                $this->httpHeaders[$k] = "{$v}";
            }
        }

        return $this;
    }

    /**
     * Gets the HTTP user agent.
     *
     * @return string.
     */
    public function getHttpUserAgent()
    {
        return $this->httpUserAgent;
    }

    /**
     * Fetches the HTTP headers.
     *
     * @param $header string Header name. If null, will fetch for all headers.
     * @return mixed The header value (if asking for a specific header); All headers if $header = null; NULL otherwise.
     */
    public function fetchHttpHeaders($header = null)
    {
        $res = $this->httpHeaders;
        if ($header) {
            $res = (isset($res[$header]) ? $res[$header] : null);
        }

        return $res;
    }
}
