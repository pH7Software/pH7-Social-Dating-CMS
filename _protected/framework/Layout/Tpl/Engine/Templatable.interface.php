<?php
/**
 * @author           Pierre-Henry Soria <hello@ph7cms.com>
 * @copyright        (c) 2011-2019, Pierre-Henry Soria. All Rights Reserved.
 * @license          MIT License; See PH7.LICENSE.txt and PH7.COPYRIGHT.txt in the root directory.
 * @package          PH7 / Framework / Layout / Tpl / Engine
 */

namespace PH7\Framework\Layout\Tpl\Engine;

use PH7\Framework\Layout\Tpl\Engine\PH7Tpl\Exception as TplException;

interface Templatable
{
    /**
     * Display the template.
     */
    public function display(?string $sTplFile, ?string $sDirPath, bool $bInclude): string;

    /**
     * @param string $sName
     * @param mixed $mValue
     * @param bool $bEscape
     * @param bool $bEscapeStrip
     */
    public function assign(string $sName, $mValue, bool $bEscape, bool $bEscapeStrip): void;

    public function assigns(array $aVars, bool $bEscape, bool $bEscapeStrip);

    /**
     * @param string $sMailTplFile
     * @param string $sEmailAddress
     *
     * @return string
     *
     * @throws TplException
     */
    public function parseMail(string $sMailTplFile, string $sEmailAddress): string;
}
