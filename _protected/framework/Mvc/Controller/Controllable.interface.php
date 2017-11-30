<?php
/**
 * @author           Pierre-Henry Soria <ph7software@gmail.com>
 * @copyright        (c) 2011-2018, Pierre-Henry Soria. All Rights Reserved.
 * @license          GNU General Public License; See PH7.LICENSE.txt and PH7.COPYRIGHT.txt in the root directory.
 * @package          PH7 / Framework / Mvc / Controller
 * @version          1.0
 */

namespace PH7\Framework\Mvc\Controller;

defined('PH7') or exit('Restricted access');

// The prototypes of the methods
interface Controllable
{
    /**
     * Output Stream Views.
     *
     * @param string|null $sFile Specify another display file instead of the default layout file. Default NULL
     *
     * @return void
     */
    public function output($sFile);

    /**
     * Set a Not Found Error Message with HTTP 404 Code Status.
     *
     * @param string $sMsg Default is empty ('')
     * @param bool $b404Status For the Ajax blocks and others, we cannot put the HTTP 404 error code, so the attribute must be set to FALSE. Default TRUE
     *
     * @return void Quits the page with the exit() function
     */
    public function displayPageNotFound($sMsg = '', $b404Status = true);
}
