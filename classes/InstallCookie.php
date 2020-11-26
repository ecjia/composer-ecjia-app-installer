<?php


namespace Ecjia\App\Installer;


use RC_Cookie;

class InstallCookie
{

    protected $cookies = [
        'install_agree',
        'install_step',
        'install_config',
    ];

    public function setInstallStep($value)
    {
        RC_Cookie::queue(RC_Cookie::make('install_step', $value, 30));
    }

    public function getInstallStep()
    {
        return RC_Cookie::get('install_step');
    }

    /**
     * 清空安装用到的cookie
     */
    public function clear()
    {
        foreach ($this->cookies as $cookie) {
            RC_Cookie::queue(RC_Cookie::forget($cookie));
        }
    }

}