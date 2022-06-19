<?php
/**
 * @author         Pierre-Henry Soria <hello@ph7builder.com>
 * @copyright      (c) 2012-2022, Pierre-Henry Soria. All Rights Reserved.
 * @license        MIT License; See LICENSE.md and COPYRIGHT.md in the root directory.
 * @package        PH7 / App / System / Module / Admin / Controller
 */

declare(strict_types=1);

namespace PH7;

use PH7\Framework\Cache\Cache;
use PH7\Framework\Date\CDateTime;
use PH7\Framework\Layout\Gzip\Gzip;
use PH7\Framework\Layout\Html\Design;
use PH7\Framework\Layout\Html\Security as HtmlSecurity;
use PH7\Framework\Layout\Tpl\Engine\PH7Tpl\PH7Tpl;
use PH7\Framework\Mvc\Model\Engine\Db;
use PH7\Framework\Mvc\Model\Engine\Util\Backup;
use PH7\Framework\Mvc\Router\Uri;
use PH7\Framework\Security\CSRF\Token;
use PH7\Framework\Url\Header;

class ToolController extends Controller
{
    private const BACKUP_FILE_EXTS = ['.sql', '.gz'];

    private const SERVER_ACTION = 'server';
    private const SERVER_ARCHIVE_ACTION = 'server_archive';
    private const CLIENT_ACTION = 'client';
    private const CLIENT_ARCHIVE_ACTION = 'client_archive';
    private const SHOW_ACTION = 'show';

    private string $sTitle;

    public function index(): void
    {
        $this->sTitle = t('General Tools');
        $this->view->page_title = $this->sTitle;
        $this->view->h1_title = $this->sTitle;

        $this->output();
    }

    public function cache(): void
    {
        // Add a CSRF token for the remove ajax cache request
        $this->view->csrf_token = (new Token)->generate('cache');

        $this->addGeneralCssFile();

        // Add JS file for the ajax cache feature
        $this->design->addJs(
            PH7_LAYOUT . PH7_SYS . PH7_MOD . $this->registry->module . PH7_SH . PH7_TPL . PH7_TPL_MOD_NAME . PH7_SH . PH7_JS,
            'common.js'
        );

        $this->sTitle = t('Caches Management');
        $this->view->page_title = $this->sTitle;
        $this->view->h1_title = $this->sTitle;

        $this->view->aChartData = [
            [
                'title' => t('Database and Other Data'),
                'size' => $this->file->getDirSize(PH7_PATH_CACHE . Cache::CACHE_DIR)
            ],
            [
                'title' => t('Server Code Template'),
                'size' => $this->file->getDirSize(PH7_PATH_CACHE . PH7Tpl::COMPILE_DIR)
            ],
            ['title' => t('HTML Template'), 'size' => $this->file->getDirSize(PH7_PATH_CACHE . PH7Tpl::CACHE_DIR)],
            ['title' => t('Static Files'), 'size' => $this->file->getDirSize(PH7_PATH_CACHE . Gzip::CACHE_DIR)]
        ];

        $this->output();
    }

    public function cacheConfig(): void
    {
        $this->sTitle = t('Cache Settings');
        $this->view->page_title = $this->sTitle;
        $this->view->h1_title = $this->sTitle;

        $this->output();
    }

    public function freeSpace(): void
    {
        $this->addGeneralCssFile();

        $this->sTitle = t('Free Space of Server');
        $this->view->page_title = $this->sTitle;
        $this->view->h1_title = $this->sTitle;

        $this->view->aChartData = [
            ['title' => t('Public Root'), 'size' => $this->file->getDirFreeSpace(PH7_PATH_ROOT)],
            ['title' => t('Public data'), 'size' => $this->file->getDirFreeSpace(PH7_PATH_PUBLIC_DATA)],
            ['title' => t('Protected Root'), 'size' => $this->file->getDirFreeSpace(PH7_PATH_PROTECTED)],
            ['title' => t('Protected data'), 'size' => $this->file->getDirFreeSpace(PH7_PATH_DATA)]
        ];

        $this->output();
    }

