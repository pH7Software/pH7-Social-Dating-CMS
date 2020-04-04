<?php
/**
 * @author         Pierre-Henry Soria <hello@ph7cms.com>
 * @copyright      (c) 2016-2019, Pierre-Henry Soria. All Rights Reserved.
 * @license        GNU General Public License; See PH7.LICENSE.txt and PH7.COPYRIGHT.txt in the root directory.
 * @package        PH7 / App / System / Module / Two-Factor Auth / Model
 */

namespace PH7;

class TwoFactorAuthModel extends TwoFactorAuthCoreModel
{
    /**
     * @param int $iIsEnabled 1 = Enabled | 0 = Disabled
     * @param int $iProfileId Profile ID.
     *
     * @return int|bool Returns the number of rows on success or FALSE on failure.
     */
    public function setStatus($iIsEnabled, $iProfileId)
    {
        $iIsEnabled = (string)$iIsEnabled; // Need to be string because in DB it's an "enum" type

        return $this->orm->update($this->sTable, 'isTwoFactorAuth', $iIsEnabled, 'profileId', $iProfileId);
    }

    /**
     * @param string $sSecret 2FA secret code.
     * @param int $iProfileId Profile ID.
     *
     * @return int|bool Returns the number of rows on success or FALSE on failure.
     */
    public function setSecret($sSecret, $iProfileId)
    {
        return $this->orm->update($this->sTable, 'twoFactorAuthSecret', $sSecret, 'profileId', $iProfileId);
    }

    /**
     * @param int $iProfileId Profile ID.
     *
     * @return string The 2FA secret code.
     */
    public function getSecret($iProfileId)
    {
        return $this->orm->getOne($this->sTable, 'profileId', $iProfileId, 'twoFactorAuthSecret')->twoFactorAuthSecret;
    }
}
