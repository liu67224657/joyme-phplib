<?php

namespace Joyme\qiniu;

use Joyme\qiniu\http;
use Joyme\qiniu\http\Qiniu_MacHttpClient;
use Joyme\qiniu\http\Qiniu_HttpClient;
use Joyme\qiniu\Qiniu_RS_PutPolicy;
use Joyme\qiniu\Qiniu_PutExtra;

global $SDK_VER;
global $QINIU_UP_HOST;
global $QINIU_RS_HOST;
global $QINIU_RSF_HOST;
//global $QINIU_ACCESS_KEY;
//global $QINIU_SECRET_KEY;
$SDK_VER = "6.1.9";
$QINIU_UP_HOST = 'http://up.qiniu.com';
$QINIU_RS_HOST = 'http://rs.qiniu.com'; //'http://rs.qbox.me';
$QINIU_RSF_HOST = 'http://rsf.qbox.me';

//$QINIU_ACCESS_KEY = '<Please apply your access key>';
//$QINIU_SECRET_KEY = '<Dont send your secret key to anyone>';

class Qiniu_Utils {

    protected $QINIU_ACCESS_KEY;
    protected $QINIU_SECRET_KEY;
    static protected $KeyConfig = array(
        'joymetest' => array('key' => 'MMuzPJz8oQrz197-KjYfPy-00s8C1qwNBtbiX7bA', 'secret' => 'ftEHE9bTV1h_wedrLYpgZaxHaJ6Np3O1hoba0OfP'),
        'joymeapp' => array('key' => 'G8_5kjfXfaufU53Da4bnGQ3YP-dhdmqct9sR6ImI', 'secret' => 'KXwyeZMxYnsZMqAwojI_IEDkYj69zkwvu8jZP5_a'),
        'joymepic' => array('key' => 'G8_5kjfXfaufU53Da4bnGQ3YP-dhdmqct9sR6ImI', 'secret' => 'KXwyeZMxYnsZMqAwojI_IEDkYj69zkwvu8jZP5_a'),
        'joymesy' => array('key' => 'G8_5kjfXfaufU53Da4bnGQ3YP-dhdmqct9sR6ImI', 'secret' => 'KXwyeZMxYnsZMqAwojI_IEDkYj69zkwvu8jZP5_a'),
        'joymeaudio' => array('key' => 'G8_5kjfXfaufU53Da4bnGQ3YP-dhdmqct9sR6ImI', 'secret' => 'KXwyeZMxYnsZMqAwojI_IEDkYj69zkwvu8jZP5_a'),
        'jyaudio' => array('key' => 'G8_5kjfXfaufU53Da4bnGQ3YP-dhdmqct9sR6ImI', 'secret' => 'KXwyeZMxYnsZMqAwojI_IEDkYj69zkwvu8jZP5_a'),
        'static' => array('key' => 'G8_5kjfXfaufU53Da4bnGQ3YP-dhdmqct9sR6ImI', 'secret' => 'KXwyeZMxYnsZMqAwojI_IEDkYj69zkwvu8jZP5_a'),
        'staticalpha' => array('key' => 'G8_5kjfXfaufU53Da4bnGQ3YP-dhdmqct9sR6ImI', 'secret' => 'KXwyeZMxYnsZMqAwojI_IEDkYj69zkwvu8jZP5_a'),
        'staticbeta' => array('key' => 'G8_5kjfXfaufU53Da4bnGQ3YP-dhdmqct9sR6ImI', 'secret' => 'KXwyeZMxYnsZMqAwojI_IEDkYj69zkwvu8jZP5_a'),
		'joymevideo' => array('key' => 'G8_5kjfXfaufU53Da4bnGQ3YP-dhdmqct9sR6ImI', 'secret' => 'KXwyeZMxYnsZMqAwojI_IEDkYj69zkwvu8jZP5_a'),
    );

    static public function Qiniu_SetKeys($accessKey, $secretKey) {
//        self::$QINIU_ACCESS_KEY = $accessKey;
//        self::$QINIU_SECRET_KEY = $secretKey;
    }

    static public function Get_KEY_BUCKET($bucket) {
        //$bucket:$key  为覆盖上传
        $dot = strpos($bucket, ":");
        if ($dot > 0) {
            $bucket = substr($bucket, 0, $dot);
        }
        if (isset(self::$KeyConfig[$bucket])) {
            return self::$KeyConfig[$bucket];
        }
        return null;
    }

    static public function Qiniu_Decode($str) {
        $find = array('-', '_');
        $replace = array('+', '/');
        return base64_decode(str_replace($find, $replace, $str));
    }

