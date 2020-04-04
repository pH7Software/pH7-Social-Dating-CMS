<?php
/**
 * @author         Pierre-Henry Soria <hello@ph7cms.com>
 * @copyright      (c) 2012-2019, Pierre-Henry Soria. All Rights Reserved.
 * @license        GNU General Public License; See PH7.LICENSE.txt and PH7.COPYRIGHT.txt in the root directory.
 * @package        PH7 / App / System / Module / Contact / Form / Processing
 */

namespace PH7;

class ContactFormProcess
{
    public function __construct()
    {
        $bSend = (new Contact)->sendMessage();

        if (!$bSend) {
            \PFBC\Form::setError('form_contact', Form::errorSendingEmail());
        } else {
            \PFBC\Form::setSuccess('form_contact', t('Your message has been successfully sent!'));
        }
    }
}
