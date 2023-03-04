<?php
/**
 * @title            RSS Class
 * @desc             RSS Dom class with the DomDocument object.
 *
 * @author           Pierre-Henry Soria <hello@ph7builder.com>
 * @copyright        (c) 2012-2019, Pierre-Henry Soria. All Rights Reserved.
 * @license          MIT License; See LICENSE.md and COPYRIGHT.md in the root directory.
 * @package          PH7 / Framework / Xml / Feed
 * @version          1.3
 * @linkDomDocument  http://php.net/manual/class.domdocument.php
 */

declare(strict_types=1);

namespace PH7\Framework\Xml\Feed;

defined('PH7') or exit('Restricted access');

use DomDocument;

class Rss extends DomDocument
{
    private const DOCUMENT_VERSION = '2.0';

    /**
     * RSS channel object.
     *
     * @var \DOMNode $oChannel
     */
    private $oChannel;

    public function __construct(string $sTitle, string $sLink, string $sDescription)
    {
        // Call the parent constructor (DomDocument)
        parent::__construct();

        // Create the root element
        $oRoot = $this->appendChild($this->createElement('rss'));

        // Sets to RSS version 2
        $oRoot->setAttribute('version', self::DOCUMENT_VERSION);

        // Sets the channel node
        $this->oChannel = $oRoot->appendChild($this->createElement('channel'));

        // Sets the title link and description elements
        $this->oChannel->appendChild($this->createElement('title', $sTitle));
        $this->oChannel->appendChild($this->createElement('link', $sLink));
        $this->oChannel->appendChild($this->createElement('description', $sDescription));
    }


    /**
     * Adding the Items to the RSS Feed.
     *
     * @param array $aItems
     *
     * @return self
     */
    public function addItem($aItems): self
    {
        $oItem = $this->createElement('item');

        foreach ($aItems as $sElement => $mValue) {
            switch ($sElement) {
                // Create the sub elements here
                case 'image':
                case 'skipHour':
                case 'skipDay':
                    $oImage = $this->createElement('image');
                    $this->oChannel->appendChild($oImage);

                    foreach ($mValue as $sSubElement => $sSubValue) {
                        $oSub = $this->createElement($sSubElement, $sSubValue);
                        $oImage->appendChild($oSub);
                    }
                    break;
                case 'title':
                case 'pubDate':
                case 'link':
                case 'description':
                case 'copyright':
                case 'managingEditor':
                case 'webMaster':
                case 'lastbuildDate':
                case 'category':
                case 'generator':
                case 'docs':
                case 'language':
                case 'cloud':
                case 'ttl':
                case 'rating':
                case 'textInput':
                case 'source':
                    $oItem->appendChild($this->createElement($sElement, $mValue));
                    break;
            }
        }

        // Append the item to the channel
        $this->oChannel->appendChild($oItem);

        // Allow chaining with $this
        return $this;
    }

    /**
     * Create the XML.
     *
     * @return string|false The XML string, or FALSE if an error occurred.
     */
    public function __toString()
    {
        return $this->saveXML();
    }
}
