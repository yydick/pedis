<?php

/**
 * 命令路由器
 * 
 * Class Route 路由器
 * 
 * PHP version 7.2
 * 
 * @category Spool
 * @package  Pedis
 * @author   yydick Chen <yydick@sohu.com>
 * @license  https://spdx.org/licenses/Apache-2.0.html Apache-2.0
 * @link     http://url.com
 * @DateTime 2020-10-29
 */

namespace Spool\Pedis;

/**
 * 命令路由器
 * 
 * Class Route 路由器
 * 
 * PHP version 7.2
 * 
 * @category Spool
 * @package  Pedis
 * @author   yydick Chen <yydick@sohu.com>
 * @license  https://spdx.org/licenses/Apache-2.0.html Apache-2.0
 * @link     http://url.com
 * @DateTime 2020-10-29
 */
class Route
{
    /**
     * 客戶端映射前置
     *
     * @var array
     */
    protected static $clients = [];
    /**
     * 命令
     *
     * @var array
     */
    protected static $cmds = [];
    /**
     * 单例模式
     *
     * @var Route
     */
    protected static $instance = null;
    /**
     * 私有化构建函数,禁止外部新建类
     */
    private function __construct()
    {
    }
    /**
     * 单例模式生成器
     * 
     * @return Route
     */
    public static function getInstance(): Route
    {
        if (!self::$instance) {
            self::$instance = new Route();
        }
        return self::$instance;
    }
    /**
     * 命令路由
     * 
     * @param string $cmd      命令
     * @param string $callable 回调函数
     * 
     * @return void
     */
    public function cmd(string $cmd, string $callable): void
    {
        $this->cmds[$cmd] = $callable;
    }
}
