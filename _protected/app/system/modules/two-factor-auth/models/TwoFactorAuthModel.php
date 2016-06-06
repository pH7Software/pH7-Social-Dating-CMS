<?php
/**
 * @author         Pierre-Henry Soria <hello@ph7cms.com>
 * @copyright      (c) 2016, Pierre-Henry Soria. All Rights Reserved.
 * @license        GNU General Public License; See PH7.LICENSE.txt and PH7.COPYRIGHT.txt in the root directory.
 * @package        PH7 / App / System / Module / Two-Factor Auth / Model
 */

namespace PH7;

class TwoFactorAuthModel extends TwoFactorAuthCoreModel
{
	/**
	 * @param integer $iIsEnabled 1 = Enabled | 0 = Disabled
	 * @return integer|boolean Returns the number of rows on success or FALSE on failure.
	 */
    public function setStatus($iIsEnabled)
    {
        return $this->orm->update($this->sTable, 'isTwoFactorAuth', $iIsEnabled);
    }

    /**
	 * @param string $sSecret 2FA secret code.
	 * @return integer|boolean Returns the number of rows on success or FALSE on failure.
	 */
    public function setSecret($sSecret)
    {
        return $this->orm->update($this->sTable, 'twoFactorAuthSecret', $sSecret);
    }

    /**
	 * @param integer $iProfileId
	 * @return string The 2FA secret code.
	 */
    public function getSecret($iProfileId)
    {
        return $this->orm->getOne($this->sTable, 'profileId', $iProfileId, 'twoFactorAuthSecret')->licenseKey;
    }
}
