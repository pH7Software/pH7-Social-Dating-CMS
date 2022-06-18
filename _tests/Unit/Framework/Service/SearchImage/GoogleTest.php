<?php
/**
 * @author           Pierre-Henry Soria <hello@ph7cms.com>
 * @copyright        (c) 2017-2019, Pierre-Henry Soria. All Rights Reserved.
 * @license          MIT License; See LICENSE.md and COPYRIGHT.md in the root directory.
 * @package          PH7 / Test / Unit / Framework / Service / SearchImage
 */

declare(strict_types=1);

namespace PH7\Test\Unit\Framework\Service\SearchImage;

use PH7\Framework\Service\SearchImage\Google as GoogleImage;
use PH7\Framework\Service\SearchImage\Url as ImageUrl;
use PHPUnit\Framework\TestCase;

class GoogleTest extends TestCase
{
    private GoogleImage $oGoogleImage;

    protected function setUp(): void
    {
        $oUrl = new ImageUrl('http://localhost/data/system/modules/user/avatar/img/paul/8-400.jpg');
        $this->oGoogleImage = new GoogleImage($oUrl);
    }

    public function testProviderLink(): void
    {
        $sExpectedUrl = 'https://www.google.com/searchbyimage?image_url=';
        $this->assertSame($sExpectedUrl, $this->oGoogleImage->getProviderUrl());
    }

    public function testSearchImageUrl(): void
    {
        $sExpectedUrl = 'https://www.google.com/searchbyimage?image_url=http%3A%2F%2Flocalhost%2Fdata%2Fsystem%2Fmodules%2Fuser%2Favatar%2Fimg%2Fpaul%2F8-400.jpg';
        $this->assertSame($sExpectedUrl, $this->oGoogleImage->getSearchImageUrl());
    }
}
