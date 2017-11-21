<?php
/**
 * @title            Link Class
 * @desc             Gets the Links in the XML file.
 *
 * @author           Pierre-Henry Soria <hello@ph7cms.com>
 * @copyright        (c) 2012-2018, Pierre-Henry Soria. All Rights Reserved.
 * @license          GNU General Public License; See PH7.LICENSE.txt and PH7.COPYRIGHT.txt in the root directory.
 * @package          PH7 / Framework / Xml
 * @version          1.4
 */

namespace PH7\Framework\Xml;

defined('PH7') or exit('Restricted access');

use DOMDocument;

class Link
{
    /** @var DOMDocument */
    private $oXml;

    /** @var string */
    private $sPath;

    /** @var array */
    private $aRet = array();

    /**
     * Constructor with the instance of the DOMDocument object.
     *
     * @param string $sPath The path to the XML file. You can also specify an URL if the "allow_url_fopen" PHP directive is enabled.
     */
    public function __construct($sPath)
    {
        $this->sPath = $sPath;
        $this->oXml = new DOMDocument;
    }

    /**
     * Gets the XML links.
     *
     * @return array The XML tree.
     *
     * @throws Exception If the XML file is not found.
     */
    public function get()
    {
        if (!@$this->oXml->load($this->sPath)) {
            throw new Exception(t("URL '%0%' doesn't exist or isn't a valid XML file.", $this->sPath));
        }

        foreach ($this->oXml->getElementsByTagName('link') as $oTag) {
            $this->aRet[$oTag->getAttribute('url')] = $oTag->getAttribute('title');
        }

        return $this->aRet;
    }
}
