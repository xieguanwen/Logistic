<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2015/7/27
 * Time: 15:10
 */

namespace Order\Model;

class Common {

    /**
     * @param OrderGoodsTable $orderGoodsTable
     * @param ProductTable $productTable
     * @param $orderId
     * @return string
     * @throws \Exception
     */
    public static function getItemsns(OrderGoodsTable $orderGoodsTable,$orderId){
        $itemsns = '';

        $orderGoodsAll = $orderGoodsTable->fetchAll(array('order_id'=>$orderId));
        foreach($orderGoodsAll as $orderGoods){
            if($orderGoods->is_real == 1 && strtoupper(substr($orderGoods->goods_sn,0,5))!='AAAAA')
                $itemsns .= ltrim($orderGoods->goods_sn,'-') . ',';
        }

        $itemsns = rtrim($itemsns,',');
        return $itemsns;
    }

    /**
     * @param OrderGoodsTable $orderGoodsTable
     * @param ProductTable $productTable
     * @param $orderId
     * @return string
     * @throws \Exception
     */
    public static function getSkusns(OrderGoodsTable $orderGoodsTable,ProductTable $productTable,$orderId){
        $itemsns = '';

        $orderGoodsAll = $orderGoodsTable->fetchAll(array('order_id'=>$orderId));
        foreach($orderGoodsAll as $orderGoods){
            if($orderGoods->is_real == 1 && strtoupper(substr($orderGoods->goods_sn,0,5))!='AAAAA'){
                $product = $productTable->fetch($orderGoods->product_id);
                $itemsns .= ltrim($product->product_sn,'-') . ',';
            }
        }

        $itemsns = rtrim($itemsns,',');
        return $itemsns;
    }

    /**
     * @param OrderGoodsTable $orderGoodsTable
     * @param $orderId
     * @return string
     */
    public static function getPrices(OrderGoodsTable $orderGoodsTable,$orderId){
        $prices = '';

        $orderGoodsAll = $orderGoodsTable->fetchAll(array('order_id'=>$orderId));
        foreach($orderGoodsAll as $orderGoods){
            if($orderGoods->is_real == 1 && strtoupper(substr($orderGoods->goods_sn,0,5))!='AAAAA'){
                $prices .= $orderGoods->goods_price . ',';
            }
        }

        $prices = rtrim($prices,',');
        return $prices;
    }

    /**
     * @param OrderGoodsTable $orderGoodsTable
     * @param $orderId
     * @return string
     */
    public static function getNumbers(OrderGoodsTable $orderGoodsTable,$orderId){
        $numbers = '';

        $orderGoodsAll = $orderGoodsTable->fetchAll(array('order_id'=>$orderId));
        foreach($orderGoodsAll as $orderGoods){
            if($orderGoods->is_real == 1 && strtoupper(substr($orderGoods->goods_sn,0,5))!='AAAAA'){
                $numbers .= $orderGoods->goods_number . ',';
            }
        }

        $numbers = rtrim($numbers,',');
        return $numbers;
    }


    /**
     * @param ShippingTable $shippingTable
     * @param $shippingId
     * @return mixed
     * @throws \Exception
     */
    public static function getShippingCode(ShippingTable $shippingTable,$shippingId){
        $shippingCode = $shippingTable->fetch($shippingId);
        $shippingCodeArray = array('sf_express'=>'SF','ems'=>'EMS','yto'=>'YTO');
        return $shippingCodeArray[$shippingCode->shipping_code];
    }
}