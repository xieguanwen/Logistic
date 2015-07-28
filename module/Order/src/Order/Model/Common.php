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
    public static function getItemsns(OrderGoodsTable $orderGoodsTable,ProductTable $productTable,$orderId){
        $itemsns = '';

        $orderGoodsAll = $orderGoodsTable->fetchAll(array('order_id'=>$orderId));
        foreach($orderGoodsAll as $orderGoods){
            if(intval($orderGoods->product_id)>0){
                $product = $productTable->fetch($orderGoods->product_id);
                $itemsns .= $product->product_sn . ',';
            } else {
                $itemsns .= $orderGoods->goods_sn . ',';
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
            $prices .= $orderGoods->goods_price . ',';
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
            $numbers .= $orderGoods->goods_number . ',';
        }

        $numbers = rtrim($numbers,',');
        return $numbers;
    }


    public static function getShippingCode(ShippingTable $shippingTable,$shippingId){
        $shippingCode = $shippingTable->fetch($shippingId);
        return $shippingCode->shipping_code;
    }
}