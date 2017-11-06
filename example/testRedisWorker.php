<?php

/**
 * Description of testRedis
 * 
 * 
 * @author clarkzhao
 * @date 2015-05-06 06:24:43
 * @copyright joyme.com
 */
require_once dirname(__FILE__). DIRECTORY_SEPARATOR.'..' . DIRECTORY_SEPARATOR . 'phplib.php';

use Joyme\net\RedisHelper;


$redisHelper = new RedisHelper('172.16.75.30',6379);

$redisKey = 'testRedis';
while(true){
        $value =    $redisHelper->blPop($redisKey);
        echo "memory_get_usage ".memory_get_usage()."\n";
        if(empty($value)){
            if($value===null){
                echo "system error ".$redisHelper->getErrMessage()."\n";
                echo "begin sleep 10 seconds\n";
                sleep(10);
            }else{
                echo "nth todo continue\n";
            }
            continue;
        }else{
            var_dump('$value',$value);
            //todo sth
        }        

}