<?php


namespace Ecjia\App\Installer\Hookers;


use Ecjia\App\Installer\DatabaseConfig;
use ecjia_config;
use ecjia_error;
use RC_Time;
use Royalcms\Component\Database\QueryException;

/**
 * 重设数据库连接池
 *
 * Class ResetDatabaseConfigAction
 * @package Ecjia\App\Installer\Hookers
 */
class ResetDatabaseConfigAction
{

    /**
     * Handle the event.
     * @return void
     */
    public function handle()
    {
        $db_host     = env('DB_HOST');
        $db_port     = env('DB_PORT', 3306);
        $db_user     = env('DB_USERNAME');
        $db_pass     = env('DB_PASSWORD');
        $db_database = env('DB_DATABASE');
        $db_prefix   = env('DB_PREFIX');

        //重设数据库连接
        (new DatabaseConfig('default'))->resetConfig($db_host, $db_port, $db_user, $db_pass, $db_database, $db_prefix);
    }

}