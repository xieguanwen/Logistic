<?php
namespace Timer\Model;

use Order\Model\OrderInfoTable;

class Receive extends Transport
{
    const RECEIVE_ORDER_URI = "http://113.105.67.138:8088/xyapi/api/service/wmc/order/searchSmSalerOrderFinish?customerId=143631&token=uMbYz4Dtfhj7yeokoZaMAuUUMm+h+v/h/vgnjZHyWMC1&language=zh_CN";
    private $result;
    
    /**
     * @param string $orderId
     * @return Ambigous <\Timer\Model\mixed, mixed>
     */
    public function receiveOrder($orderId){
    	$dataObject = new \stdClass();
    	$dataObject->custVoucherId = $orderId;
    	$data = $this->json->encode($dataObject);
    	
    	$resultData = $this->excuteService(self::RECEIVE_ORDER_URI, $data);
    	$this->result = $resultData;
    	return $this->analysisOfData($resultData);
    }
    
    public function pretreatment(){
        /**
         * @var $orderInfoTable OrderInfoTable
         */
        $orderInfoTable = $this->serviceManager->get('Order\Model\OrderInfoTable');
        $receiveOrderTable = $this->serviceManager->get('Timer\Model\ReceiveOrderTable');
        $resultSet = $orderInfoTable->payedOrderInfo();
        
        foreach ($resultSet as $row) {
        	if ($receiveOrderTable->fetchByOrderId($row->order_id) == null) {
        		$receiveOrder = new ReceiveOrder();
        		$receiveOrder->order_id = $row->order_id;
        		$receiveOrder->order_sn = $row->order_sn;
        		$receiveOrder->count = 0;
        		$receiveOrder->status = 0;
        		try {
        			$receiveOrderTable->save($receiveOrder);
        		} catch (Exception $e) {
        		}
        	}
        }
    }
    
    
    public function getResult(){
        return $this->result;
    }
    
}