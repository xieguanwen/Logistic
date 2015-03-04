<?php
namespace Timer\Model;

use Zend\Http\Client;
use Zend\Http\Client\Adapter\Curl;
use Zend\Form\Annotation\Type;
use Zend\Config\Reader\Xml;
use Zend\Config\Config;
use Order\Model\OrderInfo;
use Order\Model\OrderGoodsTable;
use Zend\ServiceManager\ServiceManager;
use Zend\Json\Json;
use Zend\Db\Sql\Sql;
use Zend\Db\Sql\Where;
use Zend\Debug\Debug;
use Zend\Http\Headers;
use Zend\Http\Header\ContentType;
use Zend\Http\Request;
use Order\Model\OrderInfoTable;
use Zend\Db\ResultSet\ResultSet;

class Send extends Transport
{
    const SEND_ORDER_URI = "http://113.105.67.138:8088/xyapi/api/service/wmc/order/addSmSalerOrder?customerId=143631&token=uMbYz4Dtfhj7yeokoZaMAuUUMm+h+v/h/vgnjZHyWMC1&language=zh_CN";    
//     const SEND_ORDER_URI = "http://www.logisticapi.com/timer/receive";
    const SEND_INVALID_ORDER_URI = "http://113.105.67.138:8088/xyapi/api/service/wmc/order/deposeSmSalerOrder?customerId=143631&token=uMbYz4Dtfhj7yeokoZaMAuUUMm+h+v/h/vgnjZHyWMC1&language=zh_CN";
    
    
    /**
     * @param int $orderId
     * @return Ambigous <NULL, \Zend\Json\mixed, \Zend\Json\$_tokenValue, multitype:, multitype:Ambigous <\Zend\Json\mixed, \Zend\Json\$_tokenValue, NULL> >
     */
    public function sendOrder($orderId){
        $data=$this->json($orderId);
        if($data === null) return 2;
        $resultData = $this->excuteService(self::SEND_ORDER_URI, $data);
        return $this->analysisOfData($resultData);
    }
    
    public function sendOrderAll(){
        
        
    }

    /**
     * @param int $orderId
     * @return Ambigous <number, NULL, \Zend\Json\mixed, \Zend\Json\$_tokenValue, multitype:, multitype:Ambigous <\Zend\Json\mixed, \Zend\Json\$_tokenValue, NULL> >
     */
    public function testSendOrder($orderId){
    	$data=$this->json($orderId);
    	print_r($data);exit;
    	$resultData = $this->excuteService(self::SEND_ORDER_URI, $data);
    	return $this->analysisOfData($resultData);
    }
    
    protected function  json($orderId){
        
        $orderId = (int) $orderId;
        
        $orderInfoTable = $this->serviceManager->get('Order\Model\OrderInfoTable');
        /**
         * @var OrderInfo $orderInfo
         */
        $orderInfo = $orderInfoTable->getOrderInfo($orderId);

        $orderGoodsTable = $this->serviceManager->get('Order\Model\OrderGoodsTable');

        $dataObject = new \stdClass();
        $postData = null;

        $dataObject->businessMemo = $orderInfo->postscript.'#####'.$orderInfo->shipping_name.'#####'.$orderInfo->best_time;
        if(strlen($orderInfo->inv_payee) > 15) $dataObject->businessMemo = $dataObject->businessMemo.'#####'.$orderInfo->inv_payee;
        
        $dataObject->receiveOrderTime = date('Y-m-d H:i:s',$orderInfo->pay_time);
        $dataObject->custVoucherId = $orderInfo->order_sn;
//         $dataObject->customerCorpId = 'YXGWCS';// @todo 店铺编号
        $dataObject->customerCorpId = 'YXGW';
        $dataObject->receiver = $orderInfo->consignee;
        $dataObject->receiverContact = $orderInfo->consignee;
        $dataObject->receiverContactTel = $orderInfo->mobile;
        $zipcode = $orderInfo->zipcode;
        if (empty($zipcode)) {
        	$dataObject->receiverPostcode = '518000';
        } else {
            $dataObject->receiverPostcode = $orderInfo->zipcode;
        }
        $dataObject->receiverAddress = $this->getAddress($orderInfo->province,$orderInfo->city,$orderInfo->district);//@todo 没有完成;
        $address = str_replace('"', '', $orderInfo->address);
        $dataObject->receiverStreet = $address;
        $dataObject->transpostSum = $orderInfo->shipping_fee;
        $dataObject->salesModel = '1'; //销售模式 1：零售,2：代发3：批发,4：自提
        $dataObject->orderSource = '17';        

        $goods = $orderGoodsTable->getOrderGoodsByOrderId($orderId);

        $goodsItems = $this->mergeArray($goods);
        if(empty($goodsItems)) return null;

        $dataObject->items = $goodsItems;

//         return preg_replace('/"/','\"', preg_replace("#\\\u([0-9a-f]{4})#ie", "iconv('UCS-2BE', 'UTF-8', pack('H4', '\\1'))", $json->encode($dataObject)));
        return preg_replace("#\\\u([0-9a-f]{4})#ie", "iconv('UCS-2BE', 'UTF-8', pack('H4', '\\1'))", $this->json->encode($dataObject));
//         return $json->encode($dataObject);
    }
    
