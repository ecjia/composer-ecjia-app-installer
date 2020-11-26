<?php


namespace Ecjia\App\Installer;


use Ecjia\Component\Database\Migrate;
use Ecjia\Component\Database\Seeder;
use ecjia_error;
use Royalcms\Component\Database\QueryException;
use Royalcms\Component\Exception\Exceptions\RecoverableErrorException;

class InstallMigrationFile
{

    protected $migrate;

    /**
     * InstallMigrationFile constructor.
     */
    public function __construct()
    {
        $this->migrate = new Migrate();
    }


    /**
     * 安装数据库结构
     *
     * @param int $limit
     * @return  boolean | ecjia_error   成功返回true，失败返回ecjia_error
     */
    public function installStructure($limit = 20)
    {
        try {
            return $this->migrate->fire($limit);
        }
        catch (QueryException $e) {
            return new ecjia_error($e->getCode(), $e->getMessage());
        }
    }

    /**
     * 获取将要安装的脚本数量
     */
    public function getWillMigrationFilesCount()
    {
        try {
            return $this->migrate->getWillMigrationFilesCount();
        }
        catch (QueryException $e) {
            return new ecjia_error($e->getCode(), $e->getMessage());
        }
    }

    /**
     * 填充数据表基础数据
     *
     * @return  boolean | ecjia_error    成功返回true，失败返回ecjia_error
     */
    public static function installBaseData()
    {
        try {
            $seeder = new Seeder('InitDatabaseSeeder');

            $seeder->fire();

            return true;
        }
        catch (QueryException $e) {
            return new ecjia_error($e->getCode(), $e->getMessage());
        }
        catch (RecoverableErrorException $e) {
            return new ecjia_error('recoverable_error_exception', $e->getMessage());
        }
    }

    /**
     * 填充数据表演示数据
     *
     * @return  boolean | ecjia_error   成功返回true，失败返回ecjia_error
     */
    public static function installDemoData()
    {
        try {
            $seeder = new Seeder('DemoDatabaseSeeder');

            $seeder->fire();

            return true;
        }
        catch (QueryException $e) {
            return new ecjia_error($e->getCode(), $e->getMessage());
        }
        catch (RecoverableErrorException $e) {
            return new ecjia_error('recoverable_error_exception', $e->getMessage());
        }
    }


}