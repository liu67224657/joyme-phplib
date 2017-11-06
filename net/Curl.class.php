<?php
/**
 * Description 封装CURL扩展
 *
 * @author    gradydong
 * @date      2016-04-18 09:51:19
 * @copyright joyme.com
 */

namespace Joyme\net;

use Exception as Exception;
use Joyme\core\JoymeComm;
use Joyme\core\Log;

class Curl
{

    private $ch_;       //curl 句柄
    private $body_;     //body_ 用于保存curl请求返回的结果
    const time_ = 60;    //超时限制时间
    private $gzip_ = false;     //判断是否添加gzip,默认false

    /**
     * @todo proxy
     * @构造函数，初始化CURL回话
     */
    public function Start($url)
    {
        $this->ch_ = curl_init($url);
        curl_setopt($this->ch_, CURLOPT_HEADER, 0);
        curl_setopt($this->ch_, CURLOPT_RETURNTRANSFER, 1);
    }

    /**
     * @GET请求
     */
    public function Get($url, array $params = array())
    {
        if ($params) {
            if (strpos($url, '?')) {
                $url .= "&" . http_build_query($params);
            } else {
                $url .= "?" . http_build_query($params);
            }
        }
        $this->Start($url);
        curl_setopt($this->ch_, CURLOPT_TIMEOUT, Curl::time_);
        if (strpos($url, 'https') === 0) {
            curl_setopt($this->ch_, CURLOPT_SSL_VERIFYHOST, 0);
            curl_setopt($this->ch_, CURLOPT_SSL_VERIFYPEER, 0);
        }
        if($this->gzip_){
            curl_setopt($this->ch_, CURLOPT_ENCODING, "gzip");
        }
        $this->body_ = $this->Execx();
        return $this->Close($this->body_);
    }

    /**
     * @POST请求
     */
    public function Post($url, array $params = array())
    {
        $this->Start($url);
        if (strpos($url, 'https') === 0) {
            curl_setopt($this->ch_, CURLOPT_SSL_VERIFYHOST, 0);
            curl_setopt($this->ch_, CURLOPT_SSL_VERIFYPEER, 0);
        }
        if($this->gzip_){
            curl_setopt($this->ch_, CURLOPT_ENCODING, "gzip");
        }
        curl_setopt($this->ch_, CURLOPT_HTTPHEADER, array("Content-Type: application/x-www-form-urlencoded"));
        curl_setopt($this->ch_, CURLOPT_POST, true);
        curl_setopt($this->ch_, CURLOPT_TIMEOUT, Curl::time_);
        if ($params) {
            curl_setopt($this->ch_, CURLOPT_POSTFIELDS, http_build_query($params));
        }
        $this->body_ = $this->Execx();
        return $this->Close($this->body_);
    }

    /**
     * 是否是否$gzip_ 编码
     * true 添加gzip
     * false
     */
    public function SetGzip($status)
    {
        $this->gzip_ = $status;
    }

    /**
     * @执行CURL会话
     */
    public function Execx()
    {
        return curl_exec($this->ch_);
    }

    /**
     * @关闭CURL句柄
     */
    public function Close($body_)
    {
        if ($body_ === false) {
            Log::debug(__CLASS__, "CURL Error: " . curl_error($this->ch_));
            return false;
        }
        curl_close($this->ch_);
        return $body_;
    }
}