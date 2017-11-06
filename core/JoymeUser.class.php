<?php

/**
 * Description of JoymeUser
 * 
 * 检查用户中心cookie 校验是否登录状态是否合法
 * http://wiki.enjoyf.com/wiki/Platform_cookie_dictionary
 * 
 * jmuc_cid  clientid 用户在自注册接口生成的cookie，永久有效,服务端会检验该值是否存在如果存在视为用户登录
  jmuc_u uid 用户profile的唯一id long类型，一周有效
  jmuc_uno  uno 用户的UNO，一周有效
  jmuc_t  timestamp 时间戳，一周有效
  jmuc_s  sign签名（md5 uid+uno+timestamp+COOKIE_SECR） 客户端在取cookie时要用相同方式比对签名是否有效，一周有效
  jmuc_token  token值用于验证token的有效性
  jmuc_appkey  appkey 如果是客户端调用web页面时，可设置该值，不同的业务调用时，可以重置该值
  jmuc_lgdomain logindomain 保存用户的登录方式如果logindomain为client，将用户视为未登录状态。
  COOKIE_SECR 由服务器提供，需要单独约定。
 * @author clarkzhao
 * @date 2015-06-09 01:46:20
 * @copyright joyme.com
 */

namespace Joyme\core;

use Joyme\core\Singleton;
use Joyme\core\Log;
use Exception as Exception;
use Joyme\core\JoymeString;

define('JU_LOGINDOAMIN_CLIENT', 'client');
define('JU_LOGINDOAMIN_QQ', 'qq');

define('JU_APPSIGN_VALID', 'APPCLEINT_8Vgd=8Irt%laFY1uw4');

class JoymeUser extends Singleton {

    protected static $uid = null;
    protected static $uno = null;
    protected static $token = null;
    protected static $appkey = 'default';
    protected static $app_secret_key = null;
    protected static $timestamp = null;
    protected static $logindomain = null;
    protected static $sign = null;
    protected static $pid = null;

    public static function setCheckSign($sign) {
        self::$sign = $sign;
    }

    public static function getCheckSign() {
        return self::$sign;
    }

    public static function setAppKey($appkey) {
        self::$appkey = $appkey;
    }

    public static function getAppKey() {
        return self::$appkey;
    }

    public static function setAppSeckey($appseckey) {
        self::$app_secret_key = $appseckey;
    }

    public static function getAppSeckey() {
        return self::$app_secret_key;
    }

    public static function setTimestamp($timestamp) {
        self::$timestamp = $timestamp;
    }

    public static function getTimestamp() {
        return self::$timestamp;
    }

    public static function setToken($token) {
        self::$token = $token;
    }

    public static function getToken() {
        return self::$token;
    }

    public static function setUid($uid) {
        self::$uid = intval($uid);
    }

    public static function getUid() {
        return self::$uid;
    }

    public static function setUno($uno) {
        self::$uno = $uno;
    }

    public static function getUno() {
        return self::$uno;
    }

    public static function setPid($pid) {
        self::$pid = $pid;
    }

    public static function getPid() {
        return self::$pid;
    }

    public static function setLoginDomain($logindomain) {
        self::$logindomain = $logindomain;
    }

    public static function getLoginDomain() {
        return self::$logindomain;
    }

