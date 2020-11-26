<?php


namespace Ecjia\App\Installer\Hookers;


use RC_File;

/**
 * 写入安装锁定文件
 *
 * Class SaveInstallLockFileAction
 * @package Ecjia\App\Installer\Hookers
 */
class SaveInstallLockFileAction
{

    /**
     * Handle the event.
     * @return
     */
    public function handle()
    {
        $path = storage_path() . '/data/install.lock';
        return RC_File::put($path, 'ECJIA INSTALLED');
    }

}