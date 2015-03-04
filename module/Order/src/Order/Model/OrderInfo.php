<?php
namespace Order\Model;

use Common\Model\Entity;
class OrderInfo extends Entity
{
    public $order_id;
    public $order_sn;
    public $user_id;
    public $order_status;
    public $shipping_status;
    public $pay_status; //支付状态；0，未付款；1，付款中 ；2，已付款
    public $consignee;
    public $country;
    public $province;
    public $city;
    public $district;
    public $address;
    public $zipcode;
    public $tel;
    public $mobile;
    public $email;
    public $best_time;
    public $sign_building;
    public $postscript;
    public $shipping_id;
    public $shipping_name;
    public $pay_id;
    public $pay_name;
    public $how_oos;
    public $how_surplus;
    public $pack_name;
    public $card_name;
    public $card_message;
    public $inv_payee;
    public $inv_content;
    public $goods_amount;
    public $shipping_fee;
    public $insure_fee;
    public $pay_fee;
    public $pack_fee;
    public $card_fee;
    public $money_paid;
    public $surplus;
    public $integral;
    public $integral_money;
    public $bonus;
    public $order_amount;
    public $from_ad;
    public $referer;
    public $add_time;
    public $confirm_time;
    public $pay_time;
    public $shipping_time;
    public $pack_id;
    public $card_id;
    public $bonus_id;
    public $invoice_no;
    public $extension_code;
    public $extension_id;
    public $to_buyer;
    public $pay_note;
    public $agency_id;
    public $inv_type;
    public $tax;
    public $is_separate;
    public $parent_id;
    public $discount;
    public $order_time_limit;
    public $kuaidi;
    public $kuaidi_result;

    public function exchangeArray($data)
    {
        foreach ($data as $key => $value) {
            $this->$key = $value;
        }
    }
}