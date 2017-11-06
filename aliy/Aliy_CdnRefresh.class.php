<?php
/**
 * Description of Aliy_CdnRefresh
 *
 *
 * @Author gradydong
 * @Copyright joyme.com
 */
namespace Joyme\aliy;

class Aliy_CdnRefresh
{

    protected $accessKeyId = 'm2LJu94lrAKPMGBm';
    protected $accessKeySecret = 'jO3aBvvxQKfBoBEHXadiLhG0YFi8OJ';
    protected $serverUrl = "https://cdn.aliyuncs.com/"; // 服务端地址：cdn服务的域名是http://cdn.aliyuncs.com/和https://cdn.aliyuncs.com/
    protected $format = "json";
    protected $connectTimeout = 3000;//3秒
    protected $readTimeout = 80000;//80秒
    protected $signatureMethod = "HMAC-SHA1";
    protected $signatureVersion = "1.0";
    protected $dateTimeFormat = 'Y-m-d\TH:i:s\Z'; // ISO8601规范
    protected $sdkVersion = "1.0";

    /**
     *  执行cdn刷新
     *
     *  @param string $objectPath 需要同步的文件，可以是文件路径，也可以是目录路径
     *  @param string $objectType 刷新的类型， 其值可以为File | Direcotry，默认是File。
     */
    public function execute($objectPath,$objectType='File')
    {
        if(!$objectPath){
            return false;
        }
        //获取业务参数
        $apiParams = array();
        //组装系统参数
        $apiParams["ObjectPath"] = $objectPath;
        $apiParams["ObjectType"] = $objectType;
        $apiParams["AccessKeyId"] = $this->accessKeyId;
        $apiParams["Format"] = $this->format;//
        $apiParams["SignatureMethod"] = $this->signatureMethod;
        $apiParams["SignatureVersion"] = $this->signatureVersion;
        $apiParams["SignatureNonce"] = uniqid();
        date_default_timezone_set("GMT");
        $apiParams["Timestamp"] = date($this->dateTimeFormat);
        $apiParams["Action"] = 'RefreshObjectCaches';
        $apiParams["Version11"] = '2014-11-11';
        //签名
        $apiParams["Signature"] = $this->computeSignature($apiParams, $this->accessKeySecret);

        //系统参数放入GET请求串
        $requestUrl = rtrim($this->serverUrl, "/") . "/?";
        foreach ($apiParams as $apiParamKey => $apiParamValue) {
            $requestUrl .= "$apiParamKey=" . urlencode($apiParamValue) . "&";
        }
        $requestUrl = substr($requestUrl, 0, -1);

        //发起HTTP请求
        $resp = $this->curl($requestUrl);

        //解析API返回结果
        if ("json" == $this->format) {
            $respObject = json_decode($resp);
            if(isset($respObject->Code)){
                return false;
            }else{
                return true;
            }
        } else if ("xml" == $this->format) {
            return @simplexml_load_string($resp);
        }else{
            return false;
        }
    }

    protected function percentEncode($str)
    {
        // 使用urlencode编码后，将"+","*","%7E"做替换即满足 API规定的编码规范
        $res = urlencode($str);
        $res = preg_replace('/\+/', '%20', $res);
        $res = preg_replace('/\*/', '%2A', $res);
        $res = preg_replace('/%7E/', '~', $res);
        return $res;
    }

    protected function computeSignature($parameters, $accessKeySecret)
    {
        // 将参数Key按字典顺序排序
        ksort($parameters);

        // 生成规范化请求字符串
        $canonicalizedQueryString = '';
        foreach ($parameters as $key => $value) {
            $canonicalizedQueryString .= '&' . $this->percentEncode($key)
                . '=' . $this->percentEncode($value);
        }

        // 生成用于计算签名的字符串 stringToSign
        $stringToSign = 'GET&%2F&' . $this->percentencode(substr($canonicalizedQueryString, 1));

        // 计算签名，注意accessKeySecret后面要加上字符'&'
        $signature = base64_encode(hash_hmac('sha1', $stringToSign, $accessKeySecret . '&', true));
        return $signature;
    }

    public function curl($url)
    {
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_FAILONERROR, false);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        if ($this->readTimeout) {
            curl_setopt($ch, CURLOPT_TIMEOUT, $this->readTimeout);
        }
        if ($this->connectTimeout) {
            curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, $this->connectTimeout);
        }
        //https 请求
        if (strlen($url) > 5 && strtolower(substr($url, 0, 5)) == "https") {
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
            curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
        }

        $reponse = curl_exec($ch);

        if (curl_errno($ch)) {
            return curl_errno($ch);
        }
        curl_close($ch);
        return $reponse;
    }

}
