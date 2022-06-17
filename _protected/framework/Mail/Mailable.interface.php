<?php
/**
 * @author           Pierre-Henry Soria <hi@ph7.me>
 * @copyright        (c) 2018-2019, Pierre-Henry Soria. All Rights Reserved.
 * @license          MIT License; See LICENSE.md and COPYRIGHT.md in the root directory.
 * @package          PH7 / Framework / Mail
 */

namespace PH7\Framework\Mail;

interface Mailable
{
    const HTML_FORMAT = 1;
    const TEXT_FORMAT = 2;
    const ALL_FORMATS = 3;

    public function send(array $aInfo, string $sContents, int $iFormatType): bool;
}
