<?php

/**
 * Description of a
 * 
 * 
 * @author clarkzhao
 * @date 2015-04-28 06:08:49
 * @copyright joyme.com
 */

namespace Joyme\qiniu\http;
use Joyme\qiniu\Qiniu_Mac;
use Joyme\qiniu\http as Qiniu_http;

class Qiniu_MacHttpClient {

    public $Mac;

    public function __construct($mac) {
        $this->Mac =  Qiniu_Mac::Qiniu_RequireMac($mac);
    }

    public function RoundTrip($req) { // => ($resp, $error)
        $incbody = Qiniu_http::Qiniu_Client_incBody($req);
        $token = $this->Mac->SignRequest($req, $incbody);
        $req->Header['Authorization'] = "QBox $token";
        return Qiniu_http::Qiniu_Client_do($req);
    }

}
