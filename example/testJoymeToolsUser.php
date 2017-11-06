<?php

/**
 * Description of testJoymeToolsUser
 * 
 * 
 * @author clarkzhao
 * @date 2015-07-01 01:34:03
 * @copyright joyme.com
 */
require_once dirname(__FILE__) . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR . 'phplib.php';

use Joyme\core\JoymeToolsUser;




$_COOKIE['t_jm_encrypt'] = '61d4964b17e44d863a46494f538ade96';
$_COOKIE['t_jm_message'] = urldecode('1%7C1%7Csysadmin%7C%E7%AE%A1%E7%90%86%E5%91%98%7C1436625362961');
$com = 'alpha';
$strpos = $com;
$redirect_url = "http://zozoka.joyme." . $com . "/admin";


JoymeToolsUser::init($com, $redirect_url);
$role = 100;
//注意， 如果校验失败会直接页面跳转到 $redirect_url 并exit
$ret = JoymeToolsUser::check($role);
 
var_dump('check',$ret);
exit;
$ret = JoymeToolsUser::addlog("test JoymeToolsUser",'cms','add');

var_dump('addlog',$ret);
