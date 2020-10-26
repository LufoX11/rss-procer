<?php

/**
 * News mapper.
 */

Loader::mo(array('Abstract', 'Channel', 'Sorting', 'News'), true);
Loader::da(array('News'), true);

class Mo_NewsMapperException extends Exception {}

class Mo_NewsMapper extends Mo_Abstract
{
    /**
     * @const MC keys.
     */
    const MC_KEY_CHANNELS = 'Mo_NewsMapper::%s::channels::1';
    const MC_KEY_SINGLE_NEWS = 'Mo_NewsMapper::%s::news::checksum-%s::1';
    const MC_KEY_ALL_NEWS = 'Mo_NewsMapper::%s::news::channels_id-%s::1';

    /**
     * @var array A collection of News object.
     */
    protected static $_news = array();

    /**
     * Adds / Updates a channel.
     *
     * @param $Mo_Channel object A Mo_Channel object.
     * @param $serviceName string Service name.
     * @return boolean TRUE on success; FALSE otherwise.
     */
    public static function insertChannel(Mo_Channel $Mo_Channel, $serviceName)
    {
        $res = (bool) Da_Handler_Mysql::insert(
            'channels',
            array(
                'title' => $Mo_Channel->title,
                'description' => $Mo_Channel->description,
                'image' => $Mo_Channel->image
            ),
            array('update' => array('title', 'description', 'image'))
        );
        Da_Handler_Memcached::delete(sprintf(self::MC_KEY_CHANNELS, $serviceName));

        return $res;
    }

    /**
     * Fetchs all available channels.
     *
     * @param $serviceName string Service name.
     * @return mixed An ARRAY of Mo_Channel objects on success; FALSE otherwise.
     */
    public static function fetchChannels($serviceName)
    {
        // From Memcached
        $mcKey = sprintf(self::MC_KEY_CHANNELS, $serviceName);
        if (!($res = Da_Handler_Memcached::fetch($mcKey))) {

            // From Database
            if ($raw = Da_Handler_Mysql::fetchAll('channels')) {
                $res = array();
                foreach ($raw as $v) {
                    $res[$v['id']] = new Mo_Channel(array(
                        'id' => $v['id'],
                        'title' => $v['title'],
                        'description' => $v['description'],
                        'image' => $v['image']
                    ));
                }
                Da_Handler_Memcached::store($mcKey, $res);
            }
        }

        return $res;
    }

    /**
     * Finds a channel by searching by its title.
     *
     * @param $title string Channel title.
     * @param $serviceName string Service name.
     * @return mixed A Mo_Channel object on success; FALSE otherwise.
     */
    public static function findChannelByTitle($title, $serviceName)
    {
        $res = false;
        $channels = self::fetchChannels($serviceName);
        foreach ($channels as $v) {
            if ($v->title == $title) {
                $res = $v;
            }
        }

        return $res;
    }

    /**
     * Adds / Updates a news.
     *
     * @param $Mo_News object A Mo_News object.
     * @param $serviceName string Service name.
     * @return boolean TRUE on success; FALSE otherwise.
     */
    public static function insertNews(Mo_News $Mo_News, $serviceName)
    {
        $res = (bool) Da_Handler_Mysql::insert(
            'news',
            array(
                'channels_id' => $Mo_News->channels_id,
                'title' => $Mo_News->title,
                'summary' => $Mo_News->summary,
                'description' => $Mo_News->description,
                'datetime' => $Mo_News->datetime,
                'link' => $Mo_News->link,
                'shortlink' => $Mo_News->shortlink,
                'image' => $Mo_News->image,
                'checksum' => $Mo_News->checksum
            ),
            array('update' => array('summary', 'description', 'datetime', 'link', 'shortlink', 'image'))
        );
        $mcKeySingleNews = sprintf(self::MC_KEY_SINGLE_NEWS, $serviceName, $Mo_News->checksum);
        $mcKeyAllNews = sprintf(self::MC_KEY_ALL_NEWS, $serviceName, $Mo_News->channels_id);
        Da_Handler_Memcached::delete($mcKeySingleNews);
        Da_Handler_Memcached::delete($mcKeyAllNews);

        return $res;
    }

