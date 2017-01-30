<?php
/**
 * @author           Pierre-Henry Soria <ph7software@gmail.com>
 * @copyright        (c) 2011-2017, Pierre-Henry Soria. All Rights Reserved.
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
     * @return void
     */
    public function output();

    /**
     * Not Found Error Page.
     *
     * @param string $sMsg Default is empty ('')
     * @param boolean $b404Status Default is "true"
     * @return void
     */
    public function displayPageNotFound($sMsg = '', $b404Status = true);

}
