<?php
/**
 * @author         Pierre-Henry Soria <ph7software@gmail.com>
 * @copyright      (c) 2012-2017, Pierre-Henry Soria. All Rights Reserved.
 * @license        GNU General Public License; See PH7.LICENSE.txt and PH7.COPYRIGHT.txt in the root directory.
 * @package        PH7 / App / System / Module / Admin / Controller
 */

namespace PH7;

use PH7\Framework\File\File;

class ModuleController extends Controller
{
    /** @var Module */
    private $_oModule;

    /** @var string */
    private $_sModsDirModFolder;

    /** @var string */
    private $_sTitle;

    public function __construct()
    {
        parent::__construct();

        $this->_oModule = new Module;

        $this->view->oFile = new File;
        $this->view->oModule = $this->_oModule;
    }

    public function disable()
    {
        $this->_sTitle = t('Enable/Disable System Modules');
        $this->view->page_title = $this->_sTitle;
        $this->view->h1_title = $this->_sTitle;
        $this->output();
    }

    public function index()
    {
        if ($this->httpRequest->postExists('submit_mod_install')) {
            if ($this->_oModule->checkModFolder(Module::INSTALL, $this->httpRequest->post('submit_mod_install'))) {
                $this->_sModsDirModFolder = $this->httpRequest->post('submit_mod_install'); // Module Directory Path
                $this->_install();
            }
        } elseif ($this->httpRequest->postExists('submit_mod_uninstall')) {
            if ($this->_oModule->checkModFolder(Module::UNINSTALL, $this->httpRequest->post('submit_mod_uninstall'))) {
                $this->_sModsDirModFolder = $this->httpRequest->post('submit_mod_uninstall'); // Module Directory Path
                $this->_unInstall();
            }
        } else {
            $this->_sTitle = t('Module Manager');
            $this->view->page_title = $this->_sTitle;
            $this->view->h1_title = $this->_sTitle;

            $this->output();
        }
    }

    private function _install()
    {
        $this->_sTitle = t('Install Module Finished');
        $this->view->page_title = $this->_sTitle;
        $this->view->h1_title = $this->_sTitle;

        $this->_oModule->setPath($this->_sModsDirModFolder);

        $this->_oModule->run(Module::INSTALL); // Run Install Module!

        $this->view->content = $this->_oModule->readInstruction(Module::INSTALL);

        $this->manualTplInclude('install.tpl');
        $this->output();
    }

    private function _unInstall()
    {
        $this->_sTitle = t('Uninstall Module Finished');
        $this->view->page_title = $this->_sTitle;
        $this->view->h1_title = $this->_sTitle;

        $this->_oModule->setPath($this->_sModsDirModFolder);
        $this->_oModule->run(Module::UNINSTALL); // Run Uninstall Module!

        $this->view->content = $this->_oModule->readInstruction(Module::UNINSTALL);

        $this->manualTplInclude('uninstall.tpl');
        $this->output();
    }
}
