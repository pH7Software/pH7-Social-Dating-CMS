<?php
/**
 * @title            Entity Class
 *
 * @author           Pierre-Henry Soria <ph7software@gmail.com>
 * @copyright        (c) 2013, Pierre-Henry Soria. All Rights Reserved.
 * @license          GNU General Public License; See PH7.LICENSE.txt and PH7.COPYRIGHT.txt in the root directory.
 * @package          PH7 / Framework / Mvc / Model / Engine
 * @version          1.0
 */

namespace PH7\Framework\Mvc\Model\Engine;
defined('PH7') or exit('Restricted access');

abstract class Entity
{

    /**
     * @var integer
     */
    private $_iId;

    /**
     * Get the primary key.
     *
     * @return integer
     */
    public function getId()
    {
        return $this->_iId;
    }

    /**
     * Set the primary key.
     *
     * @param integer $iId
     */
    public function setId($iId)
    {
        $this->_iId = (int) $iId;
    }

}
