<?php

/**
 * Description of Qiniu_RS_PutPolicy
 * 
 * 
 * @author clarkzhao
 * @date 2015-04-28 05:20:21
 * @copyright joyme.com
 */
namespace Joyme\qiniu;

use Joyme\qiniu\Qiniu_Mac;
use Joyme\qiniu\Qiniu_Utils;
class Qiniu_RS_PutPolicy
{
	public $Scope;                  //必填
	public $Expires;                //默认为3600s
	public $CallbackUrl;
	public $CallbackBody;
	public $ReturnUrl;
	public $ReturnBody;
	public $AsyncOps;
	public $EndUser;
	public $InsertOnly;             //若非0，则任何情况下无法覆盖上传
	public $DetectMime;             //若非0，则服务端根据内容自动确定MimeType
	public $FsizeLimit;
	public $SaveKey;
	public $PersistentOps;
	public $PersistentPipeline;
	public $PersistentNotifyUrl;
	public $FopTimeout;
	public $MimeLimit;

	public function __construct($scope)
	{
		$this->Scope = $scope;
	}

	public function Token($mac) // => $token
	{
                //判断是否合法bucket
                $qinniuKey = Qiniu_Utils::Get_KEY_BUCKET($this->Scope );
                if($qinniuKey==null){
                    return null;
                }
            
		$deadline = $this->Expires;
		if ($deadline == 0) {
			$deadline = 3600;
		}
		$deadline += time();

		$policy = array('scope' => $this->Scope, 'deadline' => $deadline);
		if (!empty($this->CallbackUrl)) {
			$policy['callbackUrl'] = $this->CallbackUrl;
		}
		if (!empty($this->CallbackBody)) {
			$policy['callbackBody'] = $this->CallbackBody;
		}
		if (!empty($this->ReturnUrl)) {
			$policy['returnUrl'] = $this->ReturnUrl;
		}
		if (!empty($this->ReturnBody)) {
			$policy['returnBody'] = $this->ReturnBody;
		}
		if (!empty($this->AsyncOps)) {
			$policy['asyncOps'] = $this->AsyncOps;
		}
		if (!empty($this->EndUser)) {
			$policy['endUser'] = $this->EndUser;
		}
		if (!empty($this->InsertOnly)) {
			$policy['exclusive'] = $this->InsertOnly;
		}
		if (!empty($this->DetectMime)) {
			$policy['detectMime'] = $this->DetectMime;
		}
		if (!empty($this->FsizeLimit)) {
			$policy['fsizeLimit'] = $this->FsizeLimit;
		}
		if (!empty($this->SaveKey)) {
			$policy['saveKey'] = $this->SaveKey;
		}
		if (!empty($this->PersistentOps)) {
			$policy['persistentOps'] = $this->PersistentOps;
		}
		if (!empty($this->PersistentPipeline)) {
			$policy['persistentPipeline'] = $this->PersistentPipeline;
		}
		if (!empty($this->PersistentNotifyUrl)) {
			$policy['persistentNotifyUrl'] = $this->PersistentNotifyUrl;
		}
		if (!empty($this->FopTimeout)) {
			$policy['fopTimeout'] = $this->FopTimeout;
		}
		if (!empty($this->MimeLimit)) {
			$policy['mimeLimit'] = $this->MimeLimit;
		}


		$b = json_encode($policy);
                if($mac==null){
                    $mac = new Qiniu_Mac($qinniuKey['key'],$qinniuKey['secret']);
                }
                return $mac->SignWithData($b);
//		return Qiniu_Mac::Qiniu_SignWithData($mac, $b);
	}
}
