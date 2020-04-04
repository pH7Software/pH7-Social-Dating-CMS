<?php
/**
 * @title            PH7 XSLT
 * @desc             XSLT PHP template engine.
 *
 * @author           Pierre-Henry Soria <hello@ph7cms.com>
 * @copyright        (c) 2012-2019, Pierre-Henry Soria. All Rights Reserved.
 * @license          GNU General Public License; See PH7.LICENSE.txt and PH7.COPYRIGHT.txt in the root directory.
 * @package          PH7 / Framework / Layout / Tpl / Engine / PH7Xsl
 * @version          1.1
 */

namespace PH7\Framework\Layout\Tpl\Engine\PH7Xsl;

defined('PH7') or exit('Restricted access');

use DOMDocument;
use DOMElement;
use XsltProcessor;

class PH7Xsl
{
    const ROOT_NAMESPACE = 'template';

    /** @var DOMDocument */
    private $oXml;

    /** @var DOMDocument */
    private $oXsl;

    /** @var XsltProcessor */
    private $oXslProcessor;

    /** @var string */
    private $sOutput;

    /** @var DOMElement */
    private $oRoot;

    /** @var string */
    private $sFile;

    /** @var bool */
    private $bPhpFunc = true;

    /**
     * @param string $sFile The XSL file.
     */
    public function __construct($sFile)
    {
        // Creating objects
        $this->oXml = new DOMDocument('1.0', 'UTF-8');
        $this->oXsl = new DOMDocument;
        $this->oXslProcessor = new XsltProcessor;

        $this->sFile = $sFile;

        $this->load();

        // Creation of the XML root node mandatory
        $this->oRoot = $this->oXml->createElement(static::ROOT_NAMESPACE);
        // Insertion of this node in the tree view of the XML file
        $this->oXml->appendChild($this->oRoot);
    }

    /**
     * Enable or disable the PHP functions in the XSTL template.
     *
     * @param bool $bEnable
     *
     * @return self
     */
    public function enablePhpFunctions($bEnable = true)
    {
        $this->bPhpFunc = (bool)$bEnable;

        return $this;
    }

    /**
     * Generate XML Node.
     *
     * @param mixed $aData array
     * @param string $sNamespace
     *
     * @return self
     *
     * @throws Exception If the data value is not an array.
     */
    public function generateXMLNode($aData, $sNamespace = '')
    {
        if (!is_array($aData)) {
            throw new Exception('The data value ​​must be of type array!');
        }

        $sRoot = $this->oXml->createElement($sNamespace);
        $this->oRoot->appendChild($sRoot);

        foreach ($aData as $sKey => $sValue) {
            $sNode = $this->oXml->createElement($sKey);
            $sRoot->appendChild($sNode);
            $sContent = $this->oXml->createTextNode(utf8_encode($sValue));
            $sNode->appendChild($sContent);
        }

        return $this;
    }

    /**
     * Output.
     *
     * @return void
     *
     * @throws Exception If the XSL file contains syntax errors.
     */
    public function render()
    {
        if (!@$this->sOutput = $this->oXslProcessor->transformToXML($this->oXml)) {
            throw new Exception('Transformation syntax!');
        }

        header('Content-Type: text/xml');
        echo $this->sOutput;
    }

    /**
     * Set variables.
     *
     * @param array|string $mKey
     * @param string $sValue Optional. Only if the variables are passed through an array.
     * @param string $sNamespace
     *
     * @return self
     */
    public function setParam($mKey, $sValue = '', $sNamespace = '')
    {
        if (is_array($mKey)) {
            foreach ($mKey as $sKey => $sVal) {
                $this->setParam($sKey, $sVal, $sNamespace); // Recursive method
            }
        } else {
            $this->oXslProcessor->setParameter($sNamespace, $mKey, $sValue);
        }

        return $this;
    }

    /**
     * Load XSL file.
     *
     * @return self
     *
     * @internal We use realpath() function because forward slashes can cause significant performance degradation on Windows OS.
     *
     * @throws Exception If the XSL file does not exist.
     */
    protected function load()
    {
        $sPath = realpath($this->sFile);

        if (!@$this->oXsl->load($sPath)) {
            throw new Exception('While loading file: "' . $sPath . '"');
        } else {
            if ($this->bPhpFunc) {
                $this->oXslProcessor->registerPHPFunctions();
            }

            $this->oXslProcessor->importStylesheet($this->oXsl);
        }

        return $this;
    }
}
