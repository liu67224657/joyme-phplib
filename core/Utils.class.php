<?php
/**
 * 公共工具类
 * 一些公共方法的集合
 */
namespace Joyme\core;

use Joyme\net\Curl;
use Joyme\core\Log;

class Utils
{

    /**
     * 百度推送
     */
    static public function baiduPush($srcurl)
    {
        if($srcurl){
            $curl = new Curl();
            //定义环境
            $com = $_SERVER['HTTP_HOST'];
            while ($dot = strpos($com, ".")) {
                $com = substr($com, $dot + 1);
            }
            $url = 'http://webcache.joyme.'.$com.'/api/baidu/push.do?srcurl='.$srcurl;
            $res = $curl->Get($url);
            if($com == 'alpha' || $res == false){
                Log::debug(__FUNCTION__, $url);
            }
        }else{
            Log::debug(__FUNCTION__, 'srcurl is null');
        }
    }

    /**
     * 获取字符串 $str 最后一个 $splite 之后的内容
     */
    static public function getLastSplite($str, $splite = '/')
    {
        while ($dot = strpos($str, $splite)) {
            $str = substr($str, $dot + 1);
        }
        return $str;
    }
}