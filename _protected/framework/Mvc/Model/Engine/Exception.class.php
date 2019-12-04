<?php
/**
 * @author           Pierre-Henry Soria <hello@ph7cms.com>
 * @copyright        (c) 2011-2019, Pierre-Henry Soria. All Rights Reserved.
 * @license          GNU General Public License; See PH7.LICENSE.txt and PH7.COPYRIGHT.txt in the root directory.
 * @package          PH7 / Framework / Mvc / Model / Engine
 */

namespace PH7\Framework\Mvc\Model\Engine;

defined('PH7') or exit('Restricted access');

use PDOException;
use PH7\Framework\Error\CException\Escape;

class Exception extends PDOException
{
    use Escape {
        strip as private;
    }

    /**
     * @param string $sMsg
     */
    public function __construct($sMsg)
    {
        parent::__construct($sMsg);

        $this->strip($sMsg);
    }
}
