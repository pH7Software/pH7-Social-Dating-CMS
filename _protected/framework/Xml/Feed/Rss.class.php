<?php
/**
 * @title            RSS Class
 * @desc             RSS Dom class with the DomDocument object.
 *
 * @author           Pierre-Henry Soria <ph7software@gmail.com>
 * @copyright        (c) 2012-2017, Pierre-Henry Soria. All Rights Reserved.
 * @license          GNU General Public License; See PH7.LICENSE.txt and PH7.COPYRIGHT.txt in the root directory.
 * @package          PH7 / Framework / Xml / Feed
 * @version          1.1
 * @linkDomDocument  http://php.net/manual/class.domdocument.php
 */

namespace PH7\Framework\Xml\Feed;
defined('PH7') or exit('Restricted access');

class Rss extends \DomDocument
{

    /**
     * RSS channel object.
     *
     * @access private
     * @var object $_oChannel
     */
    private $_oChannel;

    /**
     * @constructor Sets up the DOM environment.
     * @access public
     * @param string $sTitle The site title
     * @param string $sLink The link to the site
     * @param string $sDescription The site description
     * @return void
     */
    public function __construct($sTitle, $sLink, $sDescription)
    {
        // Call the parent constructor (DomDocument)
        parent::__construct();

        // Craete the root element
        $oRoot = $this->appendChild($this->createElement('rss'));

        // Sets to RSS version 2
        $oRoot->setAttribute('version', '2.0');

        // Sets the channel node
        $this->_oChannel = $oRoot->appendChild($this->createElement('channel'));

        // Sets the title link and description elements
        $this->_oChannel->appendChild($this->createElement('title', $sTitle));
        $this->_oChannel->appendChild($this->createElement('link', $sLink));
        $this->_oChannel->appendChild($this->createElement('description', $sDescription));
    }


    /**
     * Adding the Items to the RSS Feed.
     *
     * @access public
     * @param array $aItems
     * @return object this
     */
    public function addItem($aItems)
    {
        // Create an item
        $oItem = $this->createElement('item');

        foreach ($aItems as $sElement => $sValue)
        {
            switch ($sElement)
            {
                // Create the sub elements here
                case 'image':
                case 'skipHour':
                case 'skipDay':
                {
                    $oIm = $this->createElement('image');
                    $this->_oChannel->appendChild($oIm);

                    foreach ($aValue as $sSubElement => $sSubValue)
                    {
                        $oSub = $this->createElement($sSubElement, $sSubValue);
                        $oIm->appendChild($oSub);
                    }
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
                    $oItem->appendChild($this->createElement($sElement, $sValue));
                break;
            }
        }

        // Append the item to the channel
        $this->_oChannel->appendChild($oItem);

        // Allow chaining with $this
        return $this;
    }

    /**
     * Create the XML.
     *
     * @access public
     * @return string The XML string
     */
    public function __toString()
    {
        return $this->saveXML();
    }

}
