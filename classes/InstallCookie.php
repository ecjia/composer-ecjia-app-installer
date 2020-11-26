<?php


namespace Ecjia\App\Installer;


class InstallCookie
{

    protected $cookies = [
        'install_agree',
        'install_step',
        'install_config',
    ];


    public function __construct()
    {

    }

    public function setInstallStep($value)
    {
        setcookie('install_step', $value);
    }

    public function getInstallStep()
    {
        return cookie('install_step');
    }

    /**
     * 清空安装用到的cookie
     */
    public function clear()
    {
        foreach ($this->cookies as $cookie) {
            setcookie($cookie, null, SYS_TIME - 3600);
        }
    }




}