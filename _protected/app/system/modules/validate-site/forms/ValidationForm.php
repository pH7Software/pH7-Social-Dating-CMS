<?php
/**
 * @author         Pierre-Henry Soria <hello@ph7cms.com>
 * @copyright      (c) 2015-2017, Pierre-Henry Soria. All Rights Reserved.
 * @license        GNU General Public License; See PH7.LICENSE.txt and PH7.COPYRIGHT.txt in the root directory.
 * @package        PH7 / App / System / Module / Validate Form / Form
 */
namespace PH7;

use PH7\Framework\Mvc\Router\Uri;

class ValidationForm extends Form
{
    public function __construct()
    {
        parent::__construct();

        $oForm = new \PFBC\Form('form_validation', '350px');
        $oForm->configure(array('action' => $this->config->values['module.setting']['remote_url']));
        $oForm->addElement(new \PFBC\Element\Hidden('ph7cmsurl', PH7_URL_ROOT));
        $oForm->addElement(new \PFBC\Element\Hidden('ph7cmspendingurl', Uri::get('validate-site', 'main', 'pending')));
        $oForm->addElement(new \PFBC\Element\Hidden('ph7cmsvalidatorurl', Uri::get('validate-site', 'main', 'validator')));
        $oForm->addElement(new \PFBC\Element\Hidden('name', $this->session->get('admin_first_name')));
        $oForm->addElement(new \PFBC\Element\Textbox(t('Valid Email:'), 'email', array('id' => 'email',  'onblur' => 'CValid(this.value, this.id)', 'value' => $this->session->get('admin_email'), 'required' => 1), false));
        $oForm->addElement(new \PFBC\Element\HTMLExternal('<span class="input_error email"></span>'));
        $oForm->addElement(new \PFBC\Element\Button(t('Confirm my Site!'), 'submit'));
        $oForm->addElement(new \PFBC\Element\HTMLExternal('<script src="' . PH7_URL_STATIC . PH7_JS . 'validate.js"></script>'));
        $oForm->render();
    }
}
