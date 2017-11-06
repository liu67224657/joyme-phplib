<?php

/**
 * Description of testHttpclient
 * 
 * 
 * @author clarkzhao
 * @date 2015-04-29 11:23:26
 * @copyright joyme.com
 */
require_once dirname(__FILE__). DIRECTORY_SEPARATOR.'..' . DIRECTORY_SEPARATOR . 'phplib.php';

use Joyme\net\HttpClient;

$http = new HttpClient('www.joyme.com');
$path = '/news/official/201504/2878217.html';
$http->setDebug(true);
$ret =$http->get($path);
if($ret){
    $content =$http->getContent($path);
    var_dump($content);
}