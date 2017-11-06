<?php
/**
 * Description: 测试curl类
 * Author: gradydong
 * Date: 2016/4/18
 * Time: 15:59
 * Copyright: Joyme.com
 */
require_once dirname(__FILE__). DIRECTORY_SEPARATOR.'..' . DIRECTORY_SEPARATOR . 'phplib.php';

use Joyme\net\curl;

$curl = new Curl();

//$url = 'http://www.baidu.com';
//$url = 'http://www.joyme.com';
/*$url = 'http://localhost/index.php';
$param = array(
    'name' => 'hello',
    'sex' => '1',
);*/

$url = 'http://webcache.joyme.alpha/api/baidu/push.do?srcurl=http://article.joyme.com/article/news/official/201604/25133456.html';

$content = $curl->Get($url);
//$content = $curl->Get($url,$param);
print_r($content);
//var_dump($content);
