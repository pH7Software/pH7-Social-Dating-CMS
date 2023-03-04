<?php
/**
 * @author           Pierre-Henry Soria <hello@ph7builder.com>
 * @copyright        (c) 2011-2019, Pierre-Henry Soria. All Rights Reserved.
 * @license          MIT License; See LICENSE.md and COPYRIGHT.md in the root directory.
 * @package          PH7 / Framework / Mvc / Controller
 * @version          1.0
 */

declare(strict_types=1);

namespace PH7\Framework\Mvc\Controller;

defined('PH7') or exit('Restricted access');

// The prototypes of the methods
interface Controllable
{
    /**
     * Output Stream Views.
     *
     * @param string|null $sFile Specify another display file instead of the default layout file.
     *
     * @return void
     */
    public function output($sFile): void;

    /**
     * Set a Not Found Error Message with HTTP 404 Code Status.
     *
     * @param string $sMsg Optionally, a customized message.
     * @param bool $b404Status For the Ajax blocks and others, we cannot put the HTTP 404 error code, so the attribute must be set to FALSE.
     *
     * @return void Quits the page with the exit() function
     */
    public function displayPageNotFound(string $sMsg = '', bool $b404Status = true): void;
}
