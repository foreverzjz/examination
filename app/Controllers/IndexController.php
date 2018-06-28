<?php
/**
 * 默认控制器
 * User: Peter Pan
 * Date: 2017/6/29
 * Time: 21:26
 */

namespace Controllers;

use Core\Base\Controller;
use Library\Common;
use Wrappers\PromotionLogWrapper;
use Business\LogBusiness;


class IndexController extends Controller
{

    public function indexAction()
    {
        echo 'Hi,ALl!';
        exit();
    }

    public function testAction() {
        var_dump(json_encode(array('order_id' => 'ra3fq234354')));exit;
        $orderId = 'ra3fq234354';
        $logObj = new LogBusiness();
        $re = $logObj->cancel($orderId);
        var_dump($re);
        exit;
        $data = array(
            'token' => 'ab3ccc0751574223210fa569e4eff40e',
            'promotion_id' => 4,
            'shop_id' => 52,
            'type' => 1,
        );
        var_dump(rawurlencode(json_encode($data)));
        exit;
        $orderInfo = array(
            'user_id' => 12,
            'device_id' => 'fafeawfeaw',
            'app_version' => '5.1',
            'client_type' => 1,
            'order_id' => 'ra3fq234354',
            'school_id' => 30,
            'shop_id' => 2,
            'city_id' => 4,
            'day' => '20180111',
        );
        $promotionInfo = array(
            array(
                'promotion_id' => 1,
                'goods_id' => 2,
                'goods_num' => 1,
            ),
            array(
                'promotion_id' => 1,
                'goods_id' => 3,
                'goods_num' => 1,
            ),
        );
        $logObj = new LogBusiness();
        $re = $logObj->createBatch($orderInfo,$promotionInfo);
        var_dump($re);
        exit;

        $data = array(
            'promotion_id' => 1,
            'user_id' => 12,
            'device_id' => 'fafeawfeaw',
            'app_version' => '5.1',
            'client_type' => 1,
            'order_id' => 'ra3fq234354',
            'school_id' => 30,
            'shop_id' => 2,
            'city_id' => 4,
            'goods_id' => 45464,
            'goods_num' => 1,
            'day' => '20180111',
        );
        $data = array(
            'user_id' => 12,
            'device' => 'faefawfeawe',
            'client_type' => 1,
            'goods_list' => array(
                1 =>  array(
                    'goods_id' => 1,
                    'price' => 5,
                    'number' => 2,
                    'ori_price' => 10,
                ),
                2 => array(
                    'goods_id' => 2,
                    'price' => 2,
                    'number' => 2,
                    'ori_price' => 10,
                ),
                3 => array(
                    'goods_id' => 3,
                    'price' => 30,
                    'number' => 3,
                    'ori_price' => 10,
                ),
                4 => array(
                    'goods_id' => 20,
                    'price' => 1,
                    'number' => 1,
                    'ori_price' => 10,
                ),
            ),
        );
        var_dump(json_encode($data));
        exit;
        $data = array(
            'promotion_id' => 1,
            'user_id' => 12,
            'device_id' => 'fafeawfeaw',
            'app_version' => '5.1',
            'client_type' => 1,
            'order_id' => 'ra3fq234354',
            'school_id' => 30,
            'shop_id' => 2,
            'city_id' => 4,
            'goods_id' => 45464,
            'goods_num' => 1,
            'day' => '20180111',
        );
        $logWrapper = new PromotionLogWrapper($data);
        $logObj = new LogBusiness();
        //$re = $logObj->create($logWrapper);
        //$re = $logObj->listByOrder('ra3fq234354');
        $re = $logObj->cancel('ra3fq234354');
        var_dump($re);exit;
    }
}