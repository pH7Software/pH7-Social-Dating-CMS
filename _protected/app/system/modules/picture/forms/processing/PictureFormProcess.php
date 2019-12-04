<?php
/**
 * @title          Picture Form Process Class
 *
 * @author         Pierre-Henry Soria <hello@ph7cms.com>
 * @copyright      (c) 2012-2019, Pierre-Henry Soria. All Rights Reserved.
 * @license        GNU General Public License; See PH7.LICENSE.txt and PH7.COPYRIGHT.txt in the root directory.
 * @package        PH7 / App / System / Module / Picture / Form / Processing
 * @version        1.4
 */

namespace PH7;

defined('PH7') or exit('Restricted access');

use PH7\Framework\Image\Image;
use PH7\Framework\Mvc\Model\DbConfig;
use PH7\Framework\Mvc\Router\Uri;
use PH7\Framework\Security\Moderation\Filter;
use PH7\Framework\Url\Header;
use PH7\Framework\Util\Various;

class PictureFormProcess extends Form implements NudityDetectable
{
    const MAX_IMAGE_WIDTH = 2500;
    const MAX_IMAGE_HEIGHT = 2500;

    const PICTURE2_SIZE = 400;
    const PICTURE3_SIZE = 600;
    const PICTURE4_SIZE = 800;
    const PICTURE5_SIZE = 1000;
    const PICTURE6_SIZE = 1200;

    /** @var array */
    private $aPhotos;

    /** @var string */
    private $sApproved;

    /** @var int */
    private $iPhotoIndex;

