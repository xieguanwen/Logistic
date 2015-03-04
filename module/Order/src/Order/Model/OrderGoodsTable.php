<?php
namespace Order\Model;

use Zend\Db\TableGateway\TableGateway;
use Zend\Db\ResultSet\ResultSet;
use Common\Db\Table;

class OrderGoodsTable extends Table
{

    /**
     *
     * @var TableGateway
     */
    protected $tableGateway;

    public function __construct(TableGateway $tableGateway)
    {
        parent::__construct($tableGateway, 'rec_id');
    }


    public function getOrderGoods($id)
    {
        $id = (int) $id;
        /**
         *
         * @var $rowset ResultSet
         */
        $rowset = $this->tableGateway->select(array(
            'rec_id' => $id
        ));
        $row = $rowset->current();
        if (! $row) {
            throw new \Exception("Could not find row $id");
        }
        return $row;
    }

    /**
     *
     * @param int $orderId            
     * @return \ArrayObject
     */
    public function getOrderGoodsByOrderId($orderId)
    {
        $id = (int) $orderId;
        $resultSet = $this->tableGateway->select(array(
            'order_id' => $id
        ));
        return $resultSet;
    }

    public function saveOrderGoods(OrderGoods $orderGoods)
    {
        // $data = array(
        // 'artist' => $album->artist,
        // 'title' => $album->title,
        // );
        
        // $id = (int) $album->id;
        // if ($id == 0) {
        // $this->tableGateway->insert($data);
        // } else {
        // if ($this->getAlbum($id)) {
        // $this->tableGateway->update($data, array('id' => $id));
        // } else {
        // throw new \Exception('Album id does not exist');
        // }
        // }
    }

    public function deleteOrderGoods($id)
    {
//         $this->tableGateway->delete(array(
//             'order_id' => (int) $id
//         ));
    }
}

?>