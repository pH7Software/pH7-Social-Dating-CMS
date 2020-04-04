<?php
/**
 * @author           Pierre-Henry Soria <hello@ph7cms.com>
 * @copyright        (c) 2011-2019, Pierre-Henry Soria. All Rights Reserved.
 * @license          GNU General Public License; See PH7.LICENSE.txt and PH7.COPYRIGHT.txt in the root directory.
 * @package          PH7 / Framework / Layout / Tpl / Engine
 */

namespace PH7\Framework\Layout\Tpl\Engine;

use PH7\Framework\Layout\Tpl\Engine\PH7Tpl\Exception as TplException;

interface Templatable
{
    /**
     * Display the template.
     *
     * @param string $sTplFile
     * @param string $sDirPath
     * @param bool $bInclude
     *
     * @return string
     */
    public function display($sTplFile, $sDirPath, $bInclude);

    /**
     * @param string $sName
     * @param mixed $mValue
     * @param bool $bEscape
     * @param bool $bEscapeStrip
     *
     * @return void
     */
    public function assign($sName, $mValue, $bEscape, $bEscapeStrip);

    /**
     * @param array $aVars
     * @param bool $bEscape
     * @param bool $bEscapeStrip
     *
     * @return void
     */
    public function assigns(array $aVars, $bEscape, $bEscapeStrip);

    /**
     * @param string $sMailTplFile
     * @param string $sEmailAddress
     *
     * @return string
     *
     * @throws TplException
     */
    public function parseMail($sMailTplFile, $sEmailAddress);
}
