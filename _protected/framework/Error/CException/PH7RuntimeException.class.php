<?php
/**
 * @title          PH7 Runtime Exception Class
 * @desc           Exception Runtime handling.
 *
 * @author         Pierre-Henry Soria <hello@ph7cms.com>
 * @copyright      (c) 2012-2019, Pierre-Henry Soria. All Rights Reserved.
 * @license        GNU General Public License; See PH7.LICENSE.txt and PH7.COPYRIGHT.txt in the root directory.
 * @package        PH7/ Framework / Error / CException
 * @version        1.0
 */

namespace PH7\Framework\Error\CException;

defined('PH7') or exit('Restricted access');

use RuntimeException;

class PH7RuntimeException extends RuntimeException
{
    use Escape {
        strip as private;
    }

    /**
     * @param string $sMsg
     * @param int $iCode
     */
    public function __construct($sMsg, $iCode = 0)
    {
        parent::__construct($sMsg, $iCode);
        $this->strip($sMsg);
    }
}
