<?php
/**
 * @author         Pierre-Henry Soria <hello@ph7cms.com>
 * @copyright      (c) 2019, Pierre-Henry Soria. All Rights Reserved.
 * @license        GNU General Public License; See PH7.LICENSE.txt and PH7.COPYRIGHT.txt in the root directory.
 * @package        PH7 / App / System / Module / Profile Faker / Form / Processing
 */

namespace PH7;

defined('PH7') or exit('Restricted access');

class GenerateProfileFormProcess extends Form
{
    /** @var string */
    private $sProfileType;

    /**
     * @param string $sProfileType The profile type to generate.
     */
    public function __construct($sProfileType)
    {
        $this->sProfileType = $sProfileType;
        $this->generate();
    }

    private function generate()
    {
        $oFakerFactory = new FakerFactory(
            $this->httpRequest->post('amount'),
            $this->httpRequest->post('locale')
        );

        switch ($this->sProfileType) {
            case ProfileType::MEMBER:
                $oFakerFactory->generateMembers();
                break;

            case ProfileType::AFFILIATE:
                $oFakerFactory->generateAffiliates();
                break;

            case ProfileType::SUBSCRIBER:
                $oFakerFactory->generateSubscribers();
                break;
        }
    }
}
