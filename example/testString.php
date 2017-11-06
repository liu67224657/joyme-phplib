<?php

/**
 * Description of testString
 * 
 * 
 * @author kexuedong
 * @date 2015-04-29
 * @copyright joyme.com
 */
require_once dirname(__FILE__). DIRECTORY_SEPARATOR.'..' . DIRECTORY_SEPARATOR . 'phplib.php';

use Joyme\core\JoymeString;

$test_array = array('a','b','c','d');

$test_str = 'dha,idd,aiia,di,djaiad,isdai,aid,sooa,jjd,i';

$ret_str = JoymeString::arrayToString($test_array);
var_dump($ret_str);
$ret_array = JoymeString::stringToArray($test_str);
var_dump($ret_array);