<?php
/**
 * @title          Chat Messenger Ajax
 *
 * @author         Pierre-Henry Soria <hello@ph7cms.com>
 * @copyright      (c) 2012-2019, Pierre-Henry Soria. All Rights Reserved.
 * @license        GNU General Public License; See PH7.LICENSE.txt and PH7.COPYRIGHT.txt in the root directory.
 * @package        PH7 / App / System / Module / IM / Asset / Ajax
 * @version        1.6
 * @required       PHP 5.4 or higher.
 */

namespace PH7;

defined('PH7') or exit('Restricted access');

use PH7\Framework\Date\CDateTime;
use PH7\Framework\Date\Various as VDate;
use PH7\Framework\File\Import;
use PH7\Framework\Http\Http;
use PH7\Framework\Module\Various as SysMod;
use PH7\Framework\Mvc\Model\DbConfig;
use PH7\Framework\Mvc\Request\Http as HttpRequest;
use PH7\Framework\Mvc\Router\Uri;
use PH7\Framework\Parse\Emoticon;
use PH7\Framework\Session\Session;
use Teapot\StatusCode;

class MessengerAjax extends PermissionCore
{
    const DATETIME_FORMAT = 'Y-m-d H:i:s';

    /** @var HttpRequest */
    private $oHttpRequest;

    /** @var MessengerModel */
    private $oMessengerModel;

    public function __construct()
    {
        parent::__construct();

        Import::pH7App(PH7_SYS . PH7_MOD . 'im.models.MessengerModel');

        $this->oHttpRequest = new HttpRequest;
        $this->oMessengerModel = new MessengerModel;

        switch ($this->oHttpRequest->get('act')) {
            case 'heartbeat':
                $this->heartbeat();
                break;

            case 'send':
                $this->send();
                break;

            case 'close':
                $this->close();
                break;

            case 'startsession':
                $this->startSession();
                break;

            default:
                Http::setHeadersByCode(StatusCode::BAD_REQUEST);
                exit('Bad Request Error!');
        }

        if (empty($_SESSION['messenger_history'])) {
            $_SESSION['messenger_history'] = [];
        }

        if (empty($_SESSION['messenger_openBoxes'])) {
            $_SESSION['messenger_openBoxes'] = [];
        }
    }

    protected function heartbeat()
    {
        $sFrom = $_SESSION['messenger_username'];
        $sTo = !empty($_SESSION['messenger_username_to']) ? $_SESSION['messenger_username_to'] : 0;

        $oQuery = $this->oMessengerModel->select($sFrom);
        $sItems = '';

        foreach ($oQuery as $oData) {
            $sFrom = escape($oData->fromUser, true);
            $sSent = escape($oData->sent, true);
            $sMsg = $this->sanitize($oData->message);
            $sMsg = Emoticon::init($sMsg, false);

            if (!isset($_SESSION['messenger_openBoxes'][$sFrom]) && isset($_SESSION['messenger_history'][$sFrom])) {
                $sItems = $_SESSION['messenger_history'][$sFrom];
            }

            $sItems .= $this->setJsonContent(['user' => $sFrom, 'msg' => $sMsg]);

            if (!isset($_SESSION['messenger_history'][$sFrom])) {
                $_SESSION['messenger_history'][$sFrom] = '';
            }

            $_SESSION['messenger_history'][$sFrom] .= $this->setJsonContent(['user' => $sFrom, 'msg' => $sMsg]);

            unset($_SESSION['messenger_boxes'][$sFrom]);
            $_SESSION['messenger_openBoxes'][$sFrom] = $sSent;
        }

        if (!empty($_SESSION['messenger_openBoxes'])) {
            foreach ($_SESSION['messenger_openBoxes'] as $sBox => $sTime) {
                if (!isset($_SESSION['messenger_boxes'][$sBox])) {
                    $iNow = time() - strtotime($sTime);
                    $sMsg = t('Sent %0%', VDate::textTimeStamp($sTime));
                    if ($iNow > 180) {
                        $sItems .= $this->setJsonContent(['status' => '2', 'user' => $sBox, 'msg' => $sMsg]);

                        if (!isset($_SESSION['messenger_history'][$sBox])) {
                            $_SESSION['messenger_history'][$sBox] = '';
                        }

                        $_SESSION['messenger_history'][$sBox] .= $this->setJsonContent(['status' => '2', 'user' => $sBox, 'msg' => $sMsg]);
                        $_SESSION['messenger_boxes'][$sBox] = 1;
                    }
                }
            }
        }

        if (!$this->isOnline($sFrom)) {
            $sItems = t('You need the ONLINE status in order to speak instantaneous.');
        } elseif ($sTo !== 0 && !$this->isOnline($sTo)) {
            if (SysMod::isEnabled('mail')) {
                $sItems = '<small><em>' . t("%0% is offline. Send a <a href='%1%'>Private Message</a> instead.", $sTo, Uri::get('mail', 'main', 'compose', $sTo)) . '</em></small>';
            } else {
                $sItems = '<small><em>' . t('%0% is currently offline. Why not to chat later on?', $sTo) . '</em></small>';
            }
        } else {
            $this->oMessengerModel->update($sFrom, $sTo);
        }

        if ($sItems !== '') {
            $sItems = substr($sItems, 0, -1);
        }

        Http::setContentType('application/json');
        echo '{"items": [' . $sItems . ']}';
        exit;
    }