    /**
     * 
     */
    public function sendInvalidOrder(){
        /**
         * @var $orderInfoTable OrderInfoTable
         */
        $orderInfoTable = $this->serviceManager->get('Order\Model\OrderInfoTable');
        $resultSet = $orderInfoTable->invalidOrderInfo();
        foreach ($resultSet as $orderInfo) {
            $jsonData = '{"businessMemo":"'.$orderInfo->to_buyer.'","custVoucherId":"'.$orderInfo->order_sn.'"}';
            print_r($jsonData);exit;
        }
    }
    
    public function pretreatment(){
        /**
         * @var $orderInfoTable OrderInfoTable
         */
        $orderInfoTable = $this->serviceManager->get('Order\Model\OrderInfoTable');
        $sendOrderTable = $this->serviceManager->get('Timer\Model\SendOrderTable');
        $resultSet = $orderInfoTable->payedOrderInfo();
        foreach ($resultSet as $row) {
            if ($sendOrderTable->getSendOrderByOrderId($row->order_id) == null) {
            	$sendOrder = new SendOrder();
                $sendOrder->order_id = $row->order_id;
                $sendOrder->send_count = 0 ;
                $sendOrder->send_time = date('Y-m-d H:i:s',time());
//                 $sendOrder->pay_time = date('Y-m-d H:i:s',$row->pay_time);
                $sendOrder->status = 0;
                try {
                    $sendOrderTable->saveSendOrder($sendOrder);
                } catch (Exception $e) {
                }                
            }
        }
        
        
    }
    
    private function getAddress($province,$city,$district){
        $adapter = $this->serviceManager->get('Zend\Db\Adapter\Adapter');
        $sql = new Sql($adapter);
        $select = $sql->select();
        $select->columns(array('region_name'));
        $select->from('shouji_region');
        $where = new Where();
        $where->in('region_id',array($province,$city,$district));
        $select->where($where);
        
        $selectString = $sql->getSqlStringForSqlObject($select);
        
        $resultSet = $adapter->query($selectString, $adapter::QUERY_MODE_EXECUTE);
    	$address = '';
    	foreach ($resultSet as $row) {
    		$address = $address.$row['region_name'];
    	}
    	return $address;
    }
    
    private function requestSend($uri,$postKey,$postValue){
        $request = new Request();
        $request->setUri($uri);
        $request->setMethod('POST');
        $request->getPost()->set($postKey, $postValue);
        
        $client = new Client();
        $response = $client->dispatch($request);
        
        if ($response->isSuccess()) {
        	print_r($response->getBody());
        }
        exit;
    }
    
    /**
     * @param ResultSet $resultSet
     * @return multitype:\stdClass 
     */
    private function mergeArray(ResultSet $resultSet){
        $rows = iterator_to_array($resultSet);
        $items = array();
        $itemName = array();

        //modify product_id for goods_sn
        $productTable = $this->serviceManager->get('Order\Model\ProductTable');
        foreach ($rows as $key =>$orderGoods){
            if(intval($orderGoods->product_id) > 0){
                $product = $productTable->fetch($orderGoods->product_id);
                $rows[$key]->goods_sn = $product->product_sn;
            }
        }

        //合并相同的物料号(价格和数量相加)
        foreach ($rows as $key => $row) {
        	if (in_array($row->goods_sn, $itemName)) {
        	    $items[$row->goods_sn]->goods_price = $items[$row->goods_sn]->goods_price + $row->goods_price;  
        	    $items[$row->goods_sn]->goods_number = $items[$row->goods_sn]->goods_number + $row->goods_number;  
        	} else {
        	    $itemName[] = $row->goods_sn;
        	    $items[$row->goods_sn] = $row;
        	}
        }
        
        
        $returnArray = array();
        foreach ($items as $item) {
            if($item->is_real){
                $itemObject = new \stdClass();
                $itemObject->productId= trim($item->goods_sn);
                $itemObject->unitPrice= $item->goods_price;
                $itemObject->planQty= $item->goods_number;
                $itemObject->stockStatus = 'OK';
                $itemObject->serialSign= '001';
                $returnArray[] = $itemObject;
            }

        }
        return $returnArray;
    }
    
}

?>