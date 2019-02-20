<?php
/**
 * @title            Metacafe Class
 *
 * @author           Pierre-Henry Soria <hello@ph7cms.com>
 * @copyright        (c) 2012-2019, Pierre-Henry Soria. All Rights Reserved.
 * @license          GNU General Public License; See PH7.LICENSE.txt and PH7.COPYRIGHT.txt in the root directory.
 * @package          PH7 / Framework / Video / Api
 * @link             http://ph7cms.com
 */

namespace PH7\Framework\Video\Api;

use DOMDocument;
use DOMXPath;

defined('PH7') or exit('Restricted access');

class Metacafe extends Api implements IApi
{
    const API_URL = 'http://www.metacafe.com/api/item/';
    const PLAYER_URL = 'http://metacafe.com/fplayer/';
    const REGEX_URI_FORMAT = '#/(?:watch|fplayer)/([\d]+)/(?:[\w-]+)/\w*#i';

    /**
     * @param string $sUrl
     *
     * @return string|bool Returns the embed video URL if found, FALSE otherwise.
     */
    public function getVideo($sUrl)
    {
        return $this->getEmbedUrl($sUrl);
    }

    /**
     * @param string $sUrl
     *
     * @return Metacafe|bool
     */
    public function getInfo($sUrl)
    {
        $oDom = new DOMDocument;
        if (!@$oDom->load(static::API_URL . $this->getVideoId($sUrl))) {
            return false;
        }

        $this->oData = new DOMXPath($oDom);
        $sRootNameSpace = $oDom->lookupNamespaceUri($oDom->namespaceURI);
        $this->oData->registerNamespace('media', $sRootNameSpace);

        return $this;
    }

    /**
     * We redefine this method to the specific needs of the Metacafe API.
     *
     * @see Api::getInfo();
     *
     * @return string|bool The title with escape function if found otherwise returns false.
     */
    public function getTitle()
    {
        $oElements = $this->oData->query('//media:title');

        foreach ($oElements as $oElement) {
            $sTitle = $oElement->nodeValue;
        }

        return !empty($sTitle) ? $this->oStr->escape($sTitle, true) : false;
    }

    /**
     * We redefine this method to the specific needs of the Metacafe API.
     *
     * @see Api::getInfo();
     *
     * @return string|bool The description with escape function if found otherwise returns false.
     */
    public function getDescription()
    {
        $oElements = $this->oData->query('//media:description');

        foreach ($oElements as $oElement) {
            $sDescription = $oElement->nodeValue;
        }

        return !empty($sDescription) ? $this->oStr->escape($sDescription, true) : false;
    }

    /**
     * We redefine this method to the specific needs of the Metacafe API.
     *
     * @see Metacafe::getInfo();
     *
     * @return int|bool The video duration if found, FALSE otherwise.
     */
    public function getDuration()
    {
        $oElements = $this->oData->query('//media:content');

        foreach ($oElements as $oElement) {
            $iDuration = $oElement->getAttribute('duration');
        }

        return !empty($iDuration) ? (int)$iDuration : false;
    }

    /**
     * @param string $sUrl
     * @param string $sMedia
     * @param int $iWidth
     * @param int $iHeight
     *
     * @return string
     */
    public function getMeta($sUrl, $sMedia, $iWidth, $iHeight)
    {
        $sIdVideo = $this->getVideoId($sUrl);
        $sVideoUrl = $this->getEmbedUrl($sUrl);

        if ($sMedia === 'preview') {
            return 'http://s' . mt_rand(1, 4) . '.mcstatic.com/thumb/' . $sIdVideo . '.jpg';
        }

        $sParam = $this->bAutoplay ? 'autoPlay=yes' : 'autoPlay=no';

        return '<embed flashVars="playerVars=showStats=no|' . $sParam . '|" src="' . $sVideoUrl . '" width="' . $iWidth . '" height="' . $iHeight . '" wmode="transparent" allowFullScreen="true" allowScriptAccess="always" name="Metacafe_' . $sIdVideo . '" pluginspage="http://www.macromedia.com/go/getflashplayer" type="application/x-shockwave-flash"></embed>';
    }

    /**
     * @param string $sUrl
     *
     * @return int|bool Returns the ID of the video if it was found, FALSE otherwise.
     */
    public function getVideoId($sUrl)
    {
        preg_match(static::REGEX_URI_FORMAT, $sUrl, $aMatch);

        return !empty($aMatch[1]) ? $aMatch[1] : false;
    }

    /**
     *  If the video ID is valid, returns the embed URL, FALSE otherwise.
     *
     * @param string $sUrl
     *
     * @return bool|string
     */
    public function getEmbedUrl($sUrl)
    {
        if (!$this->getVideoId($sUrl)) {
            return false;
        }

        return static::PLAYER_URL . $this->getVideoId($sUrl) . '/metacefe.swf';
    }
}
