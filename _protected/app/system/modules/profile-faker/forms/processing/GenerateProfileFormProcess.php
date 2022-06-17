<?php
/**
 * @author         Pierre-Henry Soria <hello@ph7builder.com>
 * @copyright      (c) 2019-2022, Pierre-Henry Soria. All Rights Reserved.
 * @license        MIT License; See LICENSE.md and COPYRIGHT.md in the root directory.
 * @package        PH7 / App / System / Module / Profile Faker / Form / Processing
 */

declare(strict_types=1);

namespace PH7;

defined('PH7') or exit('Restricted access');

class GenerateProfileFormProcess extends Form
{
    private int $iProfileType;

    /**
     * @param int $iProfileType The profile type to generate.
     *
     * @throws Framework\Mvc\Request\WrongRequestMethodException
     */
    public function __construct(int $iProfileType)
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
     * @throws Framework\Mvc\Request\WrongRequestMethodException
     */
    private function generate(int $iAmountProfiles): void
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
