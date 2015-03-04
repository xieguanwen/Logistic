<?php
namespace Timer\Model;

class API4PXPlatformFPXWebServiceProcess
{
    protected $_serviceUrl = "http://113.106.91.58:8889/xywl/api/service/wmc";
    protected $_serviceCustomerId = "143631";
    protected $_serviceToken = "uMbYz4Dtfhj7yeokoZaMAuUUMm+h+v/h/vgnjZHyWMC1";
    protected $_serviceLanguage = "zh_CN";
    protected $_serviceProduct = "";
    
    /**
     * 创建订单
     * @param unknown_type $data
     */
    public function createDeliveryOrder($data = array()){
    	/*
    	 * 设置url
    	*/
    	$url = $this->_serviceUrl."/order/addSmSalerOrder?customerId=".$this->_serviceCustomerId."&token=".$this->_serviceToken."&language=".$this->_serviceLanguage;
    
    	/*
    	 * 请求服务
    	*/
    	$result = $this->excuteService($url,$data);
    
    	/*
    	 * 处理请求结果
    	*/
    	return $result;
    }
    
    /**
     * 取消订单
     * @param unknown_type $data
     */
    public function cancelDeliveryOrder($data = array()){
    	$return = array();
    	/*
    	 * 设置url
    	*/
    	$url = $this->_serviceUrl."/order/cancelDeliveryOrder?customerId=".$this->_serviceCustomerId."&token=".$this->_serviceToken."&language=".$this->_serviceLanguage;
    
    	/*
    	 * 请求服务
    	*/
    	$result = $this->excuteService($url,$data);
    
    	/*
    	 * 处理请求结果
    	*/
    	$errorMessage = "";
    	if($result["data"]["ack"] == "N"){
    		foreach ($result["data"]["errors"] as $eKey=>$eVal){
    			$errorMessage .= $eVal["code"].";";
    		}
    	}
    	$return["ack"] = $result["data"]["ack"];
    	$return["message"] = $errorMessage;
    	 
    	return $return;
    
    }
    
    /**
     * 调用服务
     * @param unknown_type $url 请求服务连接
     * @param unknown_type $data 请求参数
     * @return mixed 调用结果
     */
    public function excuteService($url = "",$data){
    	$ch = curl_init($url);
    
    	$objJson = json_encode($data);

    	curl_setopt($ch,CURLOPT_URL,$url);
    
    	curl_setopt($ch,CURLOPT_POST,1);
    	curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    	curl_setopt($ch,CURLOPT_HTTPHEADER,array('Content-Type: application/json'));
    	curl_setopt($ch,CURLOPT_POSTFIELDS,$objJson);
    
    	$result = curl_exec($ch);
    
    	curl_close($ch);
        print_r($result);exit;
    	$result = json_decode($result,true);
    
    	return $result;
    }
}

?>