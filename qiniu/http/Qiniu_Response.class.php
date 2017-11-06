<?php

/**
 * Description of Qiniu_Response
 * 
 * 
 * @author clarkzhao
 * @date 2015-04-28 05:52:15
 * @copyright joyme.com
 */

namespace Joyme\qiniu\http;

class Qiniu_Response
{
	public $StatusCode;
	public $Header;
	public $ContentLength;
	public $Body;

	public function __construct($code, $body)
	{
		$this->StatusCode = $code;
		$this->Header = array();
		$this->Body = $body;
		$this->ContentLength = strlen($body);
	}
}