<?php
/**
 * @author           Pierre-Henry Soria <hello@ph7builder.com>
 * @copyright        (c) 2011-2019, Pierre-Henry Soria. All Rights Reserved.
 * @license          MIT License; See LICENSE.md and COPYRIGHT.md in the root directory.
 * @package          PH7 / Framework / Layout / Tpl / Engine
 */

namespace PH7\Framework\Layout\Tpl\Engine;

use PH7\Framework\Layout\Tpl\Engine\PH7Tpl\Exception as TplException;

interface Templatable
{
    /**
     * Display/Output the template.
     *
     * @return string|never
     * TODO When min version will be PHP 8.1, add union return types "string|never"
     */
    public function display(?string $sTplFile, ?string $sDirPath, bool $bInclude);

    public function assign(string $sName, $mValue, bool $bEscape, bool $bEscapeStrip): void;

    public function assigns(array $aVars, bool $bEscape, bool $bEscapeStrip);

    /**
     * @throws TplException
     */
    public function parseMail(string $sMailTplFile, string $sEmailAddress): string;
}
