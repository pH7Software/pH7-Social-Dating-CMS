<?php
/**
 * @author           Pierre-Henry Soria <hello@ph7builder.com>
 * @copyright        (c) 2011-2026, Pierre-Henry Soria. All Rights Reserved.
 * @license          MIT License; See LICENSE.md and COPYRIGHT.md in the root directory.
 * @package          PH7 / Framework / Layout / Tpl / Engine
 */

namespace PH7\Framework\Layout\Tpl\Engine;

use PH7\Framework\Layout\Tpl\Engine\PH7Tpl\Exception as TplException;

interface Templatable
{
    /**
     * Renders a template file.
     *
     * @param string|null $sTplFile Template file name.
     * @param string|null $sDirPath Custom template directory.
     * @param bool        $bInclude If false, returns the compiled file path instead of rendering.
     *
     * @return string|null Returns the compiled template path when $bInclude is false, otherwise null.
     */
    public function display(?string $sTplFile = null, ?string $sDirPath = null, bool $bInclude = true): string|null;

    /**
     * Assigns a variable to the template.
     *
     * @param string $sName         Variable name.
     * @param mixed  $mValue        Variable value.
     * @param bool   $bEscape       Whether to escape the value against XSS.
     * @param bool   $bEscapeStrip  Whether to strip HTML/PHP tags when escaping.
     */
    public function assign(string $sName, $mValue, bool $bEscape = false, bool $bEscapeStrip = false): void;

    /**
     * Assigns multiple variables from an array.
     *
     * @param array $aVars          Array of variables to assign.
     * @param bool  $bEscape        Whether to escape the values against XSS.
     * @param bool  $bEscapeStrip   Whether to strip HTML/PHP tags when escaping.
     */
    public function assigns(array $aVars, bool $bEscape = false, bool $bEscapeStrip = false): void;

    /**
     * Parses an email template.
     *
     * @param string $sMailTplFile
     * @param string $sEmailAddress Used to generate the privacy policy footer.
     *
     * @return string The parsed email template content.
     *
     * @throws TplException
     */
    public function parseMail(string $sMailTplFile, string $sEmailAddress): string;
}
