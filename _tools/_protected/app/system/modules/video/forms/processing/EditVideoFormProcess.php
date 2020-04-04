<?php
/**
 * @author         Pierre-Henry Soria <hello@ph7cms.com>
 * @copyright      (c) 2012-2019, Pierre-Henry Soria. All Rights Reserved.
 * @license        GNU General Public License; See PH7.LICENSE.txt and PH7.COPYRIGHT.txt in the root directory.
 * @package        PH7 / App / System / Module / Video / Form / Processing
 */

namespace PH7;

defined('PH7') or exit('Restricted access');

use PH7\Framework\Mvc\Router\Uri;
use PH7\Framework\Url\Header;

class EditVideoFormProcess extends Form
{
    public function __construct()
    {
        parent::__construct();

        $iAlbumId = (int)$this->httpRequest->get('album_id');
        $sVideoTitle = MediaCore::cleanTitle($this->httpRequest->post('title'));
        $iVideoId = (int)$this->httpRequest->get('video_id');

        (new VideoModel)->updateVideo(
            $this->session->get('member_id'),
            $iAlbumId,
            $iVideoId,
            $sVideoTitle,
            $this->httpRequest->post('description'),
            $this->dateTime->get()->dateTime('Y-m-d H:i:s')
        );

        Video::clearCache();

        Header::redirect(
            Uri::get('video',
                'main',
                'video',
                $this->session->get('member_username') . ',' . $iAlbumId . ',' . $sVideoTitle . ',' . $iVideoId
            ),
            t('Your video has been successfully updated!')
        );
    }
}
