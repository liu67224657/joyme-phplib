<?php
/**
 * Description of Request
 *
 * User: gradydong
 * Date: 2015/7/23
 * Time: 15:16
 */

namespace Joyme\core;
use Joyme\core\JoymeString;


class Request
{

    /**
     * 获取header头信息
     * @parameter string $key
     * @param mixed $defaultValue
     * @return mixed $header | null
     */
    public static function header($key, $defaultValue = null)
    {
        $key = strtoupper($key);
        $headers = array();
        foreach ($_SERVER as $k => $value) {
            if ('HTTP_' == substr($k, 0, 5)) {
                $headers[substr($k, 5)] = $value;
            }
        }
        if (isset($_SERVER['PHP_AUTH_DIGEST'])) {
            $headers['AUTHORIZATION'] = $_SERVER['PHP_AUTH_DIGEST'];
        } elseif (isset($_SERVER['PHP_AUTH_USER'])
            && isset($_SERVER['PHP_AUTH_PW'])
        ) {
            $headers['AUTHORIZATION'] = base64_encode($_SERVER['PHP_AUTH_USER'] . ':' . $_SERVER['PHP_AUTH_PW']);
        } elseif (isset($_SERVER['CONTENT_LENGTH'])) {
            $headers['CONTENT-LENGTH'] = $_SERVER['CONTENT_LENGTH'];
        } elseif (isset($_SERVER['CONTENT_TYPE'])) {
            $headers['CONTENT-TYPE'] = $_SERVER['CONTENT_TYPE'];
        }

        if (isset($headers[$key])) {
            if(strpos($headers[$key],';') !== false){
                $vars = explode(';', $headers[$key]);
                $keys = array();
                foreach ($vars as $val) {
                    $val = trim($val);
                    $k = JoymeString::getFirstSplite($val, '=');
                    $v = JoymeString::getLastSplite($val, '=');
                    $keys[$k] = $v;
                }
                return $keys;
            }else{
                return self::stripSlashes(trim($headers[$key]));
            }
        } else {
            return $defaultValue;
        }
    }

    /**
     * request方式获取参数
     * @parameter string $key
     * @param mixed $defaultValue
     * @return mixed $requests | null
     */
    public static function getParam($key, $defaultValue = null)
    {
        if (isset($_REQUEST[$key])) {
            //请求参数为数组
            if (is_array($_REQUEST[$key])) {
                $requests = $_REQUEST[$key];
                foreach ($requests as $k => $request) {
                    if (is_array($request)) {
                        foreach ($request as $rk => $rv) {
                            $request[$rk] = self::stripSlashes($rv);
                        }
                    } else {
                        $requests[$k] = self::stripSlashes($request);
                    }
                }
                //过滤key值
                $keys = array_map('stripslashes', array_keys($requests));
                return array_combine($keys, array_values($requests));
            } else {
                return self::stripSlashes(trim($_REQUEST[$key]));
            }
        } else {
            return $defaultValue;
        }
    }

    /**
     * get方式获取参数
     * @parameter string $key
     * @param mixed $defaultValue
     * @return mixed $gets | null
     */
    public static function get($key, $defaultValue = null)
    {
        if (isset($_GET[$key])) {
            //请求参数为数组
            if (is_array($_GET[$key])) {
                $gets = $_GET[$key];
                foreach ($gets as $k => $get) {
                    if (is_array($get)) {
                        foreach ($get as $gk => $gv) {
                            $get[$gk] = self::stripSlashes($gv);
                        }
                    } else {
                        $gets[$k] = self::stripSlashes($get);
                    }
                }
                //过滤key值
                $keys = array_map('stripslashes', array_keys($gets));
                return array_combine($keys, array_values($gets));
            } else {
                return self::stripSlashes(trim($_GET[$key]));
            }
        } else {
            return $defaultValue;
        }
    }

    /**
     * post方式获取参数
     * @parameter string $key
     * @param mixed $defaultValue
     * @return mixed $posts | null
     */
    public static function post($key, $defaultValue = null)
    {
        //设置默认值
        if (isset($_POST[$key])) {
            //请求参数为数组
            if (is_array($_POST[$key])) {
                $posts = $_POST[$key];
                foreach ($posts as $k => $post) {
                    if (is_array($post)) {
                        foreach ($post as $pk => $pv) {
                            $post[$pk] = self::stripSlashes($pv);
                        }
                    } else {
                        $posts[$k] = self::stripSlashes($post);
                    }
                }
                //过滤key值
                $keys = array_map('stripslashes', array_keys($posts));
                return array_combine($keys, array_values($posts));
            } else {
                return self::stripSlashes(trim($_POST[$key]));
            }
        } else {
            return $defaultValue;
        }
    }

    /**
     * 处理input的数据
     * @param mixed $data
     * @return mixed data
     */
    public static function stripSlashes($data)
    {
        if (is_array($data)) {
            if (empty($data)) {
                return $data;
            }
            $keys = array_map('stripslashes', array_keys($data));
            $data = array_combine($keys, array_values($data));
            return array_map('stripSlashes', $data);
        } else {
            return stripslashes($data);
        }
    }

}