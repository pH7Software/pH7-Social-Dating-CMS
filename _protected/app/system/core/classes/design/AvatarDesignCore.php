<?php
/**
 * @title          Avatar Design Core Class
 *
 * @author         Pierre-Henry Soria <ph7software@gmail.com>
 * @copyright      (c) 2012-2017, Pierre-Henry Soria. All Rights Reserved.
 * @license        GNU General Public License; See PH7.LICENSE.txt and PH7.COPYRIGHT.txt in the root directory.
 * @package        PH7 / App / System / Core / Class / Design
 * @version        1.2
 */
namespace PH7;

class AvatarDesignCore extends Framework\Layout\Html\Design
{
    private $_oUser;

    public function __construct()
    {
        parent::__construct();
        $this->_oUser = new UserCore;
    }

    /**
     * Display image avatar.
     *
     * @param string $sUsername Default ''
     * @param string $sFirstName Default ''
     * @param string $sSex Default NULL
     * @param integer $iSize Avatar size (available sizes: 32, 64, 100, 150, 200, 400). Default: 32
     * @param boolean $bRollover CSS effect. Default FALST
     */
    public function get($sUsername = '', $sFirstName = '', $sSex = null, $iSize = 32, $bRollover = false)
    {
        // The profile does not exist, so it creates a fake profile = ghost
        if ($sUsername == PH7_ADMIN_USERNAME)
        {
            $sUsername = PH7_ADMIN_USERNAME;
            $sFirstName = t('Administrator');
            $sSex = PH7_ADMIN_USERNAME;
        }

        if (empty($sUsername))
        {
            $sUsername = PH7_GHOST_USERNAME;
            $sFirstName = t('Ghost');
            $sSex = PH7_GHOST_USERNAME;
        }

        $iSize = (int) $iSize;
        if ($bRollover)
        {
            echo '<style scoped="scoped">.rollover img{width:', ($iSize / 1), 'px;height:', ($iSize / 1), 'px;transition:all 0.3s ease-in-out;-webkit-transition:all 0.3s ease-in-out;-moz-transition:all 0.3s ease-in-out;-o-transition:all 0.3s ease-in-out;-ms-transition:all 0.3s ease-in-out;-khtml-transition:all 0.3s ease-in-out;z-index:0}.rollover a:hover >img{width:', $iSize, 'px;height:', $iSize, 'px;border:1px solid #eee;box-shadow:4px 4px 4px rgba(0,0,0,0.2);transform:scale(1.5,1.5);-webkit-transform:scale(1.5,1.5);-moz-transform:scale(1.5,1.5);-o-transform:scale(1.5,1.5);-ms-transform:scale(1.5,1.5);-khtml-transform:scale(1.5,1.5);transition:all 0.5s ease;-webkit-transition:all 0.5s ease;-moz-transition:all 0.5s ease;-o-transition:all 0.5s ease;-ms-transition:all 0.5s ease;-khtml-transition:all 0.5s ease;z-index:999}</style>';
            echo '<div class="rollover"><a href="', $this->_oUser->getProfileSignupLink($sUsername, $sFirstName, $sSex), '"><img src="', $this->getUserAvatar($sUsername, $sSex, $iSize), '" alt="', ucfirst($sUsername), '" title="', ucfirst($sFirstName), '" /></a></div>';
        }
        else
        {
            echo '<a class="pic" href="', $this->_oUser->getProfileSignupLink($sUsername, $sFirstName, $sSex), '"><img src="', $this->getUserAvatar($sUsername, $sSex, $iSize), '" alt="', ucfirst($sUsername), '" title="', ucfirst($sFirstName), '" class="avatar" /></a>';
        }
    }

    /**
     * Display the lightbox avatar.
     *
     * @param string $sUsername  Default ''
     * @param string $sFirstName Default ''
     * @param string $sSex Default NULL
     * @param integer $iSize Avatar size (available sizes: 32, 64, 100, 150, 200, 400). Default: 400
     */
    public function lightbox($sUsername = '', $sFirstName = '', $sSex = null, $iSize = 400)
    {
        // The profile does not exist, so it creates a fake profile = ghost
        if (empty($sUsername))
        {
            $sUsername = PH7_GHOST_USERNAME;
            $sFirstName = t('Ghost');
            $sSex = PH7_GHOST_USERNAME;
        }
        echo '<div class="picture_block"><a href="', $this->getUserAvatar($sUsername, $sSex, $iSize), '" title="', ucfirst($sUsername), '" data-popup="image"><img src="', $this->getUserAvatar($sUsername, $sSex, $iSize / 2), '" alt="', ucfirst($sUsername), '" title="', ucfirst($sFirstName), '" class="img_picture" /></a></div>';
    }
}
