<?php

/**
 * 客戶端信息
 * 
 * Class ClientInfo 客戶端信息
 * 
 * PHP version 7.2
 * 
 * @category Spool
 * @package  Pedis
 * @author   yydick Chen <yydick@sohu.com>
 * @license  https://spdx.org/licenses/Apache-2.0.html Apache-2.0
 * @link     http://url.com
 * @DateTime 2020-10-30
 */

namespace Spool\Pedis;

use ArrayAccess;
use stdClass;

/**
 * 客戶端信息
 * 
 * Class ClientInfo 客戶端信息
 * 
 * PHP version 7.2
 * 
 * @category Spool
 * @package  Pedis
 * @author   yydick Chen <yydick@sohu.com>
 * @license  https://spdx.org/licenses/Apache-2.0.html Apache-2.0
 * @link     http://url.com
 * @DateTime 2020-10-30
 */
class ClientInfo implements ArrayAccess
{
    /**
     * 自定义名称
     *
     * @var string
     */
    protected $name = '';
    /**
     * 选择的数据库
     *
     * @var string
     */
    protected $db = "0";
    /**
     * 最后一次发送信息的时间
     *
     * @var float
     */
    protected $lastSend = 0.0;
    /**
     * 最后一次接收信息的时间 
     *
     * @var float
     */
    protected $lastReceive = 0.0;
    /**
     * 最后一次发送心跳时间
     *
     * @var float
     */
    protected $lastSendHeartbeat = 0.0;
    /**
     * 最后一次接收心跳时间
     *
     * @var float
     */
    protected $lastReceiveHeartbeat = 0.0;
    /**
     * 最后一次发送的数据
     *
     * @var string
     */
    protected $lastSendData = '';
    /**
     * 最后一次接收的数据
     *
     * @var string
     */
    protected $lastReceiveData = '';

    /**
     * 绑定数组内容
     * 
     * @param array $items 要绑定的数组
     * 
     * @return array
     */
    public function bindArray(array $items = []): array
    {
        $backed = $this->toArray();
        $this->data = json_decode(json_encode($items));
        return $backed;
    }
    /**
     * 返回数组
     * 
     * @return array
     */
    public function toArray(): array
    {
        return json_decode(json_encode($this->data), true);
    }
    /**
     * 实现接口
     * 
     * @param [string] $key 要确认的键名
     * 
     * @return boolean
     */
    public function offsetExists($key): bool
    {
        return isset($this->data->$key);
    }
    /**
     * 实现接口
     * 
     * @param [string] $key 要获取的键名
     * 
     * @return void
     */
    public function offsetGet($key)
    {
        return $this->data->$key;
    }
    /**
     * 实现接口
     * 
     * @param [string] $key   要设置的键名
     * @param [type]   $value 要设置的值
     * 
     * @return void
     */
    public function offsetSet($key, $value): void
    {
        $this->data->key = $value;
    }
    /**
     * 实现接口
     * 
     * @param [string] $key 要删除的键名
     * 
     * @return void
     */
    public function offsetUnset($key): void
    {
        unset($this->data->$key);
    }
}
