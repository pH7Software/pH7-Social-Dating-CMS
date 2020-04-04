<?php
/**
 * @author         Pierre-Henry Soria <hello@ph7cms.com>
 * @copyright      (c) 2019, Pierre-Henry Soria. All Rights Reserved.
 * @license        GNU General Public License; See PH7.LICENSE.txt and PH7.COPYRIGHT.txt in the root directory.
 * @package        PH7 / App / System / Module / Forum / Inc / Class
 */

namespace PH7;

use PFBC\Element\CKEditor;
use PFBC\Element\Textarea;
use PH7\Framework\Mvc\Model\DbConfig;

class FormHelper
{
    /**
     * @return string
     */
    public static function getEditorPfbcClassName()
    {
        if (DbConfig::getSetting('wysiwygEditorForum')) {
            return CKEditor::class;
        }

        return Textarea::class;
    }
}
