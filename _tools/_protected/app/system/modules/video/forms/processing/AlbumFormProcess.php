<?php
/**
 * @author         Pierre-Henry Soria <hello@ph7cms.com>
 * @copyright      (c) 2012-2019, Pierre-Henry Soria. All Rights Reserved.
 * @license        GNU General Public License; See PH7.LICENSE.txt and PH7.COPYRIGHT.txt in the root directory.
 * @package        PH7 / App / System / Module / Video / Form / Processing
 */

namespace PH7;

defined('PH7') or exit('Restricted access');

use PH7\Framework\Image\Image;
use PH7\Framework\Mvc\Model\DbConfig;
use PH7\Framework\Mvc\Model\Engine\Db;
use PH7\Framework\Mvc\Router\Uri;
use PH7\Framework\Security\Moderation\Filter;
use PH7\Framework\Url\Header;
use PH7\Framework\Util\Various;

class AlbumFormProcess extends Form implements NudityDetectable
{
    const ALBUM_IMAGE_SIZE = 200;

    /** @var string */
    private $sApproved;

    public function __construct()
    {
        parent::__construct();

        /**
         * This can cause minor errors (eg if a user sent a file that is not a video).
         * So we hide the errors if we are not in development mode.
         */
        if (!isDebug()) {
            error_reporting(0);
        }

        // Resizing and saving the video album thumbnail
        $oPicture = new Image($_FILES['album']['tmp_name']);
        if (!$oPicture->validate()) {
            \PFBC\Form::setError('form_video_album', Form::wrongImgFileTypeMsg());
        } else {
            $this->sApproved = DbConfig::getSetting('videoManualApproval') == 0 ? '1' : '0';

            if ($this->isNudityFilterEligible()) {
                $this->checkNudityFilter();
            }

            $sFileName = Various::genRnd($oPicture->getFileName(), 1) . '-thumb.' . $oPicture->getExt();

            (new VideoModel)->addAlbum(
                $this->session->get('member_id'),
                MediaCore::cleanTitle($this->httpRequest->post('name')),
                $this->httpRequest->post('description'),
                $sFileName,
                $this->dateTime->get()->dateTime('Y-m-d H:i:s'),
                $this->sApproved
            );
            $iLastAlbumId = (int)Db::getInstance()->lastInsertId();

            $oPicture->square(self::ALBUM_IMAGE_SIZE);

            /* Set watermark text on thumbnail */
            $sWatermarkText = DbConfig::getSetting('watermarkTextImage');
            if (!empty(trim($sWatermarkText))) {
                $iSizeWatermarkText = DbConfig::getSetting('sizeWatermarkTextImage');
                $oPicture->watermarkText($sWatermarkText, $iSizeWatermarkText);
            }

            $sPath = PH7_PATH_PUBLIC_DATA_SYS_MOD . 'video/file/' . $this->session->get('member_username') . PH7_DS . $iLastAlbumId . PH7_DS;

            $this->file->createDir($sPath);

            $oPicture->save($sPath . $sFileName);

            Video::clearCache();

            Header::redirect(
                Uri::get(
                    'video',
                    'main',
                    'addvideo',
                    $iLastAlbumId
                )
            );
        }
    }

    public function isNudityFilterEligible()
    {
        return $this->sApproved === '1' && DbConfig::getSetting('nudityFilter');
    }

    public function checkNudityFilter()
    {
        if (Filter::isNudity($_FILES['album']['tmp_name'])) {
            // The image doesn't seem suitable for everyone. Overwrite "$sApproved" and set the image for approval
            $this->sApproved = '0';
        }
    }
}
