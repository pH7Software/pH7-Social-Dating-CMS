<?php

/**
 * @author           Pierre-Henry Soria <hello@ph7builder.com>
 * @copyright        (c) 2026, Pierre-Henry Soria. All Rights Reserved.
 * @license          MIT License; See LICENSE.md and COPYRIGHT.md in the root directory.
 */

declare(strict_types=1);

namespace PH7\Test\Unit\Root;

use Aws\S3\S3Client;
use GuzzleHttp\Client;
use GuzzleHttp\Handler\MockHandler;
use GuzzleHttp\HandlerStack;
use GuzzleHttp\Promise\Utils;
use GuzzleHttp\Psr7\Response;
use PH7\Framework\Geo\Ip\Geo;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Mailer\Transport\NullTransport;
use Symfony\Component\Mime\Email;
use Twilio\Http\Client as SmsHttpClient;
use Twilio\Http\Response as SmsResponse;
use Twilio\Rest\Client as SmsClient;

final class DependencyCompatibilityTest extends TestCase
{
    public function testProductionDependenciesHaveExplicitLicenseMetadata(): void
    {
        $sProjectPath = dirname(PH7_PATH_PROTECTED);
        $sComposer = file_get_contents($sProjectPath . '/composer.json');
        $sLock = file_get_contents($sProjectPath . '/composer.lock');

        self::assertIsString($sComposer);
        self::assertIsString($sLock);

        $aComposer = json_decode($sComposer, true, 512, JSON_THROW_ON_ERROR);
        $aLock = json_decode($sLock, true, 512, JSON_THROW_ON_ERROR);
        $aPackages = array_column($aLock['packages'], null, 'name');

        self::assertArrayNotHasKey('arcturial/clickatell', $aComposer['require']);
        self::assertArrayNotHasKey('arcturial/clickatell', $aPackages);
        self::assertArrayNotHasKey('braintree/braintree_php', $aComposer['require']);
        self::assertArrayNotHasKey('braintree/braintree_php', $aPackages);

        foreach ($aPackages as $sPackageName => $aPackage) {
            self::assertNotEmpty(
                $aPackage['license'] ?? [],
                sprintf('Production dependency %s must declare its license.', $sPackageName)
            );
        }
    }

    public function testHttpRequestsResolveThroughTheLockedPromiseStack(): void
    {
        $oHandler = new MockHandler([new Response(200, [], '{"ok":true}')]);
        $oClient = new Client(['handler' => HandlerStack::create($oHandler)]);
        $aResponses = Utils::all([$oClient->getAsync('https://example.com/check')])->wait();

        self::assertSame(200, $aResponses[0]->getStatusCode());
        self::assertSame(['ok' => true], json_decode((string)$aResponses[0]->getBody(), true));
    }

    public function testS3PresigningDoesNotNeedAProviderConnection(): void
    {
        $oClient = new S3Client([
            'version' => 'latest',
            'region' => 'us-east-1',
            'credentials' => ['key' => 'test-key', 'secret' => 'test-secret']
        ]);
        $oCommand = $oClient->getCommand('GetObject', [
            'Bucket' => 'release-test',
            'Key' => 'photos/example.jpg'
        ]);
        $oUri = $oClient->createPresignedRequest($oCommand, '+20 minutes')->getUri();

        self::assertSame('https', $oUri->getScheme());
        self::assertSame('/photos/example.jpg', $oUri->getPath());
        self::assertStringContainsString('X-Amz-Signature=', $oUri->getQuery());
    }

    public function testSmsRequestRetainsTheProviderContractWithoutSending(): void
    {
        $oHttp = $this->createMock(SmsHttpClient::class);
        $oHttp->expects(self::once())->method('request')
            ->with(
                'POST',
                self::stringContains('/2010-04-01/Accounts/'),
                [],
                self::callback(static function (array $aData): bool {
                    return $aData['To'] === '+15005550006'
                        && $aData['From'] === '+15005550001'
                        && $aData['Body'] === 'Test message';
                })
            )
            ->willReturn(new SmsResponse(201, '{"sid":"SM-test-message","status":"queued"}'));
        $oClient = new SmsClient('AC' . str_repeat('0', 32), 'test-token', null, null, $oHttp);
        $oMessage = $oClient->messages->create('+15005550006', [
            'from' => '+15005550001',
            'body' => 'Test message'
        ]);

        self::assertSame('SM-test-message', $oMessage->sid);
    }

    public function testBundledGeoDatabaseWorksWithTheUpdatedReader(): void
    {
        self::assertMatchesRegularExpression('/\A[A-Z]{2}\z/', Geo::getCountryCode('8.8.8.8'));
        self::assertNull(Geo::getCountryCode('127.0.0.1'));
    }

    public function testMimeMessageCanBeBuiltWithTheNullMailTransport(): void
    {
        $oEmail = (new Email())->from('owner@example.com')->to('member@example.com')
            ->subject('Welcome to pH7Builder')->text('Your account is ready.');
        $oMessage = (new NullTransport())->send($oEmail);

        self::assertNotNull($oMessage);
        self::assertStringContainsString('Your account is ready.', $oMessage->toString());
    }
}
