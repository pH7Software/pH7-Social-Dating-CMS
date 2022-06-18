<?php
/**
 * @author         Pierre-Henry Soria <hi@ph7.me>
 * @copyright      (c) 2018-2022, Pierre-Henry Soria. All Rights Reserved.
 * @license        MIT License; See LICENSE.md and COPYRIGHT.md in the root directory.
 * @package        PH7 / App / System / Core / Class
 */

declare(strict_types=1);

namespace PH7;

class UserMilestoneCore
{
    public const MILLENARIAN = 1000;

    private const NUMBER_USERS = [
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

    private UserCoreModel $oUserModel;

    public function __construct(UserCoreModel $oUserModel)
    {
        $this->oUserModel = $oUserModel;
    }

    public function isTotalUserReached(): bool
    {
        return in_array(
            $this->oUserModel->total(),
            self::NUMBER_USERS,
            true
        );
    }
}
