<?php
/**
 * @title          Measure Abstract Class
 *
 * @author         Pierre-Henry Soria <hello@ph7builder.com>
 * @copyright      (c) 2013-2019, Pierre-Henry Soria. All Rights Reserved.
 * @license        MIT License; See LICENSE.md and COPYRIGHT.md in the root directory.
 * @package        PH7 / Framework / Math / Measure
 * @version        1.0
 */

namespace PH7\Framework\Math\Measure;

defined('PH7') or exit('Restricted access');

abstract class Measure
{
    /** @var int */
    protected $iUnit;

    /**
     * @param int $iUnit
     */
    public function __construct($iUnit)
    {
        $this->iUnit = $iUnit;
    }
}
