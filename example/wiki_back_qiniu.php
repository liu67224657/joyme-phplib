<?php

/**
 * Description of wiki_back_qiniu
 * 需要有前置脚本，把需要备份的文件列表生成出来
 * 把wiki图片备份到七牛上
 * 
 * @author clarkzhao
 * @date 2015-05-18 09:41:52
 * @copyright joyme.com
 */
set_time_limit(0);

require_once dirname(__FILE__) . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR . 'phplib.php';

use Joyme\qiniu\conf as qiniu_utils;
use Joyme\qiniu\Qiniu_RS_PutPolicy;
use Joyme\qiniu\Qiniu_PutExtra;
use Joyme\core\Log;
use Joyme\core\JoymeString;

global $putPolicy;
global $upToken;
global $putExtra;

Log::config(Log::INFO);

$bucket = 'joymepic';
$accessKey = 'G8_5kjfXfaufU53Da4bnGQ3YP-dhdmqct9sR6ImI';
$secretKey = 'KXwyeZMxYnsZMqAwojI_IEDkYj69zkwvu8jZP5_a';
qiniu_utils::Qiniu_SetKeys($accessKey, $secretKey);
$putPolicy = new Qiniu_RS_PutPolicy($bucket);
$putExtra = new Qiniu_PutExtra();
$putExtra->Crc32 = 1;

function getMemUsage() {
    return round(memory_get_usage() / 1024, 2) . "K";
}

//
//function readfilelist($listfile) {
//    $lines = file($listfile);
//    foreach ($lines as $line) {
//        $line = trim($line);
//        if ((is_dir($line))) {
//            Log::info(__FUNCTION__, "is_dir $line", getMemUsage());
//            //目录返回
//            continue;
//        } else {
//            if ($line != "." && $line != "..") {
//                $file = String::getLastSplite($line, '/');
//                $dir = str_replace($file, '', $line);
//                copy2qiniu($dir, $file,$listfile);
//            }
//        }
//    }
//}

function readlistfile($listfile) {
    $lines = file($listfile);
    foreach ($lines as $line) {
        $line = trim($line);
        copy2qiniu($line);
    }
}

function listDir($dir) {
    if (is_dir($dir)) {
        if ($dh = opendir($dir)) {
            while (($file = readdir($dh)) !== false) {
                if ((is_dir($dir . "/" . $file)) && $file != "." && $file != "..") {
                    listDir($dir . "/" . $file . "/");
                } else {
                    if ($file != "." && $file != "..") {
                        copy2qiniu($dir . $file);
                    }
                }
            }
            closedir($dh);
        }
    }
}

if ($argc < 2) {
    die("need a list file");
}
readlistfile($argv[1]);
Log::info(__FUNCTION__, $argv[1], 'backup end', getMemUsage());

function copy2qiniu($filename) {
    $filename = str_replace('//', '/', $filename);
    $qiniufile = str_replace('/opt/wiki/', '', $filename);
    global $putPolicy;
    global $upToken;
    global $putExtra;
    $upToken = $putPolicy->Token(null);
    list($ret, $err) = qiniu_utils::Qiniu_PutFile($upToken, $qiniufile, $filename, $putExtra);
    if ($err !== null) {
        Log::error(__FUNCTION__, $err, $filename, getMemUsage());
    } else {
        Log::info(__FUNCTION__, "http://joymepic.joyme.com/" . $ret['key'], 'succ', getMemUsage());
    }
}

exit;


