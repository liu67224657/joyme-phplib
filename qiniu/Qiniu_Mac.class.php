<?php

namespace Joyme\qiniu;
use Joyme\qiniu\Qiniu_Utils;
//require_once("utils.php");
//require_once("conf.php");
// ----------------------------------------------------------

class Qiniu_Mac {

    static protected $AccessKey;
    static protected $SecretKey;

    public function __construct($accessKey, $secretKey) {
        self::$AccessKey = $accessKey;
        self::$SecretKey = $secretKey;
    }

    public function Sign($data) { // => $token
        $sign = hash_hmac('sha1', $data, self::$SecretKey, true);
        return self::$AccessKey . ':' . Qiniu_Utils::Qiniu_Encode($sign);
    }

    public function SignWithData($data) { // => $token
        $data = Qiniu_Utils::Qiniu_Encode($data);
        return $this->Sign($data) . ':' . $data;
    }

    public function SignRequest($req, $incbody) { // => ($token, $error)
        $url = $req->URL;
        $url = parse_url($url['path']);
        $data = '';
        if (isset($url['path'])) {
            $data = $url['path'];
        }
        if (isset($url['query'])) {
            $data .= '?' . $url['query'];
        }
        $data .= "\n";

        if ($incbody) {
            $data .= $req->Body;
        }
        return $this->Sign($data);
    }

    public function VerifyCallback($auth, $url, $body) { // ==> bool
        $url = parse_url($url);
        $data = '';
        if (isset($url['path'])) {
            $data = $url['path'];
        }
        if (isset($url['query'])) {
            $data .= '?' . $url['query'];
        }
        $data .= "\n";

        $data .= $body;
        $token = 'QBox ' . $this->Sign($data);
        return $auth === $token;
    }

    static public function Qiniu_SetKeys($accessKey, $secretKey) {
        self::$AccessKey = $accessKey;
        self::$SecretKey = $secretKey;
    }

    static public function Qiniu_RequireMac($mac) { // => $mac
        if (isset($mac)) {
            return $mac;
        }
        return new Qiniu_Mac(self::$AccessKey , self::$SecretKey);
    }

    static public function Qiniu_Sign($mac, $data) { // => $token
        return self::Qiniu_RequireMac($mac)->Sign($data);
    }

    static public function Qiniu_SignWithData($mac, $data) { // => $token
        return self::Qiniu_RequireMac($mac)->SignWithData($data);
    }

}

// ----------------------------------------------------------

