<?php

/**
 * @author         Pierre-Henry Soria <hello@ph7builder.com>
 * @copyright      (c) 2013-2019, Pierre-Henry Soria. All Rights Reserved.
 * @license        MIT License; See LICENSE.md and COPYRIGHT.md in the root directory.
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

        $mMod = $_GET['mod'] ?? null;
        $mCurrentName = $_GET['name'] ?? null;
        $mName = $_POST['name'] ?? null;
        if (
            !is_string($mMod)
            || !in_array($mMod, ['user', 'aff'], true)
            || !FieldModel::isValidColumnName($mCurrentName)
            || !FieldModel::isValidColumnName($mName)
        ) {
            \PFBC\Form::setError('form_edit_field', t('Please enter a valid field name.'));

            return;
        }

        $sMod = $mMod;
        $sCurrentName = $mCurrentName;
        $sName = $mName;
        $sType = $this->httpRequest->post('type');
        $iLength = $this->httpRequest->post('length');
        $sDefVal = $this->httpRequest->post('value');

        $bCurrentNameProtected = Field::unmodifiable($sMod, $sCurrentName);
        $bSubmittedNameProtected = Field::unmodifiable($sMod, $sName);

        if ($bCurrentNameProtected || $bSubmittedNameProtected) {
            $sProtectedName = $bCurrentNameProtected ? $sCurrentName : $sName;
            \PFBC\Form::setError(
                'form_edit_field',
                t('Wrong field name submitted. %0% cannot be modified.', $sProtectedName)
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
