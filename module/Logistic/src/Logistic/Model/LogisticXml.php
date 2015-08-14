<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2015/7/28
 * Time: 10:19
 */

namespace Logistic\Model;


use Zend\Config\Reader\Xml;

class LogisticXml {
    private $readerXml;
    private $exception;

    /**
     * @return \Logistic\Exception\Xml
     */
    public function getException()
    {
        return $this->exception;
    }

    /**
     * @param \Logistic\Exception\Xml $exception
     */
    public function setException($exception)
    {
        $this->exception = $exception;
    }

    public function __construct(){
        $this->readerXml = new Xml();
        $this->exception = new \Logistic\Exception\Xml();
    }

    /**
     * @param $stringXml
     * @return string|null
     */
    public function getTid($stringXml){
        $arrayXml = $this->readerXml->fromString($stringXml);
        if(isset($arrayXml['trade_orders_response']['trade']['tid']) && strlen($arrayXml['trade_orders_response']['trade']['tid'])>0){
            return $arrayXml['trade_orders_response']['trade']['tid'];
        } else {
            if(isset($arrayXml['ERROR']))
                $this->exception->setMessage($arrayXml['ERROR'])->setCode(10001);
            else
                $this->exception->setMessage('其他错误')->setCode(10001);
            return null;
        }
    }

    /**
     * @param $stringXml
     * @return string|null
     */
    public function getWldh($stringXml){
        $arrayXml = $this->readerXml->fromString($stringXml);
        if(isset($arrayXml['sendorders']['sendorder']['wldh']) && strlen($arrayXml['sendorders']['sendorder']['wldh'])>0){
            return $arrayXml['sendorders']['sendorder']['wldh'];
        } else {
            if(isset($arrayXml['ERROR']))
                $this->exception->setMessage($arrayXml['ERROR'])->setCode(10001);
            else
                $this->exception->setMessage('其他错误')->setCode(10001);
            return null;
        }
    }
}