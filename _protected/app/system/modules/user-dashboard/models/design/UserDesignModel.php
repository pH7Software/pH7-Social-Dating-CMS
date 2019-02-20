<?php
/**
 * @author         Pierre-Henry Soria <hello@ph7cms.com>
 * @copyright      (c) 2016-2019, Pierre-Henry Soria. All Rights Reserved.
 * @license        GNU General Public License; See PH7.LICENSE.txt and PH7.COPYRIGHT.txt in the root directory.
 * @package        PH7 / App / System / Module / User Dashboard / Model / Design
 */

namespace PH7;

class UserDesignModel extends UserDesignCoreModel
{
    const PROFILE_BLOCK_LIMIT = 36;
    const PROFILE_BLOCK_AVATAR_SIZE = 64;

    public function profilesBlock($iOffset = 0, $iLimit = self::PROFILE_BLOCK_LIMIT)
    {
        $iSize = self::PROFILE_BLOCK_AVATAR_SIZE;
        $oUser = $this->oUserModel->getProfiles(SearchCoreModel::LATEST, $iOffset, $iLimit);

        if (!empty($oUser)) {
            echo '<ul class="zoomer_pic">';

            foreach ($oUser as $oRow) {
                $sFirstName = $this->oStr->upperFirst($oRow->firstName);
                $sCity = $this->oStr->upperFirst($oRow->city);
                $sTitleInfo = t('Meet %0%, from %1%', $oRow->username, $sCity);

                echo '<li>
                    <a href="', $this->oUser->getProfileSignupLink($oRow->username, $sFirstName, $oRow->sex), '">
                        <img src="', $this->getUserAvatar($oRow->username, $oRow->sex, $iSize), '" alt="', $sTitleInfo, '" title="', $sTitleInfo, '" width="', $iSize, '" height="', $iSize, '" class="avatar" />
                    </a>
                </li>';
            }

            echo '</ul>';
        }
    }
}
