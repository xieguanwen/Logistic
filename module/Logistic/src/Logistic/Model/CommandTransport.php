<?php
namespace Logistic\Model;

use Order\Model\OrderInfo;
use Timer\Model\ReceiveOrder;
use Timer\Model\ReceiveOrderTable;
use Zend\EventManager\EventManager;
use Common\Model\Log;
use Zend\Db\ResultSet\ResultSet;
use Order\Model\OrderInfoTable;
use Timer\Model\UpdateLogisticTable;
use Order\Model\OrderGoodsTable;
use Timer\Model\SendOrderTable;
use Timer\Model\SendOrder;
use Zend\Http\Client;

class CommandTransport {
    private $receiver;
    private $sender;
    private $eventManager;
    private $xml;
    const SEND_COUNT = 4;
    const INVALID_ORDER = 'deposeSmSalerOrder';
    const SEND_ORDER = 'ecerp.trade.add_order_new';
    const SEARCH_SHIPPING_FINISH = 'searchSmSalerOrderFinish';
    
    public function __construct(Receiver $receiver,Sender $sender,EventManager $eventManager){
        $this->receiver = $receiver;
        $this->sender = $sender;
        $this->eventManager = $eventManager;
        $this->log('sendOrderError');
        $this->log('sendOrder');
        $this->log('receiveOrder');
        $this->log('receiveOrderError');
        $this->xml = new LogisticXml();
    }

    /**
     * 接收发货订单信息
     *
     * @param OrderInfo $orderInfo
     * @return bool
     */
    public function receiveOrder(OrderInfo $orderInfo){
        $response = $this->sender->sendParam($this->receiver->receiveOrderData($orderInfo),'ecerp.sendorder.get','GET');
        $logisticNo = $this->xml->getWldh($response->getBody());
        if($logisticNo){
            $result = $logisticNo;
        } else {
            $this->eventManager->trigger('receiveOrderError',null,array($orderInfo->order_id,$orderInfo->order_sn,$this->xml->getException()->getMessage(),$this->xml->getException()->getCode()));
            $result = false;
        }
        return $result;

    }
    
    /**
     * 发送作废订单
     */
    public function sendInvalidOrder(OrderInfo $orderInfo){
        $result = $this->sender->send($this->receiver->invalidOrderData($orderInfo),self::INVALID_ORDER);
        $this->eventManager->trigger('sendOrder',null,$orderInfo);
        return $result;
    }
    
    /**
     * 发送订单信息
     */
    public function sendOrder(OrderInfo $orderInfo,OrderGoodsTable $orderGoodsTable){
        try{
            $response = $this->sender->sendParam($this->receiver->sendOrderData($orderInfo,$orderGoodsTable), self::SEND_ORDER,'GET');
        } catch (\Exception $e){
            $this->eventManager->trigger('sendOrderError',null,array($orderInfo->order_id,$orderInfo->order_sn,$e->getMessage(),$e->getCode()));
            return $result = false;
        }
        if($this->xml->getTid($response->getBody())){
            $result = true;
        } else {
            $responseCollection = $this->eventManager->trigger('sendOrderError',null,array($orderInfo->order_id,$orderInfo->order_sn,$this->xml->getException()->getMessage(),$this->xml->getException()->getCode()));
            $responseCollection->rewind();
            $result = false;
        }
        return $result;
    }
    
    /**
     * 发送更新订单信息
     */
    public function sendUpdateOrder(){
        
    }
    
    /**
     * 批量接收发货订单信息
     */
    public function batchReceiveShipOrder(){
        
    }
    
