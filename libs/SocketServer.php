<?php

/**
 * 网络服务
 * 
 * Class Socket 网络服务
 * 
 * PHP version 7.2
 * 
 * @category Spool
 * @package  Pedis
 * @author   yydick Chen <yydick@sohu.com>
 * @license  https://spdx.org/licenses/Apache-2.0.html Apache-2.0
 * @link     http://url.com
 * @DateTime 2020-10-27
 */

namespace Spool\Pedis;

use Spool\Pedis\Socket;
use Spool\PeasLog\Log;
use Spool\Pedis\Lib\bak\Log as BakLog;

/**
 * 网络服务
 * 
 * Class Socket 网络服务
 * 
 * PHP version 7.2
 * 
 * @category Spool
 * @package  Pedis
 * @author   yydick Chen <yydick@sohu.com>
 * @license  https://spdx.org/licenses/Apache-2.0.html Apache-2.0
 * @link     http://url.com
 * @DateTime 2020-10-27
 */
class SocketServer extends Socket
{
    const WORK_TYPE = 'server';
    const CONN_TYPE = 'tcp';
    /**
     * 客户端队列
     *
     * @var array
     */
    protected $clients = [];
    /**
     * 工作运行标志,为true时终止循环
     *
     * @var boolean
     */
    protected $stopped = false;
    /**
     * 要发送的队列
     *
     * @var array
     */
    public $sendArray = [];
    /**
     * 接收到的队列
     *
     * @var array
     */
    public $recvArray = [];
    /**
     * 客户端状态队列
     *
     * @var array
     */
    public $statusList = [];
    /**
     * 与主进程通讯用的IPC通道
     *
     * @var mixed
     */
    public $fd = null;
    /**
     * 启动服务
     * 
     * @param string  $host 要监听的域名或IP
     * @param integer $port 要监听的端口号
     * 
     * @return void
     */
    public function start(string $host = 'localhost', int $port = 9736): void
    {
        $this->listen($host, $port);
        Log::debug("Socket server is listening {$host}:{$port}!");
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
        Log::warning("Socket server is shutdown!");
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
    /**
     * 获取Master进程返回的数据
     * 
     * @return void
     */
    public function getRecv(): void
    {
    }
    /**
     * 开始监听
     * 
     * @param string  $host 要监听的域名或IP
     * @param integer $port 要监听的端口号
     * 
     * @return void
     */
    protected function listen(string $host = 'localhost', int $port = 9736)
    {
        $this->host = $host ?: $this->host;
        $this->port = $port ?: $this->port;
        $this->socket = socket_create(AF_INET, SOCK_STREAM, SOL_TCP);
        $bind = socket_bind($this->socket, $host, $port);
        /**
         * Socket 系列函数不抛异常,没法捕获,只能if判断
         */
        if (!$bind) {
            $errorNo = socket_last_error($this->socket);
            $errorMsg = socket_strerror($errorNo);
            Log::error("Socket bind is failed for {$host}:{$port}");
            throw new \Error($errorMsg, $errorNo);
        }
        $listen = socket_listen($this->socket);
        /**
         * Socket 系列函数不抛异常,没法捕获,只能if判断
         */
        if (!$listen) {
            $errorNo = socket_last_error($this->socket);
            $errorMsg = socket_strerror($errorNo);
            Log::error("Socket listen is failed for {$host}:{$port}");
            throw new \Error($errorMsg, $errorNo);
        }
        socket_set_option($this->socket, SOL_SOCKET, SO_REUSEADDR, 1); //重用端口
        socket_set_nonblock($this->socket); //非阻塞
        $this->clients['root'] = $this->socket;
        $this->clients['master'] = $this->fd;
    }
    /**
     * 开始工作
     * 
     * @return void
     */
    protected function beginning()
    {
        $read = $write = $except = null;
        $bufferSize = config('pedis.NETWORK.bufferSize', 1024 * 1024 * 64);
        Log::info("Socket read buffer size: {$bufferSize}!");
        $heartbeat = explode('.', $this->heartbeat);
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
            // create a copy, so $clients doesn't get modified by socket_select()
            $read = $this->clients;
            $write = $this->clients;
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

            // check if there is a client trying to connect
            if (in_array($this->socket, $read)) {
                // accept the client, and add him to the $clients array
                $newsock = socket_accept($this->socket);
                socket_getpeername($newsock, $ip, $port);
                $clientName = $ip . ':' . $port;
                $this->clients[$clientName] = $newsock;
                $msg = "New client connected: {$ip}:{$port}\n";
                socket_write($newsock, $msg);
                Log::debug($msg);
                // remove the listening socket from the clients-with-data array
                $key = array_search($this->socket, $read);
                unset($read[$key]);
            }

            // loop through all the clients that have data to read from
            foreach ($read as $key => $readSock) {
                // read until newline or bufferSize bytes
                // socket_read while show errors when the client is disconnected, so silence the error messages
                $data = socket_read($readSock, $bufferSize, PHP_BINARY_READ);
                // check if the client is disconnected
                if ($data === false) {
                    // remove client for $clients array
                    // $key = array_search($readSock, $this->clients);
                    unset($this->clients[$key]);
                    Log::debug("client disconnected {$key}.\n");
                    // continue to the next client to read from, if any
                    continue;
                }

                // trim off the trailing/beginning white spaces
                $data = trim($data);
                if ($data == 'shutdown' && $key == 'master') {
                    Log::alert("Master is down! Io process be about to stop!");
                    $this->stop();
                    break 2;
                }

                // check if there is any data after trimming off the spaces
                if (!empty($data)) {
                    if ($key == 'master') {
                        Log::debug("Get data from master: {$data}");
                        continue;
                    }
                    if ($data == 'quit') {
                        socket_close($readSock);
                        // $key = array_search($readSock, $this->clients);
                        unset($this->clients[$key]);
                        Log::debug("client disconnected key: {$key}.\n");
                        continue;
                    }
                    if ($data == 'shutdown') {
                        $this->stop();
                        break;
                    }
                    // send this to all the clients in the $clients array (except the first one, which is a listening socket)
                    foreach ($this->clients as $sendSock) {

                        // if its the listening sock or the client that we got the message from, go to the next one in the list
                        if ($sendSock == $this->socket || $sendSock == $readSock)
                            continue;

                        // write the message to the client -- add a newline character to the end of the message
                        socket_write($sendSock, "{$key} send: {$data}" . "\n");
                    } // end of broadcast foreach
                    socket_write($this->fd, $data);
                }
            } // end of reading foreach
            //判断是否需要usleep
            $this->workUsleep($workStartTimer);
        }
    }
}
