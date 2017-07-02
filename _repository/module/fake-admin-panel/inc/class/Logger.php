<?php
/**
 * @title          Logger Class
 *
 * @author         Pierre-Henry Soria <ph7software@gmail.com>
 * @copyright      (c) 2012-2017, Pierre-Henry Soria. All Rights Reserved.
 * @license        GNU General Public License <http://www.gnu.org/licenses/gpl.html>
 * @package        PH7 / App / Module / Fake Admin Panel / Inc / Class
 * @version        1.1.8
 */

namespace PH7;

use PH7\Framework\Http\Http;
use PH7\Framework\Ip\Ip;
use PH7\Framework\Mail\Mail;
use PH7\Framework\Security\Ban\Ban;

class Logger extends Core
{

    /**
     * Folder of the information logs files.
     */
    const ATTACK_DIR = '_attackers/';

    /**
     * Data contents.
     *
     * @access private
     * @var $_aData
     */
     private $_aData;

    /**
     * IP address.
     *
     * @access private
     * @var string $_sIp
     */
    private $_sIp;
    /**
     * The information contents.
     *
     * @access private
     * @var string $_sContents
     */
    private $_sContents;

    /**
     * Constructor.
     *
     * @access public
     * @param array $aData The data.
     * @return void
     */
    public function init(array $aData)
    {
        // Add form data in the variable.
        $this->_aData = $aData;

        // Creates the log message and adds it to the list of logs.
        $this->setLogMsg()->writeFile();

        if ($this->config->values['module.setting']['report_email.enabled'])
            $this->sendMessage();

        if ($this->config->values['module.setting']['auto_banned_ip.enabled'])
            $this->blockIp();

    }

    /**
     * Build the log message.
     *
     * @access protected
     * @return object this
     */
     protected function setLogMsg()
     {
        $sReferer = (null !== ($mReferer = $this->browser->getHttpReferer() )) ? $mReferer : 'NO HTTP REFERER';
        $sAgent = (null !== ($mAgent = $this->browser->getUserAgent() )) ? $mAgent : 'NO USER AGENT';
        $sQuery = (null !== ($mQuery = (new Http)->getQueryString() )) ? $mQuery : 'NO QUERY STRING';

        $this->_sIp = Ip::get();

        $this->_sContents =
        t('Date: %0%', $this->dateTime->get()->dateTime()) . "\n" .
        t('IP: %0%', $this->_sIp) . "\n" .
        t('QUERY: %0%', $sQuery) . "\n" .
        t('Agent: %0%', $sAgent) . "\n" .
        t('Referer: %0%', $sReferer) . "\n" .
        t('LOGIN - Email: %0% - Username: %1% - Password: %2%', $this->_aData['mail'], $this->_aData['username'], $this->_aData['password']) . "\n\n\n";

        return $this;
    }

    /**
     * Write a log file with the hacher information.
     *
     * @access protected
     * @return object this
     */
    protected function writeFile()
    {
        $sFullPath = $this->registry->path_module_inc . static::ATTACK_DIR . $this->_sIp . '.log';
        file_put_contents($sFullPath, $this->_sContents, FILE_APPEND);

        return $this;
    }

    /**
     * Blocking IP address.
     *
     * @access protected
     * @return object this
     */
    protected function blockIp()
    {
        $sFullPath = PH7_PATH_APP_CONFIG . Ban::DIR . Ban::IP_FILE;
        file_put_contents($sFullPath, $this->_sIp . "\n", FILE_APPEND);

        return $this;
    }

    /**
     * Send an email to admin.
     *
     * @access protected
     * @return integer
     */
    protected function sendMessage()
    {
        $aInfo = [
          'to' => $this->config->values['logging']['bug_report_email'],
          'subject' => t('Reporting of the Fake Admin Honeypot')
        ];

        return (new Mail)->send($aInfo, $this->_sContents, false);
    }

}
