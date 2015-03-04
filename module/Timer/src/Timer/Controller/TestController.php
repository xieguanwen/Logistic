<?php
namespace Timer\Controller;

use Zend\Mvc\Controller\AbstractActionController;
use Zend\EventManager\EventManagerInterface;
use Zend\View\Model\JsonModel;
use Zend\Mvc\MvcEvent;

class TestController extends AbstractActionController
{
    protected function attachDefaultListeners(){
    	parent::attachDefaultListeners();
    	echo "ok";
    }
    
    /**
     *
     * @see \Zend\Mvc\Controller\AbstractController::setEventManager()
     */
    public function setEventManager(EventManagerInterface $events)
    {
    	parent::setEventManager($events);
    	$this->init();
    }
       
    public function preDispatch(MvcEvent $e)
    {
    	echo 4;
    }
    
    public function postDispatch()
    {
    	echo 5;
    }
    
    public function onDispatch(MvcEvent $e){
        echo 18;
        return parent::onDispatch($e);
    }
    
//     public function onRender(MvcEvent $e){
//         echo 18;
//     }
    
    public function preRender()
    {
    	echo 7;
    }
    
    public function postRender()
    {
    	echo 8;
    }
    
    public function init()
    {
    	echo 3;
    }
    
    public function __construct()
    {
    	echo 2;
    }
    
    public function indexAction(){
        echo "--index--";exit;
//         $variables = array( 'Foo' => 'Bar', 'Baz' => 'Test' );
//         $json = new JsonModel( $variables );
//         return $json;
    }
}