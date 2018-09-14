<?php
/**
 * @author         Pierre-Henry Soria <hello@ph7cms.com>
 * @copyright      (c) 2018, Pierre-Henry Soria. All Rights Reserved.
 * @license        GNU General Public License; See PH7.LICENSE.txt and PH7.COPYRIGHT.txt in the root directory.
 * @package        PH7 / App / System / Module / Milestone Celebration / Inc / Class
 */

namespace PH7;

class MilestoneNotifier
{
    const MAIL_TEMPLATE_FILENAME = '/global/mail/mod/milestone-celebration/admin-notifier.tpl';

    private $oUserModel;

    private $oMail;

     private $oView;


      public function __construct(UserCoreModel $oUserModel, Mailable $oMail, PH7Tpl $oView)
      {
          $this->oMail = $oMail;
          $this->oUserModel = $oUserModel;
          $this->oView = $oView;
      }

      public function sendEmailToAdmin()
      {
          $this->oView->subject


          $aInfo = [
              ‘subject’ => t('Your Website Reached % 0 % users!🍾', $this->oUserModel->total())

          ];

           return $this->oMail->send($aInfo, $mailContents, Mailable::HTML_FORMAT);
    }
}