    /**
     * 批量发送作废订单
     */
    public function batchSendInvalidOrder(UpdateLogisticTable $updateLogisticTable ,OrderInfoTable $orderInfoTable){
        $resultSet = $updateLogisticTable->fetchAll(array('status=0','type=1'));
        foreach ($resultSet as $row) {
//         	$row = new UpdateLogistic();
        	$orderInfo = $orderInfoTable->fetch($row->order_id);        	
            try {
                $result = $this->sendInvalidOrder($orderInfo);
                $receiveResult = $this->receiver->receive($updateLogisticTable,$row,$result);
                if(!$receiveResult) {
                    $this->eventManager->trigger('sendOrderError',null,$row);
                }
            } catch (Exception $e) {
                $this->eventManager->trigger('sendOrderError',null,$e);
            }
        	
        }
    }

    /**
     * 批量发送订单信息
     *
     * @param SendOrderTable $sendOrderTable
     * @param OrderInfoTable $orderInfoTable
     * @param OrderGoodsTable $orderGoodsTable
     * @throws \Exception
     */
    public function batchSendOrder(SendOrderTable $sendOrderTable,OrderInfoTable $orderInfoTable,OrderGoodsTable $orderGoodsTable){

        $resultSet = $sendOrderTable->fetchAll(array('status=0'));
        $client = new Client();

        foreach ($resultSet as $sendOrder) {
            $orderInfo = $orderInfoTable->fetch($sendOrder->order_id);
            //$this->eventManager->trigger('sendOrder',null,array($sendOrder,$orderInfo->order_sn));
        	$result = $this->sendOrder($orderInfo, $orderGoodsTable);
        	if($result){
        	    //save 发送成功
        		$sendOrder->status = 1;
        		$sendOrder->send_count = $sendOrder->send_count + 1;
        		$sendOrderTable->save($sendOrder);
        	
        		//save 备货中(3)
        		$orderInfo = new OrderInfo();
        		$orderInfo->order_id = $sendOrder->order_id;
        		$orderInfo->shipping_status = 3;
        		try {
        			$orderInfoTable->save($orderInfo);
        		} catch (Exception $e) {
        		}

        	} else {
//        		$row = $orderGoodsTable->fetchAll(array('order_id'=>$sendOrder->order_id));
//        		$content = print_r(iterator_to_array($row),true);

                //$content = $orderInfo->order_sn;
        		//$content = $result ."\n". $content;
        		//$this->eventManager->trigger('sendOrderError',null,array($content));
        	
        		if($sendOrder->send_count >=self::SEND_COUNT) {
        			$sendOrder->status = 4;
        		}
        		$sendOrder->send_count = $sendOrder->send_count + 1;
        		$sendOrderTable->save($sendOrder);
        	}
        }
    }

    /**
     * @param ReceiveOrderTable $receiveOrderTable
     * @param OrderInfoTable $orderInfoTable
     * @throws \Exception
     */
    public function batchReceiveOrder(ReceiveOrderTable $receiveOrderTable,OrderInfoTable $orderInfoTable){
        $resultSet = $receiveOrderTable->fetchAll(array('status=0'));
        $client = new Client();
        foreach ($resultSet as $receiveOrder) {
            $orderInfo = $orderInfoTable->fetch($receiveOrder->order_id);
            $invoiceNo = $this->receiveOrder($orderInfo,$receiveOrderTable);
            if($invoiceNo !== false){
                //改变发货状态
                $orderInfo->shipping_status = 1;
                $orderInfo->invoice_no = $invoiceNo;
                $orderInfoTable->save($orderInfo);

                //save receive success
                $receiveOrder->status = 1;
                $receiveOrder->count = $receiveOrder->count + 1;
                $receiveOrderTable->save($receiveOrder);

                try {
                    //get kuaidi information
                    $client->setUri('http://www.xiaolajiao.com/kuaidi.php?com='.$receiveOrder->order_id);
                    $client->setMethod('GET');
                    $response = $client->send();

                    //发送短信
                    $msg = "亲爱的用户，您的手机已经由{$orderInfo->shipping_name}发出，快递单号：{$orderInfo->invoice_no}，如有任何问题请联系客服4001665678，祝您生活愉快!【小辣椒】";
                    $url = "http://www.xiaolajiao.com/sendsms.php?mobile={$orderInfo->mobile}&applicationkey=smskey&msg={$msg}";
                    $client->setUri($url);
                    $client->send();

                } catch (Exception $e) {
                    $this->eventManager->trigger('receiveOrderError',null,$e);
                }
            } else {
                //save receive fault
                $receiveOrder->count = $receiveOrder->count + 1;
                $receiveOrderTable->save($receiveOrder);
            }
        }

    }
    
