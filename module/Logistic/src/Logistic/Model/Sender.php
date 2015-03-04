<?php
namespace Logistic\Model;

use Zend\Uri\Uri;
use Zend\Http\Client;
use Zend\Http\Client\Adapter\Curl;
use Zend\Http\Response;
use Zend\Config\Reader\Xml;
use Zend\Json\Json;

class Sender {
    private $uri;
    private $client;
    private $curl;
//     const HOST = '113.105.67.138';
//     const PORT = '8088';
    const HOST = '113.105.67.139';
    const PORT = '8088';
    const SCHEME = 'http';
    const SHORT_PATH = '/xyapi/api/service/wmc/order/';
    const TOKEN = 'uMbYz4Dtfhj7yeokoZaMAuUUMm+h+v/h/vgnjZHyWMC1';
    const CUSTOMERID = '143631';
    const LANGUAGE = 'zh_CN';
    
    public function __construct(){
        $this->uri = new Uri();
//         $this->curl = new Curl();   
        $this->client = new Client();
//         $this->client->setAdapter($this->curl);
    }
    
    /**
     * 这个curl设置
     * curl_setopt($ch,CURLOPT_URL,$url);
     * curl_setopt($ch,CURLOPT_POST,1);
     * curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
     * curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: application/json'));
     * curl_setopt($ch,CURLOPT_POSTFIELDS,$data);
     * @param string $jsonData
     * @param string $methodName
     */
    public function send($jsonData,$methodName){
        $this->setUrl($methodName);
//         print_r($this->uri->toString());exit;
        $this->client->setUri($this->uri->toString());
        $this->client->setMethod('POST');
        $this->client->setHeaders(array('Content-Type'=>'application/json'));
        $this->client->setRawBody($jsonData);
        $response = $this->client->send();
//         print_r($response->getBody());exit;
        return $this->analysisOfData($response);
    }
    
    /**
     * http://113.105.67.138:8088/xyapi/api/service/wmc/order/addSmSalerOrder?
     * customerId=143631&token=uMbYz4Dtfhj7yeokoZaMAuUUMm+h+v/h/vgnjZHyWMC1&language=zh_CN
     * @param string $methodName
     */
    public function setUrl($methodName){
        $this->uri  ->setHost(self::HOST)
                    ->setScheme(self::SCHEME)
                    ->setPort(self::PORT)
                    ->setPath(self::SHORT_PATH.$methodName)
                    ->setQuery(array('customerId'=>self::CUSTOMERID,'token'=>self::TOKEN,'language'=>self::LANGUAGE))
                    ;
        return $this;
    }
    
    /**
     * @param unknown $resultData
     * @return Ambigous <NULL, unknown>|number
     */
    public function analysisOfData(Response $response){
    	if (strpos($response->getBody(), "<?xml") !== false) {
    		$xml = new Xml();
    		$resultData = $xml->fromString($response->getBody());
    	} else {
    		$resultData = Json::decode($response->getBody());
    	}
    	if (is_array($resultData)) {
    		return isset($resultData['errorCode'])? $resultData['errorCode']: 2;
    	}
    	return isset($resultData->errorCode) ? $resultData->errorCode : 1;
    }
    
    
    /**
     * @return \Zend\Http\Client
     */
    public function getClient(){
        return $this->client;
    }
    
    
    
}