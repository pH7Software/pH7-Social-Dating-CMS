<?php
/**
 * @title            Entity Class
 *
 * @author           Pierre-Henry Soria <hello@ph7cms.com>
 * @copyright        (c) 2013-2019, Pierre-Henry Soria. All Rights Reserved.
 * @license          GNU General Public License; See PH7.LICENSE.txt and PH7.COPYRIGHT.txt in the root directory.
 * @package          PH7 / Framework / Mvc / Model / Engine
 */

namespace PH7\Framework\Mvc\Model\Engine;

defined('PH7') or exit('Restricted access');

abstract class Entity
{
    /** @var int */
    private $iId;

    /**
     * Get the primary key.
     *
     * @return int
     */
    public function getKeyId()
    {
        $this->checkKeyId();

        return $this->iId;
    }

    /**
     * Set the primary key.
     *
     * @param int $iId
     *
     * @return void
     */
    public function setKeyId($iId)
    {
        $this->iId = (int)$iId;
    }

    /**
     * Check if the self::$iId attribute is not empty, otherwise we set the last insert ID.
     *
     * @see Db::lastInsertId()
     *
     * @return void
     */
    protected function checkKeyId()
    {
        if (empty($this->iId)) {
            $this->setKeyId(Db::getInstance()->lastInsertId());
        }
    }
}
