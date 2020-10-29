<?php

/**
 * Pedis守护进程
 * 
 * Class Manager 守护进程
 * 
 * PHP version 7.2
 * 
 * @category Spool
 * @package  Pedis
 * @author   yydick Chen <yydick@sohu.com>
 * @license  https://spdx.org/licenses/Apache-2.0.html Apache-2.0
 * @link     http://url.com
 * @DateTime 2020-10-20
 */

namespace Spool\Pedis;

use Spool\Pedis\Fork;
use Spool\PeasLog\Log;
use Spool\Pedis\Master;
use Spool\Pedis\CheckEvent;

/**
 * Pedis守护进程
 * 
 * Class Manager 守护进程
 * 
 * PHP version 7.2
 * 
 * @category Spool
 * @package  Pedis
 * @author   yydick Chen <yydick@sohu.com>
 * @license  https://spdx.org/licenses/Apache-2.0.html Apache-2.0
 * @link     http://url.com
 * @DateTime 2020-10-20
 */
class Manager
{
    protected $pid = 0;
    protected $masterPid = 0;
    /**
     * 启动守护进程
     * 
     * @return void
     */
    public function run(): void
    {
        $fork = Fork::getInstance();
        $this->pid = $fork->daemonize();
        $pidFile = CheckEvent::$data['pidFile'] ?? '';
        if ($pidFile) {
            \file_put_contents($pidFile, $this->pid);
        }
        Log::debug("当前进程的pid是: {$this->pid}");
        $master = new Master();
        $this->masterPid = $fork->process([$master, 'run']);
        /**
         * 这里需要加master进程终止后，拉起的逻辑和进程终止的逻辑
         */
        // while (true) {
        $pidInfo = pcntl_wait($status, WNOHANG);
        Log::debug("Manager pid: {$this->pid}, isSuccess: {" . pcntl_wifexited($status) . "} pidInfo: {$pidInfo}");
        // }
        if ($pidFile) unlink($pidFile);
    }
}
