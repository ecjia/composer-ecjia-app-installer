<?php


namespace Ecjia\App\Installer\Hookers;


use ecjia_error;
use RC_DB;
use RC_Time;
use Royalcms\Component\Database\QueryException;

/**
 * 更新 ECJIA 安装日期
 *
 * Class UpdateEcjiaInstallDateAction
 * @package Ecjia\App\Installer\Hookers
 */
class UpdateEcjiaInstallDateAction
{

    /**
     * Handle the event.
     * @return ecjia_error
     */
    public function handle()
    {

        try {
            return RC_DB::table('shop_config')->where('code', 'install_date')->update(array('value' => RC_Time::gmtime()));
        } catch (QueryException $e) {
            return new ecjia_error($e->getCode(), $e->getMessage());
        }

    }

}