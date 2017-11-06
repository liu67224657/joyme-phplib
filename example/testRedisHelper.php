<?php

/**
 * Description of testRedisWriter
 * 
 * 
 * @author clarkzhao
 * @date 2015-05-07 09:20:50
 * @copyright joyme.com
 */
require_once dirname(__FILE__). DIRECTORY_SEPARATOR.'..' . DIRECTORY_SEPARATOR . 'phplib.php';

use Joyme\net\RedisHelper;

$redisHelper = new RedisHelper('172.16.75.30', 6379);

$key = time();




$ret = $redisHelper->set($key, '$redis '.time());
if($ret==null){
    echo "system error ".$redisHelper->getErrMessage()."\n";
}

$value = $redisHelper->get($key);
var_dump('$value set',$value);

$ret = $redisHelper->del($key.'dddd');

$value = $redisHelper->get($key);
var_dump('$value',$value);
exit;

$table = 'testRedis';
$value = time();
$redisHelper->lpush($table, $value);


$redisHelper->lpush($table, array('t'=>time()));


$redisHelper->lpush($table, "string value ".  time());

$key = 'rediskey';
$ret = $redisHelper->set($key, '$redis '.time());
if($ret==null){
    echo "system error ".$redisHelper->getErrMessage()."\n";
}


$value = $redisHelper->get($key);
var_dump('$value',$value);
$table = "redislist";
$redisHelper->lpush($table,'push:'. time());
$ret = $redisHelper->lPop($table);
var_dump('lPop $ret',$ret);

$redisHelper->lpush($table,array('kk'=>'push:'. time()));
$ret = $redisHelper->lPop($table);
var_dump('lPop $ret',$ret);

$redisHelper->lpush($table,array('kk'=>'push:'. time()));
$ret = $redisHelper->blPop($table);
var_dump('blPop $ret',$ret);
