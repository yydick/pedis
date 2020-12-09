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
class CmdRequire
{
    /**
     * 客户端名称
     * 
     * @var string
     */
    public $client = '';
    /**
     * 命令
     * 
     * @var string
     */
    public $cmd = '';
    /**
     * 参数
     * 
     * @var array
     */
    public $agvs = [];
    /**
     * 监控的类型,包括:[exists|set]
     * 
     * @var string
     *
     * @var [type]
     */
    public $watchType = '';
    /**
     * 请求的序号
     *
     * @var integer
     */
    public $requireId = 0;
}
