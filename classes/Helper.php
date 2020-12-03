<?php
//
//    ______         ______           __         __         ______
//   /\  ___\       /\  ___\         /\_\       /\_\       /\  __ \
//   \/\  __\       \/\ \____        \/\_\      \/\_\      \/\ \_\ \
//    \/\_____\      \/\_____\     /\_\/\_\      \/\_\      \/\_\ \_\
//     \/_____/       \/_____/     \/__\/_/       \/_/       \/_/ /_/
//
//   上海商创网络科技有限公司
//
//  ---------------------------------------------------------------------------------
//
//   一、协议的许可和权利
//
//    1. 您可以在完全遵守本协议的基础上，将本软件应用于商业用途；
//    2. 您可以在协议规定的约束和限制范围内修改本产品源代码或界面风格以适应您的要求；
//    3. 您拥有使用本产品中的全部内容资料、商品信息及其他信息的所有权，并独立承担与其内容相关的
//       法律义务；
//    4. 获得商业授权之后，您可以将本软件应用于商业用途，自授权时刻起，在技术支持期限内拥有通过
//       指定的方式获得指定范围内的技术支持服务；
//
//   二、协议的约束和限制
//
//    1. 未获商业授权之前，禁止将本软件用于商业用途（包括但不限于企业法人经营的产品、经营性产品
//       以及以盈利为目的或实现盈利产品）；
//    2. 未获商业授权之前，禁止在本产品的整体或在任何部分基础上发展任何派生版本、修改版本或第三
//       方版本用于重新开发；
//    3. 如果您未能遵守本协议的条款，您的授权将被终止，所被许可的权利将被收回并承担相应法律责任；
//
//   三、有限担保和免责声明
//
//    1. 本软件及所附带的文件是作为不提供任何明确的或隐含的赔偿或担保的形式提供的；
//    2. 用户出于自愿而使用本软件，您必须了解使用本软件的风险，在尚未获得商业授权之前，我们不承
//       诺提供任何形式的技术支持、使用担保，也不承担任何因使用本软件而产生问题的相关责任；
//    3. 上海商创网络科技有限公司不对使用本产品构建的商城中的内容信息承担责任，但在不侵犯用户隐
//       私信息的前提下，保留以任何方式获取用户信息及商品信息的权利；
//
//   有关本产品最终用户授权协议、商业授权与技术服务的详细内容，均由上海商创网络科技有限公司独家
//   提供。上海商创网络科技有限公司拥有在不事先通知的情况下，修改授权协议的权力，修改后的协议对
//   改变之日起的新授权用户生效。电子文本形式的授权协议如同双方书面签署的协议一样，具有完全的和
//   等同的法律效力。您一旦开始修改、安装或使用本产品，即被视为完全理解并接受本协议的各项条款，
//   在享有上述条款授予的权力的同时，受到相关的约束和限制。协议许可范围以外的行为，将直接违反本
//   授权协议并构成侵权，我们有权随时终止授权，责令停止损害，并保留追究相关责任的权力。
//
//  ---------------------------------------------------------------------------------
//

namespace Ecjia\App\Installer;

use Royalcms\Component\Database\Connection;
use Royalcms\Component\Database\QueryException;
use Royalcms\Component\Exception\RecoverableErrorException;
use Ecjia\System\Database\Seeder;
use Ecjia\System\Database\Migrate;
use RC_Package;
use RC_Lang;
use RC_Time;
use RC_DB;
use RC_File;
use RC_Config;
use RC_Uri;
use RC_Ip;
use RC_Upload;
use PDO;
use PDOException;
use Exception;
use ecjia_error;
use ecjia_cloud;

class Helper
{

