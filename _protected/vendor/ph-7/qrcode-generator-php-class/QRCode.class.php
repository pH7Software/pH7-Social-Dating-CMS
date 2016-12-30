<?php
/**
 * @title            QR Code
 * @desc             Compatible to vCard 4.0 or higher.
 *
 * @author           Pierre-Henry Soria <ph7software@gmail.com>
 * @copyright        (c) 2012-2017, Pierre-Henry Soria. All Rights Reserved.
 * @license          GNU General Public License <http://www.gnu.org/licenses/gpl.html>
 * @version          1.2
 */

class QRCode
{

    const API_URL = 'https://chart.googleapis.com/chart?chs=';

    private $_sData;

    /**
     * Constructor.
     */
    public function __construct()
    {
        $this->_sData = 'BEGIN:VCARD' . "\n";
        $this->_sData .= 'VERSION:4.0' . "\n";
        return $this;
    }

    /**
     * The name of the person.
     *
     * @param string $sName
     * @return object this
     */
    public function name($sName)
    {
        $this->_sData .= 'N:' . $sName . "\n";
        return $this;
    }

    /**
     * The full name of the person.
     *
     * @param string $sFullName
     * @return object this
     */
    public function fullName($sFullName)
    {
        $this->_sData .= 'FN:' . $sFullName . "\n";
        return $this;
    }

    /**
     * Delivery address.
     *
     * @param string $sAddress
     * @return object this
     */
    public function address($sAddress)
    {
        $this->_sData .= 'ADR:' . $sAddress . "\n";
        return $this;
    }

    /**
     * Nickname.
     *
     * @param string $sNickname
     * @return object this
     */
    public function nickName($sNickname)
    {
        $this->_sData .= 'NICKNAME:' . $sNickname . "\n";
        return $this;
    }

    /**
     * Email address.
     *
     * @param string $sMail
     * @return object this
     */
    public function email($sMail)
    {
        $this->_sData .= 'EMAIL;TYPE=PREF,INTERNET:' . $sMail . "\n";
        return $this;
    }

    /**
     * Work Phone.
     *
     * @param string $sVal
     * @return object this
     */
    public function workPhone($sVal)
    {
        $this->_sData .= 'TEL;TYPE=WORK:' . $sVal . "\n";
        return $this;
    }

    /**
     * Home Phone.
     *
     * @param string $sVal
     * @return object this
     */
    public function homePhone($sVal)
    {
        $this->_sData .= 'TEL;TYPE=HOME:' . $sVal . "\n";
        return $this;
    }

    /**
     * URL address.
     *
     * @param string $sUrl
     * @return object this
     */
    public function url($sUrl)
    {
        $sUrl = (substr($sUrl, 0, 4) != 'http') ? 'http://' . $sUrl : $sUrl;
        $this->_sData .= 'URL:' . $sUrl . "\n";
        return $this;
    }

    /**
     * SMS code.
     *
     * @param string $sPhone
     * @param string $sText
     * @return object this
     */
    public function sms($sPhone, $sText)
    {
        $this->_sData .= 'SMSTO:' . $sPhone . ':' . $sText . "\n";
        return $this;
    }

    /**
     * Birthday.
     *
     * @param string $sBirthday Date in the format YYYY-MM-DD or ISO 8601
     * @return object this
     */
    public function birthday($sBirthday)
    {
        $this->_sData .= 'BDAY:' . $sBirthday . "\n";
        return $this;
    }

    /**
     * Anniversary.
     *
     * @param string $sBirthDate Date in the format YYYY-MM-DD or ISO 8601
     * @return object this
     */
    public function anniversary($sBirthDate)
    {
        $this->_sData .= 'ANNIVERSARY:' . $sBirthDate . "\n";
        return $this;
    }

    /**
     * Gender.
     *
     * @param string $sSex F = Female. M = Male
     * @return object this
     */
    public function gender($sSex)
    {
        $this->_sData .= 'GENDER:' . $sSex . "\n";
        return $this;
    }

    /**
     * A list of "tags" that can be used to describe the object represented by this vCard.
     *
     * @param string $sCategory
     * @return object this
     */
    public function categories($sCategories)
    {
        $this->_sData .= 'CATEGORIES:' . $sCategories . "\n";
        return $this;
    }