    public function envMode(): void
    {
        $this->sTitle = t('Environment Mode');
        $this->view->page_title = $this->sTitle;
        $this->view->h1_title = $this->sTitle;

        $this->output();
    }

    public function blockCountry(): void
    {
        $this->view->page_title = t('Country Blacklist');
        $this->view->h1_title = t('Block Countries');

        $this->output();
    }

    public function backup(): void
    {
        $this->addGeneralCssFile();

        $this->view->designSecurity = new HtmlSecurity; // Security Design Class

        $this->sTitle = t('Backup Management');
        $this->view->page_title = $this->sTitle;
        $this->view->h1_title = $this->sTitle;

        $aDumpList = $this->file->getFileList(PH7_PATH_BACKUP_SQL, static::BACKUP_FILE_EXTS);
        $aDumpList = $this->removePaths($aDumpList);
        $this->view->aDumpList = $aDumpList;

        $oSecurityToken = new Token;
        if ($this->httpRequest->postExists('backup')) {
            if (!$oSecurityToken->check('backup')) {
                $this->design->setFlashMsg(Form::errorTokenMsg(), Design::ERROR_TYPE);
            } else {
                // Clean the site name to avoid bug with the backup path
                $sSiteName = str_replace([' ', '/', '\\'], '_', $this->registry->site_name);
                $sCurrentDate = (new CDateTime)->get()->date();

                switch ($this->httpRequest->post('backup_type')) {
                    case self::SERVER_ACTION:
                        $sFileName = 'Database-dump.' . $sCurrentDate . '.sql';
                        (new Backup(PH7_PATH_BACKUP_SQL . $sFileName))->back()->save();
                        $this->view->msg_success = t('Data dumped into backup file "%0%"', $sFileName);
                        break;

                    case self::SERVER_ARCHIVE_ACTION:
                        $sFileName = 'Database-dump.' . $sCurrentDate . '.sql.gz';
                        (new Backup(PH7_PATH_BACKUP_SQL . $sFileName))->back()->saveArchive();
                        $this->view->msg_success = t('Data dumped into backup file "%0%"', $sFileName);
                        break;

                    case self::CLIENT_ACTION:
                        (new Backup($sSiteName . '_' . $sCurrentDate . '.sql'))->back()->download();
                        break;

                    case self::CLIENT_ARCHIVE_ACTION:
                        (new Backup($sSiteName . '_' . $sCurrentDate . '.sql.gz'))->back()->downloadArchive();
                        break;

                    case self::SHOW_ACTION:
                        $this->view->sql_content = (new Backup)->back()->show();
                        break;

                    default:
                        $this->design->setFlashMsg(
                            t('Please select a field.'),
                            Design::ERROR_TYPE
                        );
                }
            }
        }

        if ($this->httpRequest->postExists('restore_dump')) {
            if (!$oSecurityToken->check('backup')) {
                $this->design->setFlashMsg(Form::errorTokenMsg(), Design::ERROR_TYPE);
            } else {
                $sDumpFile = $this->httpRequest->post('dump_file');

                if (!empty($sDumpFile)) {
                    if ($this->file->getFileExt($sDumpFile) === Backup::SQL_FILE_EXT) {
                        $mStatus = (new Backup(PH7_PATH_BACKUP_SQL . $sDumpFile))->restore();
                    } elseif ($this->file->getFileExt($sDumpFile) === Backup::ARCHIVE_FILE_EXT) {
                        $mStatus = (new Backup(PH7_PATH_BACKUP_SQL . $sDumpFile))->restoreArchive();
                    } else {
                        $mStatus = t('Dump file must be a SQL type (extension ".sql" or compressed archive ".gz")');
                    }
                } else {
                    $mStatus = t('Please select a dump file.');
                }

                $sMsg = $mStatus === true ?
                    t('Data successfully restored from server!') :
                    (empty($mStatus) ? t('An error occurred.') : $mStatus);
                $sMsgType = $mStatus === true ? Design::SUCCESS_TYPE : Design::ERROR_TYPE;
                $this->design->setFlashMsg($sMsg, $sMsgType);
            }
        }

        if ($this->httpRequest->postExists('remove_dump')) {
            if (!$oSecurityToken->check('backup')) {
                $this->design->setFlashMsg(Form::errorTokenMsg(), Design::ERROR_TYPE);
            } else {
                $sDumpFile = $this->httpRequest->post('dump_file');

                if (!empty($sDumpFile)) {
                    $this->file->deleteFile(PH7_PATH_BACKUP_SQL . $sDumpFile);
                    $this->design->setFlashMsg(t('Dump file successfully deleted!'));
                } else {
                    $this->design->setFlashMsg(
                        t('Please select a dump file.'),
                        Design::ERROR_TYPE
                    );
                }
            }
        }
        unset($oSecurityToken);


        if ($this->httpRequest->postExists('restore_sql_file')) {
            if (!empty($_FILES['sql_file']['tmp_name'])) {
                $sNameFile = $_FILES['sql_file']['name'];
                $sTmpFile = $_FILES['sql_file']['tmp_name'];

                if ($this->file->getFileExt($sNameFile) === Backup::SQL_FILE_EXT) {
                    $mStatus = (new Backup($sTmpFile))->restore();
                } elseif ($this->file->getFileExt($sNameFile) === Backup::ARCHIVE_FILE_EXT) {
                    $mStatus = (new Backup($sTmpFile))->restoreArchive();
                } else {
                    $mStatus = t('Dump file must be a SQL type (extension ".sql" or compressed archive ".gz")');
                }

                // Remove the temporary file
                $this->file->deleteFile($sTmpFile);
            } else {
                $mStatus = t('Please select a dump SQL file.');
            }

            $sMsg = ($mStatus === true) ? t('Data successfully restored from desktop!') : $mStatus;
            $sMsgType = ($mStatus === true) ? Design::SUCCESS_TYPE : Design::ERROR_TYPE;
            $this->design->setFlashMsg($sMsg, $sMsgType);
        }

        $this->output();
    }

