<?php
/**
 * Created by PhpStorm.
 * User: xieguanwen
 * Date: 15-1-19
 * Time: 下午2:05
 */

namespace Order\Model;

use Common\Model\Entity;

class Product extends Entity {
    public $product_id;
    public $goods_id;
    public $goods_attr;
    public $product_sn;
    public $product_number;

    public function exchangeArray($data)
    {
        foreach ($data as $key => $value) {
            $this->$key = $value;
        }
    }
}