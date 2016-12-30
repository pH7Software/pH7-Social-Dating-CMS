<?php
/**
 * @title            Metacafe Class
 *
 * @author           Pierre-Henry Soria <ph7software@gmail.com>
 * @copyright        (c) 2012-2017, Pierre-Henry Soria. All Rights Reserved.
 * @license          GNU General Public License; See PH7.LICENSE.txt and PH7.COPYRIGHT.txt in the root directory.
 * @package          PH7 / Framework / Video / Api
 * @version          1.1
 * @link             http://hizup.com
 */

namespace PH7\Framework\Video\Api;
defined('PH7') or exit('Restricted access');

class Metacafe extends Api implements IApi
{

    const
    API_URL = 'http://www.metacafe.com/api/item/',
    PLAYER_URL = 'http://metacafe.com/fplayer/';

    /**
     * @param string $sUrl
     * @return mixed (string | boolean) Returns the embed video URL if found, FALSE otherwise.
     */
    public function getVideo($sUrl)
    {
        return $this->getEmbedUrl($sUrl);
    }

    public function getInfo($sUrl)
    {
        $oDom = new \DOMDocument;
        if (!@$oDom->load(static::API_URL . $this->getVideoId($sUrl))) return false;

        $this->oData = new \DOMXPath($oDom);
        $sRootNameSpace = $oDom->lookupNamespaceUri($oDom->namespaceURI);
        $this->oData->registerNamespace('media', $sRootNameSpace);

        return $this;
    }

    /**
     * We redefine this method to the specific needs of the Metacafe API.
     *
     * @see \PH7\Framework\Video\Api::getInfo();
     * @return mixed (string | boolean) The title with escape function if found otherwise returns false.
     */
    public function getTitle()
    {
        $oElements = $this->oData->query('//media:title');
        foreach ($oElements as $oElement) $sTitle = $oElement->nodeValue;
        return (!empty($sTitle) ? $this->oStr->escape($sTitle, true) : false);
    }

    /**
     * We redefine this method to the specific needs of the Metacafe API.
     *
     * @see \PH7\Framework\Video\Api::getInfo();
     * @return mixed (string | boolean) The description with escape function if found otherwise returns false.
     */
    public function getDescription()
    {
        $oElements = $this->oData->query('//media:description');
        foreach ($oElements as $oElement) $sDescription = $oElement->nodeValue;
        return (!empty($sDescription) ? $this->oStr->escape($sDescription, true) : false);
    }

    /**
     * We redefine this method to the specific needs of the Metacafe API.
     *
     * @see \PH7\Framework\Video\Api\Metacafe::getInfo();
     * @return mixed (integer | boolean) The video duration if found, FALSE otherwise.
     */
    public function getDuration()
    {
        $oElements = $this->oData->query('//media:content');
        foreach ($oElements as $oElement) $iDuration = $oElement->getAttribute('duration');
        return (!empty($iDuration) ? (int)$iDuration : false);
    }

    public function getMeta($sUrl, $sMedia, $iWidth, $iHeight)
    {
        $sIdVideo = $this->getVideoId($sUrl);
        $sVideoUrl = $this->getEmbedUrl($sUrl);

        if ($sMedia == 'preview')
        {
            return 'http://s' . mt_rand(1,4) . '.mcstatic.com/thumb/' . $sIdVideo . '.jpg';
        }
        else
        {
            $sParam = ($this->bAutoplay) ? 'autoPlay=yes' : 'autoPlay=no';
            return '<embed flashVars="playerVars=showStats=no|' . $sParam . '|" src="' . $sVideoUrl . '" width="' . $iWidth . '" height="' . $iHeight . '" wmode="transparent" allowFullScreen="true" allowScriptAccess="always" name="Metacafe_'. $sIdVideo . '" pluginspage="http://www.macromedia.com/go/getflashplayer" type="application/x-shockwave-flash"></embed>';
        }
    }

    /**
     * @param string $sUrl
     * @return mixed (integer | boolean) Returns the ID of the video if it was found, FALSE otherwise.
     */
    public function getVideoId($sUrl)
    {
        preg_match('#/(?:watch|fplayer)/([\w-]+)/\w*#i', $sUrl, $aMatch);
        return (!empty($aMatch[1])) ? $aMatch[1] : false;
    }

    public function getEmbedUrl($sUrl)
    {    // Checks if the ID is valid, otherwise returns false.
        if (!$this->getVideoId($sUrl)) return false;

        return static::PLAYER_URL . $this->getVideoId($sUrl) . '/metacefe.swf';
    }

}
