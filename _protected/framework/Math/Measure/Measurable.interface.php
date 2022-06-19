<?php
/**
 * @title          Measure Interface
 *
 * @author         Pierre-Henry Soria <hello@ph7builder.com>
 * @copyright      (c) 2013-2019, Pierre-Henry Soria. All Rights Reserved.
 * @license        MIT License; See LICENSE.md and COPYRIGHT.md in the root directory.
 * @package        PH7 / Framework / Math / Measure
 * @version        1.0
 */

namespace PH7\Framework\Math\Measure;

defined('PH7') or exit('Restricted access');

interface Measurable
{
    /**
     * Get the measure.
     *
     * @return mixed
     */
    public function get();
}
