<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace Spool\Pedis\Data;

use Spool\Pedis\Exceptions\PedisException;
use Spool\Pedis\Constants\ErrorCode;

/**
 * Description of CacheNode
 *
 * @author 陈浩波
 */
abstract class KeyNode
{

    /**
     * 该键实际保存的数据
     * @var void
     */
    protected $data;

    /**
     * 生存时间
     * @var int -2已过期|-1永不过期|n剩余的生存时间
     */
    protected $expire		 = -1;

    /**
     * 设置生存时间的时间
     * @var int 
     */
    protected $expireTime	 = 0;

    /**
     * 键的类型
     * @var string String|List|Set|Zset|Hash
     */
    protected $type		 = '';

    public function __sleep(): array
    {
	return ['type' => $this->type, 'expire' => $this->expire, 'data' => $this->data];
    }

    public function expire(int $seconds): int
    {
	if (-2 === $this->expire) {
	    return 0;
	}
	$time			 = time();
	$this->expireTime	 = $time;
	$this->expire		 = $seconds;
	return 1;
    }

    public function expireAt(int $seconds): int
    {
	if (-2 === $this->expire) {
	    return 0;
	}
	$time = time();
	//如果设置的时间小于当前时间,返回0
	if ($seconds < $time) {
	    return 0;
	}
	$this->expireTime	 = $time;
	$this->expire		 = $seconds - $time;
	return 1;
    }

    /**
     * 移除该健之前设定的生存时间
     * 
     */
    public function persist(): int
    {
	if (-1 === $this->expire || -2 === $this->expire) {
	    return 0;
	}
	$this->expire		 = -1;
	$this->expireTime	 = 0;
	return 1;
    }
    /**
     * 返回当前键的类型
     * @return string
     */
    public function getType(): string
    {
	return $this->type;
    }

}
