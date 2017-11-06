<?php

/**
 * Description of JoymeComm
 * 
 * 
 * @author clarkzhao
 * @date 2015-05-08 10:14:49
 * @copyright joyme.com
 */
namespace Joyme\core;
use Joyme\core\Log;
use Exception as Exception;

class JoymeComm{
    private $e=null;
    public function setErrMessage($exc){
        $this->e = $exc;
        Log::error(get_called_class(),$exc->getMessage());
        return NULL;
    }    
    
    public function getErrMessage(){
        if($this->e!=null){
            return $this->e->getMessage();
        }
    }
    
}