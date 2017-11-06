<?php

/**
 * Description of Qiniu_Error
 * 
 * 
 * @author clarkzhao
 * @date 2015-04-28 06:01:12
 * @copyright joyme.com
 */
namespace Joyme\qiniu\http;
class Qiniu_Error
{
	public $Err;	 // string
	public $Reqid;	 // string
	public $Details; // []string
	public $Code;	 // int

	public function __construct($code, $err)
	{
		$this->Code = $code;
		$this->Err = $err;
	}
}