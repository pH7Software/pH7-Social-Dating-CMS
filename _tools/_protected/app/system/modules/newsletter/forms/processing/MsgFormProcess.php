<?php
/**
 * @author         Pierre-Henry Soria <hello@ph7cms.com>
 * @copyright      (c) 2012-2019, Pierre-Henry Soria. All Rights Reserved.
 * @license        GNU General Public License; See PH7.LICENSE.txt and PH7.COPYRIGHT.txt in the root directory.
 * @package        PH7 / App / System / Module / Newsletter / Form / Processing
 */

namespace PH7;

use Swift_RfcComplianceException;

defined('PH7') or exit('Restricted access');

class MsgFormProcess
{
    public function __construct()
    {
        try {
            $aData = (new Newsletter)->sendMessages();

            if (!$aData['status']) {
                \PFBC\Form::setError('form_msg', Form::errorSendingEmail());
            } else {
                \PFBC\Form::setSuccess(
                    'form_msg',
                    nt(
                        '%n% newsletter has been successfully sent',
                        '%n% newsletters were successfully sent!',
                        $aData['nb_mail_sent']
                    )
                );
            }
        } catch (Swift_RfcComplianceException $oE) {
            \PFBC\Form::setError('form_msg', $oE->getMessage());
        }
    }
}
