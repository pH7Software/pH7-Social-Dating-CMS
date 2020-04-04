<?php
/**
 * @author         Pierre-Henry Soria <hello@ph7cms.com>
 * @copyright      (c) 2019, Pierre-Henry Soria. All Rights Reserved.
 * @license        GNU General Public License; See PH7.LICENSE.txt and PH7.COPYRIGHT.txt in the root directory.
 * @package        PH7 / App / System / Core / Class
 */

namespace PH7;

use PH7\Framework\Error\CException\PH7InvalidArgumentException;
use PH7\Framework\Geo\Map\Map;

class MapDrawerCore
{
    /** @var Map */
    private $oMap;

    /** @var string */
    private $sMapWidthSize = '100%';

    /** @var string */
    private $sMapHeightSize = '520px';

    /** @var string */
    private $sMapDivId = 'country_map';

    /** @var string */
    private $sApiKey;

    private $iMapZoomLevel = 12;

    public function __construct(Map $oMap, $sApiKey)
    {
        $this->oMap = $oMap;
        $this->sApiKey = $sApiKey;
    }

    /**
     * @param string $sWidthSize
     */
    public function setWidthSize($sWidthSize)
    {
        $this->sMapWidthSize = $sWidthSize;
    }

    /**
     * @param string $sHeightSize
     */
    public function setHeightSize($sHeightSize)
    {
        $this->sMapHeightSize = $sHeightSize;
    }

    /**
     * @param int $iZoomLevel
     *
     * @throws PH7InvalidArgumentException
     */
    public function setZoomLevel($iZoomLevel)
    {
        if (!is_int($iZoomLevel)) {
            throw new PH7InvalidArgumentException('Invalid Zoom Level Type. It must be an integer.');
        }

        $this->iMapZoomLevel = $iZoomLevel;
    }

    /**
     * @param string $sDivId
     *
     * @throws PH7InvalidArgumentException
     */
    public function setDivId($sDivId)
    {
        if ($this->isDivIdInvalid($sDivId)) {
            throw new PH7InvalidArgumentException('The map div class ID argument is invalid.');
        }

        $this->sMapDivId = $sDivId;
    }

    /**
     * Set the Google Maps code to the view.
     *
     * @param string $sFullAddress
     * @param string $sMarkerText
     *
     * @return string
     */
    public function getMap($sFullAddress, $sMarkerText)
    {
        $this->oMap->setKey($this->sApiKey);
        $this->oMap->setCenter($sFullAddress);
        $this->oMap->setSize($this->sMapWidthSize, $this->sMapHeightSize);
        $this->oMap->setDivId($this->sMapDivId);
        $this->oMap->setZoom($this->iMapZoomLevel);
        $this->oMap->addMarkerByAddress($sFullAddress, $sMarkerText, $sMarkerText);
        $this->oMap->generate();

        return $this->oMap->getMap();
    }

    /**
     * @param string $sDivId
     *
     * @return bool
     */
    private function isDivIdInvalid($sDivId)
    {
        return !is_string($sDivId) || strlen($sDivId) < 2;
    }
}
