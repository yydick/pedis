<?php

/**
 * 主进程
 * 
 * Class Master 主进程
 * 
 * PHP version 7.2
 * 
 * @category Spool
 * @package  Pedis
 * @author   yydick Chen <yydick@sohu.com>
 * @license  https://spdx.org/licenses/Apache-2.0.html Apache-2.0
 * @link     http://url.com
 * @DateTime 2020-10-26
 */

namespace Spool\Pedis;

use Spool\PeasLog\Log;
use Spool\Pedis\SocketServer;

/**
 * 主进程
 * 
 * Class Master 主进程
 * 
 * PHP version 7.2
 * 
 * @category Spool
 * @package  Pedis
 * @author   yydick Chen <yydick@sohu.com>
 * @license  https://spdx.org/licenses/Apache-2.0.html Apache-2.0
 * @link     http://url.com
 * @DateTime 2020-10-26
 */
class Master extends Socket
{
    /**
     * 当前进程的pid
     *
     * @var integer
     */
    protected $pid = 0;
    /**
     * 所有子进程的pid
     *
     * @var array
     */
    protected $pids = [
        'db' => 0,
        'io' => 0,
        'completion' => 0,
    ];
    /**
     * 守护进程的pid
     *
     * @var integer
     */
    protected $ManagerPid = 0;
    /**
     * 工作运行标志,为true时终止循环
     *
     * @var boolean
     */
    protected $stopped = false;
    /**
     * 与子进程通讯的IPC
     *
     * @var array
     */
    protected $fd = [];
    /**
     * 主进程启动入口
     * 
     * @return void
     */
    public function run(): void
    {
        $pid = posix_getpid();
        $this->pid = $pid;
        Log::info("Master is run! pid: {$pid}");
        $this->start();
    }
    /**
     * 服务启动
     * 
     * @return void
     */
    public function start(): void
    {
        $this->createIOServer();
        /**
         * 注册shutdown函数
         */
        register_shutdown_function([$this, 'stop']);
        foreach ($this->signals as $value) {
            pcntl_signal($value, [$this, 'sigHandler']);
        }
        $this->working();
    }
    /**
     * 退出
     * 
     * @return void
     */
    public function stop(): void
    {
        Log::debug("Master stop is calling!");
        if (!$this->stopped) {
            $this->stopped = true;
            foreach ($this->fd as $key => $value) {
                @socket_write($value, "shutdown");
                $status = null;
                pcntl_waitpid($this->pids[$key], $status, WNOHANG);
            }
            return;
        }
    }
    /**
     * 管理进程
     * 
     * @return void
     */
    protected function working(): void
    {
        $heartbeat = explode('.', $this->heartbeat);
        $bufferSize = config('pedis.NETWORK.bufferSize', 1024 * 1024 * 64);
        $tvSec = is_array($heartbeat) ? intval($heartbeat[0]) : intval($heartbeat);
        $tvUsec = is_array($heartbeat) && isset($heartbeat[1]) ? intval($heartbeat[1]) : 0;
        if ($tvSec <= 0) {
            // 超时1秒,避免CPU跑满
            $tvSec = 1;
        }
        if ($tvUsec < 0) {
            $tvUsec = 0;
        }
        while (!$this->stopped) {
            // create a copy, so $fd doesn't get modified by socket_select()
            $read = $this->fd;
            $write = $this->fd;
            // get a list of all the clients that have data to be read from
            // if there are no clients with data, go to next iteration
            if (socket_select($read, $write, $except, $tvSec, $tvUsec) < 1) {
                /**
                 * 发送心跳
                 */
                // if ($this->fd) socket_write($this->fd, chr(7));
                continue;
            }
            /**
             * 有连接时记录开始时间, 避免循环过快导致CPU跑满了
             */
            $workStartTimer = microtime(true);
            // loop through all the clients that have data to read from
            foreach ($read as $key => $readSock) {
                // read until newline or bufferSize bytes
                // socket_read while show errors when the client is disconnected, so silence the error messages
                $data = socket_read($readSock, $bufferSize, PHP_BINARY_READ);
                Log::debug("Get data from {$key}: {$data}");
                // check if the client is disconnected
                if ($data === "shutdown" || !trim($data)) {
                    // remove client for $clients array
                    // $key = array_search($readSock, $this->clients);
                    $status = null;
                    /**
                     * 如果保存了这个子进程的pid, 则回收该进程
                     */
                    if (isset($this->pids[$key])) {
                        $subPid = $this->pids[$key];
                        pcntl_waitpid($subPid, $status, WNOHANG);
                    }
                    unset($this->clients[$key]);
                    Log::debug("{$key} client disconnected. need restart it!\n");
                    /**
                     * 重新创建IO服务
                     */
                    $this->createIOServer();
                    // exit;
                    // continue to the next client to read from, if any
                    continue;
                }

                // trim off the trailing/beginning white spaces
                $data = trim($data);
                if (!$data) {
                    continue;
                }
                switch ($key) {
                        // case 'io':
                        //     Log::debug("Get data from io: {$data}");
                        //     socket_write($readSock, $data);
                        //     break;

                    default:
                        // Log::debug("Get data from {$key}: {$data}");
                        break;
                }
            } // end of reading foreach
            //判断是否需要usleep
            $this->workUsleep($workStartTimer);
            $workEndTimer = microtime(true) * 10000;
            // Log::debug("Work time use:{$workEndTimer} " . ($workStartTimer * 10000));
        }
    }
    /**
     * 开始绑定socket
     * 
     * @return void
     */
    protected function bindSocket(): void
    {
        $host = config('pedis.NETWORK.host', 'localhost');
        $port = config('pedis.NETWORK.port', 9736);
        $socket = socket_create(AF_INET, SOCK_STREAM, SOL_TCP);
        socket_bind($socket, $host, $port);
        socket_listen($socket);
        socket_set_nonblock($socket);
    }
    /**
     * 信号处理
     * 
     * @param integer $signo 要处理的信号
     * 
     * @return void
     */
    protected function sigHandler(int $signo): void
    {
        switch ($signo) {
            case SIGTERM:
            case SIGTRAP:
            case SIGKILL:
                $this->stop();
                exit;
                break;
            case SIGUSR1:
                Log::info("Get SIGUSR1!");
                break;
            case SIGALRM:
                Log::info("Get SIGALRM!");
                break;
            default:
                break;
        }
    }
    /**
     * 创建IO进程
     * 
     * @return void
     */
    protected function createIOServer(): void
    {
        $heartbeat = explode('.', $this->heartbeat);
        $sockets = array();
        /* On Windows we need to use AF_INET */
        $domain = (strtoupper(substr(PHP_OS, 0, 3)) == 'WIN' ? AF_INET : AF_UNIX);
        /* Setup socket pair */
        if (socket_create_pair($domain, SOCK_STREAM, 0, $sockets) === false) {
            echo "socket_create_pair failed. Reason: " . socket_strerror(socket_last_error());
            $this->stop();
        }
        socket_set_option($sockets[0], SOL_SOCKET, SO_REUSEADDR, 1); //重用端口
        socket_set_option($sockets[1], SOL_SOCKET, SO_REUSEADDR, 1); //重用端口
        socket_set_nonblock($sockets[0]); //非阻塞
        socket_set_nonblock($sockets[1]); //非阻塞
        $ioServer = new SocketServer();
        $ioServer->setHeartbeat(implode('.', $heartbeat));
        $ioServer->setName('io');
        $ioServer->fd = $sockets[0];
        $fork = Fork::getInstance();
        $this->pids['io'] = $fork->process([$ioServer, 'start']);
        socket_close($sockets[0]);
        $this->fd['io'] = $sockets[1];
    }
}
