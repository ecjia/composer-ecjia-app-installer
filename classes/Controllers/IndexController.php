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

use Ecjia\App\Installer\Checkers\DirectoryPermissionCheck;
use Ecjia\App\Installer\Checkers\DNSCheck;
use Ecjia\App\Installer\Checkers\DomainCheck;
use Ecjia\App\Installer\Checkers\ExtensionCurlCheck;
use Ecjia\App\Installer\Checkers\ExtensionFileinfoCheck;
use Ecjia\App\Installer\Checkers\ExtensionGDCheck;
use Ecjia\App\Installer\Checkers\ExtensionMysqliCheck;
use Ecjia\App\Installer\Checkers\ExtensionOpensslCheck;
use Ecjia\App\Installer\Checkers\ExtensionPdoMysqlCheck;
use Ecjia\App\Installer\Checkers\ExtensionSocketCheck;
use Ecjia\App\Installer\Checkers\ExtensionZlibCheck;
use Ecjia\App\Installer\Checkers\PHPOSCheck;
use Ecjia\App\Installer\Checkers\PHPVersionCheck;
use Ecjia\App\Installer\Checkers\SafeModeCheck;
use Ecjia\App\Installer\Checkers\TimezoneCheck;
use Ecjia\App\Installer\Checkers\UploadMaxFilesizeCheck;
use Ecjia\App\Installer\Checkers\WebPathCheck;
use Ecjia\App\Installer\Checkers\WebServerCheck;
use Ecjia\App\Installer\InstallChecker;
use Ecjia\App\Installer\InstallCheckStatus;
use Ecjia\App\Installer\InstallCookie;
use Ecjia\App\Installer\InstallDatabase;
use Ecjia\App\Installer\InstallEnvConfig;
use Ecjia\App\Installer\InstallMigrationFile;
use RC_App;
use RC_Hook;
use RC_Uri;
use RC_Cache;
use Ecjia\App\Installer\Helper;
use ecjia;

