<?php
namespace Timer\Model;

use Zend\ServiceManager\ServiceManager;
use Zend\Json\Json;
use Zend\Config\Reader\Xml;

abstract class Transport
{
    protected $json;
    protected $serviceManager;
    
    public function __construct(ServiceManager $serviceManager){
    	$this->serviceManager = $serviceManager;
    	$this->json = new Json();
    }
    
    public function analysisOfData($resultData){
    	if (strpos($resultData, "<?xml") !== false) {
    		$xml = new Xml();
    		$resultData = $xml->fromString($resultData);
    	} else {
    		$json = new Json();
    		$resultData = $json->decode($resultData);
    	}
    	if (is_array($resultData)) {
    		return isset($resultData['errorCode'])? $resultData['errorCode']: null;
    	}
    	return isset($resultData->errorCode) ? $resultData->errorCode : 1;
    }
    
    
    /**
     * @param string $url
     * @param string $data //是json数据
     * @return mixed
     */
    protected function excuteService($url,$data){
    	$ch = curl_init($url);
    	curl_setopt($ch,CURLOPT_URL,$url);
    	curl_setopt($ch,CURLOPT_POST,1);
    	curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    	curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: application/json'));
    	curl_setopt($ch,CURLOPT_POSTFIELDS,$data);
    	$result = curl_exec($ch);
    	curl_close($ch);
    	return $result;
    }
    
    abstract public function pretreatment();
}