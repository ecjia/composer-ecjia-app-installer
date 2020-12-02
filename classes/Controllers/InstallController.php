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

namespace Ecjia\App\Installer\Controllers;

use Ecjia\App\Installer\BrowserEvent\AgreeChangeEvent;
use Ecjia\App\Installer\BrowserEvent\InstallCheckAgreeSubmitEvent;
use Ecjia\App\Installer\BrowserEvent\InstallCheckDatabaseAccountEvent;
use Ecjia\App\Installer\BrowserEvent\InstallCheckDatabaseExistsEvent;
use Ecjia\App\Installer\BrowserEvent\InstallCheckUserPasswordEvent;
use Ecjia\App\Installer\BrowserEvent\InstallStartEvent;
use Ecjia\App\Installer\BrowserEvent\PageEventManager;
use Ecjia\App\Installer\Exceptions\InstallLockedException;
use Ecjia\App\Installer\InstallChecker\Checkers\DirectoryPermissionCheck;
use Ecjia\App\Installer\InstallChecker\Checkers\DNSCheck;
use Ecjia\App\Installer\InstallChecker\Checkers\DomainCheck;
use Ecjia\App\Installer\InstallChecker\Checkers\ExtensionCurlCheck;
use Ecjia\App\Installer\InstallChecker\Checkers\ExtensionFileinfoCheck;
use Ecjia\App\Installer\InstallChecker\Checkers\ExtensionGDCheck;
use Ecjia\App\Installer\InstallChecker\Checkers\ExtensionMysqliCheck;
use Ecjia\App\Installer\InstallChecker\Checkers\ExtensionOpensslCheck;
use Ecjia\App\Installer\InstallChecker\Checkers\ExtensionPdoMysqlCheck;
use Ecjia\App\Installer\InstallChecker\Checkers\ExtensionSocketCheck;
use Ecjia\App\Installer\InstallChecker\Checkers\ExtensionZlibCheck;
use Ecjia\App\Installer\InstallChecker\Checkers\PHPOSCheck;
use Ecjia\App\Installer\InstallChecker\Checkers\PHPVersionCheck;
use Ecjia\App\Installer\InstallChecker\Checkers\SafeModeCheck;
use Ecjia\App\Installer\InstallChecker\Checkers\TimezoneCheck;
use Ecjia\App\Installer\InstallChecker\Checkers\UploadMaxFilesizeCheck;
use Ecjia\App\Installer\InstallChecker\Checkers\WebPathCheck;
use Ecjia\App\Installer\InstallChecker\Checkers\WebServerCheck;
use Ecjia\App\Installer\InstallChecker\InstallChecker;
use Ecjia\App\Installer\InstallChecker\InstallCheckStatus;
use Ecjia\App\Installer\InstallCookie;
use Ecjia\App\Installer\InstallDatabase;
use Ecjia\App\Installer\InstallEnvConfig;
use Ecjia\App\Installer\InstallMigrationFile;
use Ecjia\App\Installer\InstallPercent;
use Ecjia\App\Installer\Timezone;
use RC_App;
use RC_Hook;
use RC_Uri;
use RC_Cache;
use Ecjia\App\Installer\Helper;
use ecjia;

class InstallController extends BaseControllerAbstract
{

    protected $cookie;

    public function __construct()
    {
        parent::__construct();

        //安装脚本不限制超时时间
        set_time_limit(0);

        $this->cookie = new InstallCookie();
    }