    /**
     * 检测目录权限
     */
    public static function getCheckDirPermission()
    {
        $dirs = [
            '/'                 => '',
            'content/configs'   => str_replace(SITE_ROOT, '', SITE_CONTENT_PATH) . 'configs',
            'content/uploads'   => str_replace(SITE_ROOT, '', RC_Upload::upload_path()),
            'content/storages'  => str_replace(SITE_ROOT, '', storage_path()),
        ];

        $list = array();

        /* 检查目录 */
        foreach ($dirs AS $key => $val) {
            $mark = RC_File::file_mode_info(SITE_ROOT . $val);

            $list[] = array(
                'item'  => $key . __('目录', 'installer'),
                'mark'  => $mark,
                'r'     => $mark & 1,
                'w'     => $mark & 2,
                'm'     => $mark & 4
            );

        }

        return $list;
    }

    
    /**
     * 获取一个新的auth_key
     * @return string
     */
    public static function getAuthKey() {
        return rc_random(32, 'AaBbCcDdEeFfGgHhIiJjKkLlMmNnOoPpQqRrSsTtUuVvWwXxYyZz0123456789');
    }
    
//    /**
//     * 修改.env文件
//     * @param array $data
//     * @return string | null | ecjia_error
//     */
//    public static function modifyEnv(array $data)
//    {
//        try {
//            $envPath = base_path() . DIRECTORY_SEPARATOR . '.env';
//
//            $contentArray = collect(file($envPath, FILE_IGNORE_NEW_LINES));
//
//            $contentArray->transform(function ($item) use ($data){
//                foreach ($data as $key => $value) {
//                    if (str_contains($item, $key)) {
//                        return $key . '=' . $value;
//                    }
//                }
//
//                return $item;
//            });
//
//            $content = implode($contentArray->toArray(), "\n");
//
//            return RC_File::put($envPath, $content);
//
//        } catch (Exception $e) {
//            return new ecjia_error('write_config_file_failed', __('写入配置文件出错', 'installer'));
//        }
//
//    }
    
//    /**
//     * 创建.env文件
//     */
//    public static function createEnv()
//    {
//        $envExamplePath = DATA_PATH . 'env.example';
//        $envPath = base_path() . DIRECTORY_SEPARATOR . '.env';
//
//        if (RC_File::exists($envExamplePath)) {
//            RC_File::copy($envExamplePath, $envPath);
//        }
//    }
    
    /**
     * 更新 ECJIA 安装日期
     * @return array | ecjia_error
     */
    public static function updateInstallDate()
    {
        try {
            return RC_DB::table('shop_config')->where('code', 'install_date')->update(array('value' => RC_Time::gmtime()));
        } catch (QueryException $e) {
            return new ecjia_error($e->getCode(), $e->getMessage());
        }
    }
    
    /**
     * 更新 ECJIA 版本
     * @return ecjia_error
     */
    public static function updateEcjiaVersion()
    {
        try {
            $version = RC_Config::get('release.version', '1.3.0');
            RC_DB::table('shop_config')->where('code', 'mobile_app_version')->update(array('value' => $version));
            return RC_DB::table('shop_config')->where('code', 'ecjia_version')->update(array('value' => $version));
        } catch (QueryException $e) {
            return new ecjia_error($e->getCode(), $e->getMessage());
        }
    }
    
    /**
     * 写入 hash_code，做为网站唯一性密钥
     * @return ecjia_error
     */
    public static function updateHashCode()
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
            'mysql_ver' => DatabaseConnection::getMysqlVersionByConnection(RC_DB::connection()),
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
    
    /**
     * 更新PC内嵌的H5地址
     */
    public static function updateDemoApiUrl()
    {
        try {
            $url = RC_Uri::home_url() . '/sites/m/';
            
            return RC_DB::table('shop_config')->where('code', 'mobile_touch_url')->update(array('value' => $url));
        } catch (QueryException $e) {
            return new ecjia_error($e->getCode(), $e->getMessage());
        }
    }
    
    /**
     * 创建存储目录
     */
    public static function createStorageDirectory()
    {
        $dirs = RC_Package::package('app::installer')->loadConfig('checking_dirs');
        collect($dirs)->map(function ($dir) {
            if (!RC_File::isDirectory($dir)) {
                RC_File::makeDirectory($dir);
            }
        });
    }
    
    
    /**
     * 写入安装锁定文件
     */
    public static function saveInstallLock()
    {        
        $path = storage_path() . '/data/install.lock';
        return RC_File::put($path, 'ECJIA INSTALLED');
    }
    
    /**
     * 判断安装锁文件是否存在
     */
    public static function checkInstallLock()
    {
        $path = storage_path() . '/data/install.lock';
        return RC_File::exists($path);
    }


}

//end