    /**
     * Fetchs a news by its checksum.
     *
     * @param $checksum string News checksum
     * @param $serviceName string Service name.
     * @return mixed News object on success; FALSE otherwise.
     */
    public static function findNewsByChecksum($checksum, $serviceName)
    {
        $mcKey = sprintf(self::MC_KEY_SINGLE_NEWS, $serviceName, $checksum);

        // From Local cache
        if (!($res = self::_findNewsByChecksumFromLocalCache($checksum))) {

            // From Memcached
            if (!($res = Da_Handler_Memcached::fetch($mcKey))) {

                // From Database
                if ($res = Da_Handler_Mysql::fetchRow('news', array('checksum' => $checksum))) {
                    $res = new Mo_News(array(
                        'id' => $res['id'],
                        'channels_id' => $res['channels_id'],
                        'title' => $res['title'],
                        'summary' => $res['summary'],
                        'description' => $res['description'],
                        'datetime' => $res['datetime'],
                        'link' => $res['link'],
                        'shortlink' => $res['shortlink'],
                        'image' => $res['image'],
                        'checksum' => $res['checksum']
                    ));
                    self::$_news[$checksum] = $res;
                    Da_Handler_Memcached::store($mcKey, $res);
                }
            } else {
                self::$_news[$checksum] = $res;
            }
        }

        return $res;
    }

    /**
     * Fetchs all (last saved) news from the channel.
     *
     * @param $channelId integer Channel ID to retrieve data from.
     * @param $serviceName string Service name.
     * @return mixed An ARRAY of news objects on success; FALSE otherwise.
     */
    public static function fetchNews($channelId, $serviceName)
    {
        // From Memcached
        $mcKey = sprintf(self::MC_KEY_ALL_NEWS, $serviceName, $channelId);
        if (!($res = Da_Handler_Memcached::fetch($mcKey))) {

            // From Database
            if (($sorting = self::fetchSorting())
                && isset($sorting[$channelId])
                && $raw = Da_News::fetchByChecksum($sorting[$channelId]->value))
            {
                $res = array();
                foreach ($raw as $v) {
                    $res[$v['checksum']] = new Mo_News(array(
                        'id' => $v['id'],
                        'channels_id' => $v['channels_id'],
                        'title' => $v['title'],
                        'summary' => $v['summary'],
                        'description' => $v['description'],
                        'datetime' => $v['datetime'],
                        'link' => $v['link'],
                        'shortlink' => $v['shortlink'],
                        'image' => $v['image'],
                        'checksum' => $v['checksum']
                    ));
                }

                // Sort news by checksum
                $res = array_merge(array_flip($sorting[$channelId]->value), $res);

                Da_Handler_Memcached::store($mcKey, $res);
            }
        }

        return $res;
    }

    /**
     * Adds / Updates news sorting data.
     *
     * @param $Mo_Sorting object A Mo_Sorting object.
     * @return boolean TRUE on success; FALSE otherwise.
     */
    public static function insertSorting(Mo_Sorting $Mo_Sorting)
    {
        $res = (bool) Da_Handler_Mysql::insert(
            'sorting',
            array(
                'channels_id' => $Mo_Sorting->channels_id,
                'value' => serialize($Mo_Sorting->value)
            ),
            array('update' => array('value'))
        );

        return $res;
    }

    /**
     * Fetchs sorting data.
     *
     * @return mixed An ARRAY of Mo_Sorting objects on success; FALSE otherwise.
     */
    public static function fetchSorting()
    {
        // From Database
        $res = false;
        if ($raw = Da_Handler_Mysql::fetchAll('sorting')) {
            $res = array();
            foreach ($raw as $v) {
                $res[$v['channels_id']] = new Mo_Sorting(array(
                    'channels_id' => $v['channels_id'],
                    'value' => unserialize($v['value'])
                ));
            }
        }

        return $res;
    }

    /**
     * Fetchs a News from local cache.
     *
     * @param $checksum string News checksum.
     * @return mixed News object on success; FALSE otherwise.
     */
    protected static function _findNewsByChecksumFromLocalCache($checksum)
    {
        $res = false;
        if (isset(self::$_news[$checksum])) {
            $res = self::$_news[$checksum];
        }

        return $res;
    }
}
