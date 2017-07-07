<?php
/**
 * @title          File Controller
 *
 * @author         Pierre-Henry Soria <ph7software@gmail.com>
 * @copyright      (c) 2012-2017, Pierre-Henry Soria. All Rights Reserved.
 * @license        GNU General Public License; See PH7.LICENSE.txt and PH7.COPYRIGHT.txt in the root directory.
 * @package        PH7 / App / System / Module / Admin / Controller
 * @version        1.4
 * @history        We have removed the functionality of deletions and adding files for security reasons and to maintain proper operation at CMS
 */

namespace PH7;

class FileController extends Controller
{

    private $sTitle;

    public function index()
    {
        Framework\Url\Header::redirect(Framework\Mvc\Router\Uri::get(PH7_ADMIN_MOD, 'file', 'display'));
    }

    public function display($sDir = '')
    {
        /* Add the stylesheet files for the Elfinder File Manager */
        $this->design->addCss(PH7_STATIC . 'fileManager/css/', 'elfinder.css,theme.css');

        $sIsDirTxt = ($sDir == 'protected') ? t('Protected') : t('Public');
        $this->sTitle = t('File Manager System | %0%', $sIsDirTxt);
        $this->view->type = ($sDir == 'protected') ? 'protected' : 'public';
        $this->view->page_title = $this->view->h2_title = $this->sTitle;
        $this->output();
    }

    public function themeDisplay()
    {
        $this->sTitle = t('Template Files');

        $this->_displayAction(PH7_PATH_TPL, array('.tpl', '.css', '.js'));
        $this->manualTplInclude('publicdisplay.inc.tpl');
        $this->output();
    }

    public function mailDisplay()
    {
        $this->sTitle = t('Email Templates');

        $this->_displayAction(PH7_PATH_SYS . 'global' . PH7_DS . PH7_VIEWS . PH7_TPL_MAIL_NAME . PH7_DS . 'tpl' . PH7_DS . 'mail' . PH7_DS, '.tpl');
        $this->manualTplInclude('protecteddisplay.inc.tpl');
        $this->output();
    }

    public function banDisplay()
    {
        $this->sTitle = t('Ban Files');

        $this->_displayAction(PH7_PATH_APP_CONFIG . \PH7\Framework\Security\Ban\Ban::DIR, '.txt');
        $this->manualTplInclude('protecteddisplay.inc.tpl');
        $this->output();
    }

    public function suggestionDisplay()
    {
        $this->sTitle = t('Suggestion Files');

        $this->_displayAction(PH7_PATH_APP_CONFIG . \PH7\Framework\Service\Suggestion::DIR, '.txt');
        $this->manualTplInclude('protecteddisplay.inc.tpl');
        $this->output();
    }

    public function pageDisplay()
    {
        $this->sTitle = t('Pages');

        $this->_displayAction(PH7_PATH_SYS_MOD . 'page' . PH7_DS . PH7_VIEWS . PH7_TPL_MOD_NAME, '.tpl');
        $this->manualTplInclude('protecteddisplay.inc.tpl');
        $this->output();
    }

    public function somethingProtectedAppDisplay()
    {
        $this->_displayAction(PH7_PATH_APP . $this->httpRequest->get('dir'));
        $this->manualTplInclude('protecteddisplay.inc.tpl');
        $this->output();
    }

    public function publicEdit()
    {
        $this->sTitle = t('Edit Public Files');

        $this->view->page_title = $this->view->h2_title = $this->sTitle;
        $this->output();
    }

    public function protectedEdit()
    {
        $this->sTitle = t('Edit Protected Files');

        $this->view->page_title = $this->view->h2_title = $this->sTitle;
        $this->output();
    }

    /**
     * Prototype method to show the public and protected files.
     *
     * @param string $sFile Full path.
     * @param mixed (string | array) $mExt Retrieves only files with specific extensions. Default NULL
     * @return void
     */
    private function _displayAction($sFile, $mExt = null)
    {
        if (empty($this->sTitle))
            $this->sTitle = t('File Manager');

        $this->view->page_title = $this->view->h2_title = $this->sTitle;

        $this->view->filesList = $this->file->getFileList($sFile, $mExt);
    }

}
