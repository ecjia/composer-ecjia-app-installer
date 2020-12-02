<?php


namespace Ecjia\App\Installer;


class InstallPercent
{
    const CREATE_CONFIG_FILE_PART    = 'create_config_file';
    const CREATE_DATABASE_PART       = 'create_database';
    const INSTALL_STRUCTURE_PART     = 'install_structure';
    const INSTALL_BASE_DATA_PART     = 'install_base_data';
    const INSTALL_DEMO_DATA_PART     = 'install_demo_data';
    const CREATE_ADMIN_PASSPORT_PART = 'create_admin_passport';

    private $parts = [
        self::CREATE_CONFIG_FILE_PART    => 20,
        self::CREATE_DATABASE_PART       => 20,
        self::INSTALL_STRUCTURE_PART     => 20,
        self::INSTALL_BASE_DATA_PART     => 20,
        self::INSTALL_DEMO_DATA_PART     => 20,
        self::CREATE_ADMIN_PASSPORT_PART => 20,
    ];

    private $offset = 0;

    /**
     * @var InstallCookie
     */
    private $cookie;

    public function __construct(InstallCookie $cookie)
    {
        $count = (new InstallMigrationFile())->getMigrationFilesCount();
        if (!is_ecjia_error($count)) {
            $this->parts['install_structure'] = $count;
        }

        $this->cookie = $cookie;

        $this->offset = $cookie->getInstallOffset();
    }

    /**
     * 重置安装进度
     */
    public function reset()
    {
        $this->offset = 0;
        $this->cookie->setInstallOffset($this->offset);
        return $this;
    }

    /**
     * 设置进度值
     * @param $value
     */
    public function setValue($value)
    {
        $this->offset += $value;
        $this->cookie->setInstallOffset($this->offset);
        return $this;
    }

    /**
     * 设置步骤值
     * @param $part
     * @param int $step
     * @return InstallPercent
     */
    public function setStepValue($part, $step = 20)
    {
        if (isset($this->parts[$part])) {
            if ($part == self::INSTALL_STRUCTURE_PART) {
                $over = (new InstallMigrationFile())->getWillMigrationFilesCount();
                if (!is_ecjia_error($over)) {
                    if ($over < $step) {
                        $step = $over;
                    }
                    $this->setValue($step);
                }
            } else {
                $this->setValue($this->parts[$part]);
            }
        }

        return $this;
    }

    /**
     * 获取百分比进度
     * @return false|float
     */
    public function getPercent()
    {
        $total = array_sum($this->parts);
        return $percent = floor($this->offset / $total * 100);
    }


}