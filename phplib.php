<?php
/**
 * PHPLIB公共库
 * @author 张鹏
 * @date 2015-4-24
 *
 */
ini_set('date.timezone', 'Asia/Chongqing');

//自动调用类
function lib_auto_load($class) {
    //判断是否是 Joyme\ 开始！
//    echo __FUNCTION__ . " " . $class . "\n";
    $namebase = substr($class, 0, 5);
    if ($namebase == 'Joyme') {
        $path = dirname(__FILE__);
        $class = str_replace('Joyme\\', '', $class);
        $names = explode('\\', $class);
        $filename = $path;
        foreach ($names as $i => $dir) {
            $filename.=DIRECTORY_SEPARATOR . $dir;
        }
        $filename.='.class.php';
//        echo " load class ".$filename."\n";
        if (is_file($filename))
            include_once $filename;
    } else {
        //echo "begin not with " . $namebase . "\n";
        return;
    }
}

use Joyme\core\Log;

function joyme_error_handler($errno, $errstr, $errfile, $errline) {
    if (!(error_reporting() & $errno)) {
        return;
    }
    switch ($errno) {
        case E_USER_ERROR:
            Log::error($errno, $errstr, $errfile, $errline);
            break;
        case E_USER_WARNING:
            Log::warning($errno, $errstr, $errfile, $errline);
            break;
        case E_WARNING:
            Log::warning($errno, $errstr, $errfile, $errline);
            break;
        case E_USER_NOTICE:
            Log::debug($errno, $errstr, $errfile, $errline);
            break;
        default:
            Log::error($errno, $errstr, $errfile, $errline);
            break;
    }
    return true;
}

spl_autoload_register('lib_auto_load');

set_error_handler('joyme_error_handler');

function getJoymeLibClass() {
    $dir = dirname(__FILE__);
    $files = array();
    getJoymeLibClassFiles($dir, $files);
    foreach ($files as $file) {
        echo $file . "\n";
    }
}

function getJoymeLibClassFiles($dir, &$result) {
    $handle = opendir($dir);
    if ($handle) {
        while (( $file = readdir($handle) ) !== false) {
            if ($file != '.' && $file != '..') {
                $cur_path = $dir . DIRECTORY_SEPARATOR . $file;
                if (is_dir($cur_path)) {
                    getJoymeLibClassFiles($cur_path, $result);
                } else {
                    if (substr($cur_path, -10) == '.class.php') {
                        $result[] = $cur_path;
                    }
                }
            }
        }
        closedir($handle);
    }
    return $result;
}

?>
