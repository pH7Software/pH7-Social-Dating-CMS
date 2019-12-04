<?php
/**
 * @title            File Class
 * @desc             Handle File Registry Class.
 *
 * @author           Pierre-Henry Soria <hello@ph7cms.com>
 * @copyright        (c) 2012-2019, Pierre-Henry Soria. All Rights Reserved.
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
    private $sPath;

    /** @var resource|null */
    private $rFile = null;

    public function __construct()
    {
        $this->sPath = PH7_PATH_TMP . 'hashList.tmp';
        $this->open();
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
        $this->close();
    }

    /**
     * @return void
     */
    public function __wakeup()
    {
        $this->close();
    }

    /**
     * @return string|boolean Returns the read string or FALSE on failure.
     */
    public function read()
    {
        rewind($this->rFile);

        return fread($this->rFile, filesize($this->sPath));
    }

    /**
     * @param string $sData
     *
     * @return void
     */
    public function write($sData)
    {
        fwrite($this->rFile, $sData);
    }

    /**
     * @return void
     */
    private function open()
    {
        $this->rFile = fopen($this->sPath, 'wb+');
    }

    /**
     * @return boolean
     */
    private function close()
    {
        if (null === $this->rFile) {
            return false;
        }

        fclose($this->rFile);
        $this->rFile = null;

        return true;
    }

    public function __destruct()
    {
        if (null !== $this->rFile) {
            $this->close();
        }
    }
}
