<?php
/**
 * @title            Model Class
 *
 * @author           Pierre-Henry Soria <ph7software@gmail.com>
 * @copyright        (c) 2012-2013, Pierre-Henry Soria. All Rights Reserved.
 * @license          GNU General Public License; See PH7.LICENSE.txt and PH7.COPYRIGHT.txt in the root directory.
 * @package          PH7 / Framework / Mvc / Model / Engine
 * @version          0.6
 */

namespace PH7\Framework\Mvc\Model\Engine;
defined('PH7') or exit('Restricted access');

use PH7\Framework\Cache\Cache;

abstract class Model extends Entity
{

    protected $orm, $cache;

    public function __construct()
    {
        $this->orm = Record::getInstance();
        $this->cache = new Cache;
    }

    public function __destruct()
    {
        unset($this->orm, $this->cache);
    }

}