    protected function boxSession($sBox)
    {
        $sItems = '';

        if (isset($_SESSION['messenger_history'][$sBox])) {
            $sItems = $_SESSION['messenger_history'][$sBox];
        }

        return $sItems;
    }

    protected function startSession()
    {
        $sItems = '';
        if (!empty($_SESSION['messenger_openBoxes'])) {
            foreach ($_SESSION['messenger_openBoxes'] as $sBox => $sVoid) {
                $sItems .= $this->boxSession($sBox);
            }
        }

        if ($sItems !== '') {
            $sItems = substr($sItems, 0, -1);
        }

        Http::setContentType('application/json');
        echo '{
            "user": "' . $_SESSION['messenger_username'] . '",
            "items": [' . $sItems . ']
        }';
        exit;
    }

    protected function send()
    {
        $sFrom = $_SESSION['messenger_username'];
        $sTo = $_SESSION['messenger_username_to'] = $this->oHttpRequest->post('to');
        $sMsg = $this->oHttpRequest->post('message');

        $_SESSION['messenger_openBoxes'][$this->oHttpRequest->post('to')] = date(self::DATETIME_FORMAT, time());

        $sMsgTransform = $this->sanitize($sMsg);
        $sMsgTransform = Emoticon::init($sMsgTransform, false);

        if (!isset($_SESSION['messenger_history'][$this->oHttpRequest->post('to')])) {
            $_SESSION['messenger_history'][$this->oHttpRequest->post('to')] = '';
        }

        if (!$this->checkMembership() || !$this->group->instant_messaging) {
            $sMsgTransform = t("You need to <a href='%0%'>upgrade your membership</a> to be able to chat.", Uri::get('payment', 'main', 'index'));
        } elseif (!$this->isOnline($sFrom)) {
            $sMsgTransform = t('You need the ONLINE status in order to chat with other users.');
        } elseif (!$this->isOnline($sTo)) {
            if (SysMod::isEnabled('mail')) {
                $sMsgTransform = '<small><em>' . t("%0% is offline. Send a <a href='%1%'>Private Message</a> instead.", $sTo, Uri::get('mail', 'main', 'compose', $sTo)) . '</em></small>';
            } else {
                $sMsgTransform = '<small><em>' . t('%0% is currently offline. Maybe, try to chat later on? 😉', $sTo) . '</em></small>';
            }
        } else {
            $this->oMessengerModel->insert($sFrom, $sTo, $sMsg, (new CDateTime)->get()->dateTime(self::DATETIME_FORMAT));
        }

        $_SESSION['messenger_history'][$this->oHttpRequest->post('to')] .= $this->setJsonContent(['status' => '1', 'user' => $sTo, 'msg' => $sMsgTransform]);

        unset($_SESSION['messenger_boxes'][$this->oHttpRequest->post('to')]);

        Http::setContentType('application/json');
        echo $this->setJsonContent(
            [
                'user' => $sFrom,
                'msg' => $sMsgTransform
            ],
            false
        );
        exit;
    }

    protected function close()
    {
        unset($_SESSION['messenger_openBoxes'][$this->oHttpRequest->post('box')]);
        exit(1);
    }

    protected function setJsonContent(array $aData, $bEndComma = true)
    {
        // Default array
        $aDefData = [
            'status' => '0',
            'user' => '',
            'msg' => ''
        ];

        // Update array
        $aData += $aDefData;

        $sJsonData = <<<EOD
        {
            "status": "{$aData['status']}",
            "user": "{$aData['user']}",
            "msg": "{$aData['msg']}"
        }
EOD;
        return $bEndComma ? $sJsonData . ',' : $sJsonData;
    }

    /**
     * @param string $sUsername
     *
     * @return bool
     */
    protected function isOnline($sUsername)
    {
        $oUserModel = new UserCoreModel;
        $iProfileId = $oUserModel->getId(null, $sUsername);
        $bIsOnline = $oUserModel->isOnline($iProfileId, DbConfig::getSetting('userTimeout'));
        unset($oUserModel);

        return $bIsOnline;
    }

    /**
     * @param string $sText
     *
     * @return string
     */
    protected function sanitize($sText)
    {
        $sText = escape($sText, true);
        $sText = str_replace("\n\r", "\n", $sText);
        $sText = str_replace("\r\n", "\n", $sText);
        $sText = str_replace("\n", "<br>", $sText);

        return $sText;
    }
}

// Go only if the user is logged
if (UserCore::auth()) {
    $oSession = new Session; // Initialize session & start_session() func
    if (empty($_SESSION['messenger_username'])) {
        $_SESSION['messenger_username'] = $oSession->get('member_username');
    }
    unset($oSession);

    new MessengerAjax;
}
