<?php
/**
 * @title            Link Class
 * @desc             Gets the Links in the XML file.
 *
 * @author           Pierre-Henry Soria <ph7software@gmail.com>
 * @copyright        (c) 2012-2017, Pierre-Henry Soria. All Rights Reserved.
 * @license          GNU General Public License; See PH7.LICENSE.txt and PH7.COPYRIGHT.txt in the root directory.
 * @package          PH7 / Framework / Xml
 * @version          1.0
 */

namespace PH7\Framework\Xml;
defined('PH7') or exit('Restricted access');

class Link
{

    private $_oXml, $_sPath, $_aRet = array();

    /**
     * Constructor with the instance of the DOMDocument object.
     *
     * @param string $sPath The path to the XML file. You can also specify an URL if the "allow_url_fopen" PHP directive is enabled.
     * @return void
     */
    public function __construct($sPath)
    {
        $this->_sPath = $sPath;
        // Creating a DOM Object
        $this->_oXml = new \DOMDocument;
    }

    /**
     * Gets the XML links.
     *
     * @return array The XML tree.
     * @throws \PH7\Framework\Xml\Exception If the XML file is not found.
     */
    public function get()
    {
        if (!@$this->_oXml->load($this->_sPath))
            throw new Exception('The file \'' . $this->_sPath . '\' does not exist or is not a valid XML file.');

        foreach ($this->_oXml->getElementsByTagName('link') as $oTag)
            $this->_aRet[$oTag->getAttribute('url')] = $oTag->getAttribute('title');

        return $this->_aRet;
    }

    public function __destruct()
    {
        unset($this->_oXml, $this->_sPath, $this->_aRet);
    }

}
