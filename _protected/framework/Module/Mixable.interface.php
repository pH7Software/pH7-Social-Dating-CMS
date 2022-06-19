<?php
/**
 * @title            Mixer Interface
 *
 * @author           Pierre-Henry Soria <hello@ph7builder.com>
 * @copyright        (c) 2012-2019, Pierre-Henry Soria. All Rights Reserved.
 * @license          MIT License; See LICENSE.md and COPYRIGHT.md in the root directory.
 * @package          PH7 / Framework / Module
 * @version          0.1
 */

namespace PH7\Framework\Module;

interface Mixable
{
    public function cms();

    public function framework();

    public function mixer();
}
