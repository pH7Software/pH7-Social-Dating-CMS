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
    private $sProfileType;

    /**
     * @param int $sProfileType The profile type to generate.
     *
     * @throws Framework\Mvc\Request\WrongRequestMethodException
     */
    public function __construct($sProfileType)
    {
        parent::__construct();

        $this->sProfileType = $sProfileType;
        $this->generate();

        \PFBC\Form::setSuccess(
            'form_generate_profiles',
            nt('%n% profile has been generated.', '%n% profiles have been generated.', $this->httpRequest->post('amount', 'int'))
        );
    }

    private function generate()
    {
        $oFakerFactory = new FakerFactory(
            $this->httpRequest->post('amount', 'int')
        );

        switch ($this->sProfileType) {
            case ProfileType::BUYER:
                $oFakerFactory->generateBuyers();
                break;

            case ProfileType::SELLER:
                $oFakerFactory->generateSellers();
                break;
        }
    }
}
