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

class AddFieldFormProcess extends Form
{
    public function __construct()
    {
        parent::__construct();

        $mMod = $_GET['mod'] ?? null;
        $mName = $_POST['name'] ?? null;
        if (
            !is_string($mMod)
            || !in_array($mMod, ['user', 'aff'], true)
            || !FieldModel::isValidColumnName($mName)
        ) {
            \PFBC\Form::setError('form_add_field', t('Please enter a valid field name.'));

            return;
        }

        $sMod = $mMod;
        $sName = $mName;
        $sType = $this->httpRequest->post('type');
        $iLength = $this->httpRequest->post('length');
        $sDefVal = $this->httpRequest->post('value');

        if (Field::doesExist($sMod, $sName)) {
            \PFBC\Form::setError(
                'form_add_field',
                t('Oops! The field already exists!')
            );
        } else {
            $bRet = (new FieldModel(Field::getTable($sMod), $sName, $sType, $iLength, $sDefVal))->insert();

            if ($bRet) {
                Field::clearCache();

                Header::redirect(
                    Uri::get('field', 'field', 'all', $sMod),
                    t('The field has been added.')
                );
            } else {
                \PFBC\Form::setError(
                    'form_add_field',
                    t('Oops! An error occurred while adding the field. Please try again.')
                );
            }
        }
    }
}
