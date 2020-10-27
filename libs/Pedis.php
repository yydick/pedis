<?php

/**
 * 服务入口类,检测环境,初始化
 * 
 * Class Server 服务入口类
 * 
 * PHP version 7.2
 * 
 * @category Spool
 * @package  Pedis
 * @author   yydick Chen <yydick@sohu.com>
 * @license  https://spdx.org/licenses/Apache-2.0.html Apache-2.0
 * @link     http://url.com
 * @DateTime 2020-10-20
 */

namespace Spool\Pedis;

use Spool\Config\Config;
use Spool\Config\Env;
use Spool\PeasLog\LogConfig;
use Spool\PeasLog\Log;
use Spool\Pedis\ShowLogo;

/**
 * 服务入口类,检测环境,初始化
 * 
 * Class Server 服务入口类
 * 
 * PHP version 7.2
 * 
 * @category Spool
 * @package  Pedis
 * @author   yydick Chen <yydick@sohu.com>
 * @license  https://spdx.org/licenses/Apache-2.0.html Apache-2.0
 * @link     http://url.com
 * @DateTime 2020-10-20
 */
class Pedis
{
    const VERSION = '0.0.1';
    protected $config;
    protected $basePath;
    protected $log;
    protected $manager;
    protected $master;
    /**
     * 构造函数
     * 
     * @param string $basePath 初始化服务
     */
    public function __construct(string $basePath = '')
    {
        if ($basePath) {
            $this->basePath = rtrim($basePath, '\/');
        }
        Env::getInstance()->setRootDir($this->basePath);
        $logConfig = new LogConfig();
        $logConfig->defaultBasepath = env('LOG_FILE', $this->basePath . '/storage/logs');
        Log::setConfig($logConfig);
        $configPath = env('CONFIG_PATH', $this->basePath . '/config');
        $this->config = Config::getInstance();
        $this->config->getConfigFiles($configPath);
    }
    /**
     * 返回版本号
     * 
     * @return string
     */
    public function version(): string
    {
        return static::VERSION;
    }
    /**
     * 服务启动入口
     * 
     * @return void
     */
    public function run()
    {
        $check = $this->checkEvent();
        if (!$check) {
            Log::error("Event check is Error!");
            return;
        }
        Log::info("Check event is right!");
        Log::info(ShowLogo::show());
        if (config('pedis.GENERAL.daemonize', false)) {
            $this->manager = new Manager();
            $this->manager->run();
        } else {
            $this->master = new Master();
            $this->master->run();
        }
    }
    /**
     * 监测运行环境
     * 
     * @return boolean
     */
    public function checkEvent(): bool
    {
        Log::info("Start check event!");
        $checkEvent = new CheckEvent();
        return $checkEvent->check();
    }
}
