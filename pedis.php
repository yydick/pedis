#!/usr/bin/php
<?php

declare(strict_types=1);
/**
 * 服务启动文件
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

define('PEDIS_START', microtime(true));
require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$app->run();
