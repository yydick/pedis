<?php

/**
 * 生成子进程
 * 
 * Class Fork 生成子进程类
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

/**
 * 生成子进程
 * 
 * Class Fork 生成子进程类
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
class Fork
{
    protected static $instance;
    /**
     * 私有化构建函数,禁止外部新建类
     */
    private function __construct()
    {
    }
    /**
     * 单例模式生成器
     * 
     * @return Fork
     */
    public static function getInstance(): Fork
    {
        if (!static::$instance) {
            static::$instance = new Fork();
        }
        //将pcntl信号处理改为异步,避免性能损耗
        if (!pcntl_async_signals()) {
            pcntl_async_signals(true);
        }
        return static::$instance;
    }
    /**
     * 生成守护进程
     * 
     * @return integer
     */
    public function daemonize(): int
    {
        $pid = pcntl_fork();
        if ($pid == -1) {
            exit('daemonize is failed!');
        } elseif ($pid) {
            exit(1);
        } else {
            $sid = posix_setsid();
            if ($sid < 0) {
                exit('daemonize is failed!');
            }
            while (posix_getppid() != 1) {
                usleep(10);
            }
        }
        Log::info('pid: ' . posix_getpid() . ' ppid: ' . posix_getppid() . ' sid: ' . $sid);
        return posix_getpid();
    }
    /**
     * 创建一个子进程
     * 
     * @param callable $callback 子进程要运行的入口
     * 
     * @return void
     */
    public function process(callable $callback): int
    {
        $pid = pcntl_fork();
        if ($pid == -1) {
            exit('process is failed!');
        } elseif ($pid) {
            return $pid;
        } else {
            call_user_func($callback);
            //子进程完成任务后应退出, 而不是继续执行父进程之后的代码
            exit(1);
        }
        return $pid;
    }
}
