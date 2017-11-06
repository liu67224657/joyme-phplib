<?php

namespace Joyme\qiniu;

use Joyme\qiniu\http\Qiniu_Request;
use Joyme\qiniu\http\Qiniu_Error;
use Joyme\qiniu\http\Qiniu_Response;

// class Qiniu_Header
class http {

    static public function Qiniu_Header_Get($header, $key) { // => $val
        $val = @$header[$key];
        if (isset($val)) {
            if (is_array($val)) {
                return $val[0];
            }
            return $val;
        } else {
            return '';
        }
    }

    static public function Qiniu_ResponseError($resp) { // => $error
        $header = $resp->Header;
        $details = self::Qiniu_Header_Get($header, 'X-Log');
        $reqId = self::Qiniu_Header_Get($header, 'X-Reqid');
        $err = new Qiniu_Error($resp->StatusCode, null);

        if ($err->Code > 299) {
            if ($resp->ContentLength !== 0) {
                if (self::Qiniu_Header_Get($header, 'Content-Type') === 'application/json') {
                    $ret = json_decode($resp->Body, true);
                    $err->Err = $ret['error'];
                }
            }
        }
        $err->Reqid = $reqId;
        $err->Details = $details;
        return $err;
    }

// --------------------------------------------------------------------------------
// class Qiniu_Client

    static public function Qiniu_Client_incBody($req) { // => $incbody
        $body = $req->Body;
        if (!isset($body)) {
            return false;
        }

        $ct = self::Qiniu_Header_Get($req->Header, 'Content-Type');
        if ($ct === 'application/x-www-form-urlencoded') {
            return true;
        }
        return false;
    }

    static public function Qiniu_Client_do($req) { // => ($resp, $error)
        $ch = curl_init();
        $url = $req->URL;
        $options = array(
            CURLOPT_USERAGENT => $req->UA,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_SSL_VERIFYPEER => false,
            CURLOPT_SSL_VERIFYHOST => false,
            CURLOPT_HEADER => true,
            CURLOPT_NOBODY => false,
            CURLOPT_CUSTOMREQUEST => 'POST',
            CURLOPT_URL => $url['path']
        );
        $httpHeader = $req->Header;
        if (!empty($httpHeader)) {
            $header = array();
            foreach ($httpHeader as $key => $parsedUrlValue) {
                $header[] = "$key: $parsedUrlValue";
            }
            $options[CURLOPT_HTTPHEADER] = $header;
        }
        $body = $req->Body;
        if (!empty($body)) {
            $options[CURLOPT_POSTFIELDS] = $body;
        } else {
            $options[CURLOPT_POSTFIELDS] = "";
        }
        curl_setopt_array($ch, $options);
        $result = curl_exec($ch);
        $ret = curl_errno($ch);
        if ($ret !== 0) {
            $err = new Qiniu_Error(0, curl_error($ch));
            curl_close($ch);
            return array(null, $err);
        }
        $code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        $contentType = curl_getinfo($ch, CURLINFO_CONTENT_TYPE);
        curl_close($ch);

        $responseArray = explode("\r\n\r\n", $result);
        $responseArraySize = sizeof($responseArray);
        $respHeader = $responseArray[$responseArraySize - 2];
        $respBody = $responseArray[$responseArraySize - 1];

        list($reqid, $xLog) = self::getReqInfo($respHeader);

        $resp = new Qiniu_Response($code, $respBody);
        $resp->Header['Content-Type'] = $contentType;
        $resp->Header["X-Reqid"] = $reqid;
        return array($resp, null);
    }

    static public function getReqInfo($headerContent) {
        $headers = explode("\r\n", $headerContent);
        $reqid = null;
        $xLog = null;
        foreach ($headers as $header) {
            $header = trim($header);
            if (strpos($header, 'X-Reqid') !== false) {
                list($k, $v) = explode(':', $header);
                $reqid = trim($v);
            } elseif (strpos($header, 'X-Log') !== false) {
                list($k, $v) = explode(':', $header);
                $xLog = trim($v);
            }
        }
        return array($reqid, $xLog);
    }

// --------------------------------------------------------------------------------
    static public function Qiniu_Client_ret($resp) { // => ($data, $error)
        $code = $resp->StatusCode;
        $data = null;
        if ($code >= 200 && $code <= 299) {
            if ($resp->ContentLength !== 0) {
                $data = json_decode($resp->Body, true);
                if ($data === null) {
                    $err_msg = function_exists('json_last_error_msg') ? json_last_error_msg() : "error with content:" . $resp->Body;
                    $err = new Qiniu_Error(0, $err_msg);
                    return array(null, $err);
                }
            }
            if ($code === 200) {
                return array($data, null);
            }
        }
        return array($data, self::Qiniu_ResponseError($resp));
    }

