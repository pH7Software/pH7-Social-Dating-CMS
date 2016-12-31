<?php
 /**
 * @title          Set User Last Activity
 *
 * @author         Pierre-Henry Soria <ph7software@gmail.com>
 * @copyright      (c) 2012-2017, Pierre-Henry Soria. All Rights Reserved.
 * @license        GNU General Public License; See PH7.LICENSE.txt and PH7.COPYRIGHT.txt in the root directory.
 * @package        PH7 / App / System / Module / User / Asset / Ajax
 * @version        1.0
 */
namespace PH7;
defined('PH7') or exit('Restricted access');

// Only for members
if(UserCore::auth())
{
    (new UserCoreModel)->setLastActivity((new Framework\Session\Session)->get('member_id'));
}
