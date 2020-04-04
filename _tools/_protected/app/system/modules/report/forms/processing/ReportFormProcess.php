<?php
/**
 * @author         Pierre-Henry Soria <hello@ph7cms.com>
 * @copyright      (c) 2012-2019, Pierre-Henry Soria. All Rights Reserved.
 * @license        GNU General Public License; See PH7.LICENSE.txt and PH7.COPYRIGHT.txt in the root directory.
 * @package        PH7 / App / System / Module / Report / Form / Processing
 */

namespace PH7;

defined('PH7') or exit('Restricted access');

class ReportFormProcess extends Form
{
    public function __construct()
    {
        parent::__construct();

        $sUrl = $this->getUrl();
        $mNeedle = strstr($sUrl, '?', true);
        $aData = [
            'reporter_id' => $this->session->get('member_id'),
            'spammer_id' => $this->httpRequest->post('spammer'),
            'url' => ($mNeedle ? $mNeedle : $sUrl),
            'type' => $this->httpRequest->post('type'),
            'desc' => $this->httpRequest->post('desc'),
            'date' => $this->dateTime->get()->dateTime('Y-m-d H:i:s')
        ];

        $mReport = (new Report($this->view))->add($aData)->get();

        unset($aData);


        if ($mReport === 'already_reported') {
            \PFBC\Form::setError('form_report', t('You have already reported abuse about this profile.'));
        } elseif (!$mReport) {
            \PFBC\Form::setError('form_report', t('Unable to report abuse.'));
        } else {
            \PFBC\Form::setSuccess('form_report', t('You have successfully reported abuse about this profile.'));
        }
    }

    /**
     * @return string
     */
    private function getUrl()
    {
        return $this->httpRequest->postExists('url') ?
            $this->httpRequest->post('url') :
            $this->httpRequest->currentUrl();
    }
}
