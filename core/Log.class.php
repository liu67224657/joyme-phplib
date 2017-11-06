<?php

/**
 * Description of Log
 * 
 * 
 * @author clarkzhao
 * @date 2015-04-28 03:03:53
 * @copyright joyme.com
 */

namespace Joyme\core;

use Joyme\core\Singleton;

class Log extends Singleton {

    // log levels
    const NONE = 0;
    const ERROR = 100;
    const WARN = 200;
    const INFO = 300;
    const DEBUG = 400;
    const VERBOSE = 500;
    const ALL = 600;
    // config option names
    const LOG_LEVEL_CONFIG_OPTION = 'log_level';
    const LOG_WRITERS_CONFIG_OPTION = 'log_writers';
    const LOGGER_FILE_PATH_CONFIG_OPTION = 'logger_file_path';
    const STRING_MESSAGE_FORMAT_OPTION = 'string_message_format';

    /**
     * The backtrace string to use when testing.
     *
     * @var string
     */
    public static $debugBacktraceForTests;
    private $level;
    private $logfile;

    /**
     * @param LoggerInterface $logger
     */
    public function __construct($logLevel = self::ALL) {
        $this->level = $logLevel;
        $this->logfile = null;
    }

    /**
     * Logs a message using the ERROR log level.
     *
     * @param string $message The log message. This can be a sprintf format string.
     * @param ... mixed Optional sprintf params.
     * @api
     *
     */
    public static function error($message /* ... */) {
        self::logMessage(static::ERROR, $message, array_slice(func_get_args(), 1));
    }

    /**
     * Logs a message using the WARNING log level.
     *
     * @param string $message The log message. This can be a sprintf format string.
     * @param ... mixed Optional sprintf params.
     * @api
     *
     */
    public static function warning($message /* ... */) {
        self::logMessage(self::WARN, $message, array_slice(func_get_args(), 1));
    }

    /**
     * Logs a message using the INFO log level.
     *
     * @param string $message The log message. This can be a sprintf format string.
     * @param ... mixed Optional sprintf params.
     * @api
     *
     */
    public static function info($message /* ... */) {
        self::logMessage(self::INFO, $message, array_slice(func_get_args(), 1));
    }

    /**
     * Logs a message using the DEBUG log level.
     *
     * @param string $message The log message. This can be a sprintf format string.
     * @param ... mixed Optional sprintf params.
     * @api
     *
     */
    public static function debug($message /* ... */) {
        self::logMessage(self::DEBUG, $message, array_slice(func_get_args(), 1));
    }

    /**
     * Logs a message using the VERBOSE log level.
     *
     * @param string $message The log message. This can be a sprintf format string.
     * @param ... mixed Optional sprintf params.
     * @api
     *
     */
    public static function verbose($message /* ... */) {
        self::logMessage(self::VERBOSE, $message, array_slice(func_get_args(), 1));
    }

    /**
     * @param string $logFile
     */
    private function setLogFile($logFile) {
        $this->logfile = $logFile;
    }

    /**
     * @param int $logLevel
     */
    private function setLogLevel($logLevel) {
        $this->level = $logLevel;
    }

    /**
     */
    private function getLogLevel() {
        return $this->level;
    }

    public function getLevelName($logLevel) {
        switch ($logLevel) {
            case self::NONE:
                $name = "none";
                break;
            case self::ERROR:
                $name = "error";
                break;
            case self::WARN:
                $name = "warn";
                break;
            case self::INFO:
                $name = "info";
                break;
            case self::DEBUG:
                $name = "debug";
                break;
            case self::VERBOSE:
                $name = "verbose";
        }

        return $name;
    }

    private function doLog($logLevel, $message, $parameters = array()) {
        // To ensure the compatibility with PSR-3, the message must be a string
        if ($message instanceof \Exception) {
            $parameters['exception'] = $message;
            $message = $message->getMessage();
        }
        if (!(is_string($message) || is_numeric($message))) {
            $message = var_export($message, true);
        }
        if ($logLevel > $this->level) {
            //不做记录
            return;
        }
        $levelName = $this->getLevelName($logLevel);
        $string = @date('H:i:s') . " " . $levelName;
        $string.=" " . $message;
        foreach ($parameters as $parameter) {
            if (!is_string($parameter)) {
                $parameter = var_export($parameter, true);
            }
            if(is_bool($parameter)){
                $parameter = $parameter ? 'true' :'false' ;
            }
            $string.=" " . $parameter;
        }
        if ($this->logfile == null) {
            if (DIRECTORY_SEPARATOR == '/') {
//                $path = '/opt/servicelogs/phplog';
                $path = '/opt/servicelogs/php';
            } else {
                $path = 'D:' . DIRECTORY_SEPARATOR . 'log';
            }
            is_dir($path) || mkdir($path, 0755, true);
            $file = $path . DIRECTORY_SEPARATOR . 'joymephplib_' . date('Ymd') . '.log';
            
        } else {
            $file = $this->logfile;
        }
        $string.="\n";
        error_log($string, 3, $file);
    }

    private static function logMessage($level, $message, $parameters) {
        self::getInstance()->doLog($level, $message, $parameters);
    }

    /**
     * 设置日志级别
     *
     *
     */
    public static function config($level = self::ALL) {
        self::getInstance()->setLogLevel($level);
    }

    /**
     * 设置日志文件
     */
    public static function setfile($logFile) {
        self::getInstance()->setLogFile($logFile);
    }

}
