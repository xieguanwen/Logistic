<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 14-9-2
 * Time: 下午2:41
 */

namespace Analytics\Controller;

use Zend\Mvc\Controller\AbstractActionController;
use Order\Model\OrderInfo;
use Order\Model\OrderInfoTable;
use Order\Model\OrderGoodsTable;
use Zend\Http\Client;
use Zend\Uri\Uri;
use Zend\Http\Headers;
use Zend\Http\Header\UserAgent;
use Zend\Barcode\Renderer\Image;

class AnalyticsController extends AbstractActionController {
    /* 
     * @param
     *          v=1              // Version.
                &tid=UA-XXXX-Y   // Tracking ID / Property ID.
                &cid=555         // Anonymous Client ID.
        
                &t=transaction   // Transaction hit type.
                &ti=12345        // transaction ID. Required.
                &ta=westernWear  // Transaction affiliation.
                &tr=50.00        // Transaction revenue.
                &ts=32.00        // Transaction shipping.
                &tt=12.00        // Transaction tax.
                &cu=EUR          // Currency code.
     */
    public function indexAction(){
        set_time_limit(1800);
        date_default_timezone_set('Asia/Shanghai');
        
        /** @var $orderInfoTable Order\Model\OrderInfoTable **/
        $orderInfoTable = $this->getServiceLocator()->get("Order\Model\OrderInfoTable");
        $orderGoodsTable = $this->getServiceLocator()->get("Order\Model\OrderGoodsTable");
        $client = new Client();
        $headers = new Headers();
        $headers->addHeader(UserAgent::fromString('User-Agent: Mozilla/5.0 (Windows NT 6.3; WOW64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/30.0.1599.69 Safari/537.36'));
        $client->setHeaders($headers);
        $client->setMethod('GET');
        $uri = new Uri();
        $uri->setHost('www.google-analytics.com');
        $uri->setScheme('http');
        $uri->setPath('/collect');
        $uri->setPort(80);
                
        $query = array();        
        $resultSet = $orderInfoTable->analyticsOrders();
        foreach ($resultSet as $orderInfo) {
//         	$orderInfo = new OrderInfo();
            $query['v'] = 1;
            $query['tid'] = 'UA-46390821-1';
            $query['cid'] = $orderInfo->user_id;
            $query['t'] = 'transaction';
            $query['ti'] = $orderInfo->order_id;
            $query['ta'] = '小辣椒手机网站';        	
        	$query['tr'] = $orderInfo->money_paid;
        	$query['ts'] = $orderInfo->shipping_fee;
        	$query['tt'] = $orderInfo->tax;
        	$query['cu'] = 'CNY';
        	$uri->setQuery($query);
        	$client->setUri($uri->toString());
        	$response = $client->send();
//         	print_r($response->getStatusCode());exit;
        }
        
        exit;
    }
} 