    /**
     * 创建配置文件
     */
    public function create_config_file()
    {
//        $this->check_installed();

        try {

            $db_host     = trim($this->request->input('db_host'));
            $db_port     = trim($this->request->input('db_port'));
            $db_user     = trim($this->request->input('db_user'));
            $db_pass     = trim($this->request->input('db_pass'));
            $db_database = trim($this->request->input('db_database'));
            $db_prefix   = trim($this->request->input('db_prefix'));
            $timezone    = trim($this->request->input('timezone', 'Asia/Shanghai'));

            $auth_key = Helper::getAuthKey();

            $data = array(
                'DB_HOST'     => $db_host,
                'DB_PORT'     => $db_port,
                'DB_DATABASE' => $db_database,
                'DB_USERNAME' => $db_user,
                'DB_PASSWORD' => $db_pass,
                'DB_PREFIX'   => $db_prefix,
                'TIMEZONE'    => $timezone,
                'AUTH_KEY'    => $auth_key,
            );

            $installEnv = new InstallEnvConfig();
            $installEnv->createEnv();
            $result = $installEnv->modifyEnv($data);

            if (is_ecjia_error($result)) {
                return $this->showmessage($result->get_error_message(), ecjia::MSGTYPE_JSON | ecjia::MSGSTAT_ERROR);
            }

            $percent = (new InstallPercent($this->cookie))->setStepValue(InstallPercent::CREATE_CONFIG_FILE_PART)->getPercent();
            return $this->showmessage('', ecjia::MSGTYPE_JSON | ecjia::MSGSTAT_SUCCESS, array('percent' => $percent));

        } catch (\Exception $exception) {
            return $this->showmessage($exception->getMessage(), ecjia::MSGTYPE_JSON | ecjia::MSGSTAT_ERROR);
        }
    }

    /**
     * 创建数据库
     */
    public function create_database()
    {
        try {
//        $this->check_installed();

            $db_host     = trim($this->request->input('db_host'));
            $db_port     = trim($this->request->input('db_port'));
            $db_user     = trim($this->request->input('db_user'));
            $db_pass     = trim($this->request->input('db_pass'));
            $db_database = trim($this->request->input('db_database'));

            $result = (new InstallDatabase($db_host, $db_port, $db_user, $db_pass))->createDatabase($db_database);

            if (is_ecjia_error($result)) {
                return $this->showmessage($result->get_error_message(), ecjia::MSGTYPE_JSON | ecjia::MSGSTAT_ERROR);
            }

            $percent = (new InstallPercent($this->cookie))->setStepValue(InstallPercent::CREATE_DATABASE_PART)->getPercent();
//            $percent = $this->get_percent('create_database');
            return $this->showmessage('', ecjia::MSGTYPE_JSON | ecjia::MSGSTAT_SUCCESS, array('percent' => $percent));
        } catch (\Exception $exception) {
            return $this->showmessage($exception->getMessage(), ecjia::MSGTYPE_JSON | ecjia::MSGSTAT_ERROR);
        }
    }

    /**
     * 安装数据库结构
     */
    public function install_structure()
    {
        try {
//        $this->check_installed();

//            $config  = config('database');
            $default = royalcms('config')->get('database.connections.default');

            $db_host     = trim($this->request->input('db_host'));
            $db_port     = trim($this->request->input('db_port'));
            $db_user     = trim($this->request->input('db_user'));
            $db_pass     = trim($this->request->input('db_pass'));
            $db_database = trim($this->request->input('db_database'));
            $db_prefix   = trim($this->request->input('db_prefix'));

            //动态修改数据库连接配置
            $default['host']     = $db_host;
            $default['port']     = $db_port;
            $default['username'] = $db_user;
            $default['password'] = $db_pass;
            $default['database'] = $db_database;
            $default['prefix']   = $db_prefix;

            royalcms('config')->set('database.connections.default', $default);

            $limit = 20;

            $migrate = new InstallMigrationFile();

            $result = $migrate->installStructure($limit);

            if (is_ecjia_error($result)) {
                return $this->showmessage($result->get_error_message(), ecjia::MSGTYPE_JSON | ecjia::MSGSTAT_ERROR);
            }

            $more = 0;

            //还剩余多少个脚本未执行
            $over = $migrate->getWillMigrationFilesCount();

            if (is_ecjia_error($over)) {
                return $this->showmessage($over->get_error_message(), ecjia::MSGTYPE_JSON | ecjia::MSGSTAT_ERROR);
            }

            if ($over > 0) {
                $more = $over;
            }

//        $percent = $this->get_percent('install_structure');
            $percent = (new InstallPercent($this->cookie))->setStepValue(InstallPercent::INSTALL_STRUCTURE_PART)->getPercent();

            return $this->showmessage('', ecjia::MSGTYPE_JSON | ecjia::MSGSTAT_SUCCESS, array('percent' => $percent, 'more' => $more));
        } catch (\Exception $exception) {
            return $this->showmessage($exception->getMessage(), ecjia::MSGTYPE_JSON | ecjia::MSGSTAT_ERROR);
        } catch (\TypeError $exception) {
            // 捕获类型错误 返回值/参数不正确
            return $this->showmessage($exception->getMessage(), ecjia::MSGTYPE_JSON | ecjia::MSGSTAT_ERROR);
        } catch (\Error $exception) {
            // 捕获类型错误 返回值/参数不正确
            return $this->showmessage($exception->getMessage(), ecjia::MSGTYPE_JSON | ecjia::MSGSTAT_ERROR);
        }
    }