    static public function Qiniu_Client_Call($self, $url) { // => ($data, $error)
        $u = array('path' => $url);
        $req = new Qiniu_Request($u, null);
        list($resp, $err) = $self->RoundTrip($req);
        if ($err !== null) {
            return array(null, $err);
        }
        return self::Qiniu_Client_ret($resp);
    }

    static public function Qiniu_Client_CallNoRet($self, $url) { // => $error
        $u = array('path' => $url);
        $req = new Qiniu_Request($u, null);
        list($resp, $err) = $self->RoundTrip($req);
        if ($err !== null) {
            return array(null, $err);
        }
        if ($resp->StatusCode === 200) {
            return null;
        }
        return self::Qiniu_ResponseError($resp);
    }

    static public function Qiniu_Client_CallWithForm(
    $self, $url, $params, $contentType = 'application/x-www-form-urlencoded') { // => ($data, $error)
        $u = array('path' => $url);
        if ($contentType === 'application/x-www-form-urlencoded') {
            if (is_array($params)) {
                $params = http_build_query($params);
            }
        }
        $req = new Qiniu_Request($u, $params);
        if ($contentType !== 'multipart/form-data') {
            $req->Header['Content-Type'] = $contentType;
        }
        if (isset($params['token'])) {
            $req->Header['Authorization'] = "QBox " . $params['token'];
        }
        list($resp, $err) = $self->RoundTrip($req);
        if ($err !== null) {
            return array(null, $err);
        }
        return self::Qiniu_Client_ret($resp);
    }

// --------------------------------------------------------------------------------

    static public function Qiniu_Client_CallWithMultipartForm($self, $url, $fields, $files) {
        list($contentType, $body) = Qiniu_Build_MultipartForm($fields, $files);
        return self::Qiniu_Client_CallWithForm($self, $url, $body, $contentType);
    }

    static public function Qiniu_Build_MultipartForm($fields, $files) { // => ($contentType, $body)
        $data = array();
        $mimeBoundary = md5(microtime());

        foreach ($fields as $name => $val) {
            array_push($data, '--' . $mimeBoundary);
            array_push($data, "Content-Disposition: form-data; name=\"$name\"");
            array_push($data, '');
            array_push($data, $val);
        }

        foreach ($files as $file) {
            array_push($data, '--' . $mimeBoundary);
            list($name, $fileName, $fileBody, $mimeType) = $file;
            $mimeType = empty($mimeType) ? 'application/octet-stream' : $mimeType;
            $fileName = Qiniu_escapeQuotes($fileName);
            array_push($data, "Content-Disposition: form-data; name=\"$name\"; filename=\"$fileName\"");
            array_push($data, "Content-Type: $mimeType");
            array_push($data, '');
            array_push($data, $fileBody);
        }

        array_push($data, '--' . $mimeBoundary . '--');
        array_push($data, '');

        $body = implode("\r\n", $data);
        $contentType = 'multipart/form-data; boundary=' . $mimeBoundary;
        return array($contentType, $body);
    }

    static public function Qiniu_UserAgent() {
        global $SDK_VER;
        $sdkInfo = "QiniuPHP/$SDK_VER";

        $systemInfo = php_uname("s");
        $machineInfo = php_uname("m");

        $envInfo = "($systemInfo/$machineInfo)";

        $phpVer = phpversion();

        $ua = "$sdkInfo $envInfo PHP/$phpVer";
        return $ua;
    }

    static public function Qiniu_escapeQuotes($str) {
        $find = array("\\", "\"");
        $replace = array("\\\\", "\\\"");
        return str_replace($find, $replace, $str);
    }

}

// --------------------------------------------------------------------------------

