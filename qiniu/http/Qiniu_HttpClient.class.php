<?php

/**
 * Description of Qiniu_HttpClient
 * 
 * 
 * @author clarkzhao
 * @date 2015-04-28 06:07:40
 * @copyright joyme.com
 */

namespace Joyme\qiniu\http;
use Joyme\qiniu\http;
class Qiniu_HttpClient {

    public function RoundTrip($req) { // => ($resp, $error)
        return http::Qiniu_Client_do($req);
    }

}
