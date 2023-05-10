<?php
/**
 * @author         Pierre-Henry Soria <hello@ph7builder.com>
 * @copyright      (c) 2012-2019, Pierre-Henry Soria. All Rights Reserved.
 * @license        MIT License; See LICENSE.md and COPYRIGHT.md in the root directory.
 * @package        PH7 / App / System / Module / Admin / From
 */

namespace PH7;

defined('PH7') or exit('Restricted access');

use PFBC\Element\Button;
use PFBC\Element\Hidden;
use PFBC\Element\Textarea;
use PFBC\Element\Token;
use PH7\Framework\Url\Header;
use RuntimeException;

class PublicFileForm
{
    public static function display(): void
    {
        if (isset($_POST['submit_file'])) {
            if (\PFBC\Form::isValid($_POST['submit_file'])) {
                new PublicFileFormProcess;
            }

            Header::redirect();
        }

        try {
            if (!$rData = @file_get_contents(self::getRealPath())) {
                // First, remove the previous error message (if existing) to avoid duplicate error messages
                \PFBC\Form::clearErrors('form_file');

                \PFBC\Form::setError('form_file', t('The following requested file was not found: %0%', escape(PH7_PATH_ROOT . $_GET['file'])));
            }

            $oForm = new \PFBC\Form('form_file');
            $oForm->configure(['action' => '']);
            $oForm->addElement(new Hidden('submit_file', 'form_file'));
            $oForm->addElement(new Token('file'));
            $oForm->addElement(
                new Textarea(
                    t('File Contents'),
                    'content',
                    [
                        'value' => $rData,
                        'style' => 'height:50rem',
                        'required' => 1
                    ]
                )
            );
            $oForm->addElement(new Button(t('Save')));
            $oForm->render();
        } catch (RuntimeException $oExcept) {
            self::showErrorMessage($oExcept);
        }
    }

    /**
     * Get the full file path and prevent path traversal and null byte attacks.
     *
     * @return string|bool The canonicalized absolute path, or FALSE on failure.
     */
    private static function getRealPath(): string|bool
    {
        $sFullPath = PH7_PATH_ROOT . $_GET['file'];
        $mRealPublicPath = realpath(PH7_PATH_ROOT);
        $mRealFullPath = realpath($sFullPath);

        if ($mRealFullPath === false || strpos($mRealFullPath, $mRealPublicPath) !== 0) {
            throw new RuntimeException(
                t('Invalid specified path, not authorized by the system!')
            );
        }

        return $mRealFullPath;
    }

    private static function showErrorMessage(RuntimeException $oExcept): void
    {
        printf('<p class="col-md-6 col-md-offset-4 red">%s</p>', $oExcept->getMessage());
    }
}
