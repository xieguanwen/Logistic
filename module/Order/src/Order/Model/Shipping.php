<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2015/7/27
 * Time: 17:28
 */

namespace Order\Model;


use Common\Model\Entity;

class Shipping extends Entity {
    public $shipping_id;
    public $shipping_code;
    public $shipping_name;
    public $shipping_desc;
    public $insure;
    public $support_cod;
    public $enabled;
    public $shipping_print;
    public $print_bg;
    public $config_lable;
    public $print_model;
    public $shipping_order;
}