    /**
     * 批量发送更新订单信息
     */
    public function batchSendUpdateOrder(UpdateLogisticTable $updateLogisticTable,OrderInfoTable $orderInfoTable,OrderGoodsTable $orderGoodsTable){
        $resultSet = $updateLogisticTable->fetchAll(array('status=0','type=0'));
        foreach ($resultSet as $row) {
        	//         	$row = new UpdateLogistic();
        	$orderInfo = $orderInfoTable->fetch($row->order_id);
        	try {
        	    $resultInvalid = $this->sendInvalidOrder($orderInfo);
        	    if ($resultInvalid == 0) {
        	    	$resultSend = $this->sendOrder($orderInfo,$orderGoodsTable);
        	    	print_r($resultSend);exit;
        	    	$receiveResult = $this->receiver->receive($updateLogisticTable,$row,$resultSend);
            	    if(!$receiveResult) {
                        $this->eventManager->trigger('sendOrderError',null,$row);
                    }
        	    } else {
        	       $this->eventManager->trigger('sendOrderError',null,$row);
        	    }
        		    		
        	} catch (Exception $e) {
        		$this->eventManager->trigger('sendOrderError',null,$e);
        	}
        	 
        }
    }
    
    /**
     * 预先的处理发送数据
     * 
     * @param OrderInfoTable $orderInfoTable
     * @param SendOrderTable $sendOrderTable
     */
    public function sendOrderPretreatment(OrderInfoTable $orderInfoTable,SendOrderTable $sendOrderTable){
    	$resultSet = $orderInfoTable->fetchAll(array("pay_status=2","shipping_status=0","pay_time>".(time()-3600*24*2)));
    	foreach ($resultSet as $row) {
    		if ($sendOrderTable->getSendOrderByOrderId($row->order_id) == null) {
    			$sendOrder = new SendOrder();
    			$sendOrder->order_id = $row->order_id;
    			$sendOrder->send_count = 0 ;
    			$sendOrder->send_time = date('Y-m-d H:i:s',time());
    			//                 $sendOrder->pay_time = date('Y-m-d H:i:s',$row->pay_time);
    			$sendOrder->status = 0;
    			try {
    				$sendOrderTable->save($sendOrder);
    			} catch (\Exception $e) {
                    print_r($e->getTrace());
    			}
    		}
    	}
    }

    /**
     * 预先的处理接收订单信息
     *
     * @param OrderInfoTable $orderInfoTable
     * @param ReceiveOrderTable $receiveOrderTable
     */
    public function receiveOrderPretreatment(OrderInfoTable $orderInfoTable,ReceiveOrderTable $receiveOrderTable){
        $resultSet = $orderInfoTable->fetchAll(array('pay_status=2','shipping_status=3','pay_time>'.(time()-3600*24)));
        foreach ($resultSet as $row) {
            try{
                $receiveOrderTable->fetchOne(array('order_id'=>$row->order_id));
                continue;
            }catch (\Exception $e){
            }
            $receiveOrder = new ReceiveOrder();
            $receiveOrder->order_id = $row->order_id;
            $receiveOrder->order_sn = $row->order_sn;
            $receiveOrder->count = 0;
            $receiveOrder->status = 0;
            try{
                $receiveOrderTable->save($receiveOrder);
            } catch (\Exception $e){
            }
        }
    }
    
    private function log($eventName){
        $this->eventManager->attach($eventName,function ($e){
            Log::file('./data/log/logistic', date("Y-m-d",time()).$e->getName().'.txt', print_r($e->getParams(),true));
        });
    }
}