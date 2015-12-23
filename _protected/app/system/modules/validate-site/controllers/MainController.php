<?php
/**
 * @author         Pierre-Henry Soria <hello@ph7cms.com>
 * @copyright      (c) 2015, Pierre-Henry Soria. All Rights Reserved.
 * @license        GNU General Public License; See PH7.LICENSE.txt and PH7.COPYRIGHT.txt in the root directory.
 * @package        PH7 / App / System / Module / Validate Site / Controller
 */
namespace PH7;

use PH7\Framework\Url\Header;

class MainController extends Controller
{
    private $oValidateModel;

    public function __construct()
    {
        parent::__construct();

        $this->oValidateModel = new ValidateSiteModel;
    }

    public function validationBox()
    {
        // Display the form box only if the site isn't validated yet
        if (!$this->oValidateModel->is()) {
            $this->view->page_title = t('Validate your Site');
            $this->session->set(ValidateSiteCore::SESS_IS_VISITED, 1);
            $this->output();
        } else {
            $this->displayPageNotFound(t('Whoops! It appears the site has already been activated.'));
        }
    }

    public function pending()
    {
        $this->view->page_title = t('Pending Status: Please confirm your site');
        $this->view->h1_title = t('We sent an email. Please confirm your Site');
        $this->output();
    }

    public function validator($sHash)
    {
        if (!empty($sHash) && $this->checkHash($sHash)) {
            $this->oValidateModel->set();
            Header::redirect(PH7_ADMIN_MOD, t('Congrats! Your site has now the Published status and you will be aware by email to any security patches or updates.'), 'success');
        }
        Header::redirect(PH7_ADMIN_MOD, t('The hash is incorrect. Please copy/paste the hash link received in your email in Web browser URL bar.'), 'error');
    }

    protected function checkHash($sHash)
    {
        return (md5('681cd81b17b71c746e9ab7ac0445d3a3c960c329') === substr($sHash,3,24));
    }
}