class IndexController extends BaseControllerAbstract
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
     * 欢迎页面加载
     */
    public function init()
    {
        $this->check_installed();

        $this->cookie->clear();

//        $install_step = (InstallCheckStatus::make())->addFinishStatus(InstallCheckStatus::STEP1)->getStatus();
//        $this->cookie->setInstallStep($install_step);

        $this->stepInstallStatus(InstallCheckStatus::STEP1);



        $this->assign('ecjia_step', 1);

        $this->assign('product_name', config('site.shop_name'));
        $this->assign('install_license', config('app-installer::daojia_license.license'));

        return $this->displayAppTemplate('installer', 'front/welcome.dwt');
    }

    /*
     * 检查环境页面加载
     */
    public function detect()
    {
        $this->check_installed();
        $this->check_step(2);

//        $install_step = $this->cookie->getInstallStep();
//        $install_step = (InstallCheckStatus::make($install_step))->addFinishStatus(InstallCheckStatus::STEP2)->getStatus();
//        $this->cookie->setInstallStep($install_step);

        $this->stepInstallStatus(InstallCheckStatus::STEP2);

        $checker = new InstallChecker();

        //检查程序所在子目录位置
        $checker->checkItem('web_path', WebPathCheck::class);
        //检查操作系统
        $checker->checkItem('os', PHPOSCheck::class);
        $checker->checkItem('ip', function () {
            return [
                'value' => $_SERVER['SERVER_ADDR'],
                'checked_status' => true
            ];
        });

        $checker->checkItem('web_server', WebServerCheck::class);
        $checker->checkItem('domain', DomainCheck::class);
        $checker->checkItem('php_check', PHPVersionCheck::class);
        $checker->checkItem('php_dns', DNSCheck::class);
        $checker->checkItem('safe_mode', SafeModeCheck::class);
        $checker->checkItem('timezone', TimezoneCheck::class);
        $checker->checkItem('ext_mysqli', ExtensionMysqliCheck::class);
        $checker->checkItem('ext_pdo', ExtensionPdoMysqlCheck::class);
        $checker->checkItem('ext_openssl', ExtensionOpensslCheck::class);
        $checker->checkItem('ext_socket', ExtensionSocketCheck::class);
        $checker->checkItem('ext_gd', ExtensionGDCheck::class);
        $checker->checkItem('ext_curl', ExtensionCurlCheck::class);
        $checker->checkItem('ext_fileinfo', ExtensionFileinfoCheck::class);
        $checker->checkItem('ext_zlib', ExtensionZlibCheck::class);
        $checker->checkItem('upload_filesize', UploadMaxFilesizeCheck::class);
        $checker->checkItem('dir_permission', DirectoryPermissionCheck::class);

        $checker->checking();

        $checked = $checker->getChecked();

        $result = $checker->getCheckResult();

        $install_errors = $checker->getEcjiaError()->get_error_messages();

        $sys_info = $checked;

        unset($sys_info['web_path']);
        unset($sys_info['ip']);
        unset($sys_info['domain']);
        unset($sys_info['dir_permission']);

        $dir_permission = $checked['dir_permission'];

        //检测必须开启项是否开启
        $check_all_right = empty($result) ? true : false;

        $this->assign('ecjia_version', \Ecjia\Component\Framework\Ecjia::VERSION);
        $this->assign('ecjia_release', \Ecjia\Component\Framework\Ecjia::RELEASE);

        $this->assign('install_errors', $install_errors);
        $this->assign('sys_info', $sys_info);
        $this->assign('dir_permission', $dir_permission);
        $this->assign('check_right', $check_all_right);

        $this->assign('ecjia_step', 2);

        return $this->displayAppTemplate('installer', 'front/detect.dwt');
    }

    /**
     * 配置项目包信息页面加载
     */
    public function deploy()
    {
        $this->check_installed();
        $this->check_step(3);
//        setcookie('install_step3', 1);

//        $install_step = $this->cookie->getInstallStep();
//        $install_step = (InstallCheckStatus::make($install_step))->addFinishStatus(InstallCheckStatus::STEP3)->getStatus();
//        $this->cookie->setInstallStep($install_step);

        $this->stepInstallStatus(InstallCheckStatus::STEP3);

        $installer_lang = 'zh_cn';
        $prefix         = 'ecjia_';
        $show_timezone  = 'yes';
        $timezones      = Helper::getTimezones($installer_lang);

        $this->assign('timezones', $timezones);
        $this->assign('show_timezone', $show_timezone);
        $this->assign('local_timezone', Helper::getLocalTimezone());
        $this->assign('correct_img', RC_App::apps_url('statics/front/images/correct.png', $this->__FILE__));
        $this->assign('error_img', RC_App::apps_url('statics/front/images/error.png', $this->__FILE__));

        $this->assign('ecjia_step', 3);

        return $this->displayAppTemplate('installer', 'front/deploy.dwt');
    }

    /**
     * 完成页面
     */
    public function finish()
    {
//        $install_step = $this->cookie->getInstallStep();
//        $install_step = (InstallCheckStatus::make($install_step))->addFinishStatus(InstallCheckStatus::STEP4)->getStatus();
//        $this->cookie->setInstallStep($install_step);

        $this->stepInstallStatus(InstallCheckStatus::STEP4);

        $result = $this->check_step(4);
        if (!$result) {
            $this->check_installed();
//            //安装完成后的一些善后处理
//            Helper::updateInstallDate();
//            Helper::updateEcjiaVersion();
//            Helper::updateHashCode();
//            Helper::updateDemoApiUrl();
//            Helper::createStorageDirectory();
//            Helper::saveInstallLock();

            RC_Hook::do_action('ecjia_installer_finished_after');

            $admin_name     = RC_Cache::app_cache_get('admin_name', 'install');
            $admin_password = RC_Cache::app_cache_get('admin_password', 'install');

            $index_url    = RC_Uri::home_url();
            $h5_url       = RC_Uri::home_url() . '/sites/m/';
            $admin_url    = RC_Uri::home_url() . '/sites/admincp/';
            $merchant_url = RC_Uri::home_url() . '/sites/merchant/';

            $this->assign('index_url', $index_url);
            $this->assign('h5_url', $h5_url);
            $this->assign('admin_url', $admin_url);
            $this->assign('merchant_url', $merchant_url);
            $this->assign('admin_name', $admin_name);
            $this->assign('admin_password', $admin_password);

            $finish_message = __('恭喜您，安装成功!', 'installer');
            $this->assign('finish_message', $finish_message);

            $this->assign('ecjia_step', 5);

            return $this->displayAppTemplate('installer', 'front/finish.dwt');
        } else {
            //@todo else没有判断
        }
    }

    /**
     * 设置安装步骤状态
     * @param $status
     */
    private function stepInstallStatus($status)
    {
        $install_step = $this->cookie->getInstallStep();
        $install_step = (InstallCheckStatus::make($install_step))->addFinishStatus($status)->getStatus();
        $this->cookie->setInstallStep($install_step);
    }

    /**
     * 已经安装过的提示页
     */
    public function installed()
    {
        $this->unset_cookie();

        $finish_message = __('安装程序已经被锁定。', 'installer');
        $locked_message = sprintf(__('如果您确定要重新安装ECJia到家，请删除%s目录下的%s。', 'installer'), 'content/storages/data', 'install.lock');
        $this->assign('finish_message', $finish_message);
        $this->assign('locked_message', $locked_message);

        $index_url    = RC_Uri::home_url();
        $h5_url       = RC_Uri::home_url() . '/sites/m/';
        $admin_url    = RC_Uri::home_url() . '/sites/admincp/';
        $merchant_url = RC_Uri::home_url() . '/sites/merchant/';

        $this->assign('index_url', $index_url);
        $this->assign('h5_url', $h5_url);
        $this->assign('admin_url', $admin_url);
        $this->assign('merchant_url', $merchant_url);

        if (!Helper::checkInstallLock()) {
            return $this->redirect(RC_Uri::url('installer/index/init'));
        }

        $this->assign('ecjia_step', 5);

        return $this->displayAppTemplate('installer', 'front/finish.dwt');
    }

    /**
     * 检查数据库密码是否正确
     * return string
     */
    public function check_db_correct()
    {
        $this->check_installed();

        $db_host = trim($this->request->input('db_host'));
        $db_port = trim($this->request->input('db_port'));
        $db_user = trim($this->request->input('db_user'));
        $db_pass = trim($this->request->input('db_pass'));

//        $databases  = Helper::getDataBases($db_host, $db_port, $db_user, $db_pass);
        $installDatabase = new InstallDatabase($db_host, $db_port, $db_user, $db_pass);
        $databases       = $installDatabase->getDataBases();
        if (is_ecjia_error($databases)) {
            return $this->showmessage(__('连接数据库失败，请检查您输入的数据库帐号是否正确。', 'installer'), ecjia::MSGTYPE_JSON | ecjia::MSGSTAT_ERROR);
        }

        $check_version = $installDatabase->getMysqlVersion();
        if (is_ecjia_error($check_version)) {
            return $this->showmessage(__('连接数据库失败，请检查您输入的数据库帐号是否正确。', 'installer'), ecjia::MSGTYPE_JSON | ecjia::MSGSTAT_ERROR);
        }

        if (version_compare($check_version, '5.5', '<')) {
            return $this->showmessage(__('MySQL数据库版本过低，请使用5.5以上版本。', 'installer'), ecjia::MSGTYPE_JSON | ecjia::MSGSTAT_ERROR);
        }

        $support_collect = $installDatabase->checkMysqlSupport();
        if (is_ecjia_error($support_collect)) {
            return $this->showmessage(__('连接数据库失败，请检查您输入的数据库帐号是否正确。', 'installer'), ecjia::MSGTYPE_JSON | ecjia::MSGSTAT_ERROR);
        }

        $check_result = $support_collect->firstWhere('Variable_name', 'have_innodb');
        if (!empty($check_result) && $check_result['Value'] != 'YES') {
            return $this->showmessage(__('当前MySQL数据库不支持InnoDB引擎，请检查后再进行安装。', 'installer'), ecjia::MSGTYPE_JSON | ecjia::MSGSTAT_ERROR);
        }

        return $this->showmessage('', ecjia::MSGTYPE_JSON | ecjia::MSGSTAT_SUCCESS);
    }

    /**
     * 获取数据库列表
     */
    public function check_db_exists()
    {
        $this->check_installed();

        $db_host     = trim($this->request->input('db_host'));
        $db_port     = trim($this->request->input('db_port'));
        $db_user     = trim($this->request->input('db_user'));
        $db_pass     = trim($this->request->input('db_pass'));
        $db_database = trim($this->request->input('dbdatabase'));

//        $databases  = Helper::getDataBases($db_host, $db_port, $db_user, $db_pass);
        $databases = (new InstallDatabase($db_host, $db_port, $db_user, $db_pass))->getDataBases();

        if (is_ecjia_error($databases)) {
            return $this->showmessage(__('连接数据库失败，请检查您输入的数据库帐号是否正确。', 'installer'), ecjia::MSGTYPE_JSON | ecjia::MSGSTAT_ERROR);
        }

        if ($databases->contains($db_database)) {
            return $this->showmessage('', ecjia::MSGTYPE_JSON | ecjia::MSGSTAT_SUCCESS, array('is_exist' => true));
        } else {
            return $this->showmessage('', ecjia::MSGTYPE_JSON | ecjia::MSGSTAT_SUCCESS, array('is_exist' => false));
        }
    }

    /**
     * 创建配置文件
     */
    public function create_config_file()
    {
        $this->check_installed();

        $db_host  = trim($this->request->input('db_host'));
        $db_port  = trim($this->request->input('db_port'));
        $db_user  = trim($this->request->input('db_user'));
        $db_pass  = trim($this->request->input('db_pass'));
        $db_name  = trim($this->request->input('db_name'));
        $prefix   = trim($this->request->input('db_prefix'));
        $timezone = trim($this->request->input('timezone', 'Asia/Shanghai'));

        $auth_key = Helper::getAuthKey();

        $data = array(
            'DB_ECJIA_HOST'     => $db_host,
            'DB_ECJIA_PORT'     => $db_port,
            'DB_ECJIA_DATABASE' => $db_name,
            'DB_ECJIA_USERNAME' => $db_user,
            'DB_ECJIA_PASSWORD' => $db_pass,
            'DB_ECJIA_PREFIX'   => $prefix,
            'TIMEZONE'    => $timezone,
            'AUTH_KEY'    => $auth_key,
        );

        $installEnv = new InstallEnvConfig();
        $installEnv->createEnv();
        $result = $installEnv->modifyEnv($data);

        if (is_ecjia_error($result)) {
            return $this->showmessage($result->get_error_message(), ecjia::MSGTYPE_JSON | ecjia::MSGSTAT_ERROR);
        } else {
            $percent = $this->get_percent('create_config_file');
            return $this->showmessage('', ecjia::MSGTYPE_JSON | ecjia::MSGSTAT_SUCCESS, array('percent' => $percent));
        }
    }

    /**
     * 创建数据库
     */
    public function create_database()
    {
        $this->check_installed();

        $db_host  = trim($this->request->input('db_host'));
        $db_port  = trim($this->request->input('db_port'));
        $db_user  = trim($this->request->input('db_user'));
        $db_pass  = trim($this->request->input('db_pass'));
        $db_name  = trim($this->request->input('db_name'));

        $result = (new InstallDatabase($db_host, $db_port, $db_user, $db_pass))->createDatabase($db_name);

        if (is_ecjia_error($result)) {
            return $this->showmessage($result->get_error_message(), ecjia::MSGTYPE_JSON | ecjia::MSGSTAT_ERROR);
        } else {
            $percent = $this->get_percent('create_database');
            return $this->showmessage('', ecjia::MSGTYPE_JSON | ecjia::MSGSTAT_SUCCESS, array('percent' => $percent));
        }
    }

    /**
     * 安装数据库结构
     */
    public function install_structure()
    {
        $this->check_installed();

        $limit = 20;

        $migrate = new InstallMigrationFile();

        $result = $migrate->installStructure($limit);

        if (is_ecjia_error($result)) {
            return $this->showmessage($result->get_error_message(), ecjia::MSGTYPE_JSON | ecjia::MSGSTAT_ERROR);
        }

        //还剩余多少个脚本未执行
        $over = $migrate->getWillMigrationFilesCount();

        if (is_ecjia_error($over)) {
            return $this->showmessage($over->get_error_message(), ecjia::MSGTYPE_JSON | ecjia::MSGSTAT_ERROR);
        }
        $more = 0;
        if ($over > 0) {
            $more = $over;
        }

        $percent = $this->get_percent('install_structure');
        return $this->showmessage('', ecjia::MSGTYPE_JSON | ecjia::MSGSTAT_SUCCESS, array('percent' => $percent, 'more' => $more));
    }

    /**
     * 安装基本数据
     */
    public function install_base_data()
    {
        $this->check_installed();

        $result = Helper::installBaseData();

        if (is_ecjia_error($result)) {
            return $this->showmessage($result->get_error_message(), ecjia::MSGTYPE_JSON | ecjia::MSGSTAT_ERROR);
        } else {
            $percent = $this->get_percent('install_base_data');
            return $this->showmessage('', ecjia::MSGTYPE_JSON | ecjia::MSGSTAT_SUCCESS, array('percent' => $percent));
        }
    }

    /**
     * 安装演示数据
     */
    public function install_demo_data()
    {
        $this->check_installed();

        $result = Helper::installDemoData();

        if (is_ecjia_error($result)) {
            return $this->showmessage($result->get_error_message(), ecjia::MSGTYPE_JSON | ecjia::MSGSTAT_ERROR);
        } else {
            $percent = $this->get_percent('install_demo_data');
            return $this->showmessage('', ecjia::MSGTYPE_JSON | ecjia::MSGSTAT_SUCCESS, array('percent' => $percent));
        }
    }

    /**
     * 创建管理员
     */
    public function create_admin_passport()
    {
        $this->check_installed();

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
        } else {
            $percent = $this->get_percent('create_admin_passport');
            return $this->showmessage('', ecjia::MSGTYPE_JSON | ecjia::MSGSTAT_SUCCESS, array('percent' => $percent));
        }
    }

    /**
     * 检查操作步骤
     * @param $step
     * @return bool
     */
    private function check_step($step)
    {
        if (InstallCheckStatus::STEP1 & $step !== InstallCheckStatus::STEP1) {

        }

        if (InstallCheckStatus::STEP2 & $step !== InstallCheckStatus::STEP2) {
            $this->redirectWithExited(RC_Uri::url('installer/index/init'));
        }

        if (InstallCheckStatus::STEP3 & $step !== InstallCheckStatus::STEP3) {
            $this->redirectWithExited(RC_Uri::url('installer/index/detect'));
        }

        if (InstallCheckStatus::STEP4 & $step !== InstallCheckStatus::STEP4) {
            $this->redirectWithExited(RC_Uri::url('installer/index/deploy'));
        }

        if (InstallCheckStatus::STEP5 & $step !== InstallCheckStatus::STEP5) {

        }

        if ($step > 1) {
            if (!isset($_COOKIE['install_step1']) || !isset($_COOKIE['agree'])) {
                $this->redirectWithExited(RC_Uri::url('installer/index/init'));
            }
            if ($step > 2) {
                if (!isset($_COOKIE['install_step2']) || $_COOKIE['install_config'] != 1) {
                    $this->redirectWithExited(RC_Uri::url('installer/index/detect'));
                } else {
                    if ($step > 3) {
                        if (!isset($_COOKIE['install_step3']) || !isset($_COOKIE['install_step4'])) {
                            $this->redirectWithExited(RC_Uri::url('installer/index/deploy'));
                        }
                    }
                }
            }
        }
        return false;
    }

    /**
     * 检测是否已安装程序
     */
    private function check_installed()
    {
        /* 初始化流程控制变量 */
        if (Helper::checkInstallLock()) {
            $this->redirectWithExited(RC_Uri::url('installer/index/installed'));
        }
    }

    /**
     * 清除流程cookie
     */
    private function unset_cookie()
    {
        setcookie('install_step1', '', time() - 3600);
        setcookie('install_step2', '', time() - 3600);
        setcookie('install_step3', '', time() - 3600);
        setcookie('install_step4', '', time() - 3600);
        setcookie('install_config', '', time() - 3600);
        setcookie('agree', '', time() - 3600);
    }

    private function get_percent($step)
    {

        $sqlcount = count(scandir(royalcms('path') . '/content/database/migrations')) - 2;

        if ($step == 'create_config_file') {
            $past = 20;
        } else if ($step == 'create_database') {
            $past = 40;
        } else if ($step == 'install_structure') {
            $over = Helper::getWillMigrationFilesCount();
            if (!is_ecjia_error($over))
                $past = 40 + $sqlcount - $over;
        } else if ($step == 'install_base_data') {
            $past = 40 + $sqlcount + 20;
        } else if ($step == 'install_demo_data') {
            $past = 40 + $sqlcount + 40;
        } else if ($step == 'create_admin_passport') {
            //             $past = 4 +  $_SESSION['temp']['sqlcount'] + 6;
            return 100;
        }
        $total = $sqlcount + 20 + 20 + 20 + 20;


        return $percent = floor($past / $total * 100);

    }
}

//end