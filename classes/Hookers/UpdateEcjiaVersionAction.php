<?php


namespace Ecjia\App\Installer\Hookers;


use ecjia_error;
use RC_Config;
use RC_DB;
use Royalcms\Component\Database\QueryException;

/**
 * 更新 ECJIA 版本
 *
 * Class UpdateEcjiaVersionAction
 * @package Ecjia\App\Installer\Hookers
 */
class UpdateEcjiaVersionAction
{

    /**
     * Handle the event.
     * @return ecjia_error|bool
     */
    public function handle()
    {
        try {
            $version = RC_Config::get('release.version', '3.0.0');
            RC_DB::table('shop_config')->where('code', 'mobile_app_version')->update(array('value' => $version));
            return RC_DB::table('shop_config')->where('code', 'ecjia_version')->update(array('value' => $version));
        } catch (QueryException $e) {
            return new ecjia_error($e->getCode(), $e->getMessage());
        }


    }

}