    /**
     * The instant messenger (Instant Messaging and Presence Protocol).
     *
     * @param string $sVal
     * @return object this
     */
    public function impp($sVal)
    {
       $this->_sData .= 'IMPP:' . $sVal . "\n";
       return $this;
    }

    /**
     * Photo (avatar).
     *
     * @param string $sImgUrl URL of the image.
     * @return object this
     * @throws InvalidArgumentException If the image format is invalid.
     */
    public function photo($sImgUrl)
    {
        $bIsImgExt = strtolower(substr(strrchr($sImgUrl, '.'), 1)); // Get the file extension.

        if ($bIsImgExt == 'jpeg' || $bIsImgExt == 'jpg' || $bIsImgExt == 'png' || $bIsImgExt == 'gif')
            $sExt = strtoupper($bIsImgExt);
        else
            throw new InvalidArgumentException('Invalid format Image!');

        $this->_sData .= 'PHOTO;VALUE=URL;TYPE=' . $sExt . ':' . $sImgUrl . "\n";
        return $this;
    }

    /**
     * The role, occupation, or business category of the vCard object within an organization.
     *
     * @param string $sRole e.g., Executive
     * @return object this
     */
    public function role($sRole)
    {
        $this->_sData .= 'ROLE:' . $sRole . "\n";
        return $this;
    }

    /**
     * The organization / company.
     *
     * The name and optionally the unit(s) of the organization
     * associated with the vCard object. This property is based on the X.520 Organization Name
     * attribute and the X.520 Organization Unit attribute.
     *
     * @param string $sOrg e.g., Google;GMail Team;Spam Detection Squad
     * @return object this
     */
    public function organization($sOrg)
    {
        $this->_sData .= 'ORG:' . $sOrg . "\n";
        return $this;
    }

    /**
     * The supplemental information or a comment that is associated with the vCard.
     *
     * @param string $sText
     * @return object this
     */
    public function note($sText)
    {
        $this->_sData .= 'NOTE:' . $sText . "\n";
        return $this;
    }

    /**
     * Bookmark.
     *
     * @param string $sTitle
     * @param string $sUrl
     * @return object this
     */
    public function bookmark($sTitle, $sUrl)
    {
        $this->_sData .= 'MEBKM:TITLE:' . $sTitle . ';URL:' . $sUrl . "\n";
        return $this;
    }

    /**
     * Geo location.
     *
     * @param string $sLat Latitude
     * @param string $sLon Longitude
     * @param integer $iHeight Height
     * @return object this
     */
    public function geo($sLat, $sLon, $iHeight)
    {
        $this->_sData .= 'GEO:' . $sLat . ',' . $sLon . ',' . $iHeight . "\n";
        return $this;
    }

    /**
     * The language that the person speaks.
     *
     * @param string $sLang e.g., en-US
     * @return object this
     */
    public function lang($sLang)
    {
        $this->_sData .= 'LANG:' . $sLang . "\n";
        return $this;
    }

    /**
     * Wifi.
     *
     * @param string $sType
     * @param string $sSsid
     * @param string $sPwd
     * @return object this
     */
    public function wifi($sType, $sSsid, $sPwd)
    {
        $this->_sData .= 'WIFI:T:' . $sType . ';S' . $sSsid . ';' . $sPwd . "\n";
        return $this;
    }

    /**
     * Generate the QR code.
     *
     * @return object this
     */
    public function finish()
    {
        $this->_sData .= 'END:VCARD';
        $this->_sData = urlencode($this->_sData);
        return $this;
    }

    /**
     * Get the URL of QR Code.
     *
     * @param integer $iSize Default 150
     * @param string $sECLevel Default L
     * @param integer $iMargin Default 1
     * @return string The API URL configure.
     */
    public function get($iSize = 150, $sECLevel = 'L', $iMargin = 1)
    {
        return self::API_URL . $iSize . 'x' . $iSize . '&cht=qr&chld=' . $sECLevel . '|' . $iMargin . '&chl=' . $this->_sData;
    }

    /**
     * The HTML code for displaying the QR Code.
     *
     * @return void
     */
    public function display()
    {
        echo '<p class="center"><img src="' . $this->_cleanUrl($this->get()) . '" alt="QR Code" /></p>';
    }

    /**
     * Clean URL.
     *
     * @param string $sUrl
     * @return string
     */
    private function _cleanUrl($sUrl)
    {
        return str_replace('&', '&amp;', $sUrl);
    }

}
