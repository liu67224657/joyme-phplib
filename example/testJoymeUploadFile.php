<?php
/**
 * Description of testJoymeUploadFile
 * 
 * 
 * @author gradydong
 * @date 2015-09-01 10:57:08
 * @copyright joyme.com
 */

function help(){
    $str = <<<EOD
     使用说明:
     1：bucket说明
        bucket名称	  所属第三方	  备注	     host
        joymepic	   七牛	      图片	    joymepic.joyme.com
        static	       七牛	     静态资源	static.joyme.com
        joymetest	   七牛	      测试      joymetest.qiniudn.com
        aliypicpro	   阿里云	  图片	    aliypic.joyme.com
        aliytestpro	   阿里云	  测试	    aliytest.joyme.com
     2: url规则说明
        规则 http://<backet--全局唯一>.joyme.com/<第三方code:qiniu、ali等>/<业务代码:wanba、game>/path
        七牛: 第三方code是"qiniu",例如：http://joymepic.joyme.com/qiniu/wanba/2015/8/12/a1439362304902.jpg
        阿里：第三方code是"ali",例如：http://aliypic.joyme.com/ali/wanba/2015/8/12/a1439362304902.jpg
     3: 数据库地址保存，要保存完整地址
        格式如 http://aliypic.joyme.com/ali/wanba/2015/8/12/a1439362304902.jpg
EOD;
    echo $str;
}

require_once dirname(__FILE__) . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR . 'phplib.php';

use Joyme\core\JoymeUploadFile;

$bucket = 'aliytestpro';
//上传文件
/*$object = 'ali/test/images/'.time().'.jpg';
$file = "../aliy/a.jpg";

$ret = JoymeUploadFile::SaveFile($bucket,$object,$file);

var_dump($ret);*/

//删除文件
//$object = 'ali/test/images/1441088927.jpg';
//$object = 'http://aliytestpro.joyme.com/ali/test/images/1441088927.jpg';
/*$ret = JoymeUploadFile::DeleteFile($bucket,$object);
var_dump($ret);*/

//复制文件
/*$from_bucket = 'aliytestpro';
$from_object = 'images/1440473718.jpg';
$to_bucket = 'aliytestpro';
$to_object = 'ali/test/images/14404737182.jpg';
$ret = JoymeUploadFile::CopyFile($from_bucket, $from_object, $to_bucket, $to_object);
var_dump($ret);*/

//移动文件
/*$from_bucket = 'aliytestpro';
$from_object = 'images/1440473787.jpg';
$to_bucket = 'aliytestpro';
$to_object = 'ali/test/images/14404737871.jpg';
$ret = JoymeUploadFile::MoveFile($from_bucket, $from_object, $to_bucket, $to_object);
var_dump($ret);*/

//查看图片

$url = 'http://aliytest.joyme.com/ali/test/images/1441085344.jpg';
JoymeUploadFile::SetWidth(230);
JoymeUploadFile::SetHeight(230);
$replaceDomain = null;
//$replaceDomain = 'http://aliypic.joyme.com/';
$ret = JoymeUploadFile::ImageView($url,$replaceDomain);
var_dump($ret);
