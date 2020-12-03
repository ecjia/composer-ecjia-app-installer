<?php


namespace Ecjia\App\Installer\Hookers;


use ecjia_error;
use RC_DB;
use RC_Uri;
use Royalcms\Component\Database\QueryException;

/**
 * 更新PC内嵌的H5地址
 *
 * Class UpdateDemoApiUrlAction
 * @package Ecjia\App\Installer\Hookers
 */
class UpdateDemoApiUrlAction
{

    /**
     * Handle the event.
     * @return ecjia_error|bool
     */
    public function handle()
    {

        try {
            $url = RC_Uri::home_url() . '/sites/m/';

            return RC_DB::table('shop_config')->where('code', 'mobile_touch_url')->update(array('value' => $url));
        } catch (QueryException $e) {
            return new ecjia_error($e->getCode(), $e->getMessage());
        }

    }

}