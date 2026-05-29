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
use PFBC\Element\HTMLExternal;
use PFBC\Element\Textarea;
use PFBC\Element\Token;
use PH7\Framework\File\File;
use PH7\Framework\Layout\Tpl\Engine\PH7Tpl\PH7Tpl;
use PH7\Framework\Url\Header;
use PH7\Framework\Security\Ban\Ban;
use PH7\Framework\Service\Suggestion;
use RuntimeException;

class ProtectedFileForm
{
    private const TERMS_FILENAME = 'terms.tpl';
    private const PRIVACY_FILENAME = 'privacy.tpl';

    private const ALLOWED_PATHS = [
        [
            'root' => PH7_PATH_SYS . 'global' . PH7_DS . PH7_VIEWS . PH7_TPL_MAIL_NAME . PH7_DS . 'tpl' . PH7_DS . 'mail' . PH7_DS,
            'extensions' => ['.tpl']
        ],
        [
            'root' => PH7_PATH_APP_CONFIG . Ban::DIR,
            'extensions' => [Ban::EXT]
        ],
        [
            'root' => PH7_PATH_APP_CONFIG . Suggestion::DIR,
            'extensions' => [Suggestion::EXT]
        ],
        [
            'root' => PH7_PATH_SYS_MOD . 'page' . PH7_DS . PH7_VIEWS . PH7_TPL_MOD_NAME,
            'extensions' => [PH7Tpl::TEMPLATE_FILE_EXT]
        ]
    ];

    public static function display(): void
    {
        if (isset($_POST['submit_file'])) {
            if (\PFBC\Form::isValid($_POST['submit_file'])) {
                new ProtectedFileFormProcess;
            }

            Header::redirect();
        }

        try {
            $sFullPath = self::getRealPath();

            if (!$rData = @file_get_contents($sFullPath)) {
                // First, remove the previous error message (if existing) to avoid duplicate error messages
                \PFBC\Form::clearErrors('form_file');

                \PFBC\Form::setError('form_file', t('The following requested file was not found: %0%', escape(PH7_PATH_PROTECTED . ($_GET['file'] ?? ''))));
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
            if (self::isLegalPage($sFullPath)) {
                $oForm->addElement(
                    new HTMLExternal(
                        '<p class="red">' .
                        t('There is no warranty that the default terms/privacy pages meets the legal requirements for your website.') . '<br />' .
                        t('You need to review it and modify it if needed.') .
                        '</p>'
                    )
                );
            }
            $oForm->addElement(new Button(t('Save')));
            $oForm->render();
        } catch (RuntimeException $oExcept) {
            self::showErrorMessage($oExcept);
        }
    }

    private static function isLegalPage(string $sFullPath): bool
    {
        $cIsFound = static function ($sPageFilename) use ($sFullPath) {
            return strpos($sFullPath, $sPageFilename) !== false;
        };

        return $cIsFound(self::TERMS_FILENAME) || $cIsFound(self::PRIVACY_FILENAME);
    }

    public static function getRealPath(?string $sFile = null): string
    {
        $sRequestedFile = (string)($sFile ?? ($_GET['file'] ?? ''));
        $sFullPath = PH7_PATH_PROTECTED . $sRequestedFile;
        $mRealFullPath = realpath($sFullPath);

        if (self::doesNotResolveToExistingProtectedFile($mRealFullPath)) {
            throw new RuntimeException(
                t('Invalid specified path, not authorized by the system!')
            );
        }

        foreach (self::ALLOWED_PATHS as $aAllowedPath) {
            $mRealProtectedPath = realpath($aAllowedPath['root']);

            if (self::isAllowedEditableProtectedFilePath($mRealProtectedPath, $mRealFullPath, $aAllowedPath['extensions'])) {
                return $mRealFullPath;
            }
        }

        throw new RuntimeException(
            t('Invalid specified path, not authorized by the system!')
        );
    }

    private static function doesNotResolveToExistingProtectedFile(string|bool $mRealFullPath): bool
    {
        return $mRealFullPath === false || !is_file($mRealFullPath);
    }

    private static function isAllowedEditableProtectedFilePath(
        string|bool $mRealProtectedPath,
        string|bool $mRealFullPath,
        array $aAllowedExtensions
    ): bool {
        return $mRealProtectedPath !== false &&
            $mRealFullPath !== false &&
            File::isPathInsideDirectory($mRealFullPath, $mRealProtectedPath) &&
            is_file($mRealFullPath) &&
            in_array(File::getFileExtWithDot($mRealFullPath), $aAllowedExtensions, true);
    }

    private static function showErrorMessage(RuntimeException $oExcept): void
    {
        printf('<p class="col-md-6 col-md-offset-4 red">%s</p>', $oExcept->getMessage());
    }
}
