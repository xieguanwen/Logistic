<?php
namespace Timer\Controller;

use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;
use Order\Model\OrderInfoTable;
use Timer\Model\Send;
use Zend\Debug\Debug;
use Timer\Model\SendOrder;
use Timer\Model\SendOrderTable;
use Zend\Log\Logger;
use Zend\Log\Writer\Stream;
use Timer\Model\API_4PX_PlatformFPXWebServiceProcess;
use Common\Model\Log;
use Timer\Model\Receive;
use Order\Model\OrderInfo;
use Zend\Http\Client;

class TimerController extends AbstractActionController
{
    // protected $albumTable;
    public function indexAction()
    {
        set_time_limit(1800);
        date_default_timezone_set('Asia/Shanghai');

        $send = new Send($this->getserviceLocator()); //定义发送对象
        $send->pretreatment();
        /**
         * @var $orderInfoTable OrderInfoTable
         */
        $orderInfoTable = $this->getserviceLocator()->get('Order\Model\OrderInfoTable');
//         print_r($orderInfoTable->getOrderGoodsByOrderId(2844));
        $sendOrderTable = $this->getserviceLocator()->get('Timer\Model\SendOrderTable');


        $resultSet = $sendOrderTable->fetchNotSendOrder();

        foreach ($resultSet as $sendOrder) {
            $result = $send->sendOrder($sendOrder->order_id);
            if((int)$result == 0){
                $sendOrder->status = 1;
                $sendOrder->send_count = $sendOrder->send_count + 1;
                $sendOrderTable->saveSendOrder($sendOrder);

                //save 备货中(3)
                $orderInfo = new OrderInfo();
                $orderInfo->order_id = $sendOrder->order_id;
                $orderInfo->shipping_status = 3;
                try {
                    $orderInfoTable->saveOrderInfo($orderInfo);
                } catch (Exception $e) {
                }
            } else {
                $orderGoodsTable = $this->getserviceLocator()->get('Order\Model\OrderGoodsTable');
                $row = $orderGoodsTable->getOrderGoodsByOrderId($sendOrder->order_id);
                $content = print_r(iterator_to_array($row),true);
                $content = $result ."\n". $content;
                Log::file('./data/log/logistic', null, $content);

                if($sendOrder->send_count >=3) {
                    $sendOrder->status = 4;
                }
                $sendOrder->send_count = $sendOrder->send_count + 1;
                $sendOrderTable->saveSendOrder($sendOrder);
            }
        }
        exit(0);
    }

    public function receiveAction(){
        set_time_limit(1800);
        date_default_timezone_set('Asia/Shanghai');
        $receive = new Receive($this->getserviceLocator()); //定义接收对象
        $receive->pretreatment(); //数据处理

        $orderInfoTable = $this->getServiceLocator()->get('Order\Model\OrderInfoTable');
        $client = new Client();

        $receiveOrderTable = $this->getServiceLocator()->get('Timer\Model\ReceiveOrderTable');
        $resultSet = $receiveOrderTable->fetchNotReceiveOrder();
        foreach ($resultSet as $receiveOrder) {
            $result = $receive->receiveOrder($receiveOrder->order_sn);
            if((int)$result == 0){
                //格式化数据
                $arrays = json_decode($receive->getResult())->data;
                $stdClass = $arrays[0];

                //save 物流号在order_info表中
                $orderInfo = new OrderInfo();
                $orderInfo->order_id = $receiveOrder->order_id;
                $orderInfo->invoice_no = $stdClass->transportNo;
                $orderInfo->shipping_status = 1;
                $orderInfoTable->saveOrderInfo($orderInfo);

                //save 接收表
                $receiveOrder->status = 1;
                $receiveOrder->count = $receiveOrder->count + 1;
                $receiveOrderTable->save($receiveOrder);

                try {
                    //发送物流
                    $client->setUri('http://www.xiaolajiao.com/kuaidi.php?com='.$receiveOrder->order_id);
                    $client->setMethod('GET');
                    $response = $client->send();

                    //发送短信
                    $orderInfo1 = $orderInfoTable->getOrderInfo($receiveOrder->order_id);
                    $msg = "亲爱的用户，您的手机已经由{$orderInfo1->shipping_name}发出，快递单号：{$orderInfo1->invoice_no}，如有任何问题请联系客服4001665678，祝您生活愉快!【小辣椒】";
                    $url = "http://www.xiaolajiao.com/sendsms.php?mobile={$orderInfo1->mobile}&applicationkey=smskey&msg={$msg}";
                    $client->setUri($url);
                    $client->send();
                } catch (Exception $e) {
                }
            } else {
                //错误写日志
                $content = $receiveOrder->order_id . print_r($receive->getResult(),true);

                Log::file('./data/log/logistic', 'receive'.date("Y-m-d",time()).'.txt', $content."\n");

                //三次已后不再接收数据
//         		if($receiveOrder->count >=3) {
//         			$receiveOrder->status = 4;
//         		}
                //加一次
                $receiveOrder->count = $receiveOrder->count + 1;
                $receiveOrderTable->save($receiveOrder);
            }
        }
        exit;
    }

    public function addsendrecordAction(){
//         $sendOrder = new SendOrder();
//         $sendOrder->send_time = time();
//         $sendOrder->order_id = 1221;
//         $sendOrder->status = 0;
//         /**
//          * @var SendOrderTable $sendOrderTable
//          */
//         $sendOrderTable = $this->getServiceLocator()->get('Timer\Model\SendOrderTable');
// //         Debug::dump($sendOrderTable);exit;
//         $sendOrderTable->saveSendOrder($sendOrder);
//         echo "ok";
//         exit;
    }


    public function invalidAction(){
        echo 'askdfkaskdf';exit;
        set_time_limit(9999999);
        $send = new Send($this->getServiceLocator());
        $send->sendInvalidOrder();
        exit;
    }


    public function testsendAction()
    {
        set_time_limit(1800);
        date_default_timezone_set('Asia/Shanghai');

        $send = new Send($this->getserviceLocator()); //定义发送对象
        $sendOrderTable = $this->getserviceLocator()->get('Timer\Model\SendOrderTable');
        $send->testSendOrder(12950);
        exit(0);
    }

}