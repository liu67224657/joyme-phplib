<?php
/**
 * Description of testaliycdn
 * 
 * 
 * @author gradydong
 * @date 2015-08-21 11:57:08
 * @copyright joyme.com
 */
require_once dirname(__FILE__) . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR . 'phplib.php';


use Joyme\aliy\Aliy_CdnRefresh;

$cdnrefresh = new Aliy_CdnRefresh();

$url = 'aliytest.joyme.com/ali/test/images/1440473718.jpg';
$objectType = 'File';// or Directory

$ret = $cdnrefresh->execute($url,$objectType);

var_dump($ret);