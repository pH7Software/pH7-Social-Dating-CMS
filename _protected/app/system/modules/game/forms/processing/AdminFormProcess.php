<?php
/**
 * @author         Pierre-Henry Soria <hello@ph7cms.com>
 * @copyright      (c) 2012-2019, Pierre-Henry Soria. All Rights Reserved.
 * @license        GNU General Public License; See PH7.LICENSE.txt and PH7.COPYRIGHT.txt in the root directory.
 * @package        PH7 / App / System / Module / Game / Form / Processing
 */

namespace PH7;

defined('PH7') or die('Restricted access');

use PH7\Framework\Image\Image;
use PH7\Framework\Mvc\Model\Engine\Db;
use PH7\Framework\Mvc\Router\Uri;
use PH7\Framework\Url\Header;
use PH7\Framework\Util\Various;

class AdminFormProcess extends Form
{
    const GAME_THUMBNAIL_SIZE = 60;
    const RANDOM_STRING_LENGTH = 30;

    public function __construct()
    {
        parent::__construct();

        // Thumbnail
        $oImg = new Image($_FILES['thumb']['tmp_name']);
        if (!$oImg->validate()) {
            \PFBC\Form::setError('form_game', Form::wrongImgFileTypeMsg());
            return; // Stop execution of the method.
        }

        $sThumbFile = Various::genRnd($oImg->getFileName(), self::RANDOM_STRING_LENGTH) . PH7_DOT . $oImg->getExt();
        $sThumbDir = PH7_PATH_PUBLIC_DATA_SYS_MOD . 'game/img/thumb/';

        $oImg->square(self::GAME_THUMBNAIL_SIZE);
        $oImg->save($sThumbDir . $sThumbFile);
        unset($oImg);

        // Game
        $sGameFile = Various::genRnd($_FILES['file']['name'], self::RANDOM_STRING_LENGTH) . PH7_DOT . $this->file->getFileExt($_FILES['file']['name']);
        $sGameDir = PH7_PATH_PUBLIC_DATA_SYS_MOD . 'game/file/';

        // If the folders is not created (games not installed), yet we will create.
        $this->file->createDir([$sThumbDir, $sGameDir]);

        if (!@move_uploaded_file($_FILES['file']['tmp_name'], $sGameDir . $sGameFile)) {
            \PFBC\Form::setError('form_game', t('Impossible to upload the game. Please check if the folder "%0%" has the write permission (CHMOD 755) or contact your host to check it.', PH7_PATH_PUBLIC_DATA_SYS_MOD . 'game/file/'));
        } else {
            $aData = [
                'category_id' => $this->httpRequest->post('category_id', 'int'),
                'name' => $this->httpRequest->post('name'),
                'title' => $this->httpRequest->post('title'),
                'description' => $this->httpRequest->post('description'),
                'keywords' => $this->httpRequest->post('keywords'),
                'thumb' => $sThumbFile,
                'file' => $sGameFile
            ];

            (new GameModel)->add($aData);

            Game::clearCache();

            Header::redirect(
                Uri::get('game',
                    'main',
                    'game',
                    $aData['title'] . ',' . Db::getInstance()->lastInsertId()
                ),
                t('The game has been successfully added!')
            );
        }
    }
}
