<?php
namespace Logistic\Controller;

use Zend\Mvc\MvcEvent;
use Zend\Mvc\Controller\AbstractRestfulController;
use Zend\View\Model\JsonModel;

class ApilogisticController extends AbstractRestfulController
{
    public function indexAction(){
        return new JsonModel(array($this->request->getQuery()));
    }
    
    public function cAction(){
        return new JsonModel(array($this->request->getQuery()));
    }
    
    public function get($id){
        return new JsonModel(array($this->request->getQuery()));
    }
    
    
    public function getList()
    {
        $a = new \stdClass();
        $a->kk = 3;
        die(json_encode($a));
        
    	return new JsonModel($a);
    }
}