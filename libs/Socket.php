<?php

/**
 * Socket基础抽象类
 * 
 * Class Socket 抽象类
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

/**
 * Socket基础抽象类
 * 
 * Class Socket 抽象类
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
abstract class Socket
{
    /**
     * Socket句柄
     *
     * @var mixed
     */
    protected $socket = null;
    /**
     * 句柄命名
     *
     * @var string
     */
    protected $name = '';
    /**
     * 要绑定的URI资源:域名或IP地址
     *
     * @var string
     */
    protected $host = 'localhost';
    /**
     * 要绑定的端口号
     *
     * @var integer
     */
    protected $port = 9736;
    /**
     * 是否已连接
     *
     * @var bool
     */
    protected $isConnection = false;
    /**
     * 连接超时时间(秒), -1为永不超时
     *
     * @var integer
     */
    public $connectionTimeout = -1;
    /**
     * 连接建立的时间
     *
     * @var integer
     */
    public $connectionTime = 0;
    /**
     * 心跳时长, 0不发送心跳包
     *
     * @var integer
     */
    public $heartbeat = 0;
    /**
     * 上一次发送心跳时间
     *
     * @var integer
     */
    public $lastHeartbeat = 0;
    /**
     * 最后一次发送时间
     *
     * @var integer
     */
    public $lastSend = 0;
    /**
     * 发送失败次数
     *
     * @var integer
     */
    public $sendFailed = 0;
    /**
     * 构建方法
     */
    public function __construct()
    {
        $this->setHost(config('pedis.NETWORK.host', 'localhost'));
        $this->setPort((int) config('pedis.NETWORK.port', 9736));
    }
    /**
     * 获取是否连接状态
     * 
     * @return boolean
     */
    public function getIsConnection(): bool
    {
        return $this->isConnection;
    }
    /**
     * 获取自定义命名的名称
     * 
     * @return string
     */
    public function getName(): string
    {
        return $this->name;
    }
    /**
     * 设置自定义名称,未连接前可定义,连接后只读
     * 
     * @param string $name 要定义的名称
     * 
     * @return boolean
     */
    public function setName(string $name): bool
    {
        return $this->getIsConnection() ? false : $this->name = $name;
    }
    /**
     * 获取要绑定的域名或IP
     * 
     * @return string
     */
    public function getHost(): string
    {
        return $this->host;
    }
    /**
     * 设置要绑定的域名或IP, 只有未连接前才可以设置
     * 
     * @param string $host 要绑定的域名或IP
     * 
     * @return void
     */
    public function setHost(string $host): bool
    {
        return $this->getIsConnection() ? false : $this->host = $host;
    }
    /**
     * 获取要绑定的端口号
     * 
     * @return integer
     */
    public function getPort(): int
    {
        return $this->port;
    }
    /**
     * 设置要绑定的端口号
     * 
     * @param integer $port 端口号
     * 
     * @return boolean
     */
    public function setPort(int $port): bool
    {
        return $this->getIsConnection() ? false : $this->port = $port;
    }
    /**
     * 获取socket资源
     * 
     * @return mixed
     */
    public function getSocket()
    {
        return $this->socket;
    }
    /**
     * 导入资源
     * 
     * @param resource $stream 要导入的资源
     * 
     * @return void
     */
    public function importSocket($stream): void
    {
        $this->socket = socket_import_stream($stream);
    }
    /**
     * 重置连接资源
     * 
     * @return boolean
     */
    public function reset(): bool
    {
        return $this->socket == null ? false : $this->socket = null;
    }
}
