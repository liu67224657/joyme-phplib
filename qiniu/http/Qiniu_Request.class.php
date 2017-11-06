<?php

/**
 * Description of Qiniu_Request
 * 
 * 
 * @author clarkzhao
 * @date 2015-04-28 05:52:27
 * @copyright joyme.com
 */
namespace Joyme\qiniu\http;
use Joyme\qiniu\http;

class Qiniu_Request
{
	public $URL;
	public $Header;
	public $Body;
	public $UA;

	public function __construct($url, $body)
	{
		$this->URL = $url;
		$this->Header = array();
		$this->Body = $body;
		$this->UA =   http::Qiniu_UserAgent();
	}
}
