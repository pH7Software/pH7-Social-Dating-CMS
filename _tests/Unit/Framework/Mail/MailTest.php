<?php

/**
 * @author         Pierre-Henry Soria <hello@ph7builder.com>
 * @copyright      (c) 2026, Pierre-Henry Soria and pH7Builder contributors.
 * @license        MIT License; See LICENSE.md and COPYRIGHT.md in the root directory.
 * @package        PH7 / Test / Unit / Framework / Mail
 */

declare(strict_types=1);

namespace PH7\Test\Unit\Framework\Mail;

use PH7\Framework\Mail\Mail;
use PH7\Framework\Mail\Mailable;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;
use ReflectionMethod;
use Symfony\Component\Mailer\Transport\NullTransport;
use Symfony\Component\Mailer\Transport\SendmailTransport;
use Symfony\Component\Mailer\Transport\TransportInterface;

final class MailTest extends TestCase
{
    protected function tearDown(): void
    {
        putenv('PH7_MAILER_DSN');
    }

    public function testSendmailTransportRemainsTheDefault(): void
    {
        putenv('PH7_MAILER_DSN');
        $oMail = new TestableMail;

        $this->assertNull($oMail->mailerDsn());
        $this->assertInstanceOf(SendmailTransport::class, $oMail->transport(null));
    }

    public function testEnvironmentDsnSelectsConfiguredTransport(): void
    {
        putenv('PH7_MAILER_DSN=null://null');
        $oMail = new TestableMail;

        $sMailerDsn = $oMail->mailerDsn();

        $this->assertSame('null://null', $sMailerDsn);
        $this->assertInstanceOf(NullTransport::class, $oMail->transport($sMailerDsn));
    }

    public function testInterfaceKeepsTheDefaultMessageFormat(): void
    {
        $oFormatParameter = (new ReflectionMethod(Mailable::class, 'send'))->getParameters()[2];

        $this->assertTrue($oFormatParameter->isDefaultValueAvailable());
        $this->assertSame(Mailable::ALL_FORMATS, $oFormatParameter->getDefaultValue());
    }

    #[DataProvider('unsafeMessageProvider')]
    public function testUnsafeMessageIsRejectedBeforeAnyTransportOrFallback(string $sField, string $sValue): void
    {
        $aInfo = [
            'from' => 'sender@example.com',
            'form_name' => 'Sender',
            'to' => 'recipient@example.com',
            'to_name' => 'Recipient',
            'subject' => 'Safe subject'
        ];
        $aInfo[$sField] = $sValue;
        $oMail = new BoundaryMail();

        $this->assertFalse($oMail->send($aInfo, '<p>Body</p>'));
        $this->assertSame(0, $oMail->iTransportCalls);
        $this->assertSame(0, $oMail->iFallbackCalls);
    }

    public static function unsafeMessageProvider(): array
    {
        return [
            'invalid sender' => ['from', 'not-an-email'],
            'sender header injection' => ['from', "sender@example.com\r\nBcc: victim@example.com"],
            'invalid recipient' => ['to', 'not-an-email'],
            'recipient header injection' => ['to', "recipient@example.com\nBcc: victim@example.com"],
            'subject header injection' => ['subject', "Subject\r\nBcc: victim@example.com"]
        ];
    }

    public function testNativeFallbackKeepsVisitorAddressOutOfFromHeader(): void
    {
        $oMail = new FallbackMail();

        $this->assertTrue(
            $oMail->send(
                [
                    'from' => 'visitor@external.example',
                    'form_name' => 'Site visitor',
                    'to' => 'recipient@example.com',
                    'subject' => 'Contact request'
                ],
                '<p>Body</p>'
            )
        );
        $this->assertSame('site@example.com', $oMail->aFallbackData['from']);
        $this->assertSame('visitor@external.example', $oMail->aFallbackData['reply_to']);
    }
}

final class BoundaryMail extends Mail
{
    public int $iTransportCalls = 0;
    public int $iFallbackCalls = 0;

    protected function createTransport(?string $sMailerDsn): TransportInterface
    {
        ++$this->iTransportCalls;

        return new NullTransport();
    }

    protected function phpMail(array $aParams): bool
    {
        ++$this->iFallbackCalls;

        return true;
    }

    protected function getConfiguredFromAddress(): mixed
    {
        return 'site@example.com';
    }

    protected function getConfiguredFromName(): mixed
    {
        return 'Example site';
    }

    protected function logTransportError(\Throwable $oException, bool $bUsesConfiguredTransport): void
    {
    }
}

final class FallbackMail extends Mail
{
    public array $aFallbackData = [];

    protected function createTransport(?string $sMailerDsn): TransportInterface
    {
        throw new \RuntimeException('Expected transport failure for fallback test.');
    }

    protected function phpMail(array $aParams): bool
    {
        $this->aFallbackData = $aParams;

        return true;
    }

    protected function getConfiguredFromAddress(): mixed
    {
        return 'site@example.com';
    }

    protected function getConfiguredFromName(): mixed
    {
        return 'Example site';
    }

    protected function logTransportError(\Throwable $oException, bool $bUsesConfiguredTransport): void
    {
    }
}

final class TestableMail extends Mail
{
    public function mailerDsn(): ?string
    {
        return $this->getMailerDsn();
    }

    public function transport(?string $sMailerDsn): TransportInterface
    {
        return $this->createTransport($sMailerDsn);
    }
}
