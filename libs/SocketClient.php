<?php

/**
 * Socket客户端
 * 
 * Class SocketClient 客户端类
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
 * Socket客户端
 * 
 * Class SocketClient 客户端类
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
class SocketClient extends Socket
{
    const WORK_TYPE = 'client';
    const CONN_TYPE = 'tcp';
    /**
     * 要发送的字符串
     *
     * @var string
     */
    public $sendStr = '';
    /**
     * 接收到的字符串
     *
     * @var string
     */
    public $recvStr = '';
}
