<?php

use Bitrix\Main\Application;
use Bitrix\Main\Localization\Loc;
use Bitrix\Main\ModuleManager;
use Bitrix\Main\UrlRewriter;

Loc::loadMessages(__FILE__);

class local_notes extends CModule
{
    public $MODULE_ID = 'local.notes';
    public $MODULE_VERSION;
    public $MODULE_VERSION_DATE;
    public $MODULE_NAME;
    public $MODULE_DESCRIPTION;
    public $PARTNER_NAME = 'Local';
    public $PARTNER_URI = '';

    public function __construct()
    {
        $arModuleVersion = [];
        include __DIR__ . '/version.php';

        $this->MODULE_VERSION = $arModuleVersion['VERSION'];
        $this->MODULE_VERSION_DATE = $arModuleVersion['VERSION_DATE'];
        $this->MODULE_NAME = Loc::getMessage('LOCAL_NOTES_MODULE_NAME');
        $this->MODULE_DESCRIPTION = Loc::getMessage('LOCAL_NOTES_MODULE_DESC');
    }

    public function DoInstall()
    {
        global $APPLICATION;

        $this->InstallDB();
        $this->InstallFiles();
        $this->InstallUrlRewrite();
        ModuleManager::registerModule($this->MODULE_ID);

        $APPLICATION->IncludeAdminFile(
            $this->MODULE_NAME,
            __DIR__ . '/step.php'
        );
    }

    public function DoUninstall()
    {
        global $APPLICATION, $step;

        $step = (int) ($_REQUEST['step'] ?? 1);
        if ($step < 2) {
            $APPLICATION->IncludeAdminFile($this->MODULE_NAME, __DIR__ . '/unstep1.php');
            return;
        }

        $keepData = !empty($_REQUEST['savedata']);

        $this->UnInstallUrlRewrite();
        $this->UnInstallFiles();
        if (!$keepData) {
            $this->UnInstallDB();
        }

        ModuleManager::unRegisterModule($this->MODULE_ID);
        $APPLICATION->IncludeAdminFile($this->MODULE_NAME, __DIR__ . '/unstep2.php');
    }

    public function InstallDB()
    {
        $connection = Application::getConnection();
        if (!$connection->isTableExists('b_local_notes')) {
            $sql = file_get_contents(__DIR__ . '/db/mysql/install.sql');
            $connection->queryExecute($sql);
        }

        return true;
    }

    public function UnInstallDB()
    {
        $sql = file_get_contents(__DIR__ . '/db/mysql/uninstall.sql');
        Application::getConnection()->queryExecute($sql);

        return true;
    }

    public function InstallFiles()
    {
        CopyDirFiles(
            __DIR__ . '/../public',
            Application::getDocumentRoot() . '/local/api',
            true,
            true
        );

        return true;
    }

    public function UnInstallFiles()
    {
        DeleteDirFilesEx('/local/api/notes');
        DeleteDirFilesEx('/local/api/docs');

        return true;
    }

    public function InstallUrlRewrite()
    {
        $sites = CSite::GetList('sort', 'asc', ['ACTIVE' => 'Y']);
        while ($site = $sites->Fetch()) {
            UrlRewriter::delete($site['LID'], ['ID' => 'local.notes.rest']);
            UrlRewriter::add($site['LID'], [
                'CONDITION' => '#^/api/(notes|health)(/|$)#',
                'RULE' => '',
                'ID' => 'local.notes.rest',
                'PATH' => '/local/api/notes/index.php',
                'SORT' => 10,
            ]);
        }
    }

    public function UnInstallUrlRewrite()
    {
        $sites = CSite::GetList('sort', 'asc', ['ACTIVE' => 'Y']);
        while ($site = $sites->Fetch()) {
            UrlRewriter::delete($site['LID'], ['ID' => 'local.notes.rest']);
        }
    }
}
