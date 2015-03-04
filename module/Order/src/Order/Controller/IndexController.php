<?php
namespace Order\Controller;

use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;
use Zend\Paginator\Paginator;
use Zend\Paginator\Adapter\DbTableGateway;
use Common\Model\Log;

class IndexController extends AbstractActionController
{
    public function indexAction(){
//         echo strtotime('2014-03-1 00:00'); echo "-----";echo strtotime('2014-03-3 00:00');exit;
        $pageCount = 20;
        $page = $this->params()->fromRoute('page');
        if(isset($page) && !empty($page)){
            $page = (int)$page;
        } else {
            $page = 0;
        }
        $start = $page*$pageCount;
        
        
        
        $paginator = $this->getServiceLocator()->get('Order\Model\OrderInfoTable')->fetchAll(null,true);        
        $paginator->setCurrentPageNumber($page);      
        $paginator->setItemCountPerPage($pageCount);
        if($start>=$paginator->count()) $start = ($page-1) * $pageCount;
        $view = new ViewModel(array('paginator'=>$paginator));
        $view->setVariable('orders', $this->getServiceLocator()->get('Order\Model\OrderInfoTable')->fetchNumber($start,$pageCount));
        return $view;
        
        
//         $view = new ViewModel();
        
//         $view->setVariable('orders', $this->getServiceLocator()->get('Order\Model\OrderInfoTable')->fetchNumber($start,$pageCount));
//         $paginator = new Paginator(new DbTableGateway($this->getServiceLocator()->get('OrderInfoTableGateway')));
//         $paginator->setDefaultItemCountPerPage($pageCount);
//         $view->setVariable('paginator', $paginator);
//         $view->setVariable('pageCount', true);
//         $view->setVariable('route', $this->getRequest()->getUri()->getPath());
//         return $view;
    }
}