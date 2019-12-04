<?php
/**
 * @author         Pierre-Henry Soria <hello@ph7cms.com>
 * @copyright      (c) 2013-2019, Pierre-Henry Soria. All Rights Reserved.
 * @license        GNU General Public License; See PH7.LICENSE.txt and PH7.COPYRIGHT.txt in the root directory.
 * @package        PH7 / App / System / Module / Field / Form / Processing
 */

namespace PH7;

defined('PH7') or exit('Restricted access');

use PH7\Framework\Mvc\Router\Uri;
use PH7\Framework\Url\Header;

class EditFieldFormProcess extends Form
{
    public function __construct()
    {
        parent::__construct();

        $sMod = $this->httpRequest->get('mod');
        $sName = $this->httpRequest->post('name');
        $sType = $this->httpRequest->post('type');
        $iLength = $this->httpRequest->post('length');
        $sDefVal = $this->httpRequest->post('value');

        if (Field::unmodifiable($sMod, $sName)) {
            \PFBC\Form::setError(
                'form_edit_field',
                t('Wrong field name submitted. %0% cannot be modified.', $sName)
            );
        } else {
            $bRet = (new FieldModel(Field::getTable($sMod), $sName, $sType, $iLength, $sDefVal))->update();

            if ($bRet) {
                Field::clearCache();

                Header::redirect(
                    Uri::get('field', 'field', 'all', $sMod),
                    t('The field has been edited.')
                );
            } else {
                \PFBC\Form::setError(
                    'form_edit_field',
                    t('Oops! An error occurred while editing the field. Please try again.')
                );
            }
        }
    }
}
