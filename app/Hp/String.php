<?php

/**
 * Helper file for parsing strings.
 */

Loader::hp(array('Base'), true);

class Hp_String extends Hp_Base
{
    /**
     * Replace all rare chars in the string.
     *
     * @param $str string String to check.
     * @return string Cleaned string.
     */
    public static function convertRareChars($str)
    {
        $res = str_replace(
            array('“', '”', '‘', '’', '–', "\xc2\xa0", '', '', chr(24), chr(25), '&ndash;', '&mdash;', '&#x10;'),
            array('"', '"', "'", "'", '-', ' ', '"', '"', '"', '"', '-', '-', ' '),
            $str);

        return $res;
    }

    /**
     * Removes empty tags or with spaces in a string.
     *
     * @param $str string String to check.
     * @return string Cleaned string.
     */
    public static function removeEmptyTags($str)
    {
        while (preg_match('@<[^/>]*>([\s|&nbsp;]?)*</[^>]*>@', $str)) {
            $str = preg_replace('@<[^/>]*>([\s|&nbsp;]?)*</[^>]*>@', '', $str);
        }

        return $str;
    }

    /**
     * Removes unnecessary extra tags.
     *
     * @param $str string String to check.
     * @return string Cleaned string.
     */
    public static function removeExtraTags($str)
    {
        $res = preg_replace('@(<br(\s*/)?>(\s*)?)+@i', '<br />', $str);

        return $res;
    }


    /**
     * Removes unnecessary tags.
     *
     * @param $str string String to make the replacements.
     * @param $tags array Tags to remove.
     * @return string Cleaned string.
     */
    public static function removeUnnecessaryTags($str, array $tags)
    {
        array_walk($tags, function (&$v) { $v = "@<{$v}[^>]*?>.*?</{$v}>@si"; });
        if (!$res = preg_filter($tags, '', $str)) {

            // No matches or error, so fallback to original content
            $res = $str;
        }

        return $res;
    }
}