    /**
     * 安装基本数据
     */
    public function install_base_data()
    {
        try {
//        $this->check_installed();

            $db_host     = trim($this->request->input('db_host'));
            $db_port     = trim($this->request->input('db_port'));
            $db_user     = trim($this->request->input('db_user'));
            $db_pass     = trim($this->request->input('db_pass'));
            $db_database = trim($this->request->input('db_database'));
            $db_prefix   = trim($this->request->input('db_prefix'));

            //动态修改数据库连接配置
            $default['host']     = $db_host;
            $default['port']     = $db_port;
            $default['username'] = $db_user;
            $default['password'] = $db_pass;
            $default['database'] = $db_database;
            $default['prefix']   = $db_prefix;

            royalcms('config')->set('database.connections.default', $default);

            $result = Helper::installBaseData();

            if (is_ecjia_error($result)) {
                return $this->showmessage($result->get_error_message(), ecjia::MSGTYPE_JSON | ecjia::MSGSTAT_ERROR);
            }

//        $percent = $this->get_percent('install_base_data');
            $percent = (new InstallPercent($this->cookie))->setStepValue(InstallPercent::INSTALL_BASE_DATA_PART)->getPercent();
            return $this->showmessage('', ecjia::MSGTYPE_JSON | ecjia::MSGSTAT_SUCCESS, array('percent' => $percent));
        } catch (\Exception $exception) {
            return $this->showmessage($exception->getMessage(), ecjia::MSGTYPE_JSON | ecjia::MSGSTAT_ERROR);
        }
    }

    /**
     * 安装演示数据
     */
    public function install_demo_data()
    {
        try {
//        $this->check_installed();
            $db_host     = trim($this->request->input('db_host'));
            $db_port     = trim($this->request->input('db_port'));
            $db_user     = trim($this->request->input('db_user'));
            $db_pass     = trim($this->request->input('db_pass'));
            $db_database = trim($this->request->input('db_database'));
            $db_prefix   = trim($this->request->input('db_prefix'));

            //动态修改数据库连接配置
            $default['host']     = $db_host;
            $default['port']     = $db_port;
            $default['username'] = $db_user;
            $default['password'] = $db_pass;
            $default['database'] = $db_database;
            $default['prefix']   = $db_prefix;

            royalcms('config')->set('database.connections.default', $default);

            $result = Helper::installDemoData();

            if (is_ecjia_error($result)) {
                return $this->showmessage($result->get_error_message(), ecjia::MSGTYPE_JSON | ecjia::MSGSTAT_ERROR);
            }

//        $percent = $this->get_percent('install_demo_data');
            $percent = (new InstallPercent($this->cookie))->setStepValue(InstallPercent::INSTALL_DEMO_DATA_PART)->getPercent();
            return $this->showmessage('', ecjia::MSGTYPE_JSON | ecjia::MSGSTAT_SUCCESS, array('percent' => $percent));
        } catch (\Exception $exception) {
            return $this->showmessage($exception->getMessage(), ecjia::MSGTYPE_JSON | ecjia::MSGSTAT_ERROR);
        }
    }

