<?php
/**
 * @title          Avatar Design Core Class
 *
 * @author         Pierre-Henry Soria <hello@ph7builder.com>
 * @copyright      (c) 2012-2023, Pierre-Henry Soria. All Rights Reserved.
 * @license        MIT License; See LICENSE.md and COPYRIGHT.md in the root directory.
 * @package        PH7 / App / System / Core / Class / Design
 */

declare(strict_types=1);

namespace PH7;

use PH7\Framework\Layout\Html\Design;
use PH7\Framework\Registry\Registry;
use PH7\Framework\Server\Server;
use PH7\Framework\Service\SearchImage\Google as GoogleImage;
use PH7\Framework\Service\SearchImage\InvalidUrlException;
use PH7\Framework\Service\SearchImage\Url as ImageUrl;

class AvatarDesignCore extends Design
{
    public const DEF_AVATAR_SIZE = 32;
    public const DEF_LIGHTBOX_AVATAR_SIZE = 400;

    private UserCore $oUser;

    public function __construct()
    {
        parent::__construct();

        $this->oUser = new UserCore;
    }

    /**
     * Display image avatar.
     *
     * @param string $sUsername
     * @param string $sFirstName
     * @param string $sSex
     * @param int $iSize Avatar size (available sizes: 32, 64, 100, 150, 200, 400)
     * @param bool $bRollover CSS effect
     */
    public function get(string $sUsername = '', string $sFirstName = '', ?string $sSex = null, int $iSize = self::DEF_AVATAR_SIZE, bool $bRollover = false): void
    {
        if ($sUsername === PH7_ADMIN_USERNAME) {
            [$sUsername, $sFirstName, $sSex] = $this->getAdminAvatarDetails();
        }

        // The profile does not exist, so it creates a fake profile = ghost
        if (empty($sUsername) || $sUsername === PH7_GHOST_USERNAME) {
            [$sUsername, $sFirstName, $sSex] = $this->getGhostAvatarDetails();
        }

        if ($bRollover) {
            echo '<style scoped="scoped">.rollover img{width:', ($iSize / 1), 'px;height:', ($iSize / 1), 'px;transition:all 0.3s ease-in-out;-webkit-transition:all 0.3s ease-in-out;-moz-transition:all 0.3s ease-in-out;-o-transition:all 0.3s ease-in-out;-ms-transition:all 0.3s ease-in-out;-khtml-transition:all 0.3s ease-in-out;z-index:0}.rollover a:hover >img{width:', $iSize, 'px;height:', $iSize, 'px;border:1px solid #eee;box-shadow:4px 4px 4px rgba(0,0,0,0.2);transform:scale(1.5,1.5);-webkit-transform:scale(1.5,1.5);-moz-transform:scale(1.5,1.5);-o-transform:scale(1.5,1.5);-ms-transform:scale(1.5,1.5);-khtml-transform:scale(1.5,1.5);transition:all 0.5s ease;-webkit-transition:all 0.5s ease;-moz-transition:all 0.5s ease;-o-transition:all 0.5s ease;-ms-transition:all 0.5s ease;-khtml-transition:all 0.5s ease;z-index:999}</style>';
            echo '<div class="rollover" itemscope="itemscope" itemtype="http://schema.org/Person"><a itemprop="image" aria-hidden="true" href="', $this->oUser->getProfileSignupLink($sUsername, $sFirstName, $sSex), '"><img src="', $this->getUserAvatar($sUsername, $sSex, $iSize), '" alt="', ucfirst($sUsername), '" title="', ucfirst($sFirstName), '" /></a></div>';
        } else {
            echo '<a itemscope="itemscope" itemtype="http://schema.org/Person" itemprop="image" aria-hidden="true" class="pic" href="', $this->oUser->getProfileSignupLink($sUsername, $sFirstName, $sSex), '">';
            echo '<img src="', $this->getUserAvatar($sUsername, $sSex, $iSize), '" alt="', ucfirst($sUsername), '" title="', ucfirst($sFirstName), '" loading="lazy" class="avatar" />';
            echo '</a>';
        }
    }

    /**
     * Display the lightbox avatar.
     *
     * @param string $sUsername
     * @param string $sFirstName
     * @param string $sSex
     * @param int $iSize Avatar size (available sizes: 32, 64, 100, 150, 200, 400)
     */
    public function lightbox(string $sUsername = '', string $sFirstName = '', ?string $sSex = null, int $iSize = self::DEF_LIGHTBOX_AVATAR_SIZE): void
    {
        // The profile does not exist, so it creates a fake profile = ghost
        if (empty($sUsername)) {
            [$sUsername, $sFirstName, $sSex] = $this->getGhostAvatarDetails();
        }

        echo '<div class="picture_block" itemscope="itemscope" itemtype="http://schema.org/Person">
            <a itemprop="image" aria-hidden="true" href="', $this->getUserAvatar($sUsername, $sSex, $iSize), '" title="', ucfirst($sUsername), '" data-popup="image">
                <img src="', $this->getUserAvatar($sUsername, $sSex, $iSize / 2), '" alt="', ucfirst($sUsername), '" title="', ucfirst($sFirstName), '" class="img_picture" />
            </a>
        </div>';

        /**
         * @internal Google Search Image works only on non-local URLs, so check if we aren't on dev environments.
         */
        if ($this->isGoogleSearchImageEligible()) {
            $sAvatarUrl = $this->getUserAvatar($sUsername, $sSex, null, false);

            echo '<p>';
            $this->showAvatarOnGoogleLink(PH7_URL_PROT . PH7_DOMAIN . $sAvatarUrl);
            echo '</p>';
        }
    }

    /**
     * @param string $sAvatarUrl Absolute URL with protocol (e.g. https://ph7cms.com)
     *
     * @throws InvalidUrlException
     */
    protected function showAvatarOnGoogleLink(string $sAvatarUrl): void
    {
        try {
            $oAvatarUrl = new ImageUrl($sAvatarUrl);
            $oSearchImage = new GoogleImage($oAvatarUrl);

            $aLinkAttrs = [
                'href' => $oSearchImage->getSearchImageUrl(),
                'title' => t('See any matching images with this profile photo'),
                'target' => '_blank',
                'class' => 'italic btn btn-default btn-xs'
            ];
            echo $this->htmlTag('a', $aLinkAttrs, true, t('Check it on Google Images'));
        } catch (InvalidUrlException $oExcept) {
            // Output nothing if URL is invalid
        }
    }

    /**
     * Check if Google Search Image feature can be enabled.
     */
    private function isGoogleSearchImageEligible(): bool
    {
        // It works only on non-local URLs, so check if we aren't on dev environments (e.g. http://127.0.0.1)
        return AdminCore::auth() &&
            Registry::getInstance()->controller === 'ModeratorController' &&
            !Server::isLocalHost();
    }

    private function getAdminAvatarDetails(): array
    {
        $sUsername = PH7_ADMIN_USERNAME;
        $sFirstName = t('Administrator');
        $sSex = PH7_ADMIN_USERNAME;

        return [$sUsername, $sFirstName, $sSex];
    }

    private function getGhostAvatarDetails(): array
    {
        $sUsername = PH7_GHOST_USERNAME;
        $sFirstName = t('Ghost');
        $sSex = PH7_GHOST_USERNAME;

        return [$sUsername, $sFirstName, $sSex];
    }
}
