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
    const HOST = '121.199.173.254';
    const PORT = '30002';
    const SCHEME = 'http';
    const SHORT_PATH = '/xyhycgerp/data.dpk';
    const TOKEN = '0527CFFEA6504311B86858993CF0F1F1';

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
     * @param string $method
     * @param array $headers
     */
    public function send($jsonData,$methodName,$method = 'POST', $headers = null){
        $this->setUrl($methodName);
//         print_r($this->uri->toString());exit;
        $this->client->setUri($this->uri->toString());
        $this->client->setMethod($method);
        if($headers !== null) $this->client->setHeaders($headers); // $headers = array('Content-Type'=>'application/json')
        $this->client->setRawBody($jsonData);
        $response = $this->client->send();
//         print_r($response->getBody());exit;
        return $this->analysisOfData($response);
    }

    /**
     * @param array $param
     * @param string $methodName
     * @param string $method
     * @param null $headers
     * @return \Zend\Http\Response
     */
    public function sendParam($param,$methodName,$method = 'POST', $headers = null){
        $this->setUrl($methodName,$param);
        $this->client->setUri($this->uri->toString());
        $this->client->setMethod($method);
        if($headers !== null) $this->client->setHeaders($headers); // $headers = array('Content-Type'=>'application/json')
//        if($method == 'POST'){
//            $this->client->setParameterPost($param);
//        } else {
//            $this->client->setParameterGet($param);
//        }
        if($method == 'POST'){
            $this->client->setParameterPost($param);
        } else {
            $this->client->setParameterGet($param);
        }
        $response = $this->client->send();
        return $response;
    }


    /**
     * @param array $param
     * @param string $methodName
     * @param string $method
     * @param null $headers
     * @return \Zend\Http\Response
     */
    public function sendParamReceive($param,$methodName,$method = 'POST', $headers = null){
        $queryParam = array('method'=>$methodName);

        //$str_md5 这个是MD5过后的字符

        $str_md5 = strtoupper(md5($param['condition'].self::TOKEN));
        $int_len = strlen($str_md5)/2;
        $str_sign = '';
        for($i=0;$i<$int_len;$i++) {
            $str_sign .= chr(hexdec(substr($str_md5,$i*2,2)));
        }

        $str_sign=base64_encode($str_sign);
        //$str_sign = 'nzs8W71a3cOlGQq50qcVkQ==';
        $str_url = '&page_size=20&page_no=1&sign='.urlencode($str_sign).'&timeSpan='.time();

        $this->uri  ->setHost(self::HOST)
            ->setScheme(self::SCHEME)
            ->setPort(self::PORT)
            ->setPath(self::SHORT_PATH)
            ->setQuery($queryParam);

        $this->client->setUri($this->uri->toString().$str_url);
        print_r($this->uri->toString().$str_url);
        $this->client->setMethod($method);
        if($headers !== null) $this->client->setHeaders($headers); // $headers = array('Content-Type'=>'application/json')

        if($method == 'POST'){
            $this->client->setParameterPost($param);
        } else {
            $this->client->setParameterGet($param);
        }
        print_r($this->client->getRequest()->getQuery());exit;
        $response = $this->client->send();
        return $response;
    }

    /**
     * http://113.105.67.138:8088/xyapi/api/service/wmc/order/addSmSalerOrder?
     * customerId=143631&token=uMbYz4Dtfhj7yeokoZaMAuUUMm+h+v/h/vgnjZHyWMC1&language=zh_CN
     * @param string $methodName
     */
    public function setUrl($methodName,$param){
//        $queryParam = array('method'=>$methodName,'appkey'=>self::TOKEN);
        $queryParam = array('method'=>$methodName);
        $queryParam['timeSpan']=time();
        print_r('['.json_encode($param).']'.self::TOKEN);
        $queryParam['sign']=base64_encode(md5('['.json_encode($param).']'.self::TOKEN));
        $this->uri  ->setHost(self::HOST)
                    ->setScheme(self::SCHEME)
                    ->setPort(self::PORT)
                    ->setPath(self::SHORT_PATH)
                    ->setQuery($queryParam)
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