    static public function Qiniu_Put($upToken, $key, $body, $putExtra, $replace = false) { // => ($putRet, $err)
        global $QINIU_UP_HOST;

        if ($replace = true) {
            // $key = buget: key
        }

        if ($putExtra === null) {
            $putExtra = new Qiniu_PutExtra;
        }

        $fields = array('token' => $upToken);
        if ($key === null) {
            $fname = '?';
        } else {
            $fname = $key;
            $fields['key'] = $key;
        }
        if ($putExtra->CheckCrc) {
            $fields['crc32'] = $putExtra->Crc32;
        }
        if ($putExtra->Params) {
            foreach ($putExtra->Params as $k => $v) {
                $fields[$k] = $v;
            }
        }

        $files = array(array('file', $fname, $body, $putExtra->MimeType));

        $client = new Qiniu_HttpClient;
        return Qiniu_Client_CallWithMultipartForm($client, $QINIU_UP_HOST, $fields, $files);
    }

    static public function createFile($filename, $mime) {
        // PHP 5.5 introduced a CurlFile object that deprecates the old @filename syntax
        // See: https://wiki.php.net/rfc/curl-file-upload
        if (function_exists('curl_file_create')) {
            return curl_file_create($filename, $mime);
        }

        // Use the old style if using an older version of PHP
        $value = "@{$filename}";
        if (!empty($mime)) {
            $value .= ';type=' . $mime;
        }
        return $value;
    }

    static public function Qiniu_CopyFile($bucketSrc, $keySrc, $bucketDest, $keyDest) {
        //判断是否合法bucket
        $qinniuKeySrc = Qiniu_Utils::Get_KEY_BUCKET($bucketSrc);
        if ($qinniuKeySrc == null) {
            return null;
        }
        $qinniuKeyDest = Qiniu_Utils::Get_KEY_BUCKET($bucketDest);
        if ($qinniuKeyDest == null) {
            return null;
        }
        $mac = new Qiniu_Mac($qinniuKeyDest['key'], $qinniuKeyDest['secret']);

        $client = new Qiniu_MacHttpClient($mac);

        $uri = self::Qiniu_RS_URICopy($bucketSrc, $keySrc, $bucketDest, $keyDest);
        global $QINIU_RS_HOST;

        return http::Qiniu_Client_CallNoRet($client, $QINIU_RS_HOST . $uri);
    }

    static public function Qiniu_MoveFile($bucketSrc, $keySrc, $bucketDest, $keyDest) {

        //判断是否合法bucket
        $qinniuKeySrc = Qiniu_Utils::Get_KEY_BUCKET($bucketSrc);
        if ($qinniuKeySrc == null) {
            return null;
        }
        $qinniuKeyDest = Qiniu_Utils::Get_KEY_BUCKET($bucketDest);
        if ($qinniuKeyDest == null) {
            return null;
        }
        $mac = new Qiniu_Mac($qinniuKeyDest['key'], $qinniuKeyDest['secret']);

        $client = new Qiniu_MacHttpClient($mac);

        $uri = self::Qiniu_RS_URIMove($bucketSrc, $keySrc, $bucketDest, $keyDest);
        global $QINIU_RS_HOST;

        return http::Qiniu_Client_CallNoRet($client, $QINIU_RS_HOST . $uri);
    }

    static public function Qiniu_DeleteFile($bucket, $key) { // => ($delRet, $err)
        //判断是否合法bucket
        $qinniuKey = Qiniu_Utils::Get_KEY_BUCKET($bucket);
        if ($qinniuKey == null) {
            return null;
        }
        global $QINIU_RS_HOST;
        $mac = new Qiniu_Mac($qinniuKey['key'], $qinniuKey['secret']);
        $client = new Qiniu_MacHttpClient($mac);
        $uri = self::Qiniu_RS_URIDelete($bucket, $key);
        return http::Qiniu_Client_CallNoRet($client, $QINIU_RS_HOST . $uri);
    }

    static public function Qiniu_SaveFile($bucket, $key, $localFile, $replace = false, $putExtra = null) { // => ($putRet, $err)
        if ($replace == true) {
            $putPolicy = new Qiniu_RS_PutPolicy($bucket . ":" . $key);
        } else {
            $putPolicy = new Qiniu_RS_PutPolicy($bucket);
        }
        $upToken = $putPolicy->Token(null);
        if ($putExtra == null) {
            $putExtra = new Qiniu_PutExtra();
            $putExtra->Crc32 = 1;
        }
        return self::Qiniu_PutFile($upToken, $key, $localFile, $putExtra);
    }

