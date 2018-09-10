<?php
/**
 * @author           Pierre-Henry Soria <hi@ph7.me>
 * @copyright        (c) 2018, Pierre-Henry Soria. All Rights Reserved.
 * @license          GNU General Public License; See PH7.LICENSE.txt and PH7.COPYRIGHT.txt in the root directory.
 * @package          PH7 / Framework / Mail
 */

namespace PH7\Framework\Mail;

interface Mailable
{
    /**
     * @param array $aInfo
     * @param string $sContents
     * @param bool $bHtmlFormat
     *
     * @return int Number of recipients who were accepted for delivery.
     */
    public function send(array $aInfo, $sContents, $bHtmlFormat);
}
