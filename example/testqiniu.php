<?php

/**
 * Description of testqiniu
 * 
 * 
 * @author clarkzhao
 * @date 2015-04-28 06:56:08
 * @copyright joyme.com
 */
require_once dirname(__FILE__) . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR . 'phplib.php';

use Joyme\qiniu\Qiniu_RS_PutPolicy;
use Joyme\qiniu\Qiniu_PutExtra;
use Joyme\qiniu\Qiniu_ImageView;


use Joyme\qiniu\Qiniu_Utils;
//不推荐使用conf后续会删除   推荐使用Qiniu_Utils conf 
//use Joyme\qiniu\conf as qiniu_utils;


$bucket = 'joymetest';
// 这段删除 无效了
//$accessKey = 'ddd-KjYfPy-00s8C1qwNBtbiX7bA';
//$secretKey = 'dddd';
//qiniu_utils::Qiniu_SetKeys($accessKey, $secretKey);
$key = 'mt/'. time() . '.jpg';

//$putPolicy = new Qiniu_RS_PutPolicy($bucket.":".$key);
//$upToken = $putPolicy->Token(null);
//
//
////var_dump($upToken);
$file = "../qiniu/a.jpg";
//$putExtra = new Qiniu_PutExtra();
//$putExtra->Crc32 = 1;
//list($ret, $err) = Qiniu_Utils::Qiniu_PutFile($upToken, $key, $file, $putExtra);
//echo "====> Qiniu_PutFile result: \n";
//if ($err !== null) {
//    var_dump($err);
//} else {
//    //    var_dump($ret);
//    echo "http://" . $bucket . ".qiniudn.com/" . $ret['key'] . "\n";
//    $baseUrl = "http://" . $bucket . ".qiniudn.com/" . $ret['key'];
//    //生成fopUrl
//    $imgView = new Qiniu_ImageView;
//    $imgView->Mode = 1;
//    $imgView->Width = 80;
//    $imgView->Height = 80;
//    $imgViewUrl = $imgView->MakeRequest($baseUrl);
//    var_dump($imgViewUrl);
//}
//
////兼容老代码 后续会删除 建议使用 Qiniu_Utils
//list($ret, $err) = qiniu_utils::Qiniu_PutFile($upToken, $key, $file, $putExtra);
//echo "====> qiniu_utils result: \n";
//if ($err !== null) {
//    var_dump($err);
//} else {
//    //    var_dump($ret);
//    echo "http://" . $bucket . ".qiniudn.com/" . $ret['key'] . "\n";
    $baseUrl = 'http://joymetest.qiniudn.com/mt/1435818592.jpg';
//    //生成fopUrl
//    $imgView = new Qiniu_ImageView;
//    $imgView->Mode = 1;
//    $imgView->Width = 80;
//    $imgView->Height = 80;
//    $imgViewUrl = $imgView->MakeRequest($baseUrl);
//    $imgViewUrl= $imgViewUrl."/".date('Ymd');
//    var_dump($imgViewUrl);
//}
//

list($ret, $err) = Qiniu_Utils::Qiniu_SaveFile($bucket , $key, $file, true);
echo "====> Qiniu_SaveFile result: \n";
if ($err !== null) {
    var_dump($err);
} else {
    echo "Qiniu_SaveFile http://" . $bucket . ".qiniudn.com/" . $ret['key'] . "\n";
}
//$ret = Qiniu_Utils::Qiniu_DeleteFile($bucket, $key);
//if(!empty($ret)){
//    echo 'Qiniu_DeleteFile faild';
//}else{
//    echo 'Qiniu_DeleteFile succ';
//}

$bucketDest = $bucket;
$keyDest= 'mt/movetest'.time().'.jpg';
echo  '$keyDest '.$keyDest."\n";
$ret = Qiniu_Utils::Qiniu_MoveFile($bucket, $key, $bucketDest, $keyDest);
var_dump('Qiniu_MoveFile',$ret);

$keyDestCopy= 'mt/copytest'.time().'.jpg';
echo  '$keyDestCopy '.$keyDestCopy."\n";
$ret = Qiniu_Utils::Qiniu_CopyFile($bucket, $keyDest, $bucketDest, $keyDestCopy);



//list($ret, $err) = Qiniu_Utils::Qiniu_SaveFile($bucket , $key, $file);
//echo "====> Qiniu_SaveFile result: \n";
//if ($err !== null) {
//    var_dump($err);
//} else {
//    //    var_dump($ret);
//    echo "Qiniu_SaveFile replace=false http://" . $bucket . ".qiniudn.com/" . $ret['key'] . "\n";
//    $baseUrl = "http://" . $bucket . ".qiniudn.com/" . $ret['key'];
//}






exit;
