<?php
/**
 * @title          Escape Exception Trait
 * @desc           Escape the exception message.
 *
 * @author         Pierre-Henry Soria <ph7software@gmail.com>
 * @copyright      (c) 2012-2017, Pierre-Henry Soria. All Rights Reserved.
 * @license        GNU General Public License; See PH7.LICENSE.txt and PH7.COPYRIGHT.txt in the root directory.
 * @package        PH7/ Framework / Error / CException
 * @version        1.1
 */

namespace PH7\Framework\Error\CException;
defined('PH7') or exit('Restricted access');

trait Escape
{

    protected $sAllowTags = '<br><i><em><b><strong><u>';

    /**
     * Escape the exception message.
     *
     * @param string $sMsg
     * @return void
     */
    protected function init($sMsg)
    {
        $this->message = strip_tags($sMsg, $this->sAllowTags);
    }

}
