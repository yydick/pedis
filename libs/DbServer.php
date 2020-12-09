<?php

/**
 * Db Server Database
 * 
 * Class DbServer Database
 * 
 * PHP version 7.2
 * 
 * @category Spool
 * @package  Pedis
 * @author   yydick Chen <yydick@sohu.com>
 * @license  https://spdx.org/licenses/Apache-2.0.html Apache-2.0
 * @link     http://url.com
 * @DateTime 2020-11-03
 */

namespace Spool\Pedis;

use StdClass;

/**
 * Db Server Database
 * 
 * Class DbServer Database
 * 
 * PHP version 7.2
 * 
 * @category Spool
 * @package  Pedis
 * @author   yydick Chen <yydick@sohu.com>
 * @license  https://spdx.org/licenses/Apache-2.0.html Apache-2.0
 * @link     http://url.com
 * @DateTime 2020-11-03
 */
class DbServer extends Socket
{
    const WORK_TYPE = 'server';
    const CONN_TYPE = 'tcp';
    /**
     * IPC for master
     */
    protected $fd;
    /**
     * Databases
     *
     * @var array
     */
    protected $db = [];
    /**
     * Clients select database
     *
     * @var StdClass
     */
    protected $clients = null;
    /**
     * 启动服务
     * 
     * @param string $dbFile 本地数据库
     * 
     * @return void
     */
    public function start(string $dbFile = 'prdb'): void
    {
        $this->name = 'db';
        Log::info("Database server is started!");
        /**
         * 注册shutdown函数
         */
        register_shutdown_function([$this, 'stop']);
        /**
         * 注册信号处理器
         */
        // foreach ($this->signals as $value) {
        //     pcntl_signal($value, [$this, 'sigHandler']);
        // }
        $this->beginning();
        Log::info("Database server is shutdown!");
    }
    /**
     * 停止IO循环
     * 
     * @return void
     */
    public function stop(): void
    {
        Log::debug("stop is calling!");
        if (!$this->stopped) {
            /**
             * 停止while循环
             */
            $this->stopped = true;
            @socket_write($this->fd, "shutdown");
            // socket_close($this->fd);
            /**
             * 关闭所有socket
             */
            foreach ($this->clients as $value) {
                socket_close($value);
            }
        }
    }
}
