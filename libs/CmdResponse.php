<?php

/**
 * 接收到的请求
 * 
 * Class CmdRequire 请求
 * 
 * PHP version 7.2
 * 
 * @category Spool
 * @package  Pedis
 * @author   yydick Chen <yydick@sohu.com>
 * @license  https://spdx.org/licenses/Apache-2.0.html Apache-2.0
 * @link     http://url.com
 * @DateTime 2020-11-04
 */

namespace Spool\Pedis;

/**
 * 接收到的请求
 * 
 * Class CmdRequire 请求
 * 
 * PHP version 7.2
 * 
 * @category Spool
 * @package  Pedis
 * @author   yydick Chen <yydick@sohu.com>
 * @license  https://spdx.org/licenses/Apache-2.0.html Apache-2.0
 * @link     http://url.com
 * @DateTime 2020-11-04
 */
class CmdResponse
{
    /**
     * 客户端名称
     * 
     * @var string
     */
    public $client;
    /**
     * 操作的键
     *
     * @var string
     */
    public $key;
    /**
     * 操作的子键
     *
     * @var string
     */
    public $subKey;
    /**
     * 返回结果
     * 
     * @var string
     */
    public $response;
    /**
     * 监控的客户端列表
     * 
     * @var array
     */
    public $clients;
    /**
     * 监控的类型,包括:[exists|set]
     * 
     * @var string
     *
     * @var [type]
     */
    public $watchType;
}
