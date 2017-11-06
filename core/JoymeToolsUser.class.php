<?php

/**
 * Description of JoymeToolsUser
 * 
 * 统一对接tools后台，处理tools账号权限判断， 记录日志等接口
 * 
 * @author clarkzhao
 * @date 2015-07-01 11:56:51
 * @copyright joyme.com
 */

namespace Joyme\core;

use Joyme\core\Singleton;
use Joyme\core\Log;
use Exception as Exception;
use Joyme\core\JoymeString;
use Joyme\net\HttpClient;

class JoymeToolsUser extends Singleton {

    protected static $com = null;
    protected static $uid = null;
    protected static $uno = null;
    protected static $username = null;
    protected static $roles = array();  //当前用户拥有的权限id
    protected static $timestamp = 0;   //登录时间
    protected static $redirect_url = null; //未登录和无权限回跳url
    protected static $t_jm_message = null;
    protected static $t_jm_encrypt = null;
    protected static $key = null;
    protected static $maxLoginTime = 86400000; //3600*24*1000 登录有效时间最长1天

    public static function setCom($com) {
        self::$com = $com;
    }

    public static function getCom() {
        return self::$com;
    }

    public static function setUid($uid) {
        self::$uid = $uid;
    }

    public static function getUid() {
        return self::$uid;
    }

    public static function setUsername($username) {
        self::$username = $username;
    }

    public static function getUsername() {
        return self::$username;
    }

    public static function setTimestamp($timestamp) {
        self::$timestamp = $timestamp;
    }

    public static function getTimestamp() {
        return self::$timestamp;
    }

    public static function setUno($uno) {
        self::$uno = $uno;
    }

    public static function getUno() {
        return self::$uno;
    }

    public static function getRoles() {
        return self::$roles;
    }

    public static function setRedirect($redirect_url) {
        self::$redirect_url = $redirect_url;
    }

    public static function getRedirect() {
        return self::$redirect_url;
    }

    public static function init($com, $redirect_url) {
        self::setCom($com);
        self::setRedirect($redirect_url);
        self::$t_jm_message = isset($_COOKIE['t_jm_message']) ? $_COOKIE['t_jm_message'] : '';
        self::$t_jm_encrypt = isset($_COOKIE['t_jm_encrypt']) ? $_COOKIE['t_jm_encrypt'] : '';
//public static final String TOOLS_COOKIEKEY_SECRET_KEY_DEV = "7ejw!9d#";
//public static final String TOOLS_COOKIEKEY_SECRET_KEY_ALPHA = "8F5&JL3";
//public static final String TOOLS_COOKIEKEY_SECRET_KEY_BETA = "#4g%klwe";        
        if ($com == 'alpha') {
            self::$key = '8F5&JL3';
        } else if ($com == 'beta') {
            self::$key = '#4g%klwe';
        } else if ($com == 'dev') {
            self::$key = '7ejw!9d#';
        } else {
            self::$key = 'yh87&sw2';
        }
        $check = md5(self::$key . urlencode(self::$t_jm_message));
        if ($check == self::$t_jm_encrypt && !empty(self::$t_jm_message)) {
            $roles = explode('|', self::$t_jm_message);
            $roleids = explode(',', $roles[0]);
            self::$roles = $roleids;
            self::setUno($roles[1]);
            self::setUid($roles[2]);
            self::setUsername($roles[3]);
            self::setTimestamp($roles[4]); //java平台时间为毫秒
        }
    }

    public static function getIP() {
        if (getenv("HTTP_CLIENT_IP"))
            $ip = getenv("HTTP_CLIENT_IP");
        else if (getenv("HTTP_X_FORWARDED_FOR"))
            $ip = getenv("HTTP_X_FORWARDED_FOR");
        else if (getenv("REMOTE_ADDR"))
            $ip = getenv("REMOTE_ADDR");
        else
            $ip = "0.0.0.0";
        return $ip;
    }

    public static function check() {
        //$role
        $params = func_get_args();
        
        if(!empty($params[0]) && is_array($params[0])){
        	$roles = $params[0];
        }else{
			$roles = $params;
		}
        $uploadimg = empty($params[1]) ? false : $params[1];
        $now = time() . "000";

        //1是超级管理员  拥有任意系统权限
        $roles[] = 1;///var_dump(self::$maxLoginTime);exit;
        //超过登录时间的直接跳登录
        //如果是未登录的， timestampe为0 ，也会跳转
//        1449198676000---0---86400000
//        echo $now.'---'.self::$timestamp.'---'.self::$maxLoginTime;exit;
        
        if ($now > (self::$timestamp + self::$maxLoginTime)) {
            Log::error(__FUNCTION__,"too old",$now,(self::$timestamp + self::$maxLoginTime));
            return self::authenticate();
        }
        // 0 角色是一个特例，只是判断tools登录成功，不考虑具体权限
        if (in_array(0, $roles) || $uploadimg) {
            return true;
        }
        foreach ($roles as $role) {
            if (in_array($role, self::$roles)) {
                return true;
            }
        }
        return self::authenticate();
    }

    //跳转
    private static function authenticate() {
        $url = "http://tools.joyme." . self::$com . "/loginpage?reurl=" . urlencode(self::$redirect_url);
        header("location:" . $url);
        exit;
    }

//log记录
    public static function addlog($message,$btype='phprelease',$stype='add') {
        $http = new HttpClient('tools.joyme.' . self::$com);
        $path = '/log/addlog';
        $http->setDebug(false);
        $data = array(
            'userid' => self::$uid,
            'btype' => $btype,
            'stype' => $stype,
            'opafter' => $message,
            'ip' => self::getIP(),
            'encrypt' => md5(self::$key . self::$uid)
        );
        $ret = $http->post($path, $data);
        if ($ret) {
            $content = $http->getContent($path);
            $result = json_decode($content, true);
            if (empty($result) || $result['rs'] != 1) {
                Log::error(__CLASS__, __FUNCTION__, 'error ' . $content);
            }
            return true;
        }
        return false;
    }

    public static function logout() {
        //删除 cookie
        setcookie("t_jm_message", '', 0, '/', '.joyme.' . self::$com);
        setcookie("t_jm_encrypt", '', 0, '/', '.joyme.' . self::$com);
        //清理内部变量
        self::setTimestamp(0);
        self::setUid(0);
        self::setUno('');
        self::setUsername('');
        self::$roles = array();
    }

}
