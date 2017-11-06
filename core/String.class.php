<?php
/**
 * Description of String
 *
 *
 * Author: kexuedong
 * Date: 2015/4/30
 * Time: 18:58
 */

namespace Joyme\core;


class String
{
    //获取字符串 $str 第一个 $splite 之前的内容
    static public function getFirstSplite($str, $splite = '/')
    {
        $dot = strpos($str, $splite);
        if($dot!==false){
            $str = substr($str,0, $dot);
        }
        return $str;
    }

    //获取字符串 $str 最后一个 $splite 之后的内容
    static public function getLastSplite($str, $splite = '/')
    {
        $dot = strrpos($str, $splite);
        if($dot!==false){
            $str = substr($str, $dot + 1);
        }
        return $str;
    }

    //字符串转换为数组
    static public function stringToArray($str,$splite = ',')
    {
        if(!$str)return;
        $arr = explode($splite,$str);
        return $arr;
    }

    //数组转换为字符串
    static public function arrayToString($arr,$splite = '')
    {
        if(!$arr)return;

        if(!$splite){
            $str = implode($arr);
        }else{
            $str = implode($splite,$arr);
        }
        return $str;
    }

}