    public static function initByRequest($secret = 'as__-d(*^(') {
        if (isset($_COOKIE['JParam'])) {
            $http_jparams = urldecode($_COOKIE['JParam']);
            $vars = explode(';', $http_jparams);
            $appinfo = array();
            foreach ($vars as $val) {
                $val = trim($val);
                $k = JoymeString::getFirstSplite($val, '=');
                $v = JoymeString::getLastSplite($val, '=');
                $appinfo[$k] = $v;
            }
            self::init($appinfo['uid'], $appinfo['uno'], time(), JU_APPSIGN_VALID, $appinfo['logindomain']);
            self::setPid($appinfo['pid']);
            self::setToken($appinfo['token']);
            self::setAppKey($appinfo['appkey']);
        }
        else if (isset($_COOKIE['HTTP_JPARAM'])) {
            $http_jparams = urldecode($_COOKIE['HTTP_JPARAM']);
            $vars = explode(';', $http_jparams);
            $appinfo = array();
            foreach ($vars as $val) {
                $val = trim($val);
                $k = JoymeString::getFirstSplite($val, '=');
                $v = JoymeString::getLastSplite($val, '=');
                $appinfo[$k] = $v;
            }
            self::init($appinfo['uid'], $appinfo['uno'], time(), JU_APPSIGN_VALID, $appinfo['logindomain']);
            self::setToken($appinfo['token']);
            self::setAppKey($appinfo['appkey']);
        } else if (isset($_SERVER["HTTP_JPARAM"])) {
            //app client请求以 HTTP_JPARAM 优先
            $vars = explode(';', $_SERVER['HTTP_JPARAM']);
            $appinfo = array();
            foreach ($vars as $val) {
                $val = trim($val);
                $k = JoymeString::getFirstSplite($val, '=');
                $v = JoymeString::getLastSplite($val, '=');
                $appinfo[$k] = $v;
            }
			if($appinfo['appkey'] == '0G30ZtEkZ4vFBhAfN7Bx4vI'){
				// 专供海贼迷
				self::init($_COOKIE['jmuc_u'], $_COOKIE['jmuc_uno'], $_COOKIE['jmuc_t'], $_COOKIE['jmuc_s'], $_COOKIE['jmuc_lgdomain']);
				self::setToken($_COOKIE['jmuc_token']);
				//web默认请求的appkey 为 default 如果要调整，在外部在自由设置
				//web需要设置 appsecret
				self::setAppSeckey($secret);
			}else{
				$appinfo['logindomain'] = empty($appinfo['logindomain'])?'':$appinfo['logindomain'];
				self::init($appinfo['uid'], $appinfo['uno'], time(), JU_APPSIGN_VALID, $appinfo['logindomain']);
				self::setToken($appinfo['token']);
				self::setAppKey($appinfo['appkey']);
			}
        } else {
            // web请求
            $jmuc_u = empty($_COOKIE['jmuc_u'])?'':$_COOKIE['jmuc_u'];
            $jmuc_uno = empty($_COOKIE['jmuc_uno'])?'':$_COOKIE['jmuc_uno'];
            $jmuc_t = empty($_COOKIE['jmuc_t'])?'':$_COOKIE['jmuc_t'];
            $jmuc_s = empty($_COOKIE['jmuc_s'])?'':$_COOKIE['jmuc_s'];
            $jmuc_token = empty($_COOKIE['jmuc_token'])?'':$_COOKIE['jmuc_token'];
            $jmuc_lgdomain = empty($_COOKIE['jmuc_lgdomain'])?'':$_COOKIE['jmuc_lgdomain'];
            $jmuc_pid = empty($_COOKIE['jmuc_pid'])?'':$_COOKIE['jmuc_pid'];
            self::init($jmuc_u, $jmuc_uno, $jmuc_t, $jmuc_s, $jmuc_lgdomain);
            self::setToken($jmuc_token);
            self::setPid($jmuc_pid);
            //web默认请求的appkey 为 default 如果要调整，在外部在自由设置
            //web需要设置 appsecret
            self::setAppSeckey($secret);
        }
    }

    public static function init($uid, $uno, $timestamp, $sign, $logindomain, $secret = 'as__-d(*^(') {
        self::setUno($uno);
        self::setCheckSign($sign);
        self::setLoginDomain($logindomain);
        self::setUid($uid);
        self::setTimestamp($timestamp);
        self::setAppSeckey($secret);
    }

    public static function getUserInfo($com = 'com') {
        if(empty(self::$token)){
            return false;
        }
        $url = "http://passport.joyme." . $com . "/api/user/getbyuid?uid=" . self::$uid . "&token=" . self::$token;
        $url.="&appkey=" . self::$appkey . "&uno=" . self::$uno . "&logindomain=" . self::$logindomain;
        $user_info = @file_get_contents($url);
        $rs = json_decode($user_info, true);
        if ($rs['rs'] !== 1) {
            Log::warning(__FUNCTION__, $com, $rs['rs'] . $rs['msg'], $url);
            return false;
        }
        return $rs['profile'];
    }

    public static function isLogin() {
        if (self::$logindomain == JU_LOGINDOAMIN_CLIENT)
            return false;
        if (self::$sign == JU_APPSIGN_VALID)
            return true;
        if (empty(self::$app_secret_key)) {
            return false;
        }
        if (empty(self::$sign)) {
            return false;
        }
        if (empty(self::$uid)) {
            return false;
        }
        if (empty(self::$uno)) {
            return false;
        }
        if (empty(self::$logindomain)) {
            return false;
        }
        if (md5(self::$uid . self::$uno . self::$timestamp . self::$app_secret_key) == self::$sign)
            return true;
        return false;
    }

}
