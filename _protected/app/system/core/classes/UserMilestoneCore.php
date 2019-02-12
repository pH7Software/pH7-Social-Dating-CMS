<?php
/**
 * @author         Pierre-Henry Soria <hi@ph7.me>
 * @copyright      (c) 2018-2019, Pierre-Henry Soria. All Rights Reserved.
 * @license        GNU General Public License; See PH7.LICENSE.txt and PH7.COPYRIGHT.txt in the root directory.
 * @package        PH7 / App / System / Core / Class
 */

namespace PH7;

class UserMilestoneCore
{
    const MILLENARIAN_WEBSITE = 1000;

    const NUMBER_USERS = [
        100,
        500,
        1000,
        2500,
        5000,
        7500,
        10000,
        25000,
        50000,
        100000,
        250000,
        500000,
        1000000 // Congrats!
    ];

    /** @var UserCoreModel */
    private $oUserModel;

    public function __construct(UserCoreModel $oUserModel)
    {
        $this->oUserModel = $oUserModel;
    }

    /**
     * @return bool
     */
    public function isTotalUserReached()
    {
        return in_array(
            $this->oUserModel->total(),
            self::NUMBER_USERS,
            true
        );
    }
}