    public function __construct()
    {
        parent::__construct();

        /**
         * This can cause minor errors (eg if a user sent a file that is not a photo).
         * So we hide the errors if we are not in development mode.
         */
        if (!isDebug()) {
            error_reporting(0);
        }

        /**
         * Check if the photo album ID is valid. The value must be numeric.
         * This test is necessary because when the selection exists but that no option is available (this can when a user wants to add photos but he has no album)
         * the return value is of type "string" and the value is "1".
         */
        if (!is_numeric($this->httpRequest->post('album_id'))) {
            \PFBC\Form::setError(
                'form_picture',
                t('Please add a category before you add some photos.')
            );
            return; // Stop execution of the method.
        }

        /**
         * Resizing and saving some photos
         */
        $this->aPhotos = $_FILES['photos']['tmp_name'];
        for ($this->iPhotoIndex = 0, $iNumPhotos = count($this->aPhotos); $this->iPhotoIndex < $iNumPhotos; $this->iPhotoIndex++) {
            $oPicture1 = new Image(
                $this->aPhotos[$this->iPhotoIndex],
                self::MAX_IMAGE_WIDTH,
                self::MAX_IMAGE_HEIGHT
            );

            if (!$oPicture1->validate()) {
                \PFBC\Form::setError('form_picture', Form::wrongImgFileTypeMsg());
                return; // Stop execution of the method.
            }

            $sAlbumTitle = MediaCore::cleanTitle($this->httpRequest->post('album_title'));
            $iAlbumId = (int)$this->httpRequest->post('album_id');

            $oPicture2 = clone $oPicture1;
            $oPicture3 = clone $oPicture1;
            $oPicture4 = clone $oPicture1;
            $oPicture5 = clone $oPicture1;
            $oPicture6 = clone $oPicture1;

            $oPicture2->square(self::PICTURE2_SIZE);
            $oPicture3->square(self::PICTURE3_SIZE);
            $oPicture4->square(self::PICTURE4_SIZE);
            $oPicture5->square(self::PICTURE5_SIZE);
            $oPicture6->square(self::PICTURE6_SIZE);

            /* Set watermark text on images */
            $sWatermarkText = DbConfig::getSetting('watermarkTextImage');
            if (!empty(trim($sWatermarkText))) {
                $iSizeWatermarkText = DbConfig::getSetting('sizeWatermarkTextImage');
                $oPicture1->watermarkText($sWatermarkText, $iSizeWatermarkText);
                $oPicture2->watermarkText($sWatermarkText, $iSizeWatermarkText);
                $oPicture3->watermarkText($sWatermarkText, $iSizeWatermarkText);
                $oPicture4->watermarkText($sWatermarkText, $iSizeWatermarkText);
                $oPicture5->watermarkText($sWatermarkText, $iSizeWatermarkText);
                $oPicture6->watermarkText($sWatermarkText, $iSizeWatermarkText);
            }

            $sPath = PH7_PATH_PUBLIC_DATA_SYS_MOD . 'picture/img/' . $this->session->get('member_username') . PH7_DS . $iAlbumId . PH7_DS;

            $sFileName = Various::genRnd($oPicture1->getFileName(), 20);

            $sFile1 = $sFileName . '-original.' . $oPicture1->getExt(); // Original one
            $sFile2 = $sFileName . '-' . self::PICTURE2_SIZE . PH7_DOT . $oPicture2->getExt();
            $sFile3 = $sFileName . '-' . self::PICTURE3_SIZE . PH7_DOT . $oPicture3->getExt();
            $sFile4 = $sFileName . '-' . self::PICTURE4_SIZE . PH7_DOT . $oPicture4->getExt();
            $sFile5 = $sFileName . '-' . self::PICTURE5_SIZE . PH7_DOT . $oPicture5->getExt();
            $sFile6 = $sFileName . '-' . self::PICTURE6_SIZE . PH7_DOT . $oPicture6->getExt();

            $oPicture1->save($sPath . $sFile1);
            $oPicture2->save($sPath . $sFile2);
            $oPicture3->save($sPath . $sFile3);
            $oPicture4->save($sPath . $sFile4);
            $oPicture5->save($sPath . $sFile5);
            $oPicture6->save($sPath . $sFile6);

            $this->sApproved = DbConfig::getSetting('pictureManualApproval') == 0 ? '1' : '0';

            if ($this->isNudityFilterEligible()) {
                $this->checkNudityFilter();
            }

            // It creates a nice title if no title is specified.
            $sTitle = $this->getImageTitle($oPicture1);
            $sTitle = MediaCore::cleanTitle($sTitle);

            (new PictureModel)->addPhoto(
                $this->session->get('member_id'),
                $iAlbumId,
                $sTitle,
                $this->httpRequest->post('description'),
                $sFile1,
                $this->dateTime->get()->dateTime('Y-m-d H:i:s'),
                $this->sApproved
            );
        }

        Picture::clearCache();

        $sModerationText = t('Your photo(s) has/have been received. It will not be visible until it is approved by our moderators. Please do not send a new one.');
        $sText = t('Your photo(s) has/have been successfully added!');
        $sMsg = $this->sApproved === '0' ? $sModerationText : $sText;

        Header::redirect(
            Uri::get('picture',
                'main',
                'album',
                $this->session->get('member_username') . ',' . $sAlbumTitle . ',' . $iAlbumId
            ),
            $sMsg
        );
    }

    public function isNudityFilterEligible()
    {
        return $this->sApproved === '1' && DbConfig::getSetting('nudityFilter');
    }

    public function checkNudityFilter()
    {
        if (Filter::isNudity($this->aPhotos[$this->iPhotoIndex])) {
            // The photo(s) seems to be suitable for adults only, so set for moderation
            $this->sApproved = '0';
        }
    }

    /**
     * Create a nice picture title if no title is specified.
     *
     * @param Image $oPicture
     *
     * @return string
     */
    private function getImageTitle(Image $oPicture)
    {
        if ($this->isPhotoTitleEligible()) {
            return $this->httpRequest->post('title');
        }

        // Otherwise get the name from the filename
        return $this->getTitleFromFileName($oPicture);
    }

    /**
     * @return bool
     */
    private function isPhotoTitleEligible()
    {
        return $this->httpRequest->postExists('title') &&
            $this->str->length($this->str->trim($this->httpRequest->post('title'))) > 2;
    }

    /**
     * @param Image $oPicture
     *
     * @return string
     */
    private function getTitleFromFileName(Image $oPicture)
    {
        return $this->str->upperFirst(
            str_replace(
                ['-', '_'],
                ' ',
                str_ireplace(
                    PH7_DOT . $oPicture->getExt(),
                    '',
                    escape($_FILES['photos']['name'][$this->iPhotoIndex], true)
                )
            )
        );
    }
}
