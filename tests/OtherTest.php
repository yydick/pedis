<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace Spool\Pedis\Tests;

use PHPUnit\Framework\TestCase;
use Spool\Pedis\Lib\Log;
use Spool\Config\Env;
use stdClass;

/**
 * Description of OtherTest
 *
 * @author 陈浩波
 */
class OtherTest extends TestCase
{
    public function testFD()
    {
        echo "fd: ", (int)STDOUT, "\n";
        $this->assertTrue(true);
    }
    public function testBaseFile()
    {
        echo "rootPath: ", PEDIS_ROOT, "\n";
        $this->assertTrue(true);
    }

    public function testLogInfo()
    {
        Log::test();
        $this->assertTrue(true);
    }
    public function testMd5()
    {
        $key = [];
        for ($i = 1; $i < 65537; $i++) {
            $key[md5($i)] = 1;
        }
        var_dump($i, count($key));
    }
    public function testEnv()
    {
        $env = Env::getInstance();
        $env->setRootDir(dirname(__DIR__));
        var_dump('env: ', $env->getRootDir(), $env('APP_ENV'), $env('MAX'));
    }
    public function testStdClass()
    {
        $std = new \Spool\Config\Data();
        $std->a = '1';
        var_dump($std);
        unset($std->a);
        var_dump($std);
        $ss = json_decode('{"a":1}');
        var_dump($ss, json_encode($ss));
        $server = new \Spool\Pedis\Server();
    }
}
