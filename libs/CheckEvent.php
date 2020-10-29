<?php

/**
 * 检测运行环境
 * 
 * Class CheckEvent 监测运行环境
 * 
 * PHP version 7.2
 * 
 * @category Spool
 * @package  Pedis
 * @author   yydick Chen <yydick@sohu.com>
 * @license  https://spdx.org/licenses/Apache-2.0.html Apache-2.0
 * @link     http://url.com
 * @DateTime 2020-10-21
 */

namespace Spool\Pedis;

use Spool\PeasLog\Log;
use Spool\Exception\SpoolException;
use Spool\Pedis\App\Constants\ErrorCode;

/**
 * 检测运行环境
 * 
 * Class CheckEvent 监测运行环境
 * 
 * PHP version 7.2
 * 
 * @category Spool
 * @package  Pedis
 * @author   yydick Chen <yydick@sohu.com>
 * @license  https://spdx.org/licenses/Apache-2.0.html Apache-2.0
 * @link     http://url.com
 * @DateTime 2020-10-21
 */
class CheckEvent
{
    public static $data = [];
    /**
     * 开始检测
     * 
     * @return boolean
     */
    public function check(): bool
    {
        $check = true;
        $check = $this->pidFileCheck();
        return $check;
    }
    /**
     * 检测pid文件是否存在, 或是否能够写入
     * 
     * @return boolean
     */
    public function pidFileCheck(): bool
    {
        $path = config('pedis.GENERAL.pidfile');
        //如果没有定义pid文件,则无需管理pid文件
        if (!$path) {
            return true;
        }
        $fileInfo = pathinfo($path);
        if ($fileInfo['dirname'][0] != '/') {
            $fileInfo['dirname'] = APP_ROOT . DS . $fileInfo['dirname'];
        }
        $tmpFile = @tempnam($fileInfo['dirname'], 'tmp');
        if (!$tmpFile) {
            Log::error("pid path cannot write!");
            return false;
        }
        unlink($tmpFile);
        if (!$fileInfo['basename']) {
            $fileInfo['basename'] = 'pedis.pid';
        }
        $pidFile = $fileInfo['dirname'] . DIRECTORY_SEPARATOR . $fileInfo['basename'];
        static::$data['pidFile'] = $pidFile;
        if (file_exists($pidFile)) {
            Log::error("pid file {$pidFile} is exists!");
            return false;
        }
        return true;
    }
}
