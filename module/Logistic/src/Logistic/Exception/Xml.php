<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2015/7/28
 * Time: 11:14
 */

namespace Logistic\Exception;


class Xml extends \Exception {

    /**
     * @param $message
     */
    public function setMessage($message){
        $this->message = $message;
        return $this;
    }

    /**
     * @param $code
     */
    public function setCode($code){
        $this->code = $code;
        return $this;
    }
}