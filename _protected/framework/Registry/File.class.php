<?php
/**
 * @title            File Class
 * @desc             Handle File Registry Class.
 *
 * @author           Pierre-Henry Soria <ph7software@gmail.com>
 * @copyright        (c) 2012-2017, Pierre-Henry Soria. All Rights Reserved.
 * @license          GNU General Public License; See PH7.LICENSE.txt and PH7.COPYRIGHT.txt in the root directory.
 * @package          PH7 / Framework / Registry
 * @version          0.9
 */

namespace PH7\Framework\Registry;

defined('PH7') or exit('Restricted access');

/**
 * @abstract
 */
abstract class File implements \Serializable
{
    /** @var string */
    private $_sPath;

    /** @var resource|null */
    private $_rFile = null;

    public function __construct()
    {
        $this->_sPath = PH7_PATH_TMP . 'hashList.tmp';
        $this->_open();
    }

    /**
     * @param mixed $mData
     *
     * @return string Returns a string containing a byte-stream representation of the value.
     */
    public function serialize($mData)
    {
        return serialize($mData);
    }

    /**
     * @param string $sData
     *
     * @return string Returns the converted value if successful otherwise returns false.
     */
    public function unserialize($sData)
    {
        return unserialize($sData);
    }

    /**
     * @return void
     */
    public function __sleep()
    {
        $this->_close();
    }

    /**
     * @return void
     */
    public function __wakeup()
    {
        $this->_close();
    }

    /**
     * @return string|boolean Returns the read string or FALSE on failure.
     */
    public function read()
    {
        rewind($this->_rFile);
        return fread($this->_rFile, filesize($this->_sPath));
    }

    /**
     * @param string $sData
     *
     * @return void
     */
    public function write($sData)
    {
        fwrite($this->_rFile, $sData);
    }

    /**
     * @return void
     */
    private function _open()
    {
        $this->_rFile = fopen($this->_sPath, 'wb+');
    }

    /**
     * @return boolean
     */
    private function _close()
    {
        if (null === $this->_rFile) {
            return false;
        }

        fclose($this->_rFile);
        $this->_rFile = null;

        return true;
    }

    public function __destruct()
    {
        if (null !== $this->_rFile) {
            $this->_close();
        }
    }
}
