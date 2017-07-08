<?php
/**
 * @title          Retrieve News Feed from a RSS URL.
 *
 * @author         Pierre-Henry Soria <ph7software@gmail.com>
 * @copyright      (c) 2013-2017, Pierre-Henry Soria. All Rights Reserved.
 * @license        GNU General Public License; See PH7.LICENSE.txt and PH7.COPYRIGHT.txt in the root directory.
 * @package        PH7 / App / System / Core / Class
 * @version        1.0
 */

namespace PH7;

use PH7\Framework\Cache\Cache;

class NewsFeedCore
{

    const
    NEWS_URL = 'http://ph7cms.com/feed/',
    CACHE_GROUP = 'str/sys/mod/admin';

    private $_oXml, $_oCache, $_aData = array();

    public function __construct()
    {
        $this->_oXml = new \DOMDocument;
        $this->_oCache = new Cache;
    }

    /**
     * Gets the XML links.
     *
     * @param integet $iNum Number of news to get. Default: 10
     * @return array The XML tree.
     * @throws \PH7\Framework\Error\CException\PH7Exception If the Feed URL is not valid.
     */
    public function getSoftware($iNum = 10)
    {
        $this->_oCache->start(self::CACHE_GROUP, 'software_feed_news' . $iNum, 3600 * 24);

        if (!$this->_aData = $this->_oCache->get()) {
            if (!@$this->_oXml->load(static::NEWS_URL))
                throw new Framework\Error\CException\PH7Exception('Unable to retrieve news feeds for the URL: "' . static::NEWS_URL . '"');

            $iCount = 0;
            foreach ($this->_oXml->getElementsByTagName('item') as $oItem) {
                $sLink = $oItem->getElementsByTagName('link')->item(0)->nodeValue;

                $this->_aData[$sLink]['title'] = $oItem->getElementsByTagName('title')->item(0)->nodeValue;
                $this->_aData[$sLink]['link'] = $sLink;
                $this->_aData[$sLink]['description'] = $oItem->getElementsByTagName('description')->item(0)->nodeValue;

                if (++$iCount == $iNum) break; // If we have the number of news we want, we stop the foreach loop.
            }

            $this->_oCache->put($this->_aData);
        }

        return $this->_aData;
    }

    public function __destruct()
    {
        unset($this->_oXml, $this->_oCache, $this->_aData);
    }

}
