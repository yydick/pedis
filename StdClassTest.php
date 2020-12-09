<?php

/**
 * 测试
 * 
 * Class StrClassTest 测试
 * 
 * PHP version 7.2
 * 
 * @category Spool
 * @package  Pedis
 * @author   yydick Chen <yydick@sohu.com>
 * @license  https://spdx.org/licenses/Apache-2.0.html Apache-2.0
 * @link     http://url.com
 * @DateTime 2020-11-03
 */

// namespace Spool\Pedis;

// use stdClass;

/**
 * 测试
 * 
 * Class StrClassTest 测试
 * 
 * PHP version 7.2
 * 
 * @category Spool
 * @package  Pedis
 * @author   yydick Chen <yydick@sohu.com>
 * @license  https://spdx.org/licenses/Apache-2.0.html Apache-2.0
 * @link     http://url.com
 * @DateTime 2020-11-03
 */
class StdClassTest extends stdClass
{
    /**
     * 魔术方法set
     * 
     * @param string $key   key
     * @param mixed  $value value
     * 
     * @return void
     */
    public function __set(string $key, $value): void
    {
        echo "this is __set!\n";
        $this->$key = $value;
    }
    /**
     * 魔术方法get
     * 
     * @param string $key key
     * 
     * @return mixed
     */
    public function __get(string $key)
    {
        return null;
    }
}

$s = new StdClassTest();

$s->s = 1;
echo $s->s, PHP_EOL;
$s->s = 2;
echo $s->s, PHP_EOL;
echo $s->d, PHP_EOL;
$s->f = [];
var_dump(get_object_vars($s));

$arr = [
    'a' => ['ex' => time() + 5, 'value' => 'a', 'key' => 'a'],
    'b' => ['ex' => 0, 'balue' => 'b', 'key' => 'b']
];

function ex($var)
{
    return $var > time();
}
$arrEx = array_column($arr, 'ex', 'key');
var_dump($arrEx);
var_dump(array_filter($arrEx, 'ex'));
exit;
$a = array_fill(1, 1000000, 1);
array_unshift($a, ['str' => 123]);
$start = microtime(true);
$keys = array_keys($a);
$end = microtime(true) - $start;
echo "array_keys: {$end}\n";
$start = microtime(true);
$keys = array_slice($a, 0, 10, true);
$end = microtime(true) - $start;
echo "array_keys: {$end}\n";
var_dump($keys);