    public function optimize(): void
    {
        $this->checkPost();

        Db::optimize();
        Header::redirect(
            Uri::get(PH7_ADMIN_MOD, 'tool', 'index'),
            t('All tables have been optimized!')
        );
    }

    public function repair(): void
    {
        $this->checkPost();

        Db::repair();
        Header::redirect(
            Uri::get(PH7_ADMIN_MOD, 'tool', 'index'),
            t('All tables have been repaired!')
        );
    }

    /**
     * Includes the CSS file for the chart and/or for backup textarea field size.
     */
    private function addGeneralCssFile(): void
    {
        $this->design->addCss(
            PH7_LAYOUT . PH7_SYS . PH7_MOD . $this->registry->module . PH7_SH . PH7_TPL . PH7_TPL_MOD_NAME . PH7_SH . PH7_CSS,
            'general.css'
        );
    }

    /**
     * Checks and stops the script if the method is not POST.
     *
     * @return void The text by exit() function.
     */
    private function checkPost(): void
    {
        if (!$this->isPost()) {
            exit(Form::wrongRequestMethodMsg('POST'));
        }
    }

    /**
     * Checks if the request been made ​​by the post method.
     */
    private function isPost(): bool
    {
        return $this->httpRequest->postExists('is');
    }

    private function removePaths(array $aDumpList): array
    {
        return array_map(function ($sFullPath) {
            return str_replace(PH7_PATH_BACKUP_SQL, '', $sFullPath);
        }, $aDumpList);
    }
}