    /**
     * 创建管理员
     */
    public function create_admin_passport()
    {
        try {
//        $this->check_installed();
            $db_host     = trim($this->request->input('db_host'));
            $db_port     = trim($this->request->input('db_port'));
            $db_user     = trim($this->request->input('db_user'));
            $db_pass     = trim($this->request->input('db_pass'));
            $db_database = trim($this->request->input('db_database'));
            $db_prefix   = trim($this->request->input('db_prefix'));

            //动态修改数据库连接配置
            $default['host']     = $db_host;
            $default['port']     = $db_port;
            $default['username'] = $db_user;
            $default['password'] = $db_pass;
            $default['database'] = $db_database;
            $default['prefix']   = $db_prefix;

            royalcms('config')->set('database.connections.default', $default);

            $admin_name      = isset($_POST['admin_name']) ? trim($_POST['admin_name']) : '';
            $admin_password  = isset($_POST['admin_password']) ? trim($_POST['admin_password']) : '';
            $admin_password2 = isset($_POST['admin_password2']) ? trim($_POST['admin_password2']) : '';
            $admin_email     = isset($_POST['admin_email']) ? trim($_POST['admin_email']) : '';

            RC_Cache::app_cache_set('admin_name', $admin_name, 'install');
            RC_Cache::app_cache_set('admin_password', $admin_password, 'install');

            if (!$admin_name) {
                return $this->showmessage(__('管理员名称不能为空', 'installer'), ecjia::MSGTYPE_JSON | ecjia::MSGSTAT_ERROR);
            }

            if (!$admin_password) {
                return $this->showmessage(__('密码不能为空', 'installer'), ecjia::MSGTYPE_JSON | ecjia::MSGSTAT_ERROR);
            }

            if (!(strlen($admin_password) >= 8 && preg_match("/\d+/", $admin_password) && preg_match("/[a-zA-Z]+/", $admin_password))) {
                return $this->showmessage(__('密码必须同时包含字母及数字', 'installer'), ecjia::MSGTYPE_JSON | ecjia::MSGSTAT_ERROR);
            }

            if (!(strlen($admin_password2) >= 8 && preg_match("/\d+/", $admin_password2) && preg_match("/[a-zA-Z]+/", $admin_password2))) {
                return $this->showmessage(__('密码必须同时包含字母及数字', 'installer'), ecjia::MSGTYPE_JSON | ecjia::MSGSTAT_ERROR);
            }

            if ($admin_password != $admin_password2) {
                return $this->showmessage(__('密码不相同', 'installer'), ecjia::MSGTYPE_JSON | ecjia::MSGSTAT_ERROR);
            }

            $result = Helper::createAdminPassport($admin_name, $admin_password, $admin_email);

            if (is_ecjia_error($result)) {
                return $this->showmessage($result->get_error_message(), ecjia::MSGTYPE_JSON | ecjia::MSGSTAT_ERROR);
            }

//            $percent = $this->get_percent('create_admin_passport');
            $percent = (new InstallPercent($this->cookie))->setStepValue(InstallPercent::CREATE_ADMIN_PASSPORT_PART)->getPercent();
            return $this->showmessage('', ecjia::MSGTYPE_JSON | ecjia::MSGSTAT_SUCCESS, array('percent' => $percent));
        } catch (\Exception $exception) {
            return $this->showmessage($exception->getMessage(), ecjia::MSGTYPE_JSON | ecjia::MSGSTAT_ERROR);
        }
    }


//    private function get_percent($step)
//    {
//
//        $sqlcount = count(scandir(royalcms('path') . '/content/database/migrations')) - 2;
//
//        if ($step == 'create_config_file') {
//            $past = 20;
//        } else if ($step == 'create_database') {
//            $past = 40;
//        } else if ($step == 'install_structure') {
//            $over = Helper::getWillMigrationFilesCount();
//            if (!is_ecjia_error($over))
//                $past = 40 + $sqlcount - $over;
//        } else if ($step == 'install_base_data') {
//            $past = 40 + $sqlcount + 20;
//        } else if ($step == 'install_demo_data') {
//            $past = 40 + $sqlcount + 40;
//        } else if ($step == 'create_admin_passport') {
//            //             $past = 4 +  $_SESSION['temp']['sqlcount'] + 6;
//            return 100;
//        }
//        $total = $sqlcount + 20 + 20 + 20 + 20;
//
//
//        return $percent = floor($past / $total * 100);
//
//    }
}

//end