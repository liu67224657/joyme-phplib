<?php

/**
 * Description of testJoymeUser
 * 
 * 
 * @author clarkzhao
 * @date 2015-06-09 02:25:41
 * @copyright joyme.com
 */
require_once dirname(__FILE__) . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR . 'phplib.php';

use Joyme\core\JoymeUser;

$web = false;
//模拟web
if ($web) {
    $_COOKIE['jmuc_u'] = '3359254';
    $_COOKIE['jmuc_uno'] = '02f1179c-5154-4d81-a47f-057deb83039f';
    $_COOKIE['jmuc_t'] = '1433131557880';
    $_COOKIE['jmuc_s'] = 'eec9a17b7aef10e7b9407364425210e6';
    $_COOKIE['jmuc_lgdomain'] = 'qq';
    $_COOKIE['jmuc_token'] = 'b40e0561-19ea-4b0f-8be6-232796c970b3';
    $com= 'com';
}

//模拟app client
else {
    $com = 'alpha';
    $_SERVER["HTTP_JPARAM"] = 'mock=0; platform=1; uno=490d6ebf-d6ed-45c3-b039-ec30ab79a593; source=0; token=110283e1-4417-4aa2-ba05-5e193176478a; appkey=17yfn24TFexGybOF0PqjdYA; logindomain=qq; version=2.0.4; uid=1065057; channelid=joyme; clientid=354834060606088; flag=false';
//    $_COOKIE["HTTP_JPARAM"] = urlencode('mock=0; platform=1; uno=490d6ebf-d6ed-45c3-b039-ec30ab79a593; source=0; token=110283e1-4417-4aa2-ba05-5e193176478a; appkey=17yfn24TFexGybOF0PqjdYA; logindomain=qq; version=2.0.4; uid=1065057; channelid=joyme; clientid=354834060606088; flag=false');
}

JoymeUser::initByRequest();
$ret = JoymeUser::isLogin();
var_dump('isLogin', $ret);


$ret = JoymeUser::getUserInfo($com);  //alhpa , beta
var_dump('getUserInfo', $ret);
exit;
JoymeUser::init($_COOKIE['jmuc_u'], $_COOKIE['jmuc_uno'], $_COOKIE['jmuc_t'], $_COOKIE['jmuc_s'], $_COOKIE['jmuc_lgdomain']);
$ret = JoymeUser::isLogin();
var_dump('isLogin', $ret);

JoymeUser::setToken($_COOKIE['jmuc_token']);
if ($web) {
    $ret = JoymeUser::getUserInfo('com');  //alhpa , beta
} else {
    $ret = JoymeUser::getUserInfo('alpha');  //alhpa , beta
}
var_dump('getUserInfo', $ret);
exit;


$_COOKIE['jmuc_lgdomain'] = 'client';
JoymeUser::init($_COOKIE['jmuc_u'], $_COOKIE['jmuc_uno'], $_COOKIE['jmuc_t'], $_COOKIE['jmuc_s'], $_COOKIE['jmuc_lgdomain']);
$ret = JoymeUser::isLogin();
var_dump('isLogin', $ret);


?>

 <script>
     var v = _jclient.getJParam();
     alert(v);
     
    </script>

