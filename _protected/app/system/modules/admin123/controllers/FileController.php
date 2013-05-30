<?php
/**
 * @title          File Controller
 *
 * @author         Pierre-Henry Soria <ph7software@gmail.com>
 * @copyright      (c) 2012-2013, Pierre-Henry Soria. All Rights Reserved.
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
        Framework\Url\HeaderUrl::redirect(Framework\Mvc\Router\UriRoute::get(PH7_ADMIN_MOD, 'file', 'display'));
    }

    public function display($sDir = '')
    {
        /* Add Css Style and JavaScript for the Elfinder File Manager */
        $this->design->addCss(PH7_STATIC . 'fileManager/css/', 'elFinder.css,theme.css');
        $this->design->addJs(PH7_STATIC . 'fileManager/js/', 'elFinder.js');

        $sIsDirTxt = ($sDir == 'protected') ? t('Protected') : t('Public');
        $this->sTitle = t('File Manager System | %0%', $sIsDirTxt);
        $this->view->type = ($sDir == 'protected') ? 'protected' : 'public';
        $this->view->page_title = $this->sTitle;
        $this->view->h2_title = $this->sTitle;
        $this->output();
    }

    public function themeDisplay()
    {
        $this->_prototypeDisplayAction(PH7_PATH_TPL);
        $this->manualTplInclude('publicdisplay.inc.tpl');
        $this->output();
    }

    public function mailDisplay()
    {
        $this->_prototypeDisplayAction(PH7_PATH_SYS . 'globals/' . PH7_VIEWS .PH7_TPL_NAME . '/mails/', '.tpl');
        $this->manualTplInclude('protecteddisplay.inc.tpl');
        $this->output();
    }

    public function banDisplay()
    {
        $this->_prototypeDisplayAction(PH7_PATH_APP_CONFIG . \PH7\Framework\Security\Ban\Ban::DIR, '.txt');
        $this->manualTplInclude('protecteddisplay.inc.tpl');
        $this->output();
    }

    public function suggestionDisplay()
    {
        $this->_prototypeDisplayAction(PH7_PATH_APP_CONFIG . \PH7\Framework\Service\Suggestion::DIR, '.txt');
        $this->manualTplInclude('protecteddisplay.inc.tpl');
        $this->output();
    }

    public function pageDisplay()
    {
        $this->_prototypeDisplayAction(PH7_PATH_SYS_MOD . 'page/' . PH7_VIEWS .PH7_TPL_NAME, '.tpl');
        $this->manualTplInclude('protecteddisplay.inc.tpl');
        $this->output();
    }

    public function somethingProtectedAppDisplay()
    {
        $this->_prototypeDisplayAction(PH7_PATH_APP . $this->httpRequest->get('dir'));
        $this->manualTplInclude('protecteddisplay.inc.tpl');
        $this->output();
    }

    public function publicEdit()
    {
        $this->sTitle = t('Edit Public Files');
        $this->view->page_title = $this->sTitle;
        $this->view->h2_title = $this->sTitle;
        $this->output();
    }

    public function protectedEdit()
    {
        $this->sTitle = t('Edit Protected Files');
        $this->view->page_title = $this->sTitle;
        $this->view->h2_title = $this->sTitle;
        $this->output();
    }

    /**
     * Prototype method to show the public and protected files.
     *
     * @param string $sFile Full path.
     * @param string $sExt Retrieves only files with specific extensions. Default NULL
     * @return void
     */
    private function _prototypeDisplayAction($sFile, $sExt = null)
    {
        $this->sTitle = t('File Management');
        $this->view->page_title = $this->sTitle;
        $this->view->h2_title = $this->sTitle;

        $this->view->filesList = $this->file->getFileList($sFile, $sExt);
    }

}
