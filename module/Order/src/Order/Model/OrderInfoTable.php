<?php
namespace Order\Model;

use Zend\Db\TableGateway\TableGateway;
use Zend\Db\ResultSet\ResultSet;
use Zend\Db\Sql\Where;
use Zend\Db\Sql\Select;
use Common\Db\Table;

class OrderInfoTable extends Table
{

    protected $tableGateway;

    public function __construct(TableGateway $tableGateway)
    {
        parent::__construct($tableGateway, 'order_id');
    }
    
    /**
     * @param int $number
     */
    public function fetchNumber($start,$number,$where = null){
        $select = $this->tableGateway->getSql()->select();
//         $select = new Select();
        $select->offset($start);
        $select->limit($number);
        $select->order('order_id desc');
        $select->where($where);
        return $this->tableGateway->selectWith($select);
    }

    public function getOrderInfo($id)
    {
        $id = (int) $id;
        /**
         *
         * @var $rowset ResultSet
         */
        $rowset = $this->tableGateway->select(array(
            'order_id' => $id
        ));
        $row = $rowset->current();
        if (! $row) {
            throw new \Exception("Could not find row $id");
        }
        return $row;
    }

    public function saveOrderInfo(OrderInfo $orderInfo)
    {
        foreach ($orderInfo as $key => $value) {
        	if ($orderInfo->$key !== null) {
        		$data[$key] = $value;
        	}
        }
        $id = (int) $orderInfo->order_id;
        if ($id == 0) {
          $this->tableGateway->insert($data);
        } else {
            if ($this->getOrderInfo($id)) {
                $this->tableGateway->update($data, array(
                    'order_id' => $id
                ));
            } else {
                throw new \Exception('table id does not exist');
            }
        }
    }

    public function deleteOrderInfo($id)
    {
//         $this->tableGateway->delete(array(
//             'order_id' => (int) $id
//         ));
    }
    
    /**
     * @return ResultSet
     */
    public function invalidOrderInfo(){
//         $this->tableGateway->select(array("pay_status=2","order_status=2","add_time>".(time()-3600*24)));
        return $this->tableGateway->select(array("pay_status=2","add_time>".(time()-3600*24)));
    }
    
    /**
     * @return ResultSet
     */
    public function payedOrderInfo(){
        return $this->tableGateway->select(array("pay_status=2","pay_time>".(time()-3600*24)));
    }
    
    /**
     * @return ResultSet
     */
    public function analyticsOrders(){
        $time = strtotime(date('Y-m-d',time()));
        return $this->tableGateway->select(array("pay_status=2","pay_time>".$time));
    }
}

?>