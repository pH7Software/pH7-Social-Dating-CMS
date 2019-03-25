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
    /** @var int */
    private $iProfileType;

    /**
     * @param int $iProfileType The profile type to generate.
     *
     * @throws Framework\Mvc\Request\WrongRequestMethodException
     */
    public function __construct($iProfileType)
    {
        parent::__construct();

        $this->iProfileType = $iProfileType;
        $iAmountProfiles = $this->httpRequest->post('amount', 'int');

        $this->generate($iAmountProfiles);

        \PFBC\Form::setSuccess(
            'form_generate_profiles',
            nt('%n% profile has been generated.', '%n% profiles have been generated.', $iAmountProfiles)
        );
    }

    /**
     * @param int $iAmountProfiles
     *
     * @throws Framework\Mvc\Request\WrongRequestMethodException
     */
    private function generate($iAmountProfiles)
    {
        $oFakerFactory = new FakerFactory(
            $iAmountProfiles,
            $this->httpRequest->post('sex'),
            $this->httpRequest->post('locale')
        );

        switch ($this->iProfileType) {
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
