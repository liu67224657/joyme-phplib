<?php
/**
 * Description of Aliy_ImageView
 *
 * Author: gradydong
 * Date: 2015/8/25
 * Time: 15:15
 */

namespace Joyme\aliy;


class Aliy_ImageView
{
    protected static $Width;
    protected static $Height;

    //拼接地址
    public static function MakeRequest($url)
    {
        $opt = array();
        if(self::$Width){
            $opt[] = self::$Width.'w';
        }
        if(self::$Height){
            $opt[] = self::$Height.'h';
        }
        if($opt){
            return $url.'@'.implode('_',$opt);
        }else{
            return $url;
        }
    }

    //设置宽度
    public static function SetWidth($width)
    {
        self::$Width = $width;
    }
    //设置高度
    public static function SetHeight($height)
    {
        self::$Height = $height;
    }
}