<?php
/**
 * Description of JoymeUploadFile
 *
 *
 * @author gradydong
 * @date 2015/8/27 12:09
 * @copyright joyme.com
 */

namespace Joyme\core;

use Joyme\qiniu\Qiniu_Utils;
use Joyme\qiniu\Qiniu_ImageView;
use Joyme\aliy\Aliy_Utils;
use Joyme\aliy\Aliy_ImageView;

class JoymeUploadFile
{

    protected static $width;
    protected static $height;

    //bucket对应云平台类型
    public static $cloudType = array(
        'joymepic' => 'qiniu',
        'static' => 'qiniu',
        'joymetest' => 'qiniu',
        'aliypicpro' => 'aliy',
        'aliytestpro' => 'aliy',
    );

    //bucket对应云平台域名
    public static $cloudDomain = array(
        'joymepic' => 'http://joymepic.joyme.com/',
        'static' => 'http://static.joyme.com/',
        'joymetest' => 'http://joymetest.qiniudn.com/',
        'aliypicpro' => 'http://aliypic.joyme.com/',
        'aliytestpro' => 'http://aliytest.joyme.com/',
    );

    /**
     * 上传文件
     * @parameter string $bucket
     * @parameter string $object
     * @parameter string $file
     * @return string URL | mixed false
     */
    public static function SaveFile($bucket, $object, $file)
    {
        self::PrecheckCommon($bucket);
        $cloudType = self::$cloudType[$bucket];
        if ($cloudType == 'qiniu') {
            list($ret, $err) = Qiniu_Utils::Qiniu_SaveFile($bucket, $object, $file, true);
            if ($err !== null) {
                return $err;
            } else {
                return self::$cloudDomain[$bucket] . $ret['key'];
            }
        } elseif ($cloudType == 'aliy') {
            $aliy = new Aliy_Utils();
            $ret = $aliy->upload_file_by_file($bucket, $object, $file);
            if ($ret->status != 200) {
                return $ret->body;
            } else {
                return self::$cloudDomain[$bucket] . $object;
            }
        } else {
            return false;
        }
    }

    /**
     * 删除文件
     * @parameter string $bucket
     * @parameter string $object
     * @parameter string $file
     * @return string URL | mixed false
     */
    public static function DeleteFile($bucket, $object)
    {
        self::PrecheckCommon($bucket);
        $object = self::PrecheckObject($object);
        $cloudType = self::$cloudType[$bucket];
        if ($cloudType == 'qiniu') {
            $ret = Qiniu_Utils::Qiniu_DeleteFile($bucket, $object);
            if (!empty($ret)) {
                return $ret;
            } else {
                return true;
            }
        } elseif ($cloudType == 'aliy') {
            $aliy = new Aliy_Utils();
            $ret = $aliy->delete_object($bucket, $object);
            if ($ret->status != 204) {
                return $ret->body;
            } else {
                return true;
            }
        } else {
            return false;
        }
    }

    /**
     * 复制文件
     * @parameter string $from_bucket
     * @parameter string $from_object
     * @parameter string $to_bucket
     * @parameter string $to_object
     * @return string URL | mixed false
     */
    public static function CopyFile($from_bucket, $from_object, $to_bucket, $to_object)
    {
        self::PrecheckCommon($from_bucket);
        self::PrecheckCommon($to_bucket);
        $cloudType = self::$cloudType[$to_bucket];
        if ($cloudType == 'qiniu') {
            $ret = Qiniu_Utils::Qiniu_CopyFile($from_bucket, $from_object, $to_bucket, $to_object);
            if (!empty($ret)) {
                return $ret;
            } else {
                return true;
            }
        } elseif ($cloudType == 'aliy') {
            $aliy = new Aliy_Utils();
            $ret = $aliy->copy_object($from_bucket, $from_object, $to_bucket, $to_object);
            if ($ret->status != 200) {
                return $ret->body;
            } else {
                return true;
            }
        } else {
            return false;
        }
    }

    /**
     * 移动文件
     * @parameter string $from_bucket
     * @parameter string $from_object
     * @parameter string $to_bucket
     * @parameter string $to_object
     * @return string URL | mixed true or false
     */
    public static function MoveFile($from_bucket, $from_object, $to_bucket, $to_object)
    {
        self::PrecheckCommon($from_bucket);
        self::PrecheckCommon($to_bucket);
        $cloudType = self::$cloudType[$to_bucket];
        if ($cloudType == 'qiniu') {
            $ret = Qiniu_Utils::Qiniu_MoveFile($from_bucket, $from_object, $to_bucket, $to_object);
            if (!empty($ret)) {
                return $ret;
            } else {
                return true;
            }
        } elseif ($cloudType == 'aliy') {
            $aliy = new Aliy_Utils();
            $ret = $aliy->copy_object($from_bucket, $from_object, $to_bucket, $to_object);
            if ($ret->status != 200) {
                return $ret->body;
            } else {
                $ret = $aliy->delete_object($from_bucket, $from_object);
                if ($ret->status != 204) {
                    return $ret->body;
                } else {
                    return true;
                }
            }
        } else {
            return false;
        }
    }

    /**
     * 图片查看
     * @parameter string $url
     * @parameter string $replaceDomain 替换域名
     * @return string $imgViewUrl |mixed false
     */
    public static function ImageView($url, $replaceDomain = null)
    {
        //新数据 阿里云
        if (strpos($url, 'joyme.com/ali/') !== false) {
            if (self::$width) {
                Aliy_ImageView::SetWidth(self::$width);
            }
            if (self::$height) {
                Aliy_ImageView::SetHeight(self::$height);
            }
            $imgViewUrl = Aliy_ImageView::MakeRequest($url);
            if ($replaceDomain) {
                $imgViewUrl = preg_replace('/http:\/\/.*?\//', '', $imgViewUrl);
                return $replaceDomain . $imgViewUrl;
            } else {
                return $imgViewUrl;
            }
        }
        //新数据 七牛
        elseif (strpos($url, 'joyme.com/qiniu/') !== false) {
            $imgView = new Qiniu_ImageView;
            if (self::$width) {
                $imgView->Width = self::$width;
            }
            if (self::$height) {
                $imgView->Height = self::$height;
            }
            $imgViewUrl = $imgView->MakeRequest($url);
            if ($replaceDomain) {
                $imgViewUrl = preg_replace('/http:\/\/.*?\//', '', $imgViewUrl);
                return $replaceDomain . $imgViewUrl;
            } else {
                return $imgViewUrl;
            }
        }
        else {
            return $url;
        }
    }

    /**
     * 设置宽度
     * @parameter string $width
     */
    public static function SetWidth($width)
    {
        self::$width = $width;
    }

    /**
     * 设置高度
     * @parameter string $height
     */
    public static function SetHeight($height)
    {
        self::$height = $height;
    }

    /**
     * 检查bucket对应的云平台类型和域名信息
     * @parameter string $bucket
     * @return string msg
     */
    private static function PrecheckCommon($bucket)
    {
        //判断云平台是否存在
        $cloudType = self::$cloudType[$bucket];
        if (!$cloudType) {
            return 'bucket对应云平台信息不存在';
        }
        //判断云平台域名是否存在
        $cloudDomain = self::$cloudDomain[$bucket];
        if (!$cloudDomain) {
            return 'bucket对应云平台域名信息不存在';
        }
    }

    /**
     *
     */
    private static function PrecheckObject($object)
    {
        if (strpos($object, 'http://') === false) {
            return $object;
        } else {
            return preg_replace('/http:\/\/.*?\//', '', $object);
        }
    }
}