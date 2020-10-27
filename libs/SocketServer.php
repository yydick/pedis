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
     * @var array
     */
    public $fd = [];
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
        $this->beginning();
        Log::warning("Socket server is shutdown!");
        /**
         * 工作结束需要用exit结束当前进程,避免继续执行后面父进程的代码
         */
        exit(0);
    }
    /**
     * 停止IO循环
     * 
     * @return void
     */
    public function stop(): void
    {
        $this->stopped = true;
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
        $this->clients['root'] = $this->socket;
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
        while (!$this->stopped) {
            // create a copy, so $clients doesn't get modified by socket_select()
            $read = $this->clients;
            $write = $this->clients;
            // get a list of all the clients that have data to be read from
            // if there are no clients with data, go to next iteration
            if (socket_select($read, $write, $except, 0) < 1) {
                continue;
            }

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

            // var_dump('read: ', $read, 'write: ', $write);
            // loop through all the clients that have data to read from
            foreach ($read as $key => $readSock) {
                // read until newline or bufferSize bytes
                // socket_read while show errors when the client is disconnected, so silence the error messages
                $data = socket_read($readSock, $bufferSize, PHP_NORMAL_READ);

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

                // check if there is any data after trimming off the spaces
                if (!empty($data)) {
                    if ($data == 'quit') {
                        socket_close($readSock);
                        // $key = array_search($readSock, $this->clients);
                        unset($this->clients[$key]);
                        Log::debug("client disconnected key: {$key}.\n");
                        continue;
                    }
                    if ($data == 'shutdown') {
                        break 2;
                    }
                    // send this to all the clients in the $clients array (except the first one, which is a listening socket)
                    foreach ($this->clients as $sendSock) {

                        // if its the listening sock or the client that we got the message from, go to the next one in the list
                        if ($sendSock == $this->socket || $sendSock == $readSock)
                            continue;

                        // write the message to the client -- add a newline character to the end of the message
                        socket_write($sendSock, "{$key} send: {$data}" . "\n");
                    } // end of broadcast foreach

                }
            } // end of reading foreach
        }
    }
}
