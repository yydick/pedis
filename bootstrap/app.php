<?php

declare(strict_types=1);
/**
 * 服务入口
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

use Spool\Pedis\Server;

$app = new Server(dirname(__DIR__));

return $app;
