<?php
/**
 * @author           Pierre-Henry Soria <hello@ph7builder.com>
 * @copyright        (c) 2026, Pierre-Henry Soria and pH7Builder contributors.
 * @license          MIT License; See LICENSE.md and COPYRIGHT.md in the root directory.
 * @package          PH7 / Test / Unit / App / System / Module / Newsletter
 */

declare(strict_types=1);

namespace PH7\Test\Unit\App\System\Module\Newsletter;

use DOMDocument;
use LibXMLError;
use PHPUnit\Framework\TestCase;

final class NewsletterMessageTemplateTest extends TestCase
{
    /**
     * Newsletters are written as HTML paragraphs and lists. Wrapped in a <p>, they became invalid
     * nested paragraphs: mail clients showed an empty paragraph first, and converting the mail
     * to plain text logged "Unexpected end tag : p" for every recipient.
     */
    public function testMessageIsWrappedInAnElementThatAcceptsParagraphs(): void
    {
        $sTemplate = (string)file_get_contents(
            PH7_PATH_SYS . 'global/views/base/tpl/mail/sys/mod/newsletter/msg.tpl'
        );
        $sBody = (string)preg_replace('/\{inc_[a-z_]+\}/', '', $sTemplate);
        $sHtml = str_replace('{content}', '<p>New features.</p><ul><li>Faster search</li></ul>', $sBody);

        $bPreviousSetting = libxml_use_internal_errors(true);
        libxml_clear_errors();
        (new DOMDocument())->loadHTML('<html><body>' . $sHtml . '</body></html>');
        $aErrors = array_map(static fn (LibXMLError $oError): string => trim($oError->message), libxml_get_errors());
        libxml_clear_errors();
        libxml_use_internal_errors($bPreviousSetting);

        $this->assertSame([], $aErrors);
    }
}
