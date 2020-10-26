<?php

/**
 * Global utilities.
 */

Loader::vendor(array('Encoding/Encoding'), true);
Loader::hp(array('String'), true);

abstract class Utils
{
    /**
     * Escapes a string to correctly displaying it.
     *
     * @param $str string String to escape.
     * @param $stripTags boolean If we should remove all HTML tags.
     * @param $nl2br boolean If we should convert all line breaks into <br /> tags.
     * @param $htmlentities boolean If we should perform an htmlentities() on the string.
     * @return string The escaped string.
     */
    public static function escape($str, $stripTags = false, $nl2br = true, $htmlentities = true)
    {
        if (!mb_detect_encoding($str, 'UTF-8', true)) {
            $str = utf8_encode($str);
        }
        if ($stripTags) {
            $str = strip_tags($str);
            $str = str_replace(array("\n\r", "\n", "\r"), '', $str);
        } else {
            $str = Hp_String::removeExtraTags(Hp_String::removeEmptyTags($str));
        }
        if ($htmlentities) {

            // Fix already escaped chars to avoid escaping twice
            if (preg_match("/(&[a-zA-Z]{2,7};)|(&#[0-9]{1,4};)/", $str, $matches)) {
                foreach ($matches as $v) {
                    $str = str_replace($v, html_entity_decode($v, ENT_QUOTES, 'UTF-8'), $str);
                }
            }

            $str = htmlentities($str, ENT_QUOTES, 'UTF-8');
        }
        if ($nl2br) {
            $str = nl2br($str);
        }

        return $str;
    }

    /**
     * Sets and Gets execution time lapses.
     *
     * @param $reset boolean On FALSE, timer will start; On TRUE, timer will return the lapse and resets.
     * @return float Time lapse.
     */
    public static function time($reset = false)
    {
        static $start;

        $res = 0;
        if ($reset) {
            $res = microtime(true) - $start;
        }
        $start = microtime(true);

        return $res;
    }

    /**
     * Writes data to a file on disk.
     *
     * @param $path string File path and name.
     * @param $stream string File content.
     * @return boolean TRUE on success; FALSE otherwise.
     */
    public static function fileWrite($path, $stream = null, $mode = 'a')
    {
        $res = false;
        if ($file = @fopen($path, $mode)) {
            $res = (fwrite($file, $stream) !== false);
            fclose($file);
        } else {
            error_log("Permission denied to write in file: '{$path}'");
        }

        return $res;
    }

    /**
     * Sanitizes a string.
     *
     * @param $str string String to sanitize.
     * @param $options array Array with options:
     *                       "translations": array A key-value array with words/phrases translations.
     *                       "stripTags": string (NONE|MOST|ALL) How many tags we want remove.
     *                           "ALL" will remove all except <br />.
     *                           "MOST" will remove all except the allowed list.
     *                       "clean": boolean If we must clean tags attributes and spaces.
     *                       "decodeEntities": boolean If we should decode HTML entities.
     *                       "fixEncoding": If we should try to fix encoding issues.
     * @return string The sanitized string.
     */
    public static function sanitizeString($str, array $options = array())
    {
        // Default options
        $options = array_merge(
            array(
                'translations' => array(),
                'stripTags' => 'ALL',
                'clean' => true,
                'fixEncoding' => true,
                'decodeEntities' => true
            ),
            $options);

        $res = trim($str);
        $res = Hp_String::convertRareChars($res);

        // Fix wrong encoding
        if ($options['fixEncoding']) {
            if (!mb_detect_encoding($res, 'UTF-8', true)) {
                $res = utf8_encode($res);
            }
            $res = str_replace('?', '[[QUEST_SIGN]]', $res);
            $res = str_replace('?', '', ForceUTF8\Encoding::fixUTF8($res)); // Drop unknown chars (marked with '?')
            $res = str_replace('[[QUEST_SIGN]]', '?', $res);
        }

        // Translations
        $res = strtr($res, $options['translations']);

        // Decode html-entitied text (if encoded)
        if ($options['decodeEntities']) {
            $maxLooping = 100;
            while (preg_match("/(&[a-zA-Z]{2,7};)|(&#[0-9]{1,4};)/", $res)) {
                $res = html_entity_decode($res, ENT_QUOTES, 'UTF-8');
                if (-- $maxLooping == 0) {
                    break; // Avoid infinite looping
                }
            }
        }

        // Strip tags
        if ($options['stripTags'] == 'ALL') {
            $res = strip_tags($res, '<br>');
        } else if ($options['stripTags'] == 'MOST') {
            $res = Hp_String::removeExtraTags(Hp_String::removeEmptyTags(
                strip_tags($res, '<br><ul><li><p><h1><h2><h3><strong><em><table><tr><td><th><b><i>')));
            $res = str_ireplace(
                array('<h1>', '</h1>', '<h2>', '</h2>', '<b>', '</b>', '<i>', '</i>'),
                array('<h3>', '</h3>', '<h3>', '</h3>', '<strong>', '</strong>', '<em>', '</em>'),
                $res);
        }

        // Remove unnecessary data
        if ($options['clean']) {
            $res = preg_replace('#<([a-z][a-z0-9]*)[^>]*?(/?)>#i','<$1$2>', $res); // remove tag attributes
            $res = preg_replace('/\s+/', ' ', $res); // remove extra spaces
        }

        // Fix unencoded "&" char for parser
        $res = str_replace(array('&amp;', '&'), array('&', '&amp;'), $res);

        return $res;
    }

    /**
     * Gets a value from the passed string following the config convention for special values.
     * Ie.: ":key1=value1:key2=value2".
     * If there's an EQUAL "=" sign in the values, it must be replaced with "%EQUAL%". The function
     * will return the proper "=" value instead of %EQUAL%.
     *
     * @param $str string The config string.
     * @param $key string The key we want retrieve from.
     * @return mixed The obtained value from the string if found; FALSE otherwise.
     */
    public static function getIniOption($str, $key)
    {
        preg_match("/:{$key}=([A-Za-z0-9\._\- <>\%@\"\*\(\[^\]\)\/\|\+]*):?/", $str, $matches);
        $res = (isset($matches[1]) ? $matches[1] : false);

        // Replace the EQUAL sign
        $res = str_ireplace('%EQUAL%', '=', $res);

        // Replace ":"
        $res = str_ireplace('%COLON%', ':', $res);

        return $res;
    }

    /**
     * Removes key-value pars from ini values allowing to retrieve real data.
     *
     * @param $str string Config value to clean.
     * @return mixed The real value from string; FALSE otherwise.
     */
    public static function getIniValue($str)
    {
        $res = $str;
        if (strpos($str, ':') !== false
            && mb_substr($str, 0, 2) != 'f:'
            && mb_substr($str, 0, 3) != '/f:'
            && mb_substr($str, 0, 11) != 'feedburner:')
        {
            $res = mb_substr($str, 0, strpos($str, ':'));
        }

        return $res;
    }
}
