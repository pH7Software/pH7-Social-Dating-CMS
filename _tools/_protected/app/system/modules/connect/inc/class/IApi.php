<?php
/**
 * @title          Interface Api Class
 *
 * @author         Pierre-Henry Soria <hello@ph7cms.com>
 * @copyright      (c) 2012-2019, Pierre-Henry Soria. All Rights Reserved.
 * @license        GNU General Public License; See PH7.LICENSE.txt and PH7.COPYRIGHT.txt in the root directory.
 * @package        PH7 / App / System / Module / Connect / Inc / Class
 * @version        1.1
 */

namespace PH7;

defined('PH7') or exit('Restricted access');

interface IApi
{

    /**
     * Set an user authentication.
     *
     * @param int $iId
     * @param UserCoreModel $oUserModel
     *
     * @return void
     */
    public function setLogin($iId, UserCoreModel $oUserModel);

    /**
     * Set Avatar.
     *
     * @param string $sUrl URL of avatar.
     *
     * @return void
     */
    public function setAvatar($sUrl);

    /**
     * Get Avatar.
     *
     * @param string $sUrl
     *
     * @return string The Avatar
     */
    public function getAvatar($sUrl);
}
