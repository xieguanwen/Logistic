<?php
namespace Order\Model;

use Common\Model\Entity;
class OrderGoods extends Entity
{

    public $rec_id;
    public $order_id;

    public $goods_id;

    public $goods_name;

    public $goods_sn;

    public $product_id;

    public $goods_number;

    public $market_price;

    public $goods_price;

    public $goods_attr;

    public $send_number;

    public $is_real;

    public $extension_code;

    public $parent_id;

    public $is_gift;

    public $goods_attr_id;

    public $refund_reason;

    public $refund_desc;

    public $refund_pic1;

    public $refund_pic2;

    public $refund_pic3;

    public $refund_add_time;

    public $refund_confirm_time;

    public $refund_confirm_desc;

    public $refund_status;

    public function exchangeArray($data)
    {
        foreach ($data as $key => $value) {
            $this->$key = $value;
        }
    }
}