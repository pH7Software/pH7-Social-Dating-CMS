<?php

declare(strict_types=1);

namespace PH7\Test\Unit\Framework\Video\Api;

use PH7\Framework\Str\Str;
use PH7\Framework\Video\Api\Api;
use PH7\Framework\Video\Api\Dailymotion;
use PH7\Framework\Video\Api\Vimeo;
use PH7\Framework\Video\Api\Youtube;
use PHPUnit\Framework\TestCase;

final class VideoApiResponseTest extends TestCase
{
    public function testDecoderRejectsTransportFailuresAndInvalidJson(): void
    {
        $oApi = $this->createDecoder();

        $this->assertFalse($oApi->decode(false));
        $this->assertFalse($oApi->decode('not-json'));
        $this->assertFalse($oApi->decode('null'));
        $this->assertFalse($oApi->decode('42'));
    }

    public function testDecoderAcceptsStructuredJson(): void
    {
        $oApi = $this->createDecoder();

        $this->assertEquals((object)['title' => 'Example'], $oApi->decode('{"title":"Example"}'));
        $this->assertSame([], $oApi->decode('[]'));
    }

    public function testDailymotionStoresReturnedMetadata(): void
    {
        $oProvider = $this->createDailymotion((object)[
            'title' => 'Dailymotion title',
            'duration' => 75
        ]);

        $this->assertSame($oProvider, $oProvider->getInfo('https://www.dailymotion.com/video/x123'));
        $this->assertSame('Dailymotion title', $oProvider->getTitle());
        $this->assertSame(75, $oProvider->getDuration());
    }

    public function testProvidersRejectUnexpectedResponseShapes(): void
    {
        $this->assertFalse(
            $this->createDailymotion((object)['error' => 'not found'])
                ->getInfo('https://www.dailymotion.com/video/x123')
        );
        $this->assertFalse(
            $this->createVimeo((object)[])->getInfo('https://vimeo.com/12345')
        );
        $this->assertFalse(
            $this->createYoutube((object)['items' => []])->getInfo('https://youtube.com/watch?v=abc123')
        );
    }

    public function testProvidersRejectMalformedMetadataLeafValues(): void
    {
        $this->assertFalse(
            $this->createDailymotion((object)['title' => ['not a string'], 'duration' => 75])
                ->getInfo('https://www.dailymotion.com/video/x123')
        );
        $this->assertFalse(
            $this->createDailymotion((object)['title' => 'Title', 'duration' => (object)[]])
                ->getInfo('https://www.dailymotion.com/video/x123')
        );
        $this->assertFalse(
            $this->createVimeo([(object)['title' => 'Title', 'duration' => []]])
                ->getInfo('https://vimeo.com/12345')
        );
        $this->assertFalse(
            $this->createYoutube((object)[
                'items' => [(object)[
                    'snippet' => (object)['title' => (object)[]],
                    'contentDetails' => (object)['duration' => 'PT1M']
                ]]
            ])->getInfo('https://youtube.com/watch?v=abc123')
        );
        $this->assertFalse(
            $this->createYoutube((object)[
                'items' => [(object)[
                    'snippet' => (object)['title' => 'Title'],
                    'contentDetails' => (object)['duration' => []]
                ]]
            ])->getInfo('https://youtube.com/watch?v=abc123')
        );
    }

    public function testVimeoPreviewFailureReturnsAnEmptyFallbackSignal(): void
    {
        $this->assertSame(
            '',
            $this->createVimeo(false)->getMeta('https://vimeo.com/12345', 'preview', 160, 120)
        );
    }

    private function createDecoder(): object
    {
        return new class extends Api {
            public function __construct()
            {
            }

            /**
             * @param string|false $mData
             *
             * @return array|\stdClass|bool
             */
            public function decode($mData)
            {
                return $this->decodeData($mData);
            }
        };
    }

    /** @param array|\stdClass|bool $mResponse */
    private function createDailymotion($mResponse): Dailymotion
    {
        return new class($mResponse) extends Dailymotion {
            /** @var array|\stdClass|bool */
            private $mResponse;

            /** @param array|\stdClass|bool $mResponse */
            public function __construct($mResponse)
            {
                $this->mResponse = $mResponse;
                $this->oStr = new Str;
            }

            protected function getData(string $sUrl)
            {
                return $this->mResponse;
            }
        };
    }

    /** @param array|\stdClass|bool $mResponse */
    private function createVimeo($mResponse): Vimeo
    {
        return new class($mResponse) extends Vimeo {
            /** @var array|\stdClass|bool */
            private $mResponse;

            /** @param array|\stdClass|bool $mResponse */
            public function __construct($mResponse)
            {
                $this->mResponse = $mResponse;
                $this->oStr = new Str;
            }

            protected function getData(string $sUrl)
            {
                return $this->mResponse;
            }
        };
    }

    /** @param array|\stdClass|bool $mResponse */
    private function createYoutube($mResponse): Youtube
    {
        return new class($mResponse) extends Youtube {
            /** @var array|\stdClass|bool */
            private $mResponse;

            /** @param array|\stdClass|bool $mResponse */
            public function __construct($mResponse)
            {
                $this->mResponse = $mResponse;
                $this->oStr = new Str;
                $this->sApiKey = 'valid-test-api-key';
            }

            protected function getData(string $sUrl)
            {
                return $this->mResponse;
            }
        };
    }
}
