<?php
/**
 * Description of testaliy
 * 
 * 
 * @author gradydong
 * @date 2015-08-21 11:57:08
 * @copyright joyme.com
 */
require_once dirname(__FILE__) . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR . 'phplib.php';

use Joyme\aliy\Aliy_Utils;
use Joyme\aliy\Aliy_ImageView;

$bucket = 'aliytestpro';
//$object = 'images/'.time().'.jpg';
//$object = 'ali/test/images/'.time().'.jpg';
$file = "../aliy/a.jpg";
$aliy = new Aliy_Utils();
//上传文件

/*$ret =  $aliy->upload_file_by_file($bucket,$object,$file);
if($ret->status != 200){
    var_dump($ret);
}else{
    var_dump($ret);
    Aliy_ImageView::SetWidth(200);
    Aliy_ImageView::SetHeight(200);
    $url = 'http://aliytest.joyme.com/'.$object;
    $imgViewUrl = Aliy_ImageView::MakeRequest($url);
    var_dump($imgViewUrl);
}

die;*/
//删除文件
//$object = 'ali/test/images/1441088469.jpg';
/*$object = 'images/1440494089.jpg';
$ret = $aliy->delete_object($bucket,$object);
if($ret->status != 204){
    var_dump($ret);
}else{
    echo 'delete success';
}
die;*/
$from_bucket = 'aliytestpro';
$from_object = 'images/1440473718.jpg';
$to_bucket = 'aliytestpro';
$to_object = 'ali/test/images/1440473718.jpg';
$ret = $aliy->copy_object($from_bucket, $from_object, $to_bucket, $to_object);

var_dump($ret);
