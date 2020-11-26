<?php


namespace Ecjia\App\Installer\Hookers;

use ecjia_cloud;
use ecjia_error;
use RC_Config;
use RC_DB;
use RC_Ip;
use RC_Uri;
use Royalcms\Component\Database\QueryException;

/**
 * 写入 hash_code，做为网站唯一性密钥
 *
 * Class UpdateHashCodeAction
 * @package Ecjia\App\Installer\Hookers
 */
class UpdateHashCodeAction
{

    /**
     * Handle the event.
     * @return ecjia_error
     */
    public function handle()
    {
        $dbhash = md5(SITE_ROOT . env('DB_HOST') . env('DB_USERNAME') . env('DB_PASSWORD') . env('DB_DATABASE'));
        $hash_code = md5(md5(time()) . md5($dbhash) . md5(time()));

        $data = array(
            'shop_url' => RC_Uri::home_url(),
            'hash_code' => $hash_code,
            'ip' => RC_Ip::server_ip(),
            'shop_type' => RC_Config::get('site.shop_type'),
            'version' => RC_Config::get('release.version'),
            'release' => RC_Config::get('release.build'),
            'language' => RC_Config::get('system.locale'),
            'charset' => 'utf-8',
            'php_ver' => PHP_VERSION,
            'mysql_ver' => self::getMysqlVersionByConnection(RC_DB::connection()),
            'ecjia_version' => VERSION,
            'ecjia_release' => RELEASE,
            'royalcms_version' => \Royalcms\Component\Foundation\Royalcms::VERSION,
            'royalcms_release' => \Royalcms\Component\Foundation\Royalcms::RELEASE,
        );
        ecjia_cloud::instance()->api('product/analysis/install')->data($data)->run();

        try {
            return RC_DB::table('shop_config')->where('code', 'hash_code')->update(array('value' => $hash_code));
        } catch (QueryException $e) {
            return new ecjia_error($e->getCode(), $e->getMessage());
        }
    }

}