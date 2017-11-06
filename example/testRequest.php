<?php

/**
 * Description of testRequest
 * 
 * 
 * @author gradydong
 * @date 2015-07-23 15:57:26
 * @copyright joyme.com
 */
require_once dirname(__FILE__). DIRECTORY_SEPARATOR.'..' . DIRECTORY_SEPARATOR . 'phplib.php';

use Joyme\core\Request;
//模拟http请求头信息
$_SERVER["HTTP_JPARAM"] = 'mock=0; platform=1; uno=490d6ebf-d6ed-45c3-b039-ec30ab79a593; source=0; token=110283e1-4417-4aa2-ba05-5e193176478a; appkey=17yfn24TFexGybOF0PqjdYA; logindomain=qq; version=2.0.4; uid=1065057; channelid=joyme; clientid=354834060606088; flag=false';
//$_SERVER["HTTP_JPARAM"] = '123456';
//$jparam = Request::header('jparam');
$jparam = Request::header('JPARAM');

var_dump($jparam);
die;

echo "<pre>";
echo "=========_REQUEST方式===========";
echo "<br/>";
var_dump($_REQUEST);


echo "=========get方式===========";
echo "<br/>";
//get方式
$get_file = Request::get('file');
var_dump($get_file);
$get_string = Request::get('string');
var_dump($get_string);
$get_set_string = Request::get('get_test','defalut_get_test');
var_dump($get_set_string);


echo "=========request方式===========";
echo "<br/>";
//request方式
$request_file = Request::getParam('file');
var_dump($request_file);
$request_string = Request::getParam('string');
var_dump($request_string);
$request_set_string = Request::getParam('test','defalut_request_test');
var_dump($request_set_string);


echo "=========post方式===========";
echo "<br/>";
//post方式
$post_file = Request::post('file');
var_dump($post_file);
$post_string = Request::post('string');
var_dump($post_string);
$post_set_string = Request::post('test','defalut_post_test');
var_dump($post_set_string);


echo "</pre>";
?>





<!DOCTYPE html>
<html>
<head lang="en">
    <meta charset="UTF-8">
    <title></title>
</head>
<body>
<form action="testRequest.php" method="get">
<!--<form action="testRequest.php" method="post">-->
    1组test：<input type="text" name="file[1][test]" value="">
    <br>
    2组test：<input type="text" name="file[2][test]" value="">
    <br>
    string：<input type="text" name="string" value="">
    <br>
    <input type="submit" value="提交">
</form>
</body>
</html>