<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/ZendSkeletonApplication for the canonical source repository
 * @copyright Copyright (c) 2005-2013 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */
namespace Application\Controller;

use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;
use Zend\Http\Client;
use Zend\Session\SessionManager;
use Zend\Session\Config\SessionConfig;
use Zend\Session\Storage\SessionStorage;
use Common\Model\Log;

class IndexController extends AbstractActionController
{

    public function indexAction()
    {
        phpinfo();exit;
//         $config = new SessionConfig();
//         $config->setOptions(array(
//     		'phpSaveHandler' => 'memcache',
//     		'savePath' => 'tcp://192.168.1.44:11211?persistent=1&weight=1&timeout=1&retry_interval=15',
//         ));
//         $manager = new SessionManager($config);
//         print_r($manager->getId());
//         $sessionStorage = new SessionStorage();
//         $sessionStorage->setMetadata("cd", 'afasd');
//         $manager->setStorage($sessionStorage);
//         print_r($manager->getStorage()->getMetadata());
//         exit;
        return new ViewModel();
    }

    public function memcacheAction(){
        $memcache = new \Memcache();
        $memcache->connect('192.168.1.44','11211');
        echo $memcache->get("cd");
        exit;
    }
    
    public function testAction()
    {
        $uri = "http://113.106.91.58:8889/xywl/api/service/wmc/order/addSmSalerOrder?customerId=143631&token=uMbYz4Dtfhj7yeokoZaMAuUUMm+h+v/h/vgnjZHyWMC1&language=en_US";
        $client = new Client($uri);
        $client->setMethod('POST');
        // $post = array('{"businessMemo":"","receiveOrderTime":"2013-12-24 19:46:23","custVoucherId":"2013122443082","customerCorpId":"YXGWCS","receiver":"\u94b1\u4f73\u4f73","receiverContact":"\u94b1\u4f73\u4f73","receiverContactTel":"13636441419","receiverPostcode":"201700","receiverAddress":"\u4e0a\u6d77\u4e0a\u6d77\u9752\u6d66\u533a","receiverStreet":"\u5916\u9752\u677e\u516c\u8def6999\u5f04123\u53f7502\u5ba4","transpostSum":"0.00","salesModel":"03","orderSource":"16","items":[{"productId":"yusun000096","unitPrice":"1399.00","planQty":"1","stockStatus":"ok","serialSign":"001"}]}');
        $post = array(
            '{\"businessMemo\":\"\",\"receiveOrderTime\":\"2013-12-24 19:46:23\",\"custVoucherId\":\"2013122443082\",\"customerCorpId\":\"YXGWCS\",\"receiver\":\"\u94b1\u4f73\u4f73\",\"receiverContact\":\"\u94b1\u4f73\u4f73\",\"receiverContactTel\":\"13636441419\",\"receiverPostcode\":\"201700\",\"receiverAddress\":\"\u4e0a\u6d77\u4e0a\u6d77\u9752\u6d66\u533a\",\"receiverStreet\":\"\u5916\u9752\u677e\u516c\u8def6999\u5f04123\u53f7502\u5ba4\",\"transpostSum\":\"0.00\",\"salesModel\":\"03\",\"orderSource\":\"16\",\"items\":[{\"productId\":\"yusun000096\",\"unitPrice\":\"1399.00\",\"planQty\":\"1\",\"stockStatus\":\"ok\",\"serialSign\":\"001\"}]}'
        );
        $client->setParameterPost($post);
        $respose = $client->send();
        print_r($respose->getBody());
        exit();
    }
}