    static public function Qiniu_PutFile($upToken, $key, $localFile, $putExtra) { // => ($putRet, $err)
        global $QINIU_UP_HOST;

        if ($putExtra === null) {
            $putExtra = new Qiniu_PutExtra;
        }

        $fields = array('token' => $upToken, 'file' => self::createFile($localFile, $putExtra->MimeType));
        if ($key === null) {
            $fname = '?';
        } else {
            $fname = $key;
            $fields['key'] = $key;
        }
        if ($putExtra->CheckCrc) {
            if ($putExtra->CheckCrc === 1) {
                $hash = hash_file('crc32b', $localFile);
                $array = unpack('N', pack('H*', $hash));
                $putExtra->Crc32 = $array[1];
            }
            $fields['crc32'] = sprintf('%u', $putExtra->Crc32);
        }
        if ($putExtra->Params) {
            foreach ($putExtra->Params as $k => $v) {
                $fields[$k] = $v;
            }
        }

        $client = new Qiniu_HttpClient;
        return http::Qiniu_Client_CallWithForm($client, $QINIU_UP_HOST, $fields, 'multipart/form-data');
    }

    static public function Qiniu_RS_MakeBaseUrl($domain, $key) { // => $baseUrl
        $keyEsc = str_replace("%2F", "/", rawurlencode($key));
        return "http://$domain/$keyEsc";
    }

    static public function Qiniu_RS_URIStat($bucket, $key) {
        return '/stat/' . self::Qiniu_Encode("$bucket:$key");
    }

    static public function Qiniu_RS_URIDelete($bucket, $key) {
        return '/delete/' . self::Qiniu_Encode("$bucket:$key");
    }

    static public function Qiniu_RS_URICopy($bucketSrc, $keySrc, $bucketDest, $keyDest) {
        return '/copy/' . self::Qiniu_Encode("$bucketSrc:$keySrc") . '/' . self::Qiniu_Encode("$bucketDest:$keyDest");
    }

    static public function Qiniu_RS_URIMove($bucketSrc, $keySrc, $bucketDest, $keyDest) {
        return '/move/' . self::Qiniu_Encode("$bucketSrc:$keySrc") . '/' . self::Qiniu_Encode("$bucketDest:$keyDest");
    }

    static public function Qiniu_Encode($str) { // URLSafeBase64Encode
        $find = array('+', '/');
        $replace = array('-', '_');
        return str_replace($find, $replace, base64_encode($str));
    }
    
    static public function Qiniu_SignRequest2($urlString, $body,$bucketSrc='static', $contentType = null)
    {
    	
    	$qnkey = self::Get_KEY_BUCKET($bucketSrc);
    	
    	$accessKey = $qnkey['key'];
    	$secretKey = $qnkey['secret'];
    	
    	$url = parse_url($urlString);
    	$data = '';
    	if (array_key_exists('path', $url)) {
            $data = $url['path'];
        }
        if (array_key_exists('query', $url)) {
            $data .= '?' . $url['query'];
        }
    	$data .= "\n";
    
    	if ($body != null && $contentType == 'application/x-www-form-urlencoded') {
    		$data .= $body;
    	}
    	$hmac = hash_hmac('sha1', $data, $secretKey, true);
    	$token =  $accessKey . ':' . self::Qiniu_Encode($hmac);
    	return $token;
    }
    
    static public function Qiniu_Refresh($urls,$bucketSrc){
    	
    	$url = 'http://fusion.qiniuapi.com/v2/tune/refresh';
    	
    	$data = json_encode(array('urls'=>$urls));

    	$accessToken = self::Qiniu_SignRequest2($url,'',$bucketSrc);
    	
    	$ch = curl_init(); //初始化curl
    	curl_setopt($ch, CURLOPT_URL, $url);//设置链接
    	
    	$header = array ();
    	$header [] = 'Authorization: ' . "QBox $accessToken";
    	$header [] = 'Content-Type: application/json';
    	
    	$options = array(
			CURLOPT_RETURNTRANSFER => true,
			CURLOPT_SSL_VERIFYPEER => false,
			CURLOPT_SSL_VERIFYHOST => false,
			CURLOPT_NOBODY => false,
			CURLOPT_CUSTOMREQUEST  => 'POST',
			CURLOPT_URL => $url
		);
		$options[CURLOPT_HTTPHEADER] = $header;
		$options[CURLOPT_POSTFIELDS] = $data;
		curl_setopt_array($ch, $options);
    	$response = curl_exec($ch);//接收返回信息
    	curl_close($ch); //关闭curl链接
    	$rs = json_decode($response,true);
    	return $rs;
    }
	
	static public function Qiniu_UploadToken($bucket, $sets = array()){
        $putPolicy = new Qiniu_RS_PutPolicy($bucket);
		if($sets){
			foreach($sets as $key=>$val){
				$putPolicy->$key = $val;
			}
		}
        $upToken = $putPolicy->Token(null);
		return $upToken;
	}
}
