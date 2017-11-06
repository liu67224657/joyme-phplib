<?php

/**
 * Description of testLog
 * 
 * 
 * @author clarkzhao
 * @date 2015-04-29 02:02:13
 * @copyright joyme.com
 */
require_once dirname(__FILE__). DIRECTORY_SEPARATOR.'..' . DIRECTORY_SEPARATOR . 'phplib.php';

use Joyme\core\Log;


$message = "just test";
//Log::warning($message,'__FILE__','test');

Log::config(Log::ERROR);

//Log::setfile('/tmp/test.log');

Log::verbose($message);
Log::debug(__FILE__);
Log::info($message);
Log::warning($message);
Log::error($message);

