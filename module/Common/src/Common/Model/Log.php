<?php
namespace Common\Model;

use Zend\Log\Writer\FirePhp;
use Zend\Log\Logger;
use Zend\Log\Writer\Stream;
class Log
{
    private static $log; 
    
    protected static function instance(){
        if(self::$log == null){
            self::$log = new Logger();
        }
        return self::$log;
    }
    
    public static function firePhp($message){
        $log = self::instance();
        $writer = new FirePhp();
        $log->addWriter($writer);
        $log->info(printf($message));
    }
    
    public static function file($path,$fileName,$message){
        $log = self::instance();
        if (file_exists($path)) {
            if (substr($path, 0,-1) != '/' || substr($path, 0,-1) != '\\') {
            	$path = $path . '/';
            }
            if ($fileName == null) {
            	$fileName = date("Y-m-d",time()).'.txt';
            }
        	$writer = new Stream($path.$fileName);
            $log->addWriter($writer);
            $log->info(print_r($message,true));
        }
        $log = null;
    }
}