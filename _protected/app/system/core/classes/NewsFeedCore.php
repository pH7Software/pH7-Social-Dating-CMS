<?php
/**
 * @title          Retrieve News Feed from a RSS URL.
 *
 * @author         Pierre-Henry Soria <hello@ph7cms.com>
 * @copyright      (c) 2013-2019, Pierre-Henry Soria. All Rights Reserved.
 * @license        GNU General Public License; See PH7.LICENSE.txt and PH7.COPYRIGHT.txt in the root directory.
 * @package        PH7 / App / System / Core / Class
 * @version        1.0
 */

namespace PH7;

use DOMDocument;
use DOMElement;
use PH7\Framework\Cache\Cache;
use PH7\Framework\Error\CException\PH7Exception;

class NewsFeedCore
{
    const DEFAULT_NUMBER_ITEMS = 10;
    const NEWS_URL = 'https://ph7cms.com/feed/';
    const CACHE_GROUP = 'str/sys/mod/admin';
    const CACHE_LIFETIME = 3600 * 24;

    /** @var DOMDocument */
    private $oXml;

    /** @var Cache */
    private $oCache;

    /** @var array */
    private $aData = [];

    public function __construct()
    {
        $this->oXml = new DOMDocument;
        $this->oCache = new Cache;
    }

    /**
     * Gets the XML links.
     *
     * @param int $iNum Number of news to get. Default: 10
     *
     * @return array The XML tree.
     *
     * @throws PH7Exception If the Feed URL is not valid.
     */
    public function getSoftware($iNum = self::DEFAULT_NUMBER_ITEMS)
    {
        $this->oCache->start(self::CACHE_GROUP, 'softwareNewsFeed' . $iNum, self::CACHE_LIFETIME);

        if (!$this->aData = $this->oCache->get()) {
            if (!@$this->oXml->load(static::NEWS_URL)) {
                throw new PH7Exception('Unable to retrieve news feeds for the URL: ' . static::NEWS_URL);
            }

            $iCount = 0;

            /** @var DOMElement $oItem */
            foreach ($this->oXml->getElementsByTagName('item') as $oItem) {
                $sLink = $oItem->getElementsByTagName('link')->item(0)->nodeValue;

                $this->aData[$sLink]['title'] = $oItem->getElementsByTagName('title')->item(0)->nodeValue;
                $this->aData[$sLink]['link'] = $sLink;
                $this->aData[$sLink]['description'] = $oItem->getElementsByTagName('description')->item(0)->nodeValue;

                if (++$iCount === $iNum) {
                    break; // If we have the number of news wanted, we stop the foreach loop
                }
            }
            $this->oCache->put($this->aData);
        }

        return $this->aData;
    }
}
