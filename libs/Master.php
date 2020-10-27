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
class Master
{
    protected $pid = 0;
    protected $db;
    protected $io;
    protected $completion;
    protected $ioFd = [];
    protected $completionFd = [];
    /**
     * 主进程启动入口
     * 
     * @param bool $daemonize 是否有守护进程存在
     * 
     * @return void
     */
    public function run(bool $daemonize = false): void
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
        $sockets = array();
        /* On Windows we need to use AF_INET */
        $domain = (strtoupper(substr(PHP_OS, 0, 3)) == 'WIN' ? AF_INET : AF_UNIX);

        /* Setup socket pair */
        if (socket_create_pair($domain, SOCK_STREAM, 0, $sockets) === false) {
            echo "socket_create_pair failed. Reason: " . socket_strerror(socket_last_error());
        }
        $this->ioFd['read'] = $sockets[0];
        $this->ioFd['write'] = $sockets[1];
        $this->io = new SocketServer();
        $this->io->start();
    }
    /**
     * @return void
     */
    public function stop(): void
    {
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
}
