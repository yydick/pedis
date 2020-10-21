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
class Server
{
    const VERSION = '0.0.1';
    protected $config;
    protected $basePath;
    /**
     * 构造函数
     * 
     * 
     * @param string $basePth 初始化服务
     */
    public function __construct(string $basePath = '')
    {
        if ($basePath) {
            $this->basePath = rtrim($basePath, '\/');
        }
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
     * 监测运行环境
     * 
     * @return boolean
     */
    public function checkEvent(): bool
    {
        return false;